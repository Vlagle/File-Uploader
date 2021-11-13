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
|   > Kategorie-Funktionen Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: categories.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","categories.php");

include_once('adminfunc.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
$auth->checkEnginePerm("canaccessadmincent");

$message = '';

if($action=='new') {
    $db_sql->sql_query("INSERT INTO $cat_table (titel,cat_desc,subcat,startorder,direct_upload,cat_style)
                VALUES ('".addslashes(htmlspecialchars($cat_name))."','".addslashes($cat_desc)."','$subcat','".addslashes($startorder)."','".$direct_upload."','".$cat_style."')");
    $new_cat = $db_sql->insert_id();
    addChildlist($new_cat,$new_cat);    
	$message .= $a_lang['categories_mes1'];
	unset ($catid);
}
	
if($action=='del') {
    delChildlist(intval($catid),$catid);
    $db_sql->sql_query("DELETE FROM $cat_table WHERE catid='$catid'");
    $db_sql->sql_query("DELETE FROM $dl_childtable WHERE catid='$catid'");    
    $message .= $a_lang['categories_mes2'];
    unset ($catid);
}
	
if($action=='edit') {
    $db_sql->sql_query("UPDATE $cat_table SET titel='".addslashes(htmlspecialchars($cat_name))."', cat_desc='".addslashes($cat_desc)."', startorder='".addslashes($startorder)."', direct_upload='".$direct_upload."', cat_style='".intval($cat_style)."' WHERE catid='".$subcat."'");    
    $message .= $a_lang['categories_mes3'];
    unset ($catid);
}

if($action=='sort') {
    foreach($order as $key=>$wert) {
        $db_sql->sql_query("UPDATE $cat_table SET catorder='$wert' WHERE catid='$key'");
    }	
    $message .= $a_lang['categories_mes4'];
    unset ($catid);
}
	
buildAdminHeader();

if ($message != "") buildMessageRow($message);
	
if(!isset ($catid)) $catid = 0;

if(!isset ($step)) {
    buildHeaderRow($a_lang['afunc_25']);
    if($catid != 0) {
        $main = $db_sql->query_array("SELECT titel,subcat FROM $cat_table WHERE catid='$catid'");
        
        $breadcrumb_array = array('Engine Index' => $sess->adminUrl("categories.php"));
        
        $mother = stripslashes($main['titel']);
        $old['subcat'] = $main['subcat'];
        
        if($old['subcat'] != 0) {
            while ($old['subcat'] != 0) {
                $old = $db_sql->query_array("SELECT catid,titel,subcat FROM $cat_table WHERE catid='".$old['subcat']."'");
                $path_array[stripslashes($old['titel'])] = $sess->adminUrl("categories.php?catid=".$old['catid']);
            }
        } else {
            $path_array[stripslashes($main['titel'])] = '';
        }
        
        $path_array = array_reverse($path_array);
        $breadcrumb_array = array_merge($breadcrumb_array,$path_array);		
        $breadcrumb_array[$mother] = '';
        echo "<p>".buildAdminBreadCrumb($breadcrumb_array,$catid)."</p>";	
    } else {
        echo "<p><b>Engine Index</b></p>";
    }    
    buildFormHeader("categories.php", "post", "sort");
    /*buildTableHeader($a_lang['afunc_26'], 4); */ 
	buildTableDescription(array($a_lang['categories_categories_id'],$a_lang['categories_diplay_order'],$a_lang['categories_options'],$a_lang['categories_subcategories']),1);
    $result = $db_sql->sql_query("SELECT catid,titel,subcat,catorder FROM $cat_table WHERE subcat='$catid' ORDER BY catorder");
    while(list($subid,$titel,$subcat,$catorder) = mysql_fetch_row($result)) {
        $count = 0;
        $result2 = $db_sql->sql_query("SELECT catid FROM $cat_table WHERE subcat='$subid'");
        while(list($dcatid,$dtitel,$dsubcat) = mysql_fetch_row($result2)) $count++;
        
        if ($count == "0") {
            $ucat = "&nbsp;";
        } else {
            $ucat = "<a class=\"menu\" href=\"".$sess->adminUrl("categories.php?catid=".$subid)."\"><img src=\"images/subcat.gif\" alt=\"$a_lang[afunc_27]\" align=\"absmiddle\" height=\"16\" width=\"16\" border=\"0\" vspace=\"2\">$a_lang[afunc_27]</a>";
        }
        echo "<tr class=\"".switchBgColor()."\">\n";
        echo "<td valign=\"top\">".stripslashes($titel)." <span class=\"smalltext\">($a_lang[afunc_1]: <b>$subid</b>)</span></td>";
        echo "<td valign=\"top\"><span class=\"smalltext\">$a_lang[afunc_311]: <input class=\"inputorder\" type=\"text\"  name=\"order[$subid]\" size=\"5\" value=\"$catorder\"></span></td>";
        echo "<td align=\"left\"><a class=\"menu\" href=\"".$sess->adminUrl("categories.php?step=cat_edit&catid=".$subid)."\"><img src=\"images/edit.gif\" alt=\"$a_lang[afunc_28]\" align=\"absmiddle\" height=\"16\" width=\"16\" border=\"0\" vspace=\"2\">$a_lang[afunc_28]</a>&nbsp;&nbsp;
        <a class=\"menu\" href=\"".$sess->adminUrl("categories.php?step=cat_del&catid=".$subid)."\"><img src=\"images/delart.gif\" alt=\"$a_lang[afunc_29]\" align=\"absmiddle\" height=\"16\" width=\"16\" border=\"0\" vspace=\"2\">$a_lang[afunc_29]</a><br>
        <a class=\"menu\" href=\"".$sess->adminUrl("categories.php?step=cat_add&catid=".$subid)."\"><img src=\"images/add.gif\" alt=\"$a_lang[afunc_30]\" align=\"absmiddle\" height=\"16\" width=\"16\" border=\"0\" vspace=\"2\">$a_lang[afunc_30]</a></td>";
        echo "<td valign=\"top\">$ucat</td>";
        echo "</tr>";
    }   
    buildFormFooter($a_lang['afunc_312'], "", 4);
    if($catid != 0) {
        $subjump = $db_sql->query_array("SELECT subcat FROM $cat_table WHERE catid='$catid' LIMIT 1");
        $linktarget = ($subjump['subcat'] == 0) ? "" : "?catid=".$subjump['subcat'] ;
        //$result = $db_sql->sql_query("SELECT catid,titel,subcat,catorder FROM $cat_table WHERE subcat='$catid' ORDER BY catorder");
        buildExternalItems(array($a_lang['categories_newcat'],$a_lang['category_step_back']),array("categories.php?step=cat_add&catid=$catid","categories.php".$linktarget),array("add.gif","back.gif"));     
    } else {
        buildExternalItems($a_lang['categories_newcat'],"categories.php?step=maincat","add.gif");         
    }    
} else {
    if($step == 'cat_add' || $step == 'maincat' || $step == 'cat_edit') {    
        if($step == 'cat_add') {
            $subcat = GetCatInfo($catid);
            $subcat = stripslashes_array($subcat);
            $action = "new";
            buildHeaderRow($a_lang['afunc_31'],"newcat.gif");  
        }elseif($step == 'cat_edit') {
            $cat = GetCatInfo($catid);
            $cat = stripslashes_array($cat);
            $subcat = GetCatInfo($cat['subcat']);
            $subcat = stripslashes_array($subcat);
            $action = "edit";
            buildHeaderRow($a_lang['afunc_40']." ".stripslashes($cat['titel']),"newcat.gif");
        } else {
            $catid = 0;
            $action = "new";
            buildHeaderRow($a_lang['afunc_31'],"newcat.gif");  
        }            
        
        buildFormHeader("categories.php", "post", $action);
        buildHiddenField("subcat",$catid);
        buildTableHeader($a_lang['afunc_32']);
        buildInputRow($a_lang['afunc_33'], "cat_name", $cat['titel']);
        buildStandardRow($a_lang['afunc_42'], ($catid == 0) ? $a_lang['afunc_38'] : $subcat['titel']);
        buildTableSeparator($a_lang['afunc_34']);
        buildTextareaRow($a_lang['afunc_35'], "cat_desc", $cat['cat_desc'], "60", "20");
        $option_field5 = "<select class=\"input\" name=\"startorder\">\n";
        $option_field5 .= "<option value=\"dateA\"\n";
        $option_field5 .= (($cat['startorder'] == "dateA") ? "SELECTED" : ""); 
        $option_field5 .= ">$a_lang[afunc_303]</option>\n";
        $option_field5 .= "<option value=\"dateD\"\n";
        $option_field5 .= (($cat['startorder'] == "dateD") ? "SELECTED" : ""); 
        $option_field5 .= ">$a_lang[afunc_304]</option>\n";
        $option_field5 .= "<option value=\"hitsA\"\n";
        $option_field5 .= (($cat['startorder'] == "hitsA") ? "SELECTED" : ""); 
        $option_field5 .= ">$a_lang[afunc_305]</option>\n";
        $option_field5 .= "<option value=\"hitsD\"\n";
        $option_field5 .= (($cat['startorder'] == "hitsD") ? "SELECTED" : "");
        $option_field5 .= ">$a_lang[afunc_306]</option>\n";
        $option_field5 .= "<option value=\"votesA\"\n";
        $option_field5 .= (($cat['startorder'] == "votesA") ? "SELECTED" : ""); 
        $option_field5 .= ">$a_lang[afunc_307]</option>\n";
        $option_field5 .= "<option value=\"votesD\"\n";
        $option_field5 .= (($cat['startorder'] == "votesD") ? "SELECTED" : ""); 
        $option_field5 .= ">$a_lang[afunc_308]</option>\n";
        $option_field5 .= "<option value=\"titleA\"\n";
        $option_field5 .= (($cat['startorder'] == "titleA") ? "SELECTED" : ""); 
        $option_field5 .= ">$a_lang[afunc_309]</option>\n";
        $option_field5 .= "<option value=\"titleD\"\n"; 
        $option_field5 .= (($cat['startorder'] == "titleD") ? "SELECTED" : ""); 
        $option_field5 .= ">$a_lang[afunc_310]</option>\n";
        $option_field5 .= "</select>\n";    
        buildStandardRow($a_lang['afunc_302'], $option_field5);   
        buildInputYesNo($a_lang['afunc_313'], "direct_upload", $cat['direct_upload']);
        
        $option_field6 = "<select class=\"input\" name=\"cat_style\">\n";
        $option_field6 .= "<option value=\"1\"\n";
        $option_field6 .= (($cat['cat_style'] == "1") ? "SELECTED" : ""); 
        $option_field6 .= ">".$a_lang['list_view']."</option>\n";
        $option_field6 .= "<option value=\"0\"\n";
        $option_field6 .= (($cat['cat_style'] == "0") ? "SELECTED" : ""); 
        $option_field6 .= ">".$a_lang['detailed_view']."</option>\n";  
        $option_field6 .= "</select>\n";       
        buildStandardRow($a_lang['categorie_view'], $option_field6);   
        buildFormFooter($a_lang['afunc_36'], $a_lang['afunc_37']);
    }
    
    if($step == 'cat_del') {
        $result = $db_sql->sql_query("SELECT * FROM $cat_table WHERE catid='$catid'");
        $del = $db_sql->fetch_array($result);  
        buildHeaderRow($a_lang['afunc_202'],"delart.gif");
        buildFormHeader("categories.php","post","del"); 
        buildHiddenField("catid",$catid);
        buildTableHeader("$a_lang[afunc_202]: <u>$del[titel]");
        buildDarkColumn("$a_lang[categories_del1] (ID: $catid) $a_lang[categories_del2]<br>$a_lang[categories_del3]",1,1,2);
        buildFormFooter($a_lang['afunc_61'], "", 2, $a_lang['afunc_62']);  
    }    
}

buildAdminFooter();
?>