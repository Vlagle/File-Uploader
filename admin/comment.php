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
|   > Kommentar-Funktionen Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: comment.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","comment.php");

include_once('adminfunc.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
$auth->checkEnginePerm("canaccessadmincent");

buildAdminHeader();

if ($step == 'conf') {
    $db_sql->sql_query("UPDATE $dl_table SET comment_count=comment_count+1 WHERE dlid='$dlid'");
    $db_sql->sql_query("UPDATE $dlcomment_table SET com_status=1 WHERE comid=$comid");
    $message = $a_lang['comment_mes1'];
}

if ($action == 'conf_multi') {
    if($_POST['public']) {
    	foreach($comid as $key=>$wert) {
    		$db_sql->sql_query("UPDATE $dlcomment_table SET com_status='1' WHERE comid='$key'");
            massConfirmation($key);
    	}	
        $message = $a_lang['comment_mes1'];        
    } elseif($_POST['delete']) {
    	foreach($comid as $key=>$wert) {
            $db_sql->sql_query("DELETE FROM $dlcomment_table WHERE comid='$key'");
    	}	
        $message = $a_lang['comment_mes2'];     
    }
}   
   
if ($step == 'del') {
    $db_sql->sql_query("DELETE FROM $dlcomment_table WHERE comid=$comid");
    $message = $a_lang['comment_mes2'];
}
   
if ($message != "") {
    buildMessageRow($message, array('auto_redirect' => 'main.php', 'is_top' => 1, 'next_script' => 'main.php', 'next_action' => array('','',$a_lang['afunc_proceed'])));
    buildAdminFooter();
    exit;
}

if ($step == 'details') {
    include_once($_ENGINE['eng_dir']."admin/enginelib/class.bbcode.php");
    $bbcode = new engineBBCode(1,0,1,0,$config['smilieurl']);	
	$result = $db_sql->sql_query(" SELECT $dlcomment_table.*, $dl_table.dltitle, $cat_table.* FROM $dlcomment_table 
							LEFT JOIN $dl_table ON $dl_table.dlid = $dlcomment_table.dlid
							LEFT JOIN $cat_table ON $dl_table.catid = $cat_table.catid
							WHERE comid='$comid'");
    $com = $db_sql->fetch_array($result);
			 
	if ($com['user_comname'] == '0') {
		$writer = CheckUserID($com['userid']);
		$author = "<a class=\"menu\" href=\"mailto:$writer[useremail]\">$writer[username]</a>";
	} elseif ($com['user_comname'] == "") {
		$writer = CheckUserID($com['userid']);
		$author = "<a class=\"menu\" href=\"mailto:$writer[useremail]\">$writer[username]</a>";
	} else {
		$author = "$com[user_comname] - GAST";
	}
        			 
	$com_date = $com['com_date'];
	$date = getdate($com_date);
					 
	if ($com['posticon'] == "") {
		$picon = "";
	} else {
		$picon = "<img src=\"$com[posticon]\">";
	}
	$post_comment = $bbcode->rebuildText($com['dl_comment']);
	$post_comment = trim($post_comment);
	
	$com['com_headline'] = trim(stripslashes($com['com_headline']));
    
	$com['com_headline'] = trim(stripslashes($com['com_headline']));
	buildHeaderRow($a_lang['comment_det'],"search.gif");
	buildTableHeader($a_lang['comment_main']);
	buildStandardRow($a_lang['comment_file'], "$com[dltitle] (ID: $com[dlid])");
	buildStandardRow($a_lang['comment_categ'], "$com[titel] (ID: $com[catid])");
	buildStandardRow($a_lang['comment_written'], "$author $a_lang[comment_at] $date[mday].$date[mon].$date[year]");
	buildTableSeparator($a_lang['comment_headcomment']);
	buildStandardRow($a_lang['comment_headline'], "$picon $com[com_headline]");
	buildStandardRow($a_lang['comment'], $post_comment);
	buildTableFooter();
	closeWindowRow();  
}

buildAdminFooter();
?>