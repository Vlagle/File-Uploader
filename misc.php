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
|   > Verschiedene Seiten und Zusatzfunktionen
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: misc.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

include_once('lib.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");

if($_REQUEST['action']) $action = $_REQUEST['action'];

if ($action == "login") {
    $tpl->loadFile('main', 'login.html'); 
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['login_login'] => '')));
    $tpl->register('title', 'Login');  
    
    $tpl->register(array('login_login' => $lang['login_login'],
                        'login_login2' => $lang['login_login2'],
                        'login_use_your_username_password' => sprintf($lang['login_use_your_username_password'],definedBoardUrls("addmember")),
                        'login_username' => $lang['login_username'],
                        'login_password' => $lang['login_password'],
                        'login_click_here_to_remember' => sprintf($lang['login_click_here_to_remember'],definedBoardUrls("remember")),
                        'login_login_btn' => $lang['login_login_btn'],
                        'login_reset_btn' => $lang['login_reset_btn'])); 
                         
    if($auth->user['userid'] != 2) {
        rideSite($sess->url("index.php"), $lang['misc_1']);
    } else {
        $tpl->register('query', showQueries($develope));         
        $tpl->register('header', $tpl->pget('header'));
        
        $tpl->register('footer', $tpl->pget('footer'));
        $tpl->pprint('main');           
    }
	exit();
}

if ($action == "userlogin") {
	$info = $lang['misc_2'];
	showLoginScreen("", $info, "&nbsp;", $sess->url("index.php"));
	exit();
}

if ($action == "invalid_login") {
    rideSite($sess->url("misc.php?action=login"), $lang['misc_3']);
	exit();
}

if ($action == "logout") {
	$auth->userLogout();
	rideSite($sess->url("index.php"), $lang['misc_4']);
	exit();
}

if($action == "perm_denied") {
	$ride_url = $sess->url('index.php');
	rideSite($ride_url, $lang['misc_5']);
    exit();
}

if($action == 'new_files') {   
    $tpl->loadFile('main', 'misc_toplist_new_files.html'); 
    $tpl->register('title', sprintf($lang['misc_last_files'],$config['newlist_q']));
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['misc_stats']=>$sess->url('misc.php?action=toplist'), sprintf($lang['misc_last_files'],$config['newlist_q']) => '')));            
	$tpl->register(array('misc_more_stats' => $lang['misc_more_stats'],
						'misc_top_downloads_total' => $lang['misc_top_downloads_total'],
						'misc_top_downloads_by_rating' => $lang['misc_top_downloads_by_rating'],
						'misc_top_downloads_by_votes' => $lang['misc_top_downloads_by_votes'],
						'misc_new_files' => sprintf($lang['misc_new_files'],$config['newlist_q']),
						'misc_last_files' => sprintf($lang['misc_last_files'],$config['newlist_q']),
						'misc_new_files_since_your_last_visit' => $lang['misc_new_files_since_your_last_visit']));		
			
    $result2 = $db_sql->sql_query("SELECT $dl_table.*, $cat_table.titel FROM $dl_table 
									LEFT JOIN $cat_table ON ($cat_table.catid = $dl_table.catid)
									WHERE $dl_table.status!='3' ORDER BY $dl_table.dl_date DESC LIMIT 0,$config[newlist_q]");
    if($db_sql->num_rows($result2) >= 1) {
		$no = 1;
	    $i = 0;
		$newest_file_loop = array();
	    while($dl = $db_sql->fetch_array($result2)) {
	        $dl = stripslashes_array($dl);
	    	$upl_date = aseDate($config['longdate'],$dl['dl_date']);        
			$rowcolor = postCss($no);	
			$newest_file_loop[] = array('no' => $no,
                                    'new_file_url' => $sess->url('comment.php?dlid='.$dl['dlid']),
                                    'new_file_filename' => $dl['dltitle'],
                                    'postcolor' => postCss($no),
									'misc_upload_dtd' => sprintf($lang['misc_upload_dtd'],aseDate($config['shortdate'],$dl['dl_date'])),
                                	'misc_top_category' => sprintf($lang['misc_top_category'],$sess->url('index.php?subcat='.$dl['catid']),$dl['titel']),									
									'misc_new_file_hits' => sprintf($lang['misc_new_file_hits'],$dl['dlhits']));
	        $no++;
	        $i++;
	    }
		$tpl->parseLoop('main', 'newest_file_loop'); 
		$parse_top_file_list = true;
		$parse_no_top_file_list = false;
	} else {
		$parse_top_file_list = false;
		$parse_no_top_file_list = true;
	}
	$tpl->parseIf('main', 'parse_top_file_list');   
	$tpl->parseIf('main', 'parse_no_top_file_list');		
    
	if($config['newlist']) $parse_new_file_list = true;
	if($sess->getSessVar("engine_last_visit")) $parse_personally_new_files = true;
	$tpl->parseIf('main', 'parse_personally_new_files');   
	$tpl->parseIf('main', 'parse_new_file_list');	
    					 
    $tpl->register('query', showQueries($develope));         
    $tpl->register('header', $tpl->pget('header'));
    
    $tpl->register('footer', $tpl->pget('footer'));
    $tpl->pprint('main');      
    
    exit();
}  

if($action == "toplist") {
	if(!$auth->user['canseetopstatsfiles']) {
	    rideSite($sess->url('index.php'), $lang['misc_2']);
	    exit();
	}
	
    $tpl->loadFile('main', 'misc_toplist.html'); 
    $tpl->register('title', $lang['misc_top_lists']);
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['misc_stats']=>$sess->url('misc.php?action=toplist'), $lang['misc_top_lists'] => '')));            
	$tpl->register(array('misc_more_stats' => $lang['misc_more_stats'],
						'misc_top_downloads_total' => $lang['misc_top_downloads_total'],
						'misc_top_downloads_by_rating' => $lang['misc_top_downloads_by_rating'],
						'misc_top_downloads_by_votes' => $lang['misc_top_downloads_by_votes'],
						'misc_new_files' => sprintf($lang['misc_new_files'],$config['newlist_q']),
						'misc_new_files_since_your_last_visit' => $lang['misc_new_files_since_your_last_visit']));	
	
    $no = 1; 
    $show_percent = $config['top_list_q'];
    $hits = 0;
    $f = 0;
    $result = $db_sql->sql_query("SELECT dlhits FROM $dl_table");
    while (list($dlhits) = mysql_fetch_row($result)) {
        $hitsalt = $dlhits;
        $total += $hitsalt;
        $f++;
    }

    $minhits = ($total / 100)*$show_percent;
    $minimum = round($minhits,2);
					 
    $leading = $db_sql->query_array("SELECT * FROM $dl_table WHERE dlhits > $minimum ORDER BY dlhits DESC LIMIT 1");
    
    $dl = array();	
    $tpl->register('misc_top_rating_headline', sprintf($lang['misc_top_rating_headline'],$show_percent.'%'));	
    $tpl->register('misc_top_total_downloads_to_total_files', sprintf($lang['misc_top_total_downloads_to_total_files'],$total,$f));
    $no = 1;
    $top_file_loop = array();
    $result2 = $db_sql->sql_query("SELECT $dl_table.*, $cat_table.titel FROM $dl_table 
                                    LEFT JOIN $cat_table ON ($cat_table.catid = $dl_table.catid)
                                    WHERE $dl_table.dlhits >= $minimum ORDER BY $dl_table.dlhits DESC");
    while($dl = $db_sql->fetch_array($result2)) {
        $dl = stripslashes_array($dl);
        if ($dl['dlhits'] == $leading['dlhits']) {
            $dl_percent = "100";
        } else {		   
            $dl_percent = round((100*$dl['dlhits'])/$leading['dlhits'],2);
        }
        $bar_percent = $dl_percent - 20;
        $see_percent = round((100*$dl['dlhits'])/$total,2);
		$rowcolor = postcolor($no);		        
        $ctitel = $dl['titel'];
        $top_file_loop[] = array('no' => $no,
                                'top_rating_url' => $sess->url('comment.php?dlid='.$dl['dlid']),
                                'top_rating_filename' => $dl['dltitle'],
                                'postcolor' => postCss($no),
                                'misc_top_category' => sprintf($lang['misc_top_category'],$sess->url('index.php?subcat='.$dl['catid']),$dl['titel']),
                                'bar_percent' => $bar_percent,
                                'misc_top_hits' => sprintf($lang['misc_top_hits'],$dl['dlhits'],$see_percent.'%'));
        $no++;
    }	
    $tpl->parseLoop('main', 'top_file_loop');
	
	if($config['newlist']) $parse_new_file_list = true;
	if($sess->getSessVar("engine_last_visit")) $parse_personally_new_files = true;
	$tpl->parseIf('main', 'parse_personally_new_files');   
	$tpl->parseIf('main', 'parse_new_file_list');	
    					 
    $tpl->register('query', showQueries($develope));         
    $tpl->register('header', $tpl->pget('header'));
    
    $tpl->register('footer', $tpl->pget('footer'));
    $tpl->pprint('main');      
    
    exit();
}

if($action == "toplist_rating") {
	if(!$auth->user['canseetopstatsfiles']) {
	    rideSite($sess->url('index.php'), $lang['misc_2']);
	    exit();
	}

    $tpl->loadFile('main', 'misc_toplist_rating.html'); 
    $tpl->register('title', $lang['misc_top10_rating_headline']);
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['misc_stats']=>$sess->url('misc.php?action=toplist'), $lang['misc_top10_rating_headline'] => '')));            
	$tpl->register(array('misc_more_stats' => $lang['misc_more_stats'],
						'misc_top_downloads_total' => $lang['misc_top_downloads_total'],
						'misc_top_downloads_by_rating' => $lang['misc_top_downloads_by_rating'],
						'misc_top_downloads_by_votes' => $lang['misc_top_downloads_by_votes'],
						'misc_new_files' => sprintf($lang['misc_new_files'],$config['newlist_q']),
						'misc_new_files_since_your_last_visit' => $lang['misc_new_files_since_your_last_visit']));		

    $tpl->register('misc_top10_rating_headline', $lang['misc_top10_rating_headline']);
    $no = 1;
    // Top 10 nach Bewertungen
    $top10_rating_loop = array();
    $result3 = $db_sql->sql_query("SELECT * FROM $dl_table WHERE dlpoints!='0' ORDER BY (dlpoints/dlvotes) ASC LIMIT 0,10");
    while($top = $db_sql->fetch_array($result3)) {
        $top = stripslashes_array($top);
        $votes = $top['votes'];
        $bar_percent = round($top['dlpoints']/$top['dlvotes'],2);
		$rowcolor = postCss($no);				
        $top10_rating_loop[] = array('no' => $no,
                                    'top10_rating_url' => $sess->url('comment.php?dlid='.$top['dlid']),
                                    'top10_rating_filename' => $top['dltitle'],
                                    'postcolor' => postCss($no),
                                    'misc_top10_rating_rate' => sprintf($lang['misc_top10_rating_rate'],$bar_percent),
                                    'misc_top10_rating_votes' => sprintf($lang['misc_top10_rating_votes'],$top['dlvotes']));
        $no++;
    }	 
    $tpl->parseLoop('main', 'top10_rating_loop');   
	
	if($config['newlist']) $parse_new_file_list = true;
	if($sess->getSessVar("engine_last_visit")) $parse_personally_new_files = true;
	$tpl->parseIf('main', 'parse_personally_new_files');   
	$tpl->parseIf('main', 'parse_new_file_list');	
    					 
    $tpl->register('query', showQueries($develope));         
    $tpl->register('header', $tpl->pget('header'));
    
    $tpl->register('footer', $tpl->pget('footer'));
    $tpl->pprint('main');      
    
    exit();
}

if($action == "toplist_votes") {
	if(!$auth->user['canseetopstatsfiles']) {
	    rideSite($sess->url('index.php'), $lang['misc_2']);
	    exit();
	}

    $tpl->loadFile('main', 'misc_toplist_votes.html'); 
    $tpl->register('title', $lang['misc_top10_voting_headline']);
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['misc_stats']=>$sess->url('misc.php?action=toplist'), $lang['misc_top10_voting_headline'] => '')));            
	$tpl->register(array('misc_more_stats' => $lang['misc_more_stats'],
						'misc_top_downloads_total' => $lang['misc_top_downloads_total'],
						'misc_top_downloads_by_rating' => $lang['misc_top_downloads_by_rating'],
						'misc_top_downloads_by_votes' => $lang['misc_top_downloads_by_votes'],
						'misc_new_files' => sprintf($lang['misc_new_files'],$config['newlist_q']),
						'misc_new_files_since_your_last_visit' => $lang['misc_new_files_since_your_last_visit']));		

    $tpl->register('misc_top10_voting_headline', $lang['misc_top10_voting_headline']);
    $no = 1;
    // Top 10 nach Bewertungen
    $top10_voting_loop = array();
    $result4 = $db_sql->sql_query("SELECT * FROM $dl_table WHERE dlpoints!='0' ORDER BY dlvotes DESC LIMIT 0,10");
    while($votes = $db_sql->fetch_array($result4)) {
        $votes = stripslashes_array($votes);
        $rating = round($votes['dlpoints']/$votes['dlvotes'],2);
		$rowcolor = postCss($no);		        
        $top10_voting_loop[] = array('no' => $no,
                                    'top10_voting_url' => $sess->url('comment.php?dlid='.$votes['dlid']),
                                    'top10_voting_filename' => $votes['dltitle'],
                                    'postcolor' => postCss($no),
                                    'misc_top10_voting_rate' => sprintf($lang['misc_top10_voting_rate'],$rating),
                                    'misc_top10_voting_votes' => sprintf($lang['misc_top10_voting_votes'],$votes['dlvotes']));
        $no++;
    }	  
    $tpl->parseLoop('main', 'top10_voting_loop'); 
	
	if($config['newlist']) $parse_new_file_list = true;
	if($sess->getSessVar("engine_last_visit")) $parse_personally_new_files = true;
	$tpl->parseIf('main', 'parse_personally_new_files');   
	$tpl->parseIf('main', 'parse_new_file_list');  
    					 
    $tpl->register('query', showQueries($develope));         
    $tpl->register('header', $tpl->pget('header'));
    
    $tpl->register('footer', $tpl->pget('footer'));
    $tpl->pprint('main');      
    
    exit();
}

if($action == 'since_last') {
    if(!isset($auth->user['userid'])) {
    	header("Location: ".$sess->url("index.php"));
    	exit;
    }
	
    $l_date = aseDate($config['longdate'],$sess->getSessVar("engine_last_visit"),1);
    $l_date .= " - ".aseDate($config['timeformat'],$sess->getSessVar("engine_last_visit"),1);
	
    $tpl->loadFile('main', 'misc_toplist_new_files_since.html'); 
    $tpl->register('title', $lang['misc_files_since_last_visit']);
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['misc_stats']=>$sess->url('misc.php?action=toplist'), $lang['misc_files_since_last_visit'] => '')));            
	$tpl->register(array('misc_more_stats' => $lang['misc_more_stats'],
						'misc_top_downloads_total' => $lang['misc_top_downloads_total'],
						'misc_top_downloads_by_rating' => $lang['misc_top_downloads_by_rating'],
						'misc_top_downloads_by_votes' => $lang['misc_top_downloads_by_votes'],
						'misc_new_files' => sprintf($lang['misc_new_files'],$config['newlist_q']),
						'misc_new_file_since' => sprintf($lang['misc_new_file_since'],$l_date),
						'misc_new_files_since_your_last_visit' => $lang['misc_new_files_since_your_last_visit']));		

    $tpl->register('misc_top10_voting_headline', $lang['misc_top10_voting_headline']);    
				
    $n_dl = $db_sql->sql_query("SELECT d.*,g.titel,g.catid from $dl_table d
    								LEFT JOIN $cat_table g ON  d.catid = g.catid
    								WHERE dl_date > '".$sess->getSessVar("engine_last_visit")."' ORDER BY dl_date DESC");
    if($db_sql->num_rows($n_dl) >= 1) {
		$no = 1;
	    $i = 0;
		$newest_file_loop = array();
	    while($dl = $db_sql->fetch_array($n_dl)) {
	        $dl = stripslashes_array($dl);
	    	$upl_date = aseDate($config['longdate'],$dl['dl_date']);        
			$rowcolor = postCss($no);	
			$newest_file_loop[] = array('no' => $no,
                                    'new_file_url' => $sess->url('comment.php?dlid='.$dl['dlid']),
                                    'new_file_filename' => $dl['dltitle'],
                                    'postcolor' => postCss($no),
									'misc_upload_dtd' => sprintf($lang['misc_upload_dtd'],aseDate($config['shortdate'],$dl['dl_date'])),
                                	'misc_top_category' => sprintf($lang['misc_top_category'],$sess->url('index.php?subcat='.$dl['catid']),$dl['titel']),									
									'misc_new_file_hits' => sprintf($lang['misc_new_file_hits'],$dl['dlhits']));
	        $no++;
	        $i++;
	    }
		$tpl->parseLoop('main', 'newest_file_loop'); 
		$parse_top_file_list = true;
		$parse_no_top_file_list = false;
	} else {
		$tpl->register('misc_no_files_since_last_vist',$lang['misc_no_files_since_last_vist']);
		$parse_top_file_list = false;
		$parse_no_top_file_list = true;
	}
	$tpl->parseIf('main', 'parse_top_file_list');   
	$tpl->parseIf('main', 'parse_no_top_file_list');		
    
	if($config['newlist']) $parse_new_file_list = true;
	if($sess->getSessVar("engine_last_visit")) $parse_personally_new_files = true;
	$tpl->parseIf('main', 'parse_personally_new_files');   
	$tpl->parseIf('main', 'parse_new_file_list');	
    					 
    $tpl->register('query', showQueries($develope));         
    $tpl->register('header', $tpl->pget('header'));
    
    $tpl->register('footer', $tpl->pget('footer'));
    $tpl->pprint('main');      
    
    exit();
}

if($action == 'formmailer') {
    if($auth->user['userid'] == 2) {
       rideSite($sess->url('index.php'), $lang['formmailer_not_allowed']);
       exit();  
    }  
    $tpl->loadFile('main', 'formmailer.html');         
    
    if($_GET['dlid']!='') {
        $member = $db_sql->query_array("SELECT dlauthor AS username, authormail AS useremail FROM $dl_table WHERE dlid='".$_GET['dlid']."'");
        $tpl->register(array('mailid' => $_GET['dlid'], 'mailkind' => 'dlid'));
    } elseif($_GET['memberid']!='') {
        $member = holeUserID($_GET['memberid']); 
        $tpl->register(array('mailid' => $_GET['memberid'], 'mailkind' => 'memberid'));
    }
    
    $formmailer_headline = sprintf($lang['formmailer_write_email_to'],$member['username']);
    $tpl->register('title', $formmailer_headline);      
    
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), sprintf($lang['formmailer_write_email_to'],$member['username']) => '')));    
    
    $tpl->register(array('formmailer_headline' => sprintf($lang['formmailer_write_email_to'],$member['username']),
                        'formmailer_topic' => $lang['formmailer_topic'],
                        'formmailer_message' => $lang['formmailer_message'],
                        'formmailer_receipient_will_see_your_mail' => $lang['formmailer_receipient_will_see_your_mail'],
                        'formmailer_send_email_btn' => $lang['formmailer_send_email_btn'],
                        'formmailer_reset_btn' => $lang['formmailer_reset_btn']));
    
    $tpl->register('query', showQueries($develope));         
    $tpl->register('header', $tpl->pget('header'));
    
    $tpl->register('footer', $tpl->pget('footer'));
    $tpl->pprint('main');      
    
    exit();
}

if($action == "send_mail") {
    if($_POST['subject'] == '' || $_POST['formmessage'] == '') {
	    rideSite($sess->url('misc.php?action=formmailer&dlid='.$_POST['dlid'].'&memberid='.$_POST['memberid']), $lang['rec_error56']);
	    exit();    
    } 
    
    if($_POST['dlid']) {
        $member = $db_sql->query_array("SELECT dlauthor AS username, authormail AS useremail FROM $dl_table WHERE dlid='".intval($_POST['dlid'])."'");
    } else {
        $member = holeUserID($_POST['memberid']);
    }
	
    include_once($_ENGINE['eng_dir']."admin/enginelib/class.phpmailer.php");
    $mail = new PHPMailer();
    $mail->SetLanguage($lang['php_mailer_lang'], $_ENGINE['eng_dir']."lang/".$config['language']."/"); 
    if($config['use_smtp']) {
        $mail->IsSMTP();
        $mail->Host = $config['smtp_server']; 
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_username']; 
        $mail->Password = $config['smtp_password']; 
    } else {
        $mail->IsMail();
    }
    
    $mail->From = $auth->user['useremail'];
    $mail->FromName = $auth->user['username'];
    $mail->AddAddress($member['useremail'],$member['username']);
    $mail->Subject = $_POST['subject'];
    $mail->Body = $_POST['formmessage']; 
    $mail->WordWrap = 50;  
    
    $mail->Send();		       

	$ride_url = $sess->url('index.php');
	rideSite($ride_url, sprintf($lang['formmailer_mail_successfully_send'],$member['useremail']));
    exit();
}

?>