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
|   > Avatar-Funktionen Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: avatar.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","avatar.php");

include_once('adminfunc.inc.php');
$auth->checkEnginePerm("canaccessadmincent");

function InsertNewAvatar($avatardata) {
    global $avat_table,$db_sql;
    $db_sql->sql_query("INSERT INTO $avat_table (avatardata) VALUES ('".addslashes($avatardata)."')");
    return mysql_insert_id();
}    

$message = "";
$max_fsize = "2097152";
$extensions = ("gif,jpeg,jpg,png");

$filesdir = strrchr($config['avaturl'],303);
$filesdir = substr($filesdir, 1); 
$filesdir = "../".$filesdir;	

if(isset ($action) && $action=="add") {
		$message = "";
		if($_FILES['upl_av']['tmp_name'] == "") { // File ist nicht dabei
			$message .= $a_lang['uploads_copy']."1";
		} else {
			$extens = explode(",",$extensions); 
			$pic_extension = strtolower(substr(strrchr($_FILES['upl_av']['name'],"."),1));
			
			if(!in_array($pic_extension,$extens)) { // Dateierweiterung ungültig
				$message .= $a_lang['uploads_extens'];
			} else {
				if($_FILES['upl_av']['size'] > $max_fsize) { // Datei zu groß
					$message .= $a_lang['uploads_size']." ".$max_fsize." Bytes";
				} else {
					if(!file_exists($filesdir."/".$_FILES['upl_av']['name'])) {
						if(@move_uploaded_file($_FILES['upl_av']['tmp_name'],$filesdir."/".$_FILES['upl_av']['name'])) { // Datei kopieren
							@chmod($filesdir."/".$_FILES['upl_av']['name'], 0777); 								
							$avatid = InsertNewAvatar($_FILES['upl_av']['name']);							
							$message .= $a_lang['avatar_mes1'];	
							$step = "edit";
						} else {
							$message .= $a_lang['uploads_copy'];
						}
					} else { // File existiert
						$message .= $a_lang['uploads_stillexist'];
					}
				}		
			}
		}	
	}	
	
if(isset ($action) && $action=="edit") {
    $db_sql->sql_query("UPDATE $avat_table SET avatardata='".addslashes($avatardata)."' WHERE avatarid='$avatarid'");
	$message .= "$a_lang[avatar_mes2]";
    $step = "edit";
	}
	
if(isset ($action) && $action=="del") {
    $db_sql->sql_query("DELETE FROM $avat_table WHERE avatarid='$avatarid'");
	$message .= "$a_lang[avatar_mes3] $avatarid";
	$step = "edit";
	}


buildAdminHeader();

if ($message != "") {
    buildMessageRow($message, array('is_top' => 1, 'next_script' => 'avatar.php', 'next_action' => array('step',$step,$a_lang['afunc_proceed'])));
    buildAdminFooter();
    exit;
}
   
if($step == "add") {
    buildHeaderRow($a_lang['uploads_avatupload'],"avat.gif");
    buildFormHeader("avatar.php", "post", "add", "", 1);
    buildHiddenField("MAX_FILE_SIZE",$max_fsize);
    buildTableHeader($a_lang['uploads_new']);
    buildLightColumn("$a_lang[uploads_note1] $max_fsize $a_lang[uploads_note2]",1,1,2);
    buildDarkColumn("$a_lang[uploads_search] $head:",1,0);
    buildLightColumn("<input class=\"input\" type=\"file\" name=\"upl_av\" size=\"40\" maxlenght=\"150\">",0,1);
    buildFormFooter($a_lang['uploads_upload'], $a_lang['uploads_reset'], 2);
}

if($step == "edit") {
    buildHeaderRow($a_lang['afunc_194'],"avat.gif");
    buildTableHeader($a_lang['afunc_195']);
    
    $result = $db_sql->sql_query("SELECT * FROM $avat_table");
    while($avat = $db_sql->fetch_array($result)) {
        buildStandardRow("<img src=\"$config[avaturl]/$avat[avatardata]\" border=\"0\">&nbsp;$avat[avatardata]", "<a class=\"menu\" href=\"".$sess->adminUrl("avatar.php?avatarid=".$avat['avatarid']."&step=rename")."\"><img src=\"images/edit.gif\" alt=\"".$a_lang['afunc_196']."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">$a_lang[afunc_196]</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class=\"menu\" href=\"".$sess->adminUrl("avatar.php?avatarid=".$avat['avatarid']."&step=del")."\"><img src=\"images/delete.gif\" alt=\"".$a_lang[afunc_197]."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">$a_lang[afunc_197]</a>");
    }
    buildTableFooter("",2);
    buildExternalItems($a_lang['avatar_new'],"avatar.php?step=add","add.gif");
}

if($step == "rename") {
    $result = $db_sql->sql_query("SELECT avatardata FROM $avat_table WHERE avatarid=$avatarid");
    $avatar = $db_sql->fetch_array($result);
    
    buildHeaderRow($a_lang['afunc_198'],"avat.gif");
    buildFormHeader("avatar.php","post", "edit", "");
    buildHiddenField("avatarid",$avatarid);
    buildTableHeader($a_lang['afunc_199'], 2);
    buildInputRow("Dateiname:", "avatardata", $avatar['avatardata'], "60");
    buildFormFooter($a_lang['afunc_200'], "", $colspan = 2, $a_lang['afunc_336']);
}

if($step == "del") {
    $result = $db_sql->sql_query("SELECT * FROM $avat_table WHERE avatarid='$avatarid'");
    $del = $db_sql->fetch_array($result);
    
    buildHeaderRow($a_lang['afunc_8'],"delart.gif");
    buildFormHeader("avatar.php", "post", "del");
    buildHiddenField("avatarid",$avatarid);
    buildTableHeader("$a_lang[afunc_8]: <u>$del[avatardata]</u>");
    buildDarkColumn("$a_lang[avatar_del1] (ID: $avatarid) $a_lang[avatar_del2]",1,1,2); 
    buildFormFooter($a_lang['afunc_61'], "", "", $a_lang['afunc_62']);
} 
   
buildAdminFooter();
?>