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
|   > Mitglieder Funktionen
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: addmember.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

include_once('lib.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");

$tpl->register('title', $lang['title_addmember']);

if ($config['userreg'] == 1) {
   header("Location: ".$sess->url("index.php"));
   exit;
}

// Login mit Mailbest&auml;tigung
if($_GET['action'] == 'register' || $_POST['action'] == 'register') {
	if(empty($_POST['u_login']) || empty($_POST['u_passwort']) || empty($_POST['u_passwort2'])) {
        rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error16']);
        exit();
    }
	if($_POST['u_passwort'] != $_POST['u_passwort2']) {
        rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error17']);
        exit();
    }
	if($_POST['u_email'] != $_POST['u_email2']) {
        rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error51']);
        exit();
    }	
   	if(!isEmail($_POST['u_email'])) {
        rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error3']);
        exit();
    }			
	if(strlen($_POST['u_passwort']) > 15) {
        rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error18']);
        exit();
    }
	if(strlen($_POST['u_login']) > 15) {
        rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error19']);
        exit();
    }
	if(holeUser($_POST['u_login'])) {
        rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error20']);
        exit();
    }

    mt_srand((double)microtime()*1000000);
    $act_code = mt_rand();
    $today = time();
    $mypass = addslashes(md5($_POST['u_passwort']));
    $mylogin = addslashes(htmlspecialchars(trim($_POST['u_login'])));
    $users_mail = addslashes(trim($_POST['u_email']));
    $result = $db_sql->sql_query("INSERT INTO $user_table (username, userpassword, useremail, regdate, lastvisit, groupid, activation) VALUES('".$mylogin."','".$mypass."', '".$users_mail."', '".$today."', '".$today."', '".$_ENGINE['std_group']."', '".$act_code."')");	
    $myuserid = $db_sql->insert_id();

    $inhalt = sprintf($lang['mail_register_inhalt'],$mylogin,$config['scriptname'],$config['engine_mainurl']."/addmember.php?action=activation&myuserid=".$myuserid."&actcode=".$act_code,$config['guestscripturl']."/addmember.php?action=activation&myuserid=".$myuserid."&actcode=".$act_code).sprintf($lang['mail_footer'],$config['scriptname']);

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

    $mail->From = $config['admin_mail'];
    $mail->FromName = $config['scriptname'];
    $mail->AddAddress($users_mail);
    $mail->Subject = $lang['mail_register_betreff'].' '.$config['scriptname'];
    $mail->Body = $inhalt;
    $mail->WordWrap = 50;

    if(!$mail->Send()) {
       $ride_message = $lang['php_mailer_error'].": ".$mail->ErrorInfo;
    } else {
       $ride_message = $lang['rec_error50'];
    }

    rideSite($sess->url('index.php'), $ride_message);
    exit();
}

// Login-Formular und Registrierung ohne Mail
if($_GET['action'] == 'addmember' || $_POST['action'] == 'addmember') {	
    $message="";
    if ($auth->user['userid'] != 2) {
        rideSite($sess->url('index.php'), $lang['rec_error15']);
        exit();
    }

    if(isset($_POST['login']) && $_POST['login']=='join') {
        if(empty($_POST['u_login']) || empty($_POST['u_passwort']) || empty($_POST['u_passwort2'])) {
            rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error16']);
            exit();
        }
        if ($_POST['u_passwort'] != $_POST['u_passwort2']) {
            rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error17']);
            exit();
        }
        if (strlen( $_POST['u_passwort']) > 15) {
            rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error18']);
            exit();
        }
    	if($_POST['u_email'] != $_POST['u_email2']) {
            rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error51']);
            exit();
        }	
       	if(!isEmail($_POST['u_email'])) {
            rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error3']);
            exit();
        }	            
        if (strlen( $_POST['u_login']) > 15) {
            rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error19']);
            exit();
        }
        if (holeUser($_POST['u_login'])) {
            rideSite($sess->url('addmember.php?action=addmember'), $lang['rec_error20']);
            exit();
        }

        $auth->userLogin($_POST['u_login'],$_POST['u_passwort'],true);
        header("Location: ".$sess->url("memberdetails.php?change=1"));
        exit;
    }

    if ($config['reg_withmail'] == 0) {
        $hidden_field = "<input name=\"login\" type=\"hidden\" value=\"join\">\n<input name=\"action\" type=\"hidden\" value=\"addmember\">";
    } else {
        $hidden_field = "<input name=\"action\" type=\"hidden\" value=\"register\">";
    }	
    
    $tpl->register('hidden_field', $hidden_field);
    
    $tpl->register(array(
                        'register_end_register' => $lang['register_end_register'],
                        'register_end_if_you_want_to_write_comments_you_have_to_register' => $lang['register_end_if_you_want_to_write_comments_you_have_to_register'],
                        'register_end_username' => $lang['register_end_username'],
                        'register_end_enter_password_for_your_account' => $lang['register_end_enter_password_for_your_account'],
                        'register_end_password' => $lang['register_end_password'],
                        'register_end_confirm_password' => $lang['register_end_confirm_password'],
                        'register_end_email' => $lang['register_end_email'],
                        'register_end_please_enter_valid_email' => $lang['register_end_please_enter_valid_email'],
                        'register_end_confirm_email' => $lang['register_end_confirm_email'],
                        'register_end_end_btn' => $lang['register_end_end_btn'],
                        'register_end_reset_btn' => $lang['register_end_reset_btn']));	

    $tpl->loadFile('main', 'register_end.html'); 
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['register_end_register'] => '')));
}

if($_GET['action'] == 'rules') {
    $message="";
    if ($auth->user['userid'] != 2) {
        rideSite($sess->url('index.php'), $lang['rec_error15']);
        exit();
    }
    $tpl->loadFile('main', 'register.html'); 
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['register_register'] => '')));
    $tpl->register(array(
                        'register_register' => $lang['register_register'],
                        'register_please_read_terms_to_submit' => $lang['register_please_read_terms_to_submit'],
                        'register_rules' => $lang['register_rules'],
                        'register_to_register_you_have_to_accept_rules' => $lang['register_to_register_you_have_to_accept_rules'],
                        'register_yes_i_accept_the_rules' => $lang['register_yes_i_accept_the_rules'],
                        'register_register_btn' => $lang['register_register_btn'],
                        'register_text' => sprintf($lang['register_text'], $config['scriptname'])));
}

if($_GET['action'] == "activation") {
	$anzahl_user = $db_sql->query_array("SELECT COUNT(userid)as anzahl FROM $user_table WHERE userid='".intval($_GET['myuserid'])."' AND activation='".addslashes($_GET['actcode'])."'");
	
	if($anzahl_user['anzahl'] <> 1) {
        rideSite($sess->url('index.php'), $lang['rec_error52']);
        exit();
	}
	
	if($anzahl_user['anzahl'] == 1) {
        $my_act = $db_sql->query_array("SELECT * FROM $user_table WHERE userid='".intval($_GET['myuserid'])."' AND activation='".addslashes($_GET['actcode'])."'");
        $db_sql->sql_query("UPDATE $user_table SET activation='1' WHERE userid='".$my_act['userid']."'");
        $sess->setSessVar("engine_id", $my_act['userid']);
        $sess->setSessVar("engine_name", $my_act['username']);
        $sess->setSessVar("engine_password", $my_act['userpassword']);
        if($auth->enableIPCheck) $sess->setSessVar("engine_user_ip", $auth->user_ip);
        $auth->setEngineCookie($my_act['userid'],$my_act['userpassword']);
        header("Location: ".$sess->url("memberdetails.php?change=1"));
        exit;	
	}
    
    $tpl->register(array(
                        'register_end_register' => $lang['register_end_register'],
                        'register_end_if_you_want_to_write_comments_you_have_to_register' => $lang['register_end_if_you_want_to_write_comments_you_have_to_register'],
                        'register_end_username' => $lang['register_end_username'],
                        'register_end_enter_password_for_your_account' => $lang['register_end_enter_password_for_your_account'],
                        'register_end_password' => $lang['register_end_password'],
                        'register_end_confirm_password' => $lang['register_end_confirm_password'],
                        'register_end_email' => $lang['register_end_email'],
                        'register_end_please_enter_valid_email' => $lang['register_end_please_enter_valid_email'],
                        'register_end_confirm_email' => $lang['register_end_confirm_email'],
                        'register_end_end_btn' => $lang['register_end_end_btn'],
                        'register_end_reset_btn' => $lang['register_end_reset_btn']));	    
	
    $tpl->loadFile('main', 'register_end.html'); 
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['register_end_register'] => '')));
}

$tpl->register('query', showQueries($develope));
$tpl->register('header', $tpl->pget('header'));

$tpl->register('footer', $tpl->pget('footer'));
$tpl->pprint('main');
?>
