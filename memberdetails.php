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
|   > Account ändern
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: memberdetails.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

include_once('lib.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");

$tpl->register('title', $lang['title_memberdetails']);

if(BOARD_DRIVER != "default") {
	$location = definedBoardUrls("changeaccount");
	header("Location: ".$location);
	exit;	
}

if($auth->user['userid'] == 2) {
    header("Location: ".$sess->url("addmember.php?action=rules"));
    exit;
}

if($auth->user['canmodifyownprofile'] != "1") {
    header("Location: ".$sess->url("index.php"));
    exit;
}
   
if($_POST['action'] == 'profile_details') {
    if($_POST['form_userhp']) $uhp = reBuildURL($_POST['form_userhp']);		
    $db_sql->sql_query("UPDATE $user_table SET usericq='".addslashes($_POST['form_icq'])."', aim='".addslashes($_POST['form_aim'])."', yim='".addslashes($_POST['form_yim'])."', interests='".addslashes(strip_tags($_POST['interests']))."', userhp='".addslashes($uhp)."', location='".addslashes(strip_tags($_POST['form_location']))."', gender='".$_POST['gender']."' WHERE userid='".intval($auth->user['userid'])."'");
    rideSite($sess->url('memberdetails.php'), $lang['memberdetails_profile_changed']);
    exit();    
} elseif($_POST['action'] == 'profile_email') {
    if(empty($_POST['form_usermail'])) {
        rideSite($sess->url('memberdetails.php?page=email'), $lang['rec_error2']);
        exit();
    }
    if(!isEmail($_POST['form_usermail'])) {
        rideSite($sess->url('memberdetails.php?page=email'), $lang['rec_error3']);
        exit();
    }
    $db_sql->sql_query("UPDATE $user_table SET  useremail='".addslashes(htmlspecialchars($_POST['form_usermail']))."', show_email_global='".intval($_POST['form_glob_mail'])."' WHERE userid='".intval($auth->user['userid'])."'");    
    rideSite($sess->url('memberdetails.php?page=email'), $lang['memberdetails_email_changed']);
    exit();    
} elseif($_POST['action'] == 'profile_avatar') {
    $db_sql->sql_query("UPDATE $user_table SET  avatarid='".intval($_POST['avatar'])."' WHERE userid='".intval($auth->user['userid'])."'");    
    rideSite($sess->url('memberdetails.php?page=avatar'), $lang['memberdetails_avatar_changed']);
    exit();  
}
   
if(isset($_POST['action']) && $_POST['action']=='pwchange') {
    if(empty($_POST['form_passwort']) || empty($_POST['form_passwort2'])) {
        rideSite($sess->url('memberdetails.php?change=1'), $lang['rec_error16']);
        exit();
    }
    if($_POST['form_passwort'] != $_POST['form_passwort2']) {
        rideSite($sess->url('memberdetails.php?change=1'), $lang['rec_error17']);
        exit();
    }
    if(strlen( $_POST['form_passwort'] ) > 15 ) {
        rideSite($sess->url('memberdetails.php?change=1'), $lang['rec_error18']);
        exit();
    }
	rewritePW($_POST['form_passwort'],$auth->user['userid']);
	header("Location: ".$sess->url("index.php"));
	exit;
}

$tpl->register('username', $auth->user['username']);

if(!$_GET['page']) {
    if($auth->user['gender'] == '0') {
        $sel1 = 'selected';
        $tpl->register('selected1', 'selected');
    } elseif($auth->user['gender'] == '2') {
    	$tpl->register('selected2', 'selected');
    } else {
    	$tpl->register('selected3', 'selected');
    }
    $tpl->loadFile('main', 'profile.html'); 
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), 'Profil' => definedBoardUrls("changeaccount"), $lang['profile_general_option'] => '')));
    $tpl->register(array('form_location' => $auth->user['location'],
                        'form_userhp' => $auth->user['userhp'],
                        'form_icq' => $auth->user['usericq'],
                        'form_aim' => $auth->user['aim'],
                        'form_yim' => $auth->user['yim'],
                        'interests' => $auth->user['interests'],
                        'profile_profile_general_option' => $lang['profile_profile_general_option'],
                        'profile_homepage' => $lang['profile_homepage'],
                        'profile_enter_url_to_your_homepage' => $lang['profile_enter_url_to_your_homepage'],
                        'profile_messenger_settings' => $lang['profile_messenger_settings'],
                        'profile_personally_messenger_data' => $lang['profile_personally_messenger_data'],
                        'profile_yahoo' => $lang['profile_yahoo'],
                        'profile_icq' => $lang['profile_icq'],
                        'profile_aim' => $lang['profile_aim'],
                        'profile_location' => $lang['profile_location'],
                        'profile_gender' => $lang['profile_gender'],
                        'profile_na' => $lang['profile_na'],
                        'profile_male' => $lang['profile_male'],
                        'profile_female' => $lang['profile_female'],
                        'profile_interests' => $lang['profile_interests'],
                        'profile_give_details_to_your_interests' => $lang['profile_give_details_to_your_interests']));
} elseif($_GET['page'] == 'email') {
    $tpl->register('form_usermail', $auth->user['useremail']);
    if ($auth->user['show_email_global'] == 1) {
        $tpl->register('mail_check', 'checked');
    }
    
    if($config['reg_withmail'] == 1) {
        $tpl->register('disabled', 'disabled="true"');
        $tpl->register('hidden', "<input name=\"form_usermail\" type=\"hidden\" value=\"".$auth->user['useremail']."\">");
    }    
    $tpl->register(array(
                        'profile_email_email_options' => $lang['profile_email_email_options'],
                        'profile_email_email' => $lang['profile_email_email'],
                        'profile_email_enter_your_email_adress' => $lang['profile_email_enter_your_email_adress'],
                        'profile_email_email_settings' => $lang['profile_email_email_settings'],
                        'profile_email_if_activated_other_can_write_emails' => $lang['profile_email_if_activated_other_can_write_emails'],
                        'profile_email_yes_other_member_can write_mails' => $lang['profile_email_yes_other_member_can write_mails']));
    
    
    $tpl->loadFile('main', 'profile_email.html'); 
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), 'Profil' => definedBoardUrls("changeaccount"), $lang['profile_email_option'] => '')));
} elseif($_GET['page'] == 'password') {
    $tpl->loadFile('main', 'profile_password.html'); 
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), 'Profil' => definedBoardUrls("changeaccount"), $lang['profile_password_option'] => '')));    
    $tpl->register(array(
                        'profile_password_password_option' => $lang['profile_password_password_option'],
                        'profile_password_change_password' => $lang['profile_password_change_password'],
                        'profile_password_change_your_current_password_here' => $lang['profile_password_change_your_current_password_here'],
                        'profile_password_new_password' => $lang['profile_password_new_password'],
                        'profile_password_confirm_password' => $lang['profile_password_confirm_password']));
} elseif($_GET['page'] == 'avatar') {
    if($auth->user['avatarid']) {
        $useravatar = $db_sql->query_array("SELECT * FROM $avat_table WHERE avatarid='".$auth->user['avatarid']."'");
        $tpl->register('useravatar', "<img src=\"".$config['avaturl']."/".$useravatar['avatardata']."\" align=\"top\" />");
    }
	$over_all = $db_sql->query_array("SELECT Count(*) as total FROM $avat_table");
	
	include_once($_ENGINE['eng_dir']."admin/enginelib/class.nav.php");
	if(!isset($_GET['start'])) {
        $start = 0;
    } else {
        $start = intval($_GET['start']);
    }
	$nav = new Nav_Link();
	$nav->overAll = $over_all['total'];
	$nav->perPage = 12;
    $nav->DisplayLast = 1;
    $nav->DisplayFirst = 1;    
	$url_neu = $sess->url("memberdetails.php?page=avatar")."&amp;";
	$nav->MyLink = $url_neu;
	$nav->LinkClass = "page_step";
	$nav->start = $start;
	$pagecount = $nav->BuildLinks();
	if(!$pagecount) $pagecount = "<b>1</b>";
    $pages = intval($over_all['total'] / $nav->perPage);
    if($over_all['total'] % $nav->perPage) $pages++;	    
	$tpl->register('pagecount', $lang['php_page']." (".$pages."): ".$pagecount);  
      
    $result = $db_sql->sql_query("SELECT * FROM $avat_table LIMIT $start,12");
    $no = 1;
    while($avt = $db_sql->fetch_array($result)) {
        if($no == 1 || $no == 5 || $no == 9) $display_avatar .= "<tr>";
        $display_avatar .= "<td class=\"list_dark\"><div align=\"center\"><img src=\"".$config['avaturl']."/".$avt['avatardata']."\" /><input type=\"radio\" name=\"avatar\" value=\"".$avt['avatarid']."\"";
        if($avt['avatarid'] == $auth->user['avatarid']) $display_avatar .= "checked";
        $display_avatar .= " /></div></td>";
        if($no == 4 || $no == 8 || $no == 12) $display_avatar .= "</tr>";
        $no++;
    }
    $tpl->register('avatars', $display_avatar);
    
    $tpl->loadFile('main', 'profile_avatar.html'); 
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), 'Profil' => definedBoardUrls("changeaccount"), $lang['profile_avatar_option'] => '')));
    $tpl->register(array(
                        'profile_avatar_profile_avatar' => $lang['profile_avatar_profile_avatar'],
                        'profile_avatar_current_avatar' => $lang['profile_avatar_current_avatar'],
                        'profile_avatar_avatars_are_small_grafics' => $lang['profile_avatar_avatars_are_small_grafics'],
                        'profile_avatar_available_avatars' => $lang['profile_avatar_available_avatars']));    
    
}

$tpl->register(array(
                    'profile_profile_option' => $lang['profile_profile_option'],
                    'profile_general_option' => $lang['profile_general_option'],
                    'profile_email_option' => $lang['profile_email_option'],
                    'profile_password_option' => $lang['profile_password_option'],
                    'profile_avatar_option' => $lang['profile_avatar_option'],
                    'profile_save_changes' => $lang['profile_save_changes'],
                    'profile_reset' => $lang['profile_reset']));
							
$tpl->register('query', showQueries($develope));
$tpl->register('header', $tpl->pget('header'));

$tpl->register('footer', $tpl->pget('footer'));
$tpl->pprint('main');				
?>
