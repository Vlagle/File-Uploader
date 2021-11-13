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
|   > Passwort Erinnerung
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: remember.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

include_once('lib.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");

$tpl->register('title', $lang['title_remember']);

$message='';

if(BOARD_DRIVER != "default") {
	$location = definedBoardUrls("addmember");
	header("Location: ".$location);
	exit;	
}

if($_GET['action'] == 'rempw') {
	if (!isset ($_GET['usid']) && !isset($_GET['pw'])) {
        rideSite($sess->url('index.php'), $lang['rec_error22']);
        exit();	
	} else {
		$usid = intval($_GET['usid']);
		$pw = addslashes($_GET['pw']);
		$result = $db_sql->query_array("SELECT userid, username, useremail FROM $user_table WHERE userid='$usid' AND userpassword='$pw'");
		if(!$result['userid']) {
	        rideSite($sess->url('index.php'), $lang['rec_error22']);
	        exit();
		} else {
			$newword = "abcdefghijklmnopqrstuvwxyz";
			for($i = 0; $i < 6; $i++) {
				$datum = date("s", time()+$i*4567);
				mt_srand($datum);
				$zahl = mt_rand(0,25);
				$newpw .= substr($newword, $zahl, 1);
			}
			$inspw = md5($newpw);
            
            $inhalt = sprintf($lang['rem_newpw_inhalt'],$result['username'],$newpw).sprintf($lang['mail_footer'],$config['scriptname']);
                      
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
		    $mail->AddAddress($result['useremail']);
		    $mail->Subject = sprintf($lang['rem_newpw_betreff'],$config['scriptname']);
		    $mail->Body = $inhalt;
		    $mail->WordWrap = 50;
		
		    if(!$mail->Send()) {
				$ride_message = $lang['php_mailer_error'].": ".$mail->ErrorInfo;
		    } else {
				$ride_message = $lang['rec_error25'];
				$db_sql->sql_query("UPDATE $user_table SET userpassword='$inspw' WHERE userid='$usid'");
		    }  	
			rideSite($sess->url('index.php'), $ride_message);
			exit(); 				
		}
	}
}

if ($_POST['action'] == 'send') {
	$member = holeUser($_POST['username']);
	if (!$member['username']) {
        rideSite($sess->url('index.php'), $lang['rec_error23']);
        exit();	
	} else {
        $inhalt = sprintf($lang['rem_pw_inhalt'],$member['username'],$config['engine_mainurl']."/remember.php?action=rempw&usid=".$member['userid']."&pw=".$member['userpassword']).sprintf($lang['mail_footer'],$config['scriptname']);      
		
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
	    $mail->AddAddress($member['useremail']);
	    $mail->Subject = sprintf($lang['rem_pw_betreff'],$config['scriptname']);
	    $mail->Body = $inhalt;
	    $mail->WordWrap = 50;
	
	    if(!$mail->Send()) {
			$ride_message = $lang['php_mailer_error'].": ".$mail->ErrorInfo;
	    } else {
			$ride_message = $lang['rec_error24'];
	    }  	
		rideSite($_GET['referer'], $ride_message);
		exit(); 		
	}
}

$tpl->register(array(
                    'remember_password_lost' => $lang['remember_password_lost'],
                    'remember_call_for_new_password' => $lang['remember_call_for_new_password'],
                    'remember_function_description' => $lang['remember_function_description'],
                    'remember_username' => $lang['remember_username'],
                    'remember_password_btn' => $lang['remember_password_btn'],
                    'remember_reset_btn' => $lang['remember_reset_btn']));

$tpl->loadFile('main', 'remember.html');
$tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['remember_password_lost'] => '')));

$tpl->register('query', showQueries($develope));
$tpl->register('header', $tpl->pget('header'));

$tpl->register('footer', $tpl->pget('footer'));
$tpl->pprint('main');
?>
