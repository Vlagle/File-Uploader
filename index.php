<?php
/*
+--------------------------------------------------------------------------
|   Alex Download Engine
|   ========================================
|   by Alex Höntschel
|   (c) 2002 AlexScriptEngine
|   http://www.alexscriptengine.de
|   ========================================
|   Web: http://www.alexscriptengine.de
|   Email: info@alexscriptengine.de
+---------------------------------------------------------------------------
|
|   > Beschreibung
|   > Startseite, Kategorien, Downloads
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|   > $Id: index.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

include_once('lib.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
$title = $lang['title_index'];

$tpl->loadFile('main', 'index.html'); 
$tpl->register('title', $lang['title_index']);

// Quickjump
$load_page = '';
switch($_GET['jump']) {
	case 'main':
		$load_page = 'index.php?';
		break;
	case 'search':
		$load_page = 'search.php?action=search&';
		break;
	case 'memberdetails':
		$load_page = 'memberdetails.php?change=1&';
		break;
	case '0':
		$load_page = 'index.php';
		break;		
    default:
        if($_GET['jump']) $_GET['subcat'] = intval($_GET['jump']);	
        break;        
}
if ($load_page != '') {
	header("Location: ".$sess->url("$load_page"));
	exit;
}
// End Quickjump

if($_GET['start']) {
    $start = $_GET['start'];
} elseif($_POST['start']) {
    $start = $_POST['start'];
}

$parse_files = false;
$parse_categories = false;

if (isset($_GET['subcat'])) {
    $subcat = intval($_GET['subcat']);
    $main = $db_sql->query_array("SELECT titel,subcat,startorder FROM $cat_table WHERE catid='$subcat'");
    
        if($_GET['sorttype'] && $_GET['sortorder']) { 
            $sort = $_GET['sorttype'].$_GET['sortorder'];
            switch($_GET['sorttype']) {
                case "hits":
                    $tpl->register('selh', "selected");
                    break;
                case "date":
                    $tpl->register('seld', "selected");
                    break;
                case "votes":
                    $tpl->register('selv', "selected");
                    break;
                case "title":
                    $tpl->register('selt', "selected");
                    break;
            }
            
            switch($_GET['sortorder']) {
                case "A":
                    $tpl->register('sel_auf', "selected");
                    break;
                case "D":
                    $tpl->register('sel_ab', "selected");
                    break;
            }
        } elseif($_GET['sort']) {
            $sort = $_GET['sort'];
        } else {
            switch($main['startorder']) {
                case "hitsA":
                case "hitsD":
                    $tpl->register('selh', "selected");
                    break;
                case "dateA":
                case "dateD":
                    $tpl->register('seld', "selected");
                    break;
                case "votesA":
                case "votesD":
                    $tpl->register('selv', "selected");
                    break;
                case "titleA":
                case "titleD":
                    $tpl->register('selt', "selected");
                    break;
            }
            
            switch($main['startorder']) {
                case "hitsA":
                case "dateA":
                case "votesA":
                case "titleA":
                    $tpl->register('sel_auf', "selected");
                    break;
                case "hitsD":
                case "dateD":
                case "votesD":
                case "titleD":
                    $tpl->register('sel_ab', "selected");
                    break;
            }        
        }
		
		$breadcrumb_array = array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'));
		
		$old['subcat'] = $main['subcat'];
		$mother = stripslashes($main['titel']);
		
		if($old['subcat'] != 0) {
			while ($old['subcat'] != 0) {
				$old = $db_sql->query_array("SELECT catid,titel,subcat FROM $cat_table WHERE catid='".$old['subcat']."'");
				$path_array[stripslashes($old['titel'])] = $sess->url("index.php?subcat=".$old['catid']);
			}
		} else {
			$path_array[stripslashes($main['titel'])] = '';
		}
		
		$path_array = array_reverse($path_array);
		$breadcrumb_array = array_merge($breadcrumb_array,$path_array);		
		$breadcrumb_array[$mother] = '';
    	$tpl->register('breadcrumb', buildBreadCrumb($breadcrumb_array));		
		
		$tpl->register('category_title',stripslashes($main['titel']));
		
	    $category_loop = displayCatBits($subcat,1);    
		$tpl->parseLoop('main', 'category_loop');
        
        $numdl = $db_sql->query_array("SELECT $dl_table.dlid, $cat_table.startorder, $cat_table.cat_style FROM $dl_table
										LEFT JOIN $cat_table ON $dl_table.catid = $cat_table.catid
										WHERE $dl_table.catid='$subcat' AND $dl_table.status!='3'");
        if (count($numdl['dlid']) != 0) {
            if(!$sort) $sort = convertorderbyin($numdl['startorder']);
        }

        if ($config['dlperpage'] != 0) {
            $over_all = $db_sql->query_array("SELECT Count(*) as total FROM $dl_table where catid='$subcat' AND status!='3'");
            if(!class_exists(Nav_Link)) include_once($_ENGINE['eng_dir']."admin/enginelib/class.nav.php");
            if(!isset($start)) $start = 0;
            $nav = new Nav_Link();
            $nav->overAll = $over_all['total'];
            $nav->perPage = $config['dlperpage'];
            $nav->DisplayLast = 1;
            $nav->DisplayFirst = 1;            
            $url_neu = $sess->url("index.php?subcat=".$subcat."&sort=".$sort)."&amp;";
            $nav->MyLink = "$url_neu";
            $nav->LinkClass = "page_step";
            $nav->start = $start;
            $pagecount = $nav->BuildLinks();
            if($over_all['total'] != 0) {
        		$pages = intval($over_all['total'] / $config['dlperpage']);
        		if($over_all['total'] % $config['dlperpage']) $pages++;	            
                if(!$pagecount) $pagecount = "<b>1</b>";
                $tpl->register('pagecount', $lang['php_page']." (".$pages."): ".$pagecount);
            }
        }
        
        if($over_all['total'] != 0) {
			$parse_files = true;		
            if ($config['pagesort'] == 1 && $numdl['cat_style'] != 1) {
                $parse_page_sorting = true;
                $tpl->register(array('index_sorting' => $lang['index_sorting'],
                                     'index_sort_now' => $lang['index_sort_now'],
                                     'index_sort_by_title' => $lang['index_sort_by_title'],
                                     'index_sort_by_date' => $lang['index_sort_by_date'],
                                     'index_sort_by_hits' => $lang['index_sort_by_hits'],
                                     'index_sort_by_votes' => $lang['index_sort_by_votes'],
                                     'index_sort_ascending' => $lang['index_sort_ascending'],
                                     'index_sort_descending' => $lang['index_sort_descending'],
                                     'index_sort_order' => $lang['index_sort_order'],
                                     'sort_subcat' =>  $subcat));
            
            }

            if($sort) {
                $orderby = convertorderbyin($sort);
            } else {
                $orderby = convertorderbyin($numdl['startorder']);
            }				
    
            if ($config['top_list']) {
    			$result4 = $db_sql->sql_query("SELECT dlhits FROM $dl_table");
    			while (list($dlhits) = mysql_fetch_row($result4)) {
    				$hitsalt = $dlhits;
    				$total_dls += $hitsalt;
    			}
                
                $result_top3 = $db_sql->sql_query("SELECT dlid, dltitle, dlhits FROM $dl_table WHERE catid='".$subcat."' AND status!='3' ORDER BY dlhits DESC LIMIT 3");
                $parse_top3_block = true;
                $i = 1;
                $top3_loop = array();
                while($top3 = $db_sql->fetch_array($result_top3)) {
                    $top3 = stripslashes_array($top3);
                    $top3_loop[] = array('top3_url' => $sess->url('comment.php?dlid='.$top3['dlid']),
                                        'top3_filename' => trim($top3['dltitle']),
                                        'top3_position' => $i.'.',
                                        'top3_hits' => sprintf($lang['index_top3_downloads'],$top3['dlhits']));
                    $i++;
                }
                $tpl->register('index_popular_files', $lang['index_popular_files']);
                $tpl->parseLoop('main', 'top3_loop');
            }
		
            if($orderby == "" || !isset($orderby)) $orderby = convertorderbyin("dateA");		
            
            include_once($_ENGINE['eng_dir']."admin/enginelib/class.bbcode.php");
            $bbcode = new engineBBCode(1,0,1,0,$config['smilieurl']);           
            
            if($numdl['cat_style'] == 1) {
				$parse_listed_files = true;
				$parse_detailed_files = false;
                $listed_files_loop = displayListedFiles($subcat,$start,$orderby);
				$tpl->parseLoop('main', 'listed_files_loop');
				$tpl->register(array('index_description' => $lang['index_description'],
									'index_date' => $lang['index_date'],
									'index_hits' => $lang['index_hits'],
									'index_rating' => $lang['index_rating'],
									'index_filesize' => $lang['index_filesize']));
				
            } else {
				$parse_listed_files = false;
				$parse_detailed_files = true;			
                $detailed_files_loop = displayDetailedFiles($subcat,$start,$orderby);
				$tpl->parseLoop('main', 'detailed_files_loop');
            }
			$tpl->parseIf('main', 'parse_listed_files');
			$tpl->parseIf('main', 'parse_detailed_files');
        } 
        $tpl->register(array('tblwidtha' => '0%',
                            'tblwidthb' => '100%',
							'subcat' => $subcat));
} else {
	$parse_categories = true;
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => '')));
    include_once($_ENGINE['eng_dir']."admin/enginelib/class.bbcode.php");
    $bbcode = new engineBBCode(1,0,1,0,$config['smilieurl']);            
	
    if ($config['newindex'] == "1") { 
        $parse_new_file_block = true;
        $result5 = $db_sql->sql_query("SELECT $dl_table.dlid, $dl_table.dltitle, $dl_table.dldesc, $dl_table.dl_date, $cat_table.titel FROM $dl_table 
                                        LEFT JOIN $cat_table ON ($cat_table.catid = $dl_table.catid)
                                        WHERE $dl_table.status!='3' ORDER BY $dl_table.dl_date DESC LIMIT 0,$config[newindex_q]");
        $new_files_loop = array();
        while($dow = $db_sql->fetch_array($result5)) {
            $dow = stripslashes_array($dow);
            if(strlen($dow['dldesc'])>30) $dow['dldesc'] = substr($dow['dldesc'],0,25)."...";
            $new_files_loop[] = array('link' => $sess->url('comment.php?dlid='.$dow['dlid']),
                                    'headline' => $dow['dltitle'],
                                    'description' => $bbcode->rebuildText($dow['dldesc']),
                                    'dldate' => aseDate($config['shortdate'],$dow['dl_date']),
                                    'cat_title' => $dow['titel']);
        }
        $tpl->parseLoop('main', 'new_files_loop');
    }

    if ($config['lastcomment'] == "1") {
        $parse_last_comment_block = true;
        $result3 = $db_sql->sql_query("SELECT $dlcomment_table.*, $dl_table.dltitle, $user_table.$username_table_column AS username, $user_table.$useremail_table_column AS useremail ".useShowMailGlobal()." FROM $dlcomment_table 
                                        LEFT JOIN $dl_table ON ($dl_table.dlid = $dlcomment_table.dlid)
                                        LEFT JOIN $user_table ON ($user_table.$userid_table_column = $dlcomment_table.userid)
                                        WHERE $dlcomment_table.com_status='1' ORDER BY $dlcomment_table.com_date DESC LIMIT 0,$config[lastcomment_q]");
        $new_comments_loop = array();        
        while($comment = $db_sql->fetch_array($result3)) {
            $comment = stripslashes_array($comment);
            if ($comment['userid'] != 0) {
                if ($comment['show_email_global'] == 0) {
                    $name = trim(stripslashes($comment['username']));
                } else {
                    $name = "<a class=\"inbox\" href=\"".$sess->url("misc.php?action=formmailer&memberid=".$comment['userid'])."\">".trim(stripslashes($comment['username']))."</a>";
                }
            } else {
                $name = "Gast";
            }
            $new_comments_loop[] = array('link' => $sess->url('comment.php?dlid='.$comment['dlid']),
                                        'comment_headline' => $comment['com_headline'],
                                        'filename' => $comment['dltitle'],
                                        'comment_date' => aseDate($config['shortdate'],$comment['com_date']),
                                        'comment_poster' => sprintf($lang['index_comment_from'],$name));
        }
        $tpl->parseLoop('main', 'new_comments_loop');
    }

    if ($config['stats'] == "1") {
        $parse_stats_block = true;
        $downloads = $db_sql->sql_query("SELECT dlid FROM $dl_table WHERE status!='3'");
        $dls = $db_sql->num_rows($downloads);
        
        $categories = $db_sql->sql_query("SELECT catid FROM $cat_table");
        $categ = $db_sql->num_rows($categories);
        $memb = $db_sql->sql_query("SELECT $userid_table_column FROM $user_table");
        $member = $db_sql->num_rows($memb);
        $member = $member-1;
        if ($member >= 1 && BOARD_DRIVER == 'default') {
            $lm = list($userid,$username,$useremail) = $db_sql->sql_fetch_row("SELECT $userid_table_column AS userid, $username_table_column AS username, $useremail_table_column AS useremail FROM $user_table ORDER BY regdate DESC LIMIT 1");
            $username = stripslashes($username);
            $tpl->register('index_last_member',sprintf($lang['index_last_member'],"<a href=\"".$sess->url('misc.php?action=formmailer&memberid='.$userid)."\">".stripslashes($username)."</a>"));
        }
        $best = $db_sql->query_array("SELECT dlid,catid,dltitle,dl_date,dlhits FROM $dl_table WHERE status='1' ORDER BY dlhits DESC LIMIT 1");
        if(strlen($best['dltitle'])>15) $best['dltitle'] = substr($best['dltitle'],0,10)."...";
        
        $lfile = list($dlid,$dl_date,$dltitle,$dlurl) = $db_sql->sql_fetch_row("SELECT dlid,dl_date,dltitle,dlurl FROM $dl_table WHERE status!='3' ORDER BY dl_date DESC LIMIT 1");
        $dltitle = stripslashes($dltitle);
        $filedate = getdate($dl_date);
        $dlurl = urlencode($dlurl);
        $all_files = sprintf($lang['cat_stats_allfiles1'],$dls,$categ);
        $tpl->register(array('number_of_files' => $dls,
                            'total_file_size' => buildFileSize(realFileSize()),
                            'number_of_categories' => $categ,
                            'index_best_file_overall' => $lang['index_best_file_overall'],
                            'best_file_link' => "<a href=\"".$sess->url('comment.php?dlid='.$best['dlid'])."\">".$best['dltitle']."</a>",
                            'last_file' => "<a href=\"".$sess->url('comment.php?dlid='.$dlid)."\">".$dltitle."</a>",
                            'number_of_registered_users' => $member));
    }
	
	if($config['newlist']) $parse_new_file_link = true;
	$tpl->parseIf('main', 'parse_new_file_link');	
    
    if ($parse_stats_block || $parse_last_comment_block || $parse_new_file_block) {
        $tpl->register(array('tblwidtha' => '20%',
                            'tblwidthb' => '80%',
							'index_number_of_files' => $lang['index_number_of_files'],
							'index_total_size_of_files' => $lang['index_total_size_of_files'],
							'index_number_of_categories' => $lang['index_number_of_categories'],
							'index_last_file' => $lang['index_last_file'],
							'index_number_of_users' => $lang['index_number_of_users'],
							'index_new_comments' => $lang['index_new_comments'],
							'index_file' => $lang['index_file'],
							'index_statistics' => $lang['index_statistics'],
							'index_new_files' => $lang['index_new_files'],
							'index_all_new_files' => $lang['index_all_new_files'],
							'index_url_new_files' => $sess->url('misc.php?action=new_files')));
    } else {
        $tpl->register(array('tblwidtha' => '0%',
                            'tblwidthb' => '100%'));
    }
    
    $tpl->register('category_title',$lang['title_engine']);

    $category_loop = displayCatBits();    
	$tpl->parseLoop('main', 'category_loop');
}

$tpl->parseIf('main', 'parse_files');
$tpl->parseIf('main', 'parse_categories');
$tpl->parseIf('main', 'parse_new_file_block');
$tpl->parseIf('main', 'parse_stats_block');
$tpl->parseIf('main', 'parse_last_comment_block');
$tpl->parseIf('main', 'parse_top3_block');
$tpl->parseIf('main', 'parse_page_sorting');

if($config['enable_quickjump'] == "1") {
    if($auth->user['canuseenginesearch']) {
        $search_option = "<option value=\"search\">$lang[catquick_search]</option>";
    }
    if($auth->user['canmodifyownprofile']) $profile_option = "<option value=\"memberdetails\">$lang[catquick_profile]</option>";
    
    $cache_result = $db_sql->sql_query("SELECT catid,subcat,catorder,titel FROM $cat_table ORDER BY subcat,catorder,catid");
    while ($ncatcache = $db_sql->fetch_array($cache_result)) {
        $ncatcache = stripslashes_array($ncatcache);
        $cat_cache["$ncatcache[subcat]"]["$ncatcache[catorder]"]["$ncatcache[catid]"] = $ncatcache;
    }
    
    $current_cat = $_GET['subcat'];
    $cat_link = makeQuickLink(0,0,"",0);
}

$tpl->register('query', showQueries($develope));
$tpl->register('header', $tpl->pget('header'));

$tpl->register('footer', $tpl->pget('footer'));
$tpl->pprint('main');

?>