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
|   > Gruppenverwaltung Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: groups.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","groups.php");

include_once('adminfunc.inc.php');
$auth->checkEnginePerm("canaccessadmincent");

$message = '';

if(isset ($action) && $action=='add') {
	$db_sql->sql_query("INSERT INTO $group_table (title,canaccessadmincent,canaccessofflineengine,canuseenginesearch,canmodifyownprofile,canseemembers,canpostcomments,caneditcomments,candeletecomments,canuploadfiles,canuseadvancedstats,canseetopstatsfiles,canaccessregisteredfiles,maxgroupdownloadspeed)
				VALUES ('".addslashes($title)."','".intval($canaccessadmincenter)."','".intval($canaccessofflineengine)."','".intval($canuseenginesearch)."','".intval($canmodifyownprofile)."','".intval($canseemembers)."','".intval($canpostcomments)."','".intval($caneditcomments)."','".intval($candeletecomments)."','".intval($canuploadfiles)."','".intval($canuseadvancedstats)."','".intval($canseetopstatsfiles)."','".intval($canaccessregisteredfiles)."','".intval($maxgroupdownloadspeed)."')");
	$message .= $a_lang['groups_1'];
	$step = "change";
}
	
if(isset ($action) && $action=='del') {
	$db_sql->sql_query("UPDATE $user_table SET groupid='7' WHERE groupid='$egroupid'");
	$db_sql->sql_query("DELETE FROM $group_table WHERE groupid='$egroupid'");
	$message .= $a_lang['groups_2'];
	$step = "change";
}
	
if(isset ($action) && $action=='edit') {

	if($egroup == 7 || $egroup == 8) {
		if($egroup == 7) {
			$where_def = "WHERE groupid='$egroup' OR groupid='3'";
		} else {
			$where_def = "WHERE groupid='$egroup' OR groupid='4'";
		}
	} else {
		$where_def = "WHERE groupid='$egroup'";
	}
	
	$db_sql->sql_query("UPDATE $group_table SET
				title = '".addslashes($title)."', 
				canaccessadmincent='".intval($canaccessadmincenter)."',
				canaccessofflineengine='".intval($canaccessofflineengine)."',
				canuseenginesearch='".intval($canuseenginesearch)."',
				canmodifyownprofile='".intval($canmodifyownprofile)."',
				canseemembers='".intval($canseemembers)."',
				canpostcomments='".intval($canpostcomments)."',
				caneditcomments='".intval($caneditcomments)."',
				candeletecomments='".intval($candeletecomments)."',
				canuploadfiles='".intval($canuploadfiles)."',
				canuseadvancedstats='".intval($canuseadvancedstats)."',
				canseetopstatsfiles='".intval($canseetopstatsfiles)."',
				canaccessregisteredfiles='".intval($canaccessregisteredfiles)."',
                maxgroupdownloadspeed='".intval($maxgroupdownloadspeed)."'
				$where_def");
	$message .= $a_lang['groups_3'];
	$step = "change";
}

buildAdminHeader();	

if ($message != "") {
    buildMessageRow($message, array('is_top' => 1, 'next_script' => 'groups.php', 'next_action' => array('step','change',$a_lang['afunc_proceed'])));
    buildAdminFooter();
    exit;
}

if(!isset ($step) && $change == '') {
  echo " <p><b>Es wurde keine Auswahl getroffen. Bitte wähle links aus der Navigation die gewünschte Option aus.</b></p>";
} else {
    if($step == 'change') {
        buildHeaderRow($a_lang['groups_4'],"group.gif",1);
		buildInfo($a_lang['info6'][0],$a_lang['info6'][1]);
        buildTableHeader($a_lang['groups_5']);
        $result = $db_sql->sql_query("SELECT groupid, title FROM $group_table WHERE groupid!='4' AND groupid!='3' ORDER BY title");
        while($exist_group = $db_sql->fetch_array($result)) {
            unset($delete_group);
            if($exist_group['groupid'] != 1 && $exist_group['groupid'] != 8 && $exist_group['groupid'] != 7) {
				$delete_group = "&nbsp;&nbsp;&nbsp;&nbsp;<a class=\"menu\" href=\"".$sess->adminUrl("groups.php?groupid=".$exist_group[groupid]."&step=del")."\"><img src=\"images/delete.gif\" alt=\"".$a_lang[groups_6]."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">$a_lang[groups_6]</a>";
			} else {
				$delete_group = "&nbsp;&nbsp;&nbsp;&nbsp;<img src=\"images/no_delete.gif\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\" alt=\"".$a_lang[afunc_174]."\">".$a_lang[groups_6]."";
			}
            buildStandardRow("<b>$exist_group[title]</b> (ID: $exist_group[groupid])","<a class=\"menu\" href=\"".$sess->adminUrl("groups.php?groupid=".$exist_group[groupid]."&step=edit")."\"><img src=\"images/edit.gif\" alt=\"".$a_lang[groups_7]."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">$a_lang[groups_7]</a>$delete_group",0);
        }
        buildTableFooter();
        buildExternalItems($a_lang['groups_28'],"groups.php?step=edit&egroupid=add","add.gif");
    }
    
    if($step == 'edit') {
        if($egroupid == "add") {
            $hidden_field = "<input class=\"input\" type=\"hidden\" name=\"action\" value=\"add\">";
        } else {
            $hidden_field = "<input class=\"input\" type=\"hidden\" name=\"action\" value=\"edit\">";
            $result = $db_sql->sql_query("SELECT * FROM $group_table WHERE groupid='$groupid'");
            $usergroup = $db_sql->fetch_array($result);		
        }
        buildHeaderRow($a_lang['groups_8'],"group.gif",1);
		buildInfo($a_lang['info7'][0],$a_lang['info7'][1]);
        buildFormHeader("groups.php");
        echo $hidden_field;  
        buildHiddenField("egroup",$usergroup['groupid']);
          
        buildTableHeader($a_lang['groups_9']);
        buildInputRow($a_lang['groups_10'], "title", $usergroup['title'],"35");
        buildRadioRow($a_lang['groups_11'], "canaccessadmincenter", $usergroup['canaccessadmincent']);
        buildRadioRow($a_lang['groups_12'], "canaccessofflineengine", $usergroup['canaccessofflineengine']);
        buildRadioRow($a_lang['groups_13'], "canuseenginesearch", $usergroup['canuseenginesearch']);
        buildRadioRow($a_lang['groups_14'], "canmodifyownprofile", $usergroup['canmodifyownprofile']);
        buildRadioRow($a_lang['groups_15'], "canseemembers", $usergroup['canseemembers']);
        buildRadioRow($a_lang['groups_16'], "canpostcomments", $usergroup['canpostcomments']);
        buildTableSeparator($a_lang['groups_17']);
        buildRadioRow($a_lang['groups_18'], "caneditcomments", $usergroup['caneditcomments']);
        buildRadioRow($a_lang['groups_19'], "candeletecomments", $usergroup['candeletecomments']);
        buildTableSeparator($a_lang['groups_20']);
        if(!@ini_get("safe_mode")) buildInputRow($a_lang['groups_29'], "maxgroupdownloadspeed", $usergroup['maxgroupdownloadspeed']);
        buildRadioRow($a_lang['groups_21'], "canuploadfiles", $usergroup['canuploadfiles']);
        buildRadioRow($a_lang['groups_22'], "canseetopstatsfiles", $usergroup['canseetopstatsfiles']);
        //buildRadioRow($a_lang['groups_23'], "canuseadvancedstats", $usergroup['canuseadvancedstats']);
        buildRadioRow($a_lang['groups_24'], "canaccessregisteredfiles", $usergroup['canaccessregisteredfiles']);
        buildFormFooter($a_lang['afunc_57'],"",2,$a_lang['afunc_258']);    
    }      
  
    if($step == 'del') {
        $result = $db_sql->sql_query("SELECT * FROM $group_table WHERE groupid='$groupid'");
        $del = $db_sql->fetch_array($result);
        buildHeaderRow($a_lang['afunc_13'],"delart.gif");
        buildFormHeader("groups.php", "post", "del");
        buildHiddenField("egroupid",$groupid);
        buildTableHeader("$a_lang[groups_25]: <u>$del[title]</u>");
        buildDarkColumn($a_lang['groups_26'],1,1,2); 
        buildFormFooter($a_lang['afunc_61'], "", "", $a_lang['afunc_62']);    
    }
  
}    

buildAdminFooter();
?>