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
|   > Moderator-Funktionen
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: moderator.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

include_once('lib.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
include_once($_ENGINE['eng_dir']."admin/enginelib/class.comment.php");

$tpl->register(array('comment_js_too_long' => $lang['comment_js_too_long'],
                    'comment_js_maximum' => $lang['comment_js_maximum'],
                    'comment_js_long1' => $lang['comment_js_long1'],
                    'comment_js_long2' => $lang['comment_js_long2'],
                    'comment_js1' => $lang['comment_js1'],
                    'comment_js2' => $lang['comment_js2'],
                    'comment_js3' => $lang['comment_js3'],
                    'comment_js4' => $lang['comment_js4'],                    
                    'max_comment_length' => $config['max_comment_length']));

$tpl->loadFile('meta', 'comment_js.html'); 
$tpl->register('header_add', $tpl->pget('meta'));

$tpl->loadFile('main', 'moderator.html'); 
$tpl->register('title', $lang['title_moderator']);
$tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['title_moderator'] => '')));

include_once($_ENGINE['eng_dir']."admin/enginelib/class.bbcode.php");
$bbcode = new engineBBCode(1,0,1,0,$config['smilieurl']); 


$conditions = array("table"=>$dlcomment_table,
					"id"=>"comid",
					"userid"=>"userid",
					"headline"=>"com_headline",
					"comment"=>"dl_comment",
					"date"=>"com_date",
					"status"=>"com_status",
					"postid"=>"dlid",
					"ip"=>"user_ip",
					"username"=>"user_comname",
					"posticon"=>"posticon");
					
$ct = new engineComment($conditions); 

if (!$auth->user['caneditcomments'] || !$auth->user['candeletecomments']) {
   rideSite($sess->url('index.php'), $lang['misc_5']);
   exit();  
}

$moderator = $auth->user['username'];

if($_POST['mod']=='confirmedit' && $_POST['comid']) {
    if($auth->user['caneditcomments']) {
        if($_POST['add_edited']) {
            $comment_message = addslashes($_POST['comment_message'])."\n\n\[edit=".$moderator."\]".aseDate($config['shortdate'],time())."\[/edit\]";
        } else {
            $comment_message = addslashes($_POST['comment_message']);
        }
        
        if(!$_POST['delete_comment']) {
            $db_sql->sql_query("UPDATE $dlcomment_table SET com_headline='".addslashes($_POST['headline'])."', dl_comment='".$comment_message."', user_comname='".addslashes($_POST['poster'])."', posticon='".addslashes($_POST['posticon'])."' WHERE comid='".intval($_POST['comid'])."'");
            $message .= $lang['rec_error36'];
        }
    }
    
    if($auth->user['candeletecomments'] && $_POST['delete_comment']) {
        $db_sql->sql_query("DELETE FROM $dlcomment_table WHERE comid='".intval($_POST['comid'])."'");
        $message .= $lang['rec_error37'];
    }
    
   rideSite($sess->url('comment.php?dlid='.$_POST['postid']), $message);
   exit();    
}

$ct->displayCommentPost($_GET['comid']);
   
if($auth->user['candeletecomments']) {
    $user_can_delete_comments = true;
} else {
    $user_can_delete_comments = false;
}

if($auth->user['caneditcomments']) {
    $user_can_edit_comments = true;
    $user_can_edit_comments2 = true;    
} else {
    $user_can_edit_comments = false;
    $user_can_edit_comments2 = false;    
}

$tpl->parseIf('main', 'user_can_delete_comments');
$tpl->parseIf('main', 'user_can_edit_comments');
$tpl->parseIf('main', 'user_can_edit_comments2');

$tpl->register(array(
                    'moderator_title_moderator' => $lang['title_moderator'],
                    'moderator_button_alt_answer' => $lang['moderator_button_alt_answer'],
                    'moderator_link_write_answer' => $lang['moderator_link_write_answer'],
                    'moderator_author' => $lang['moderator_author'],
                    'moderator_message' => $lang['moderator_message'],
                    'moderator_edit_comment_here' => $lang['moderator_edit_comment_here'],
                    'moderator_username' => $lang['moderator_username'],
                    'moderator_topic' => $lang['moderator_topic'],
                    'moderator_posticons' => $lang['moderator_posticons'],
                    'moderator_your_message' => $lang['moderator_edit_message'],
                    'moderator_bold' => $lang['moderator_bold'],
                    'moderator_italic' => $lang['moderator_italic'],
                    'moderator_underline' => $lang['moderator_underline'],
                    'moderator_url' => $lang['moderator_url'],
                    'moderator_email' => $lang['moderator_email'],
                    'moderator_code' => $lang['moderator_code'],
                    'moderator_quote' => $lang['moderator_quote'],
                    'moderator_center' => $lang['moderator_center'],
                    'moderator_line' => $lang['moderator_line'],
                    'moderator_click_smilies' => $lang['moderator_click_smilies'],
                    'moderator_edit_comment_btn' => $lang['moderator_edit_comment_btn'],
                    'moderator_reset_btn' => $lang['moderator_reset_btn'],
                    'moderator_not_allowed_to_post_comment' => $lang['moderator_not_allowed_to_post_comment'],
                    'moderator_delete_comment' => $lang['moderator_delete_comment'],
                    'moderator_confirm_this_to_delete_comment' => $lang['moderator_confirm_this_to_delete_comment'],
                    'moderator_edit_options' => $lang['moderator_edit_options'],
                    'moderator_add_edited by' => $lang['moderator_add_edited by']));

$tpl->register('query', showQueries($develope));
$tpl->register('header', $tpl->pget('header'));

$tpl->register('footer', $tpl->pget('footer'));
$tpl->pprint('main');
?>
   
