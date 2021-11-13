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
|   > Deadlink Informationen
|
|   > Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|   > bedingungen (read-me.html) für weitere Informationen.
|   >-----------------------------------------------------------------
|   > This script is NOT freeware! Please read the Copyright Notice
|   > (read-me_eng.html) for further information. 
|
|   > $Id: deadlink.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

include_once('lib.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
$title = $lang['title_deadlink']; 
include_once('include/adapt.inc.php');

$tpl->loadFile('main', 'deadlink.html'); 
$tpl->register('title', $lang['title_deadlink']);

$error_title = '';

$dlid = $_GET['dlid'];
$subcat = $_GET['subcat'];

if(!$_POST['confirm']) {
    $file = GetFile($dlid);
    $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), trim($file['titel']) => $sess->url('index.php?subcat='.$file['catid']) ,trim($file['dltitle']) => $sess->url('comment.php?dlid='.$file['dlid']), $lang['deadlink_report_missing_link'] => '')));
    $tpl->register(array('deadlink_report_missing_link' => $lang['deadlink_report_missing_link'],
                        'deadlink_error_details' => $lang['deadlink_error_details'],
                        'deadlink_thank_you' => sprintf($lang['deadlink_thank_you'],$file['dltitle']),
                        'deadlink_error_message' => $lang['deadlink_error_message'],
                        'deadlink_please_choose_error_message' => $lang['deadlink_please_choose_error_message'],
                        'deadlink_comment' => $lang['deadlink_comment'],
                        'deadlink_btn_report_missing_link' => $lang['deadlink_btn_report_missing_link'],
                        'catid' => $file['catid'],
                        'dlid' => $file['dlid']));
    $query = showQueries($develope);    
    $tpl->register('query', showQueries($develope));
    $tpl->register('header', $tpl->pget('header'));
    
    $tpl->register('footer', $tpl->pget('footer'));
    $tpl->pprint('main');   
} else {
    if (isset($_POST['dlid'])) {
        SetDeadLink($_POST['dlid']);
    } else {
        header("Location: ".$sess->url("index.php?subcat=".$_POST['subcat']));
        exit;
    }
    
    if ($config['deadmail'] == 1) {
        $error_message = $_POST['error'];
        $error_comment = $_POST['usercomment'];
        $file = GetFile($_POST['dlid']);
        
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
        $mail->AddAddress($config['admin_mail']);
        $mail->Subject = $lang['mail_deadlink_betreff'];  
        $mail->Body = sprintf($lang['mail_deadlink_inhalt'],$_POST['dlid'],$file['dltitle'],$_POST['subcat'],$_POST['error_message'],$_POST['error_comment'])."\n\n".sprintf($lang['mail_footer'],$config['scriptname']);
        $mail->WordWrap = 50;  
        
        if(!$mail->Send()) {
           $ride_message = $lang['php_mailer_error'].": ".$mail->ErrorInfo;
        } else {
           $ride_message = $lang['rec_error53'];
        }  
    
        rideSite($sess->url("index.php?subcat=".$_POST['subcat']), $ride_message);
        exit();  
    } else {
        rideSite($sess->url("index.php?subcat=".$_POST['subcat']), $lang['rec_error53']);
        exit();  
    }
}
?>