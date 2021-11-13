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
|   > Downloads einstellen
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: prog.php 26 2005-10-30 09:11:19Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","prog.php");

include_once('adminfunc.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
$auth->checkEnginePerm("canaccessadmincent");

include_once($_ENGINE['eng_dir']."admin/enginelib/class.bbcode.php");
$bbcode = new engineBBCode(1,0,1,0,$config['smilieurl']);		

$message = "";

function htmlLine($textarea) {
    global $a_lang;
    $htmlTable = "
    <script language=\"JavaScript\" type=\"text/javascript\">
    <!--  
    tag_prompt = \"".$a_lang['enter_text_to_format']."\";
    
    link_text_prompt = \"".$a_lang['enter_text_displayed_for_link']."\";
    link_url_prompt = \"".$a_lang['enter_full_url_link']."\";
    link_email_prompt = \"".$a_lang['enter_email_link']."\";
    
    list_type_prompt = \"".$a_lang['what_type_of_list']."\";
    list_item_prompt = \"".$a_lang['enter_list_item']."\";

		
    tags = new Array();
    function getarraysize(thearray) {
        for (i = 0; i < thearray.length; i++) {
            if ((thearray[i] == \"undefined\") || (thearray[i] == \"\") || (thearray[i] == null))
            return i;
        }
        return thearray.length;
    }
		
    function arraypush(thearray,value) {
        thearraysize = getarraysize(thearray);
        thearray[thearraysize] = value;
    }
    
    function arraypop(thearray) {
        thearraysize = getarraysize(thearray);
        retval = thearray[thearraysize - 1];
        delete thearray[thearraysize - 1];
        return retval;
    }
		
    function bbcode(theform,bbcode,prompttext) {
        inserttext = prompt(tag_prompt+\" [\"+bbcode+\"]xxx[/\"+bbcode+\"]\",prompttext);
        if ((inserttext != null) && (inserttext != \"\")) {
            theform.".$textarea.".value += \"[\"+bbcode+\"]\"+inserttext+\"[/\"+bbcode+\"] \";
            theform.".$textarea.".focus();
        }
    }		
		
    function namedlink(theform,thetype) {
        linktext = prompt(link_text_prompt,\"\");
        var prompttext;
        if (thetype == \"URL\") {
            prompt_text = link_url_prompt;
            prompt_contents = \"http://\";
        } else {
            prompt_text = link_email_prompt;
            prompt_contents = \"\";
        }
        linkurl = prompt(prompt_text,prompt_contents);
        if ((linkurl != null) && (linkurl != \"\")) {
            if ((linktext != null) && (linktext != \"\")) {
                theform.".$textarea.".value += \"[\"+thetype+\"=\"+linkurl+\"]\"+linktext+\"[/\"+thetype+\"] \";
            } else {
                theform.".$textarea.".value += \"[\"+thetype+\"]\"+linkurl+\"[/\"+thetype+\"] \";
            }
        }
        theform.".$textarea.".focus();
    }		
	
	function setsmile(Zeichen) {
		document.alp.dldesc.value = document.alp.dldesc.value + Zeichen;
	}
    
    //-->
    </script>
    <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
    <tr>
        <td>
            <input class=\"button\" type=\"button\" name=\"button\" value=\" B \" onclick=\"bbcode(this.form,'B','')\">
            <input class=\"button\" type=\"button\" name=\"button\" value=\" I \" onclick=\"bbcode(this.form,'I','')\">
            <input class=\"button\" type=\"button\" name=\"button\" value=\" U \" onclick=\"bbcode(this.form,'U','')\">
            <input class=\"button\" type=\"button\" name=\"button\" value=\" IMG \" onclick=\"bbcode(this.form,'IMG','http://')\">
            <input class=\"button\" type=\"button\" name=\"button\" value=\" http:// \" onclick=\"namedlink(this.form,'URL')\">
            <input class=\"button\" type=\"button\" name=\"button\" value=\" Quote \" onclick=\"bbcode(this.form,'QUOTE','')\">
        </td>
    </tr>
    </table>";
    return $htmlTable;
}

function smilieTable() {
    global $config;
    $smilie = "<p><b>Click-Smilies:</b><br>";
    $smilie .= "<a href=\"Javascript:setsmile(':-)')\"><img src=\"$config[smilieurl]/smile.gif\" border=0></a>&nbsp;";
    $smilie .= "<a href=\"Javascript:setsmile(';-)')\"><img src=\"$config[smilieurl]/wink.gif\" border=0></a>&nbsp;";
    $smilie .= "<a href=\"Javascript:setsmile(':O')\"><img src=\"$config[smilieurl]/wow.gif\" border=0></a><br>";
    $smilie .= "<a href=\"Javascript:setsmile(';-(')\"><img src=\"$config[smilieurl]/sly.gif\" border=0></a>&nbsp;";
    $smilie .= "<a href=\"Javascript:setsmile(':D')\"><img src=\"$config[smilieurl]/biggrin.gif\" border=0></a>&nbsp;";
    $smilie .= "<a href=\"Javascript:setsmile('8-)')\"><img src=\"$config[smilieurl]/music.gif\" border=0></a><br>";
    $smilie .= "<a href=\"Javascript:setsmile(':-O')\"><img src=\"$config[smilieurl]/cry.gif\" border=0></a>&nbsp;";
    $smilie .= "<a href=\"Javascript:setsmile(':-(')\"><img src=\"$config[smilieurl]/confused.gif\" border=0></a>&nbsp;";
    $smilie .= "<a href=\"Javascript:setsmile('(?)')\"><img src=\"$config[smilieurl]/sneaky2.gif\" border=0></a><br>";
    $smilie .= "<a href=\"Javascript:setsmile('(!)')\"><img src=\"$config[smilieurl]/notify.gif\" border=0></a>&nbsp;";
    $smilie .= "<a href=\"Javascript:setsmile(':!')\"><img src=\"$config[smilieurl]/thumbs-up.gif\" border=0></a>&nbsp;";
    $smilie .= "<a href=\"Javascript:setsmile(':zzz:')\"><img src=\"$config[smilieurl]/sleepy.gif\" border=0></a>&nbsp;";
    $smilie .= "</p>";
    return $smilie;
}
		
    
if($action == 'add_mirror') {
	$db_sql->sql_query("INSERT INTO $mirror_table (dlid,mirror_url,mirror_text,mirror_date) VALUES ('".$dlid."','".reBuildURL(addslashes(htmlspecialchars($mirror_url)))."','".addslashes(htmlspecialchars($mirror_text))."','".time()."')");
	$message .= $a_lang['mirror_9'];
	$step = "mirror";
}

if($action == 'conf_edit_mirror') {
	$db_sql->sql_query("UPDATE $mirror_table SET mirror_url='".reBuildURL(addslashes(htmlspecialchars($mirror_url)))."', mirror_text='".addslashes(htmlspecialchars($mirror_text))."' WHERE mirror_id='".$mirrorid."'");
	$message .= $a_lang['mirror_10'];
	$step = "mirror";
}

if($action == 'conf_del_mirror') {
	$db_sql->sql_query("DELETE FROM $mirror_table WHERE mirror_id='".$mirrorid."'");
	$message .= $a_lang['mirror_11'];
	$step = "mirror";
}

if ($action == 'dead') {
    $db_sql->sql_query("UPDATE $dl_table SET status=1 WHERE dlid='$dlid'");
    $message .= $a_lang['prog_mes5'];    
}

if ($action == 'update') {
    $db_sql->sql_query("UPDATE $dl_table SET status='1' WHERE dlid='$dlid'");
    $db_sql->sql_query("UPDATE $cat_table SET download_count=download_count+1 WHERE catid='$catid'");
    $message .= $a_lang['prog_mes1'];
}

if ($action == 'conf_multi') {
    if($_POST['public']) {
    	foreach($dlid as $key=>$wert) {
            $db_sql->sql_query("UPDATE $dl_table SET status='1' WHERE dlid='$key'");
            $db_sql->sql_query("UPDATE $cat_table SET download_count=download_count+1 WHERE catid='$key'");
    	}	
        $message .= $a_lang['prog_mes1'];       
    } elseif($_POST['delete']) {
    	foreach($dlid as $key=>$wert) {
            $db_sql->sql_query("DELETE FROM $dl_table WHERE dlid='$key'");
    	}	
        $message .= $a_lang['prog_mes3'];      
    }elseif($_POST['dead']) {
    	foreach($dlid as $key=>$wert) {
            $db_sql->sql_query("UPDATE $dl_table SET status=1 WHERE dlid='$key'");
    	}	
        $message = $a_lang['prog_mes5'];         
    }
}   

if ($action == 'edit') {
    if ($thumb == "") $thumb = 0;
    
    $result = $db_sql->sql_query("SELECT catid FROM $dl_table WHERE dlid='$dlid'");
    list($oldcat) = mysql_fetch_row($result);
    $db_sql->sql_query("UPDATE $cat_table SET download_count=download_count-1 WHERE catid='$oldcat'");
    $db_sql->sql_query("UPDATE $cat_table SET download_count=download_count+1 WHERE catid='$catid'");
    
	if($new_date) $add_update = "dl_date='".time()."',";
	if($mark_update) {
		$update_time = time();
	} else {
		$update_time = 0;
	}
    $db_sql->sql_query("UPDATE $dl_table SET
                catid='$catid',
                dltitle='".addslashes(htmlspecialchars($dltitle))."',
                dldesc='".addslashes($dldesc)."',
                status='$status',
                dlurl='".addslashes(htmlspecialchars($dlurl))."',
				$add_update
                dlhits='$dlhits',
                dlvotes='$dlvotes',
                hplink='".addslashes(htmlspecialchars($hplink))."',
                dlsize='$dlsize',
                dlpoints='$dlpoints',
                dlauthor='".addslashes(htmlspecialchars($dlauthor))."',
                authormail='".addslashes(htmlspecialchars($authormail))."',
                thumb='".addslashes(htmlspecialchars($thumb))."',
                onlyreg='$onlyreg',
				licence_id='".$licence_id."',
				update_date='".$update_time."'
                WHERE dlid='$dlid'");
    $message .= $a_lang['prog_mes2'];
    $step = 'edit';
}
   
if ($action == 'copy') {
    if($copy_active) {
        $dlid = copyFile($dlid,$catid,$edit_file,$comment_file,$date_file);
        $message .= $a_lang['prog_mes6'];
    } else {
        $message .= $a_lang['prog_mes7'];
    }
    $step = 'edit';
}   
   
if ($action == 'del') {
    if($delete == 1) {
        $result = $db_sql->sql_query("SELECT dlurl FROM $dl_table WHERE dlid='$dlid'");
        $file = $db_sql->fetch_array($result);
        $file_name = strrchr($file['dlurl'],"/");
        $file_path = "./../files/";
        $file = $file_path.$file_name;
        @unlink($file);
    }
		 
    $db_sql->sql_query("UPDATE $cat_table SET download_count=download_count-1 WHERE catid='$catid'");
    $db_sql->sql_query("DELETE FROM $dlcomment_table WHERE dlid='$dlid'");
    $db_sql->sql_query("DELETE FROM $dl_table WHERE dlid='$dlid'");
    /*$db_sql->sql_query("DELETE FROM $stats_day_table WHERE dl_id='$dlid'");
    $db_sql->sql_query("DELETE FROM $stats_month_table WHERE dl_id='$dlid'");*/   
    $message .= $a_lang['prog_mes3'];
    $step = "1";
    unset($catid);
}
   
if ($action == 'add') {
    $dl_date = time();
    $db_sql->sql_query("UPDATE $cat_table SET download_count=download_count+1 WHERE catid='$catid'");
    $db_sql->sql_query("INSERT INTO $dl_table (catid,dltitle,dldesc,status,dlurl,dl_date,hplink,dlsize,dlauthor,authormail,thumb,onlyreg)
                        VALUES ('$catid','".addslashes($dltitle)."','".addslashes($dldesc)."','$status','".addslashes($dlurl)."','$dl_date','".addslashes($hplink)."','$dlsize','".addslashes($dlauthor)."','".addslashes($authormail)."','".addslashes($thumb)."','$onlyreg')");
    $message .= $a_lang['prog_mes4'];
    $step = "1";
    unset($catid);
}   

if ($action == 'new_licence') {
	$db_sql->sql_query("INSERT INTO $licence_table (licence_title,licence,licence_date) VALUES ('".addslashes($licence_title)."','".addslashes($licence)."','".time()."')");
	$step = "licence";
	$message .= $a_lang['licence_saved'];
}

if ($action == 'update_licence') {
	$db_sql->sql_query("UPDATE $licence_table SET licence_title='".addslashes($licence_title)."',licence='".addslashes($licence)."' WHERE licence_id='".$licence_id."'");
	$step = "licence";
	$message .= $a_lang['licence_saved'];
}

if($action == 'conf_del_licence') {
	$db_sql->sql_query("DELETE FROM $licence_table WHERE licence_id='".$licence_id."'");
	$db_sql->sql_query("UPDATE $dl_table SET licence_id='0' WHERE licence_id='".$licence_id."'");
	$step = "licence";
	$message .= $a_lang['licence_deleted'];	
}

if($action == 'save_dir_files') {
	foreach($dl_name as $key=>$val) {
		foreach($val as $val2) {
			if($val2 != "") {
                $size = @filesize($_ENGINE['eng_dir']."/files/".$key);
                $db_sql->sql_query("INSERT INTO $dl_table (catid,dltitle,status,dlurl,dl_date,dlsize)
                                    VALUES ('".$val2."','".addslashes(htmlspecialchars($key))."','".intval($status)."','".$config['fileurl']."/".addslashes(htmlspecialchars($key))."','".time()."','".intval($size)."')");
                //echo $val2." ".$key." ".$size."<br>";
                if($db_sql->insert_id()) {
                    $new_file_id = $db_sql->insert_id();                
                    $db_sql->sql_query("UPDATE $cat_table SET download_count=download_count+1 WHERE catid='".$val2."'");
                    $message .= sprintf($a_lang['prog_ok'],$key,$new_file_id)."  <a href=\"".$sess->adminUrl("prog.php?step=edit&dlid=".$new_file_id)."\"><img src=\"images/edit.gif\" alt=\"".sprintf($a_lang['edit_savedir_file'], $key)."\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\"></a><br>";  
                } else {
                    $message .= sprintf($a_lang['prog_failed'],$key);
                }                                                  
            }                
		}
	}
	$step = "read_dir";
}

$sbb_style = "<style>\n.dl_textarea {\nwidth: 100%;\n}</style>\n";

if(!$step || $step == 'cat') $hide = 1;

if($step == 'choose' && !$search_col && !$search_word) {
	$hide = 1;
	$br_tag = "<br />";
}
   
buildAdminHeader($sbb_style,$hide);

if ($message != "" && $action != "dead" && $action != "conf_multi") {
    buildMessageRow($message);
} elseif($message != "" && ($action == "dead" || $action == "conf_multi")) {
    buildMessageRow($message, array('auto_redirect' => 'main.php', 'is_top' => 1, 'next_script' => 'main.php', 'next_action' => array('','',$a_lang['afunc_proceed'])));
    buildAdminFooter();
    exit;
}  
   
if(!isset ($catid)) $catid = 0;

if(!isset ($step)) {
    //FileCatSearch($catid);
} else {
	if($step == 'licence') {
		buildHeaderRow($a_lang['licence_overview'],"licence.gif");
		buildTableHeader($a_lang['following_licences_found']);
		$result = $db_sql->sql_query("SELECT * FROM $licence_table ORDER BY licence_date");
		if($db_sql->num_rows($result) >= 1) { 
			while($licence = $db_sql->fetch_array($result)) {
				$licence = stripslashes_array($licence);
				buildStandardRow($licence['licence_title']." (ID <b>".$licence['licence_id']."</b>)", "<a class=\"menu\" href=\"".$sess->adminUrl("prog.php?step=licence_edit&licence_id=".$licence['licence_id'])."\"><img src=\"images/edit.gif\" alt=\"".$a_lang['edit_licence']."\" align=\"absmiddle\" height=\"16\" width=\"16\" border=\"0\">".$a_lang['edit_licence']."</a>&nbsp;&nbsp;<a class=\"menu\" href=\"".$sess->adminUrl("prog.php?step=licence_del&licence_id=".$licence['licence_id'])."\"><img src=\"images/delart.gif\" alt=\"".$a_lang['delete_licence']."\" align=\"absmiddle\" height=\"16\" width=\"16\" border=\"0\">".$a_lang['delete_licence']."</a>");
			}
		} else {
			buildDarkColumn($a_lang['no_licence_found'],1,1,2);
		}
		
		buildTableFooter();
		buildExternalItems($a_lang['add_licence'],"prog.php?step=licence_add","add.gif");
	}
	
	if($step == 'licence_add' || $step == 'licence_edit') {
		buildHeaderRow($a_lang['edit_licence'],"edit.gif");
		if($step == 'licence_add') {
			$action = 'new_licence';
		} else {
			$action = 'update_licence';
			$licence = $db_sql->query_array("SELECT * FROM $licence_table WHERE licence_id='".$licence_id."'");
		}
		include_once('includes/spaw/spaw_control.class.php');
		$sw = new SPAW_Wysiwyg('licence',$licence['licence'],''.$lang['php_mailer_lang'].'', 'mini','','400px','200px',"../templates/".$config['template_folder'].'/style.css');
		
		buildFormHeader("prog.php", "post", $action);
		if($step == 'licence_edit') buildHiddenField("licence_id",$licence['licence_id']);
		buildTableHeader($a_lang['licence_details']);
		buildStandardRow($a_lang['licence_id']." ".$licence['licence_id'],$a_lang['licence_added']." ".aseDate($config['longdate'],($licence['licence_date']) ? $licence['licence_date'] : time(),1));
		buildInputRow($a_lang['licence_headline'], "licence_title", $licence['licence_title']);
		buildStandardRow($a_lang['licence'], $sw->show());
		buildFormFooter($a_lang['save_licence'], "", 2, $a_lang['licence_back']);
	}
	
    if($step == 'licence_del') {
        $result = $db_sql->sql_query("SELECT * FROM $licence_table WHERE licence_id='$licence_id'");
        $del = $db_sql->fetch_array($result);
        
        buildHeaderRow($a_lang['afunc_202'],"delart.gif");
        buildFormHeader("prog.php","post","conf_del_licence"); 
        buildHiddenField("licence_id",$licence_id);
        buildTableHeader("$a_lang[afunc_202]: <u>$del[licence_title]</u>");
        buildDarkColumn(sprintf($a_lang['do_you_really_want_to_delete_licence'],$licence_id),1,1,2);
        buildFormFooter($a_lang['afunc_61'], "", 2, $a_lang['afunc_62']);    
    } 		
  
    if($step == 'cat') {
        FileCatSearch($catid);
    }  
  
    if($step == "down") {
        buildHeaderRow($a_lang['prog_search_f'],"search.gif");
        buildFormHeader("prog.php", "post", "player_list");
        buildHiddenField("step","choose");
        buildHiddenField("search_query","1");
        buildTableHeader($a_lang['prog_insertname']);
        $tmp_value = "<select name=\"search_col\">\n";
        $tmp_value .= "<option value=\"dltitle\">$a_lang[search_statement1]</option>\n";
        $tmp_value .= "<option value=\"dlid\">$a_lang[search_statement2]</option>\n";
        $tmp_value .= "</select>\n";
        
        buildStandardRow($a_lang['search_define'], $tmp_value);
        buildInputRow($a_lang['search_note1'], "search_word");
        buildFormFooter($a_lang['search_button1'], $a_lang['adminutil_19']);
    }      

    if($step == 'move') {    
        $dl = $db_sql->query_array("SELECT * FROM $dl_table WHERE dlid=$dlid");
        $dl = stripslashes_array($dl); 
        $cat_link = makeACatLink($dl['catid'],0);   
    
        buildHeaderRow($a_lang['afunc_314']." ".$dl['dltitle'],"copy.gif");
		buildExternalItems(array($a_lang['file_edit_long'],$a_lang['mirror_2']),array("prog.php?step=edit&dlid=".$dlid,"frameset.php?step=catframe&initialize_category=".$dl['catid']),array("edit.gif","back.gif"));		
		        
        buildFormHeader("prog.php", "post", "copy");
        buildHiddenField("dlid",$dl['dlid']);
        buildTableHeader($a_lang['afunc_315']);        
        buildStandardRow($a_lang['afunc_316'], "<input type=\"checkbox\" name=\"copy_active\"> <span class=\"smalltext\">$a_lang[afunc_317]</span>");
        $comment = "<select class=\"input\" name=\"comment_file\">";
        $comment .= "<option value=\"1\" checked>$a_lang[afunc_328]</option>";
        $comment .= "<option value=\"0\">$a_lang[afunc_329]</option>";
        $comment .= "</select>";
        buildStandardRow($a_lang['afunc_326'], $comment);        
        $date_file = "<select class=\"input\" name=\"date_file\">";
        $date_file .= "<option value=\"1\" checked>$a_lang[afunc_328]</option>";
        $date_file .= "<option value=\"0\">$a_lang[afunc_329]</option>";
        $date_file .= "</select>";
        buildStandardRow($a_lang['afunc_327'], $date_file);        
        buildStandardRow($a_lang['afunc_330'], "<select class=\"input\" name=\"catid\">\n".$cat_link."\n</select>");        
        $edit_file = "<select class=\"input\" name=\"edit_file\">";
        $edit_file .= "<option value=\"1\" checked>$a_lang[afunc_324]</option>";
        $edit_file .= "<option value=\"0\">$a_lang[afunc_325]</option>";
        $edit_file .= "</select>";
        buildStandardRow($a_lang['afunc_323'], $edit_file);
        buildFormFooter($a_lang['afunc_318'], "");
    }
  
    if($step == 'choose') {
        if($search_col && $search_word) {
            if($search_col == "dlid") {
                $sql = "WHERE dlid = '".$search_word."'";
            } else {
                $sql = "WHERE dltitle LIKE '%$search_word%'";
            }        
            $query = "SELECT Count(*) as total FROM $dl_table ".$sql."";
            $query_long = "SELECT * FROM $dl_table ".$sql." ORDER BY dlid DESC";
            $url_neu = $sess->adminUrl("prog.php?step=choose&search_word=".$search_word."&search_col=".$search_col."&dltitle=$dltitle&catid=".$catid)."&";
            $target = "";
        } else {        
            $result = mysql_query("SELECT titel FROM $cat_table WHERE catid='$catid'");
            list($cattitel) = mysql_fetch_row($result);        
        
            $query = "SELECT Count(*) as total FROM $dl_table WHERE catid='$catid'";
            $query_long = "SELECT * FROM $dl_table WHERE catid='".$catid."' ORDER BY dlid DESC";
            $url_neu = $sess->adminUrl("prog.php?step=choose&catid=".$catid)."&";
            $target = " target=\"_parent\"";
        }
        $result = $db_sql->sql_query("".$query."");
        $over_all = $db_sql->fetch_array($result);   
        
        if(!class_exists(Nav_Link)) include_once($_ENGINE['eng_dir']."admin/enginelib/class.nav.php");
        if(!isset($start)) $start = 0;
        $nav = new Nav_Link();
        $nav->overAll = $over_all['total'];
        $nav->DisplayLast = 1;
        $nav->DisplayFirst = 1;
        $nav->perPage = 10;
        $nav->MyLink = $url_neu;
        $nav->LinkClass = "smalltext";
        $nav->start = $start;
        $pagecount = $nav->BuildLinks();
		$pages = intval($over_all['total'] / 10);
		if($over_all['total'] % 10) $pages++;				
        
        if(!$pagecount) $pagecount = "<b>1</b>";             
        echo $br_tag;
        buildHeaderRow("$a_lang[afunc_115] <b>".stripslashes($cattitel)."</b>:<br><span class=\"smalltext\">&nbsp;$a_lang[afunc_203] ($pages): $pagecount</span>","newdet.gif", $use_info);        
        if(!$search_query) buildExternalItems($a_lang['new_file'],"prog.php?step=add&catid=$catid","add.gif",1);         
				 
        $result2 = $db_sql->sql_query("".$query_long." LIMIT $start,10");
		if($db_sql->num_rows($result2) >= 1) {
	        while($dl = $db_sql->fetch_array($result2)) {
	            unset($points);
	            unset($status);
	            unset($stpic);
	            unset($pic);
	            unset($dldesc);
				 
	            if ($dl['dlpoints'] != 0) {
	                $dl_divp = $dl['dlpoints'] / $dl['dlvotes'];
	                $points = round($dl_divp,2);
	            } else {
	                $points = "0";
	            }
				 
	            $result3 = $db_sql->sql_query("SELECT comid from $dlcomment_table where dlid='$dl[dlid]' and com_status='1'");
	            $kom_i = mysql_num_rows($result3);
				   
	            if ($dl['status'] == 1) {
	                $status = "$a_lang[afunc_116]<br>";
	                $stpic = "<img align=\"absmiddle\" class=\"nav\" src=\"images/flaggreen.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"$a_lang[afunc_117]\">";
	            } elseif ($dl['status'] == 2) {
	                $status = "<b>$a_lang[afunc_118]</b><br>";
	                $stpic = "<img align=\"absmiddle\" class=\"nav\" src=\"images/caution.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"$a_lang[afunc_119]\">";
	            } else {
	                $status = "<b>$a_lang[afunc_120]</b><br>";
	                $stpic = "<img align=\"absmiddle\" class=\"nav\" src=\"images/flagred.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"$a_lang[afunc_121]\">";
	            }
				 
	            if ($dl['thumb'] == "" || $dl['thumb'] == "0") {
	                $pic = $a_lang['afunc_122'];
	            } else {
	                $pic = "<b>".$a_lang['afunc_123']."</b>";
	            }
				 
	            $date = getdate($dl['dl_date']);
				 
	            if(strlen($dl['dldesc'])>40) $dl['dldesc'] = substr($dl['dldesc'],0,80)."...";             
	            $dldesc = $bbcode->rebuildText($dl['dldesc']);
	            $dldesc = trim($dldesc);	
	            
	            $row_class = switchBgColor();            
	            
	            buildTableHeader(stripslashes($dl['dltitle']), 4);
	            echo "<tr class=\"".$row_class."\">\n";
	            echo "<td width=\"8%\" rowspan=\"3\"><p>ID: <b>$dl[dlid]</b></p></td>\n";
	            echo "<td width=\"35%\"><p>$stpic <b>$status</b></p></td>\n";
	            echo "<td width=\"22%\">$a_lang[afunc_124] <b>".stripslashes($dl['dlauthor'])."</b></td>\n";
	            echo "<td width=\"35%\"><p>$a_lang[afunc_125] $date[mday].$date[mon].$date[year]</p></td>\n";
	            echo "</tr>\n";   
	            echo "<tr class=\"".$row_class."\">\n";
	            echo "<td colspan=\"4\"><p><b>$a_lang[afunc_10]:</b><br>".$dldesc."</p></td>\n";
	            echo "</tr>\n"; 
	            echo "<tr class=\"".$row_class."\">\n";
	            echo "<td><p>$a_lang[afunc_126] <b>".$dl['dlhits']."</b> $a_lang[afunc_127] <b>".$points."</b> $a_lang[afunc_128] <b>".$dl['dlvotes']."</b></p></td>\n";
	            echo "<td><p><b>$kom_i</b> $a_lang[afunc_129]<br>$pic</p></td>\n";
	            echo "<td align=\"left\" nowrap><p>
	            <a class=\"menu\" ".$target." href=\"".$sess->adminUrl("prog.php?step=edit&dlid=".$dl['dlid'])."\"><img src=\"images/edit.gif\" alt=\"$a_lang[afunc_130]\" align=\"absmiddle\" height=\"16\" width=\"16\" border=\"0\" vspace=\"2\">$a_lang[afunc_130]</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	            <a class=\"menu\" ".$target." href=\"".$sess->adminUrl("prog.php?step=del&dlid=".$dl['dlid']."&catid=".$dl['catid'])."\"><img src=\"images/delart.gif\" alt=\"$a_lang[afunc_131]\" align=\"absmiddle\" height=\"16\" width=\"16\" border=\"0\" vspace=\"2\">$a_lang[afunc_131]</a><br>
	            <a class=\"menu\" ".$target." href=\"".$sess->adminUrl("prog.php?step=move&dlid=".$dl['dlid'])."\"><img src=\"images/copy.gif\" alt=\"$a_lang[move_file]\" align=\"absmiddle\" height=\"16\" width=\"16\" border=\"0\" vspace=\"2\">$a_lang[move_file]</a>&nbsp;
	            <a class=\"menu\" ".$target." href=\"".$sess->adminUrl("prog.php?step=mirror&dlid=".$dl['dlid']."&catid=".$dl['catid'])."\"><img src=\"images/mirror.gif\" alt=\"$a_lang[afunc_131]\" align=\"absmiddle\" height=\"16\" width=\"16\" border=\"0\" vspace=\"2\">Mirror</a></p></td>\n";
	            echo "</tr>\n";                        			 
	            buildTableFooter();            
	        } 
		} elseif(!$search_col && !$search_word) {
			echo "<table bgcolor=\"#000000\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n<tr>\n<td>\n";
			echo "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
			buildDarkColumn($a_lang['categorie_contains_no_files'],1,1,1);
			echo "</table>\n";
			echo "</td>\n</tr>\n";
			echo "</table><br />\n";			
		}       
		
        if(!$over_all['total'] && $search_col && $search_word) {
            buildTableHeader($a_lang['prog_no_result'], 1);
            buildDarkColumn($a_lang['prog_no_result1']." '<b>".$search_word."</b>' ".$a_lang['prog_no_result2'],1,1,2);    
            buildTableFooter("",1);
        }
        if(!$search_query) buildExternalItems($a_lang['new_file'],"prog.php?step=add&catid=$catid","add.gif",1);         
    }
  
    if($step == 'edit' || $step == 'add') {
        if($dlid) {
            $dl = $db_sql->query_array("SELECT * FROM $dl_table WHERE dlid=$dlid");
            $dl = stripslashes_array($dl);
            $cat_link = makeACatLink($dl['catid'],0);         
            buildHeaderRow($a_lang['afunc_132']." <b>".$dl['dltitle']."</b>","edit.gif");
            $post_action = "edit";
        } else {
            $cat_link = makeACatLink($catid,0);           
            buildHeaderRow($a_lang['afunc_155'],"edit.gif");
            $post_action = "add";
        }
		
		if($step == 'edit') buildExternalItems(array($a_lang['move_file_long'],$a_lang['delete_file_long'],$a_lang['mirror_1'],$a_lang['mirror_2']),array("prog.php?step=move&dlid=".$dlid,"prog.php?step=del&dlid=".$dlid,"prog.php?step=mirror&dlid=".$dlid."&catid=".$dl['catid'],"frameset.php?step=catframe&initialize_category=".$dl['catid']),array("move.gif","delart.gif","mirror.gif","back.gif"));  
        buildFormHeader("prog.php", "post", $post_action);
        if($dlid) buildHiddenField("dlid",$dlid);
        buildTableHeader($a_lang['afunc_133']);
        buildStandardRow("<b>File-ID</b>", $dl['dlid']);
        buildInputRow($a_lang['afunc_134'], "dltitle", $dl['dltitle']);
        buildStandardRow($a_lang['afunc_135'], "<select class=\"input\" name=\"catid\">\n".$cat_link."\n</select>");                
        buildStandardRow($a_lang['afunc_136'].smilieTable(), htmlLine("dldesc")."<textarea class=\"dl_textarea\" name=\"dldesc\" rows=\"10\" cols=\"50\" wrap=\"soft\">".$dl['dldesc']."</textarea>");
        buildTableSeparator($a_lang['afunc_137']);
        buildUploadInput($a_lang['afunc_138'], "dlurl", $dl['dlurl'], "40",0,"Uploadfile()");
        buildUploadInput($a_lang['afunc_139'], "thumb", $dl['thumb'], "40",0,"Uploadimage()");
        buildInputRow($a_lang['afunc_140'], "dlsize", $dl['dlsize']);
        
        $status_option = "<select class=\"input\" name=\"status\">";
        $status_option .= "<option value=\"1\" ";        
        if ($dl['status'] == 1) $status_option .= "selected";
        $status_option .= ">$a_lang[afunc_143]</option>";        
        $status_option .= "<option value=\"2\" ";
        if ($dl['status'] == 2) $status_option .= "selected";
        $status_option .= ">$a_lang[afunc_142]</option>";
        $status_option .= "<option value=\"3\" ";
        if ($dl['status'] == 3) $status_option .= "selected";        
        $status_option .= ">$a_lang[afunc_144]</option>"; 
        $status_option .= "</select>";               
        buildStandardRow($a_lang['afunc_141'], $status_option);
        
        $reg_option = "<select class=\"input\" name=\"onlyreg\">";
        $reg_option .= "<option value=\"0\" ";
        if ($dl['onlyreg'] == 0) $reg_option .= "selected"; 
        $reg_option .= ">$a_lang[afunc_146]</option>";
        $reg_option .= "<option value=\"1\" ";
        if ($dl['onlyreg'] == 1) $reg_option .= "selected"; 
        $reg_option .= ">$a_lang[afunc_147]</option>";
        $reg_option .= "</select>";
        buildStandardRow($a_lang['afunc_145'], $reg_option);
		if($dlid) buildRadioRow($a_lang['update_date'], "new_date", 0);
		buildTableSeparator($a_lang['licence_blank']);
		$licence_option = "<select class=\"input\" name=\"licence_id\">";
        $licence_option .= "<option value=\"0\" ";
        if ($dl['licence_id'] == 0) $licence_option .= "selected"; 
        $licence_option .= ">".$a_lang['no_licence_defined']."</option>";
		$result = $db_sql->sql_query("SELECT * FROM $licence_table");
		while($licence = $db_sql->fetch_array($result)) {
			$licence_option .= "<option value=\"".$licence['licence_id']."\"";
			if ($dl['licence_id'] == $licence['licence_id']) $licence_option .= "selected"; 
			$licence_option .= ">".$licence['licence_title']."</option>";
		}
		$licence_option .= "</select>";
		buildStandardRow($a_lang['licence_to_file'], $licence_option);
        buildTableSeparator($a_lang['afunc_148']);
        buildInputRow($a_lang['afunc_149'], "dlhits", $dl['dlhits']);
        buildInputRow($a_lang['afunc_150'], "dlvotes", $dl['dlvotes']);
        buildInputRow($a_lang['afunc_151'], "dlpoints", $dl['dlpoints']);
        buildTableSeparator($a_lang['afunc_152']);
		echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$a_lang['afunc_153']."</p></td>\n";
		echo "<td nowrap><p><input type=\"text\" size=\"40\" name=\"dlauthor\" value=\"".$dl['dlauthor']."\">&nbsp;<a href=\"Javascript: LoadUser();\"><img src=\"images/user.gif\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">".$a_lang['choose_user']."</a></p></td>\n</tr>\n";
		
        //buildInputRow($a_lang['afunc_153'], "dlauthor", $dl['dlauthor']);
        buildInputRow($a_lang['afunc_214'], "authormail", $dl['authormail']);
        buildInputRow($a_lang['afunc_154'], "hplink", $dl['hplink']);
		if($step == 'edit') {
			buildTableSeparator($a_lang['update_mark']);
			buildRadioRow($a_lang['show_update_mark'], "mark_update", 1);
		}
        buildFormFooter($a_lang['afunc_57'], "");
    }
  
    if($step == 'del') {
        $result = $db_sql->sql_query("SELECT * FROM $dl_table WHERE dlid='$dlid'");
        $del = $db_sql->fetch_array($result);
        
        buildHeaderRow($a_lang['afunc_202'],"delart.gif");
        buildFormHeader("prog.php","post","del"); 
        buildHiddenField("dlid",$dlid);
        buildHiddenField("catid",$catid);
        buildTableHeader("$a_lang[afunc_202]: <u>$del[dltitle]</u>");
        buildDarkColumn("$a_lang[prog_del1] (ID: $dlid) $a_lang[prog_del2]<br><span class=\"smalltext\">$a_lang[prog_del3]</span>",1,1,2);
        buildRadioRow($a_lang['prog_file_del'], "del_var");
        buildFormFooter($a_lang['afunc_61'], "", 2, $a_lang['afunc_62']);    
    } 
	
	if($step == 'mirror') {
		$dl = $db_sql->query_array("SELECT dltitle FROM $dl_table WHERE dlid='".$dlid."'");		
		buildHeaderRow($a_lang['mirror_1'],"mirror.gif");
		buildTableHeader(sprintf($a_lang['mirror_3'], $dl['dltitle']));
		$result = $db_sql->sql_query("SELECT * FROM $mirror_table WHERE dlid='".$dlid." ORDER BY mirror_date'");
		if($db_sql->num_rows($result) >= 1) { 
			while($mirror = $db_sql->fetch_array($result)) {
				$mirror = stripslashes_array($mirror);
				buildStandardRow($mirror['mirror_text']." (ID: <b>$mirror[mirror_id]</b>)", "<a class=\"menu\" href=\"".$sess->adminUrl("prog.php?step=edit_mirror&dlid=".$dlid."&catid=".$catid."&mirrorid=".$mirror['mirror_id'])."\"><img src=\"images/edit.gif\" alt=\"$a_lang[afunc_130]\" align=\"absmiddle\" height=\"16\" width=\"16\" border=\"0\" vspace=\"2\">$a_lang[afunc_130]</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	            <a class=\"menu\" href=\"".$sess->adminUrl("prog.php?step=del_mirror&dlid=".$dlid."&catid=".$catid."&mirrorid=".$mirror['mirror_id'])."\"><img src=\"images/delart.gif\" alt=\"$a_lang[afunc_131]\" align=\"absmiddle\" height=\"16\" width=\"16\" border=\"0\" vspace=\"2\">$a_lang[afunc_131]</a>");
			}
		} else {
			buildDarkColumn($a_lang['mirror_12'],1,1,2);
		}
		
		buildTableFooter();
		buildExternalItems(array($a_lang['file_edit_long'],$a_lang['mirror_2']),array("prog.php?step=edit&dlid=".$dlid,"frameset.php?step=catframe&initialize_category=".$catid),array("edit.gif","back.gif"));
		buildFormHeader("prog.php", "post", "add_mirror");
		buildHiddenField("catid",$catid);
		buildHiddenField("dlid",$dlid);
		buildTableHeader($a_lang['add_mirror']);
		buildInputRow($a_lang['mirror_6'], "mirror_text");
		buildInputRow($a_lang['mirror_7'], "mirror_url");
		buildFormFooter($a_lang['mirror_8'], "");		
	}
	
    if($step == 'del_mirror') {
        $result = $db_sql->sql_query("SELECT * FROM $mirror_table WHERE mirror_id='$mirrorid'");
        $del = $db_sql->fetch_array($result);
        
        buildHeaderRow($a_lang['afunc_202'],"delart.gif");
        buildFormHeader("prog.php","post","conf_del_mirror"); 
        buildHiddenField("dlid",$dlid);
        buildHiddenField("catid",$catid);
		buildHiddenField("mirrorid",$mirrorid);
        buildTableHeader("$a_lang[afunc_202]: <u>$del[mirror_text]</u>");
        buildDarkColumn("$a_lang[mirror_4] (ID: $dlid) $a_lang[prog_del2]<br><span class=\"smalltext\">$a_lang[prog_del3]</span>",1,1,2);
        buildFormFooter($a_lang['afunc_61'], "", 2, $a_lang['afunc_62']);    
    } 	
	
	if($step == 'edit_mirror') {
		buildHeaderRow($a_lang['mirror_5'],"mirror.gif");
        $result = $db_sql->sql_query("SELECT * FROM $mirror_table WHERE mirror_id='$mirrorid'");
        $mirror = $db_sql->fetch_array($result);		
		buildFormHeader("prog.php", "post", "conf_edit_mirror");
		buildHiddenField("catid",$catid);
		buildHiddenField("dlid",$dlid);
		buildHiddenField("mirrorid",$mirrorid);
		buildTableHeader($a_lang['mirror_5']);
		buildInputRow($a_lang['mirror_6'], "mirror_text",$mirror['mirror_text']);
		buildInputRow($a_lang['mirror_7'], "mirror_url",$mirror['mirror_url']);
		buildFormFooter($a_lang['mirror_8'], "", 2, $a_lang['afunc_336']);	
	}
    
	if($step == 'read_dir') {
		$dir = $_ENGINE['eng_dir']."files";
		$config_pre = "prev_";
        $array = array();
		
		$result = $db_sql->sql_query("SELECT dlid, dltitle, dlurl FROM $dl_table ORDER BY dltitle");
        while($dl_cache = $db_sql->fetch_array($result)) {		
            $dl_cache['file_name'] = substr(strrchr($dl_cache['dlurl'],"/"),1);
			$cache[$dl_cache['file_name']] = $dl_cache;
            //echo $dl_cache['file_name']."<br>";
		}
        
		
		$handle=opendir($dir); 
		while ($file = readdir ($handle)) { 
			if ($file != "." && $file != ".." && !is_dir($dir."/".$file)) {
				$array[] = $file; 
			}
		}		
		closedir($handle); 
		sort($array);
		$option_field2 .= "<option value=\"\" selected>----- ".$a_lang['read_1']." -----</option>";
		$option_field2 .= "<option value=\"\">".$a_lang['read_2']."</option>";
		$option_field2 .= "<option value=\"\">-----------------------------</option>";
    	$option_field2 .= makeACatLink();
    	$option_field2 .= "</select>";
		
		buildHeaderRow($a_lang['read_3'],"upload.gif");
		//buildInfo($a_lang['info14'][0],$a_lang['info14'][1]);
		buildFormHeader("prog.php", "post", "save_dir_files");
		buildTableHeader($a_lang['read_4']);
        $option_field3 = "<select class=\"input\" name=\"status\">\n";
        $option_field3 .= "<option value=\"2\">".$a_lang['file_deadlink_reported']."</option>\n";
        $option_field3 .= "<option value=\"3\">".$a_lang['file_not_active']."</option>\n";  
        $option_field3 .= "<option value=\"1\" selected>".$a_lang['file_active']."</option>\n"; 
        $option_field3 .= "</select>\n";
        buildStandardRow($a_lang['read_5'], $option_field3);        
        buildTableSeparator($a_lang['read_6']);

        $colcount = 1;
		foreach($array as $file) { 
			if(strstr($file,$config_pre)) continue;
            if(strstr($file,".php")) continue;
			if($cache[$file]['file_name'] == $cache[$file][$file] && $file != $config['gd_pic']) {
				$option_field1 = "<select name=\"dl_name[".$file."][]\">";
				buildStandardRow(sprintf($a_lang['read_7'],$file),sprintf($a_lang['read_8'],$option_field1,$option_field2));
                $colcount++;
			}                
		}	
        if($colcount <= 1) buildDarkColumn($a_lang['read_9'],1,1,2);
		buildFormFooter($a_lang['insert_dir_files'], "", 3,$a_lang['uploads_reset']);	
	}
}    
   
buildAdminFooter();
?>