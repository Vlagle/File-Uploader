<?php
/*
+--------------------------------------------------------------------------
|   Alex Download Engine
|   ========================================
|   by Alex H&ouml;ntschel
|   (c) 2002 AlexScriptEngine
|   http://www.alexscriptengine.de
|   ========================================
|   Web: http://www.alexscriptengine.de
|   Email: info@alexscriptengine.de
+---------------------------------------------------------------------------
|
|   > Beschreibung
|   > Dateisuche
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|   > $Id: search.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

include_once('lib.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");

$tpl->register('title', $lang['title_search']);

if(!$auth->user['canuseenginesearch']) {
	header("Location: ".$sess->url("index.php"));
	exit;
}

if ($_GET['action'] == 'search') {
    $tpl->loadFile('main', 'search.html');
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['search_search'] => '')));
    $tpl->register(array(
                        'search_search' => $lang['search_search'],
                        'search_searchphrase' => $lang['search_searchphrase'],
                        'search_define_a_searchphrase' => $lang['search_define_a_searchphrase'],
                        'search_contains_all_words' => $lang['search_contains_all_words'],
                        'search_contains_one_word' => $lang['search_contains_one_word'],
                        'search_search_option' => $lang['search_search_option'],
                        'search_search_in_headline' => $lang['search_search_in_headline'],
                        'search_search_in_entry' => $lang['search_search_in_entry'],
                        'search_search_in_headline_entry' => $lang['search_search_in_headline_entry'],
                        'search_number_result' => $lang['search_number_result'],
                        'search_10_results' => $lang['search_10_results'],
                        'search_20_results' => $lang['search_20_results'],
                        'search_no_limitation' => $lang['search_no_limitation'],
                        'search_sorting_results' => $lang['search_sorting_results'],
                        'search_date_ascending' => $lang['search_date_ascending'],
                        'search_date_descending' => $lang['search_date_descending'],
                        'search_name_ascending' => $lang['search_name_ascending'],
                        'search_name_descending' => $lang['search_name_descending'],
                        'search_search_btn' => $lang['search_search_btn'],
                        'search_reset_btn' => $lang['search_reset_btn']));
}    

if ($_POST['action'] == 'result') {
    if($_POST['searchstring'] != '' && strlen($_POST['searchstring'])>=4) {
        $search_loop = array();
        $tpl->loadFile('main', 'search_result.html');	
        $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['search_search'] => $sess->url("search.php?action=search"), $lang['search_result_search_result'] => '')));                

        include_once($_ENGINE['eng_dir']."admin/enginelib/class.bbcode.php");
        $bbcode = new engineBBCode(1,0,1,0,$config['smilieurl']);
    
        $searchstring = str_replace("*","%",$_POST['searchstring']);
        $searchstring = str_replace("_", "\\_", $searchstring);
        $searchstring=preg_replace("/[\/:;'\"\(\)\[\]?!#{}%\-+\\\\]/s","",$searchstring);
        $searchstring=preg_replace("/\s{2,}/"," ",$searchstring);
		$searchstring = preg_replace("/(%){2,}/s", '%', $searchstring);		
		$searchwords = explode(" ",strtolower(addslashes($searchstring)));
        switch($_POST['destination']) {
            case "head":
                $search_col1 = "dltitle";
                break;
            case "desc":
                $search_col1 = "dldesc";
                break;
            default:
                $search_col1 = "dltitle";
                $search_col2 = "dldesc";
        }
			
        if($search_col1) {
            foreach ($searchwords as $key => $val) {
                $words1[$key] .= " ".$search_col1." LIKE '%".$val."%'";
            }
        }
        
        if($search_col2) {
            foreach ($searchwords as $key => $val) {
                $words2[$key] .= " ".$search_col2." LIKE '%".$val."%'";
            }
        }	
			
        $condition1 = implode(" ".strtoupper($_POST['connector'])." ", $words1);
        if($words2 != "") $condition2 = " OR ".implode(" ".strtoupper($_POST['connector'])." ", $words2);
        $where_def .= $condition1.$condition2;
					
        $bord = "LIMIT ".$_POST['limit'];
        if($_POST['limit'] == '0') $bord = '';
        if(!$_POST['orderby']) $_POST['orderby'] = "dateD";
        $res = 0;
        
        if ($config['top_list']) {
			$result4 = $db_sql->sql_query("SELECT dlhits FROM $dl_table");
			while (list($dlhits) = mysql_fetch_row($result4)) {
				$hitsalt = $dlhits;
				$total_dls += $hitsalt;
			}
        }        
        
        $result = $db_sql->sql_query("SELECT $dl_table.*, $cat_table.titel FROM $dl_table 
                                    LEFT JOIN $cat_table ON ( $dl_table.catid = $cat_table.catid )
                                    WHERE $where_def AND status !='3' ORDER BY ".convertOrderBy($dl_table, $_POST['orderby'])." $bord");
        while($sresult = $db_sql->fetch_array($result)) {
            $sresult = stripslashes_array($sresult);
            unset($stars);
            unset($graph);
            unset($cool);
            unset($points);
            
            $categ = $db_sql->query_array("SELECT * FROM $cat_table WHERE catid='$sresult[catid]'");
            if ($sresult['dlpoints'] != 0) {
                $dl_divp = $sresult['dlpoints'] / $sresult['dlvotes'];
                $points = round($dl_divp,2);
            }
            
            if ($config['newmark']) $graph = newgraph($sresult['dl_date']);
            
            if ($config['top_list']) $cool = CoolDL($sresult['dlhits'],$total_dls);
            $stars = Stars($points);            

            if ($sresult['authormail'] != "") {
                $file_author = "<a class=\"list\" href=\"mailto:".$sresult['authormail']."\">".$sresult['dlauthor']."</a>";
            } else {
                $file_author = $sresult['dlauthor'];
            }
            $kom_i = $sresult['comment_count'];
            if ($sresult['dlpoints'] == 0) {
                $rate = sprintf($lang['search_result_no_rating_on_points'],$sresult['dlhits'],$sresult['comment_count']);
            } else {
                $rate = sprintf($lang['search_result_rating_on_points'],$points,$sresult['dlvotes'],$sresult['dlhits'],$sresult['comment_count']);
            }
			
	        //if ($sresult['onlyreg'] == 0 || $auth->user['canaccessregisteredfiles'] == "1") {
	            $headline = "<a class=\"list\" href=\"".$sess->url("comment.php?dlid=".$sresult['dlid'])."\">".trim($sresult['dltitle'])."</a>";
	        /*} else {
	            $headline = trim($sresult['dltitle'])." - <span class=\"list_desc\">".$lang['php_only_reg']."</span>";
	        }*/				

			$search_loop[] = array('rate' => $rate,
									'download_description' => $bbcode->rebuildText($sresult['dldesc']),
									'headline' => $headline,
                                    'graph' => $graph,
                                    'cool' => $cool,
                                    'stars' => $stars,
									'date' => aseDate($config['shortdate'],$sresult['dl_date'],1)." ".$lang['php_last_visit2']." ".aseDate($config['timeformat'],$sresult['dl_date']),
									'postcolor' => postCss($res),
                                    'number' => $res+1,
									'category_titel' => sprintf($lang['search_result_category'], $sresult['titel']));			
    		$res++;	
        }
		if($res == 0) {
	        rideSite($sess->url('search.php?action=search'), $lang['rec_error40']);
	        exit();		
		} else {		
			$tpl->parseLoop('main', 'search_loop');
		}
		
        $tpl->register(array(
                            'search_result_search_result' => $lang['search_result_search_result'],
                            'search_result_headline_entry' => $lang['search_result_headline_entry'],
                            'search_result_author' => $lang['search_result_author'],
                            'search_result_from' => $lang['search_result_from']));	
    } else {
        rideSite($sess->url('search.php?action=search'), $lang['rec_error39']);
        exit(); 
    }
}

$tpl->register('query', showQueries($develope));
$tpl->register('header', $tpl->pget('header'));

$tpl->register('footer', $tpl->pget('footer'));
$tpl->pprint('main'); 
?>