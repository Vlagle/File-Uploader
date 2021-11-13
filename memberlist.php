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
|   > Mitgliederliste und Einzelansicht
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: memberlist.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

include_once('lib.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");

$tpl->register('title', $lang['title_memberlist']);

if(!isset($_GET['action'])) { 
	if(BOARD_DRIVER != "default") {
		$location = definedBoardUrls("memberlist");
		header("Location: ".$location);
		exit;	
	}
    
	if ($auth->user['canseemembers'] == 0) {
		header("Location: ".$sess->url("index.php"));
		exit;
	}  

    $tpl->loadFile('main', 'memberlist.html');	
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['memberlist_memberlist_overview'] => '')));
    
	if ($config['reg_withmail'] == 1) {
		$sql_add1 = "WHERE activation='1'";
		$sql_add2 = "AND $user_table.activation='1'";
	}

	$over_all = $db_sql->query_array("SELECT Count(*) as total FROM $user_table $sql_add1");

	if(!class_exists(Nav_Link)) include_once($_ENGINE['eng_dir']."admin/enginelib/class.nav.php");
	if(!isset($_GET['start'])) {
		$start = 0;
	} else {
		$start = intval($_GET['start']);
	}
	
	$members_per_page = 15;
   	if(!$_GET['orderby']) $_GET['orderby'] = "nameA";

	
	$nav = new Nav_Link();
	$nav->overAll = ($over_all['total']-1);
	$nav->perPage = $members_per_page;
	$nav->MyLink = $sess->url("memberlist.php?orderby=".$_GET['orderby'])."&amp;";
	$nav->LinkClass = "navichain";
	$nav->start = $start;
	$pagecount = $nav->BuildLinks();
    if($over_all['total'] != 0) {
		$pages = intval($over_all['total'] / $members_per_page);
		if($over_all['total'] % $members_per_page) $pages++;	            
        if(!$pagecount) $pagecount = "<b>1</b>";
        $tpl->register('pagecount', $lang['php_page']." (".$pages."): ".$pagecount);
    }	

    $member_loop = array();
	$result2 = $db_sql->sql_query("SELECT $user_table.*, $group_table.title FROM $user_table 
                                    LEFT JOIN $group_table ON $group_table.groupid = $user_table.groupid
                                    WHERE $user_table.groupid != '8' $sql_add2 
                                    ORDER BY ".convertOrderBy($user_table, $_GET['orderby'])."
                                    LIMIT $start,$members_per_page");
	while($user = $db_sql->fetch_array($result2)) {
		$user = stripslashes_array($user);
		if ($user['regdate'] == '0') {
			$active = "&nbsp;";
		} else {
			$reg = $user['regdate'];
			$regdate = getdate($reg);
			$active = GetGerMonth($regdate['mon'])." ".$regdate['year'];
		}
        
		if ($user['blocked'] == '1') {
		   $block = "<span style=\"color: #FF3300\"><b>".$lang['php_yes']."</b></span>";
		} else {
		   $block = $lang['php_no'];
		}
		$username = trim($user['username']);
        $nameid = $user['userid'];
        $rank = $user['title'];
        
        $postcolor = postCss($no);
        
        $member_loop[] = array('postcolor' => $postcolor,
                               'block' => $block,
                               'nameid' => $nameid,
                               'username' => $username,
                               'active' => $active,
                               'rank' => $rank);
		$no++;
	}
    $tpl->parseLoop('main', 'member_loop');
    
    $tpl->register(array('memberlist_memberlist_overview' => $lang['memberlist_memberlist_overview'],
                        'memberlist_username' => $lang['memberlist_username'],
                        'memberlist_rank' => $lang['memberlist_rank'],
                        'memberlist_member_since' => $lang['memberlist_member_since'],
                        'memberlist_locked' => $lang['memberlist_locked']));
}

if($_GET['action'] == 'userdetail') {
	if (!isset($_GET['nameid'])) {
		header("Location: ".$sess->url("memberlist.php"));
		exit;
	}
    
	if(BOARD_DRIVER != "default") {
		$location = definedBoardUrls("memberdetail",$_GET['nameid']);
		header("Location: ".$location);
		exit;	
	}	     
    
    $tpl->loadFile('main', 'memberlist_profile.html');	 
			
	$member = holeUserID($_GET['nameid']);
	if ($config['showvisitorinfo'] == '1' && $auth->user['canseemembers'] == '0') {
        rideSite($sess->url('memberlist.php'), $lang['rec_error38']);
        exit();      	
	} 					
					
	$username = $member['username'];
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['memberlist_memberlist_overview'] => definedBoardUrls("memberlist"), sprintf($lang['memberlist_profile_profile_from'],$username) => '')));        
	if ($member['avatarid'] != '' && $member['avatarid'] != '0') {
		list ($avatar) = $db_sql->sql_fetch_row("SELECT avatardata FROM $avat_table WHERE avatarid='$member[avatarid]'");
		$u_avatar = "<img src=\"$config[avaturl]/$avatar\" alt=\"$lang[php_avat_of] $member[username]\" />";
	}

	if ($member['show_email_global'] == 1) $email = "<a class=\"member\" href=\"".$sess->url("misc.php?action=formmailer&memberid=".$member['userid'])."\">".sprintf($lang['php_write_email_to'],$username)."</a>";

	$reg_date = aseDate($config['longdate'],$member['regdate'],1);
	if ($member['gender'] == '1') {
		$gender = $lang['memberlist_profile_male'];
	} elseif ($member['gender'] == '2') {
		$gender = $lang['memberlist_profile_female'];
	} else {
		$gender = $lang['memberlist_profile_na'];
	}
	if ($member['aim'] != '') $aim = "<a href=\"aim:goim?screenname=".$member['aim']."&message=Hi.+Are+you+there?\">".$member['aim']."</a>";
	if ($member['usericq'] != '') $icq = "<a href=\"http://wwp.icq.com/scripts/search.dll?to=".$member['usericq']."\">".$member['usericq']."</a>";
	if ($member['yim'] != '') $yim = "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=".$member['yim']."&.src=pg\">".$member['yim']."</a>";
	if ($member['userhp'] != '') $member['userhp'] = "<a target=\"_blank\" class=\"member\" href=\"".htmlentities($member['userhp'])."\">".htmlentities($member['userhp'])."</a>";
				
	if($member['aim'] == '') $member['aim'] = $lang['php_na'];
	if($member['useremail'] == '') $member['useremail'] = $lang['php_na'];
	if($member['usericq'] == '') $member['usericq'] = $lang['php_na'];
	if($member['yim'] == '') $member['yim'] = $lang['php_na'];
	if($member['location'] == '') $member['location'] = $lang['php_na'];
	if($email == '') $email = $lang['php_na'];
	if($member['interests'] == '') $member['interests'] = $lang['php_na'];
	if($member['userhp'] == '') $member['userhp'] = $lang['php_na'];
	$member['interests'] = htmlentities($member['interests']);					
	$member['location'] = htmlentities($member['location']);
    
    $title = getGroupNameByGroupID($member['groupid']);
    
    $tpl->register(array('userhp' => $member['userhp'],
                        'location' => $member['location'],
                        'interests' => $member['interests'],
                        'aim' => $member['aim'],
                        'yim' => $member['yim'],
                        'icq' => $member['usericq'],
                        'gender' => $gender,
                        'email' => $email,
                        'reg_date' => $reg_date,
                        'avatar' => $u_avatar,
                        'title' => $title['title'],
                        'user' => $username,
                        'memberlist_profile_profile_from' => sprintf($lang['memberlist_profile_profile_from'],$username),
                        'memberlist_profile_registered_since' => $lang['memberlist_profile_registered_since'],
                        'memberlist_profile_messenger' => $lang['memberlist_profile_messenger'],
                        'memberlist_profile_icq' => $lang['memberlist_profile_icq'],
                        'memberlist_profile_aim' => $lang['memberlist_profile_aim'],
                        'memberlist_profile_yim' => $lang['memberlist_profile_yim'],
                        'memberlist_profile_email_homepage' => $lang['memberlist_profile_email_homepage'],
                        'memberlist_profile_email' => $lang['memberlist_profile_email'],
                        'memberlist_profile_homepage' => $lang['memberlist_profile_homepage'],
                        'memberlist_profile_personally_information' => $lang['memberlist_profile_personally_information'],
                        'memberlist_profile_gender' => $lang['memberlist_profile_gender'],
                        'memberlist_profile_location' => $lang['memberlist_profile_location'],
                        'memberlist_profile_interests' => $lang['memberlist_profile_interests']));
	
}	
		
$tpl->register('query', showQueries($develope));
$tpl->register('header', $tpl->pget('header'));

$tpl->register('footer', $tpl->pget('footer'));
$tpl->pprint('main');		
?>
