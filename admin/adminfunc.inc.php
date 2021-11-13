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
|   > Funktionsbibliothek Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: adminfunc.inc.php 28 2005-10-30 10:09:00Z alex $
|
+--------------------------------------------------------------------------
*/
define("IN_ADMIN_CENTER", true);

include_once('../lib.inc.php');

$source_file_array = array("adminutil.php","avatar.php","bbhelp.php","categories.php","comment.php","frame.php","global.php","groups.php","head.php","help.php","img_global.php","index.php","league.php","main.php","prog.php","member.php","navi.php","upload.php","settings.php","templates.php","progress.php","frameset.php");

if(!in_array(FILE_NAME,$source_file_array) || !defined("FILE_NAME")) {
    die("&raquo;&nbsp;Aufrufende Datei nicht registriert!<br>&raquo;&nbsp;Calling file is not registered!");
}     

if(isset($_POST['step']) || isset($_GET['step'])) {
	$step = (isset($_GET['step'])) ? stripslashes(trim($_GET['step'])) : stripslashes(trim($_POST['step']));
} else {
	unset($step);
}

if(isset($_POST['change']) || isset($_GET['change'])) {
	$change = (isset($_GET['change'])) ? stripslashes(trim($_GET['change'])) : stripslashes(trim($_POST['change']));
} else {
	$change = '';
}

if(isset($_POST['cat']) || isset($_GET['cat'])) {
	$cat = (isset($_GET['cat'])) ? stripslashes(trim($_GET['cat'])) : stripslashes(trim($_POST['cat']));
} else {
	unset($cat);
}

if ($register_globals != 1) {
	@extract($_SERVER, EXTR_SKIP);
	@extract($_COOKIE, EXTR_SKIP);
	@extract($_FILES, EXTR_SKIP);
	@extract($_POST, EXTR_SKIP);
	@extract($_GET, EXTR_SKIP);
	@extract($_ENV, EXTR_SKIP);
}

function ConfirmComment() {
    global $config, $dlcomment_table, $preart_table, $bbcode,$a_lang,$db_sql,$sess;
    $no = 1;
    $result = $db_sql->sql_query("SELECT * FROM $dlcomment_table WHERE com_status='2'");
    if($db_sql->num_rows($result) >= 1) buildFormHeader("comment.php", "post", "conf_multi");
    echo "<table bgcolor=\"#000000\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n<tr>\n<td>\n";
    echo "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
    echo "<tr>\n<td class=\"menu_desc\">&nbsp;</td>";
    echo "<td class=\"menu_desc\" width=\"5%\"><b>$a_lang[afunc_1]:</b></td>";
    echo "\n<td width=\"45%\" class=\"menu_desc\"><b>$a_lang[afunc_2]</b></td>";
    echo "\n<td class=\"menu_desc\" width=\"30%\"><b>$a_lang[afunc_3]</b></td>";
    echo "\n<td class=\"menu_desc\"><b>$a_lang[afunc_4]</b></td>";
    echo "\n<td width=\"8%\" class=\"menu_desc\" nowrap><b>$a_lang[afunc_5]</b></td>";
    echo "\n</td>\n</tr>\n";         
    
	if($db_sql->num_rows($result) >= 1) { 
        while ($com = $db_sql->fetch_array($result)) {
            $com = stripslashes_array($com);
            if ($com['user_comname'] == '0' || $com['user_comname'] == "") {
                $postuser = CheckUserID($com['userid']);
                $author = "<a href=\"mailto:$postuser[useremail]\">$postuser[username]</a>";
            } else {
                $author = "$com[user_comname] - $a_lang[afunc_6]";
            }
        			 
            $com_date = $com['com_date'];
            $date = getdate($com_date);
					 
            if ($com['posticon'] == "") {
                $picon = "";
            } else {
                $picon = "<img src=\"$com[posticon]\">";
            }
            if(strlen($com['dl_comment'])>50) $com['dl_comment'] = substr($com['dl_comment'],0,35)."...";
            $post_comment = $bbcode->rebuildText($com['dl_comment']);
            $post_comment = trim($post_comment);					 
            $com['com_headline'] = trim(stripslashes($com['com_headline']));
            if(strlen($com['com_headline'])>30) $com['com_headline'] = substr($com['com_headline'],0,25)."...";
                     
            buildDarkColumn("<input type=\"checkbox\" name=\"comid[$com[comid]]\">",1);
            buildDarkColumn("<b>$com[comid]</b>");
            buildDarkColumn(ConfCat($com['dlid']));
            buildDarkColumn("$picon $com[com_headline]<br><font size=\"1\">$post_comment</font>");
            buildDarkColumn("$author / $date[mday].$date[mon].$date[year]");
            buildDarkColumn("<a href=\"".$sess->adminUrl("comment.php?step=conf&dlid=".$com['dlid']."&comid=".$com['comid'])."\"><img src=\"images/okcom.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"$a_lang[afunc_7]\"></a>&nbsp;
                            <a href=\"".$sess->adminUrl("comment.php?step=del&dlid=".$com['dlid']."&comid=".$com['comid'])."\"><img src=\"images/delcom.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"$a_lang[afunc_8]\"></a>&nbsp;
                            <a href=\"Javascript:CommentInfo($com[comid])\"><img src=\"images/detcom.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\" $a_lang[afunc_9]\"></a>",0,1);
            $no++;
        }	
        echo "<tr class=\"table_footer\">\n<td colspan=\"6\" align=\"left\">\n";
        echo "<img src= \"images/arrow.gif\" border=\"0\" align=\"absmiddle\">";
        echo "<input type=\"submit\" name=\"public\" value=\"   ".$a_lang['afunc_7']."   \" class=\"button\">\n";
        echo "<input type=\"submit\" name=\"delete\" value=\"   ".$a_lang['afunc_8']."   \" class=\"button\">\n";
    	echo "&nbsp;\n</td>\n</tr>\n</table>\n";
    	echo "</td>\n</tr>\n</table>\n";
    	echo "</form><br />\n"; 
	} else {
		buildDarkColumn($a_lang['afunc_331'],1,1,6);
        buildTableFooter("",6);	
	}
}

function ConfirmFile() {
    global $config, $dl_table, $bbcode,$a_lang,$db_sql,$sess;
    $no = 1;
    $result = $db_sql->sql_query("SELECT * FROM $dl_table WHERE status='3'");
    if($db_sql->num_rows($result) >= 1) buildFormHeader("prog.php", "post", "conf_multi");
    echo "<table bgcolor=\"#000000\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n<tr>\n<td>\n";
    echo "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
    echo "<tr>\n<td class=\"menu_desc\">&nbsp;</td>";
    echo "<td class=\"menu_desc\" width=\"5%\"><b>$a_lang[afunc_1]:</b></td>";
    echo "\n<td width=\"45%\" class=\"menu_desc\"><b>$a_lang[afunc_2]</b></td>";
    echo "\n<td class=\"menu_desc\" width=\"30%\"><b>$a_lang[afunc_10]</b></td>";
    echo "\n<td class=\"menu_desc\"><b>$a_lang[afunc_4]</b></td>";
    echo "\n<td width=\"8%\" class=\"menu_desc\" nowrap><b>$a_lang[afunc_5]</b></td>";
    echo "\n</td>\n</tr>\n";         
    
	if($db_sql->num_rows($result) >= 1) { 
        while ($dl = $db_sql->fetch_array($result)) {
            $dl = stripslashes_array($dl);
            
            
            $dl_date = $dl['dl_date'];
            $date = getdate($dl_date);
            
            if ($dl['thumb'] == "") {
                $pic = $a_lang['afunc_11'];
            } else {
                $pic = "<img src=\"$config[thumburl]/$dl[thumb]\" border=\"0\">";
            }
            
            $ltime = LoadTime($dl['dlsize']);
            if(strlen($dl['dldesc'])>40) $dl['dldesc'] = substr($dl['dldesc'],0,35)."...";
            $dldesc = $bbcode->rebuildText($dl['dldesc']);
            $dldesc = trim($dldesc);            
            
            buildDarkColumn("<input type=\"checkbox\" name=\"dlid[$dl[dlid]]\">",1);
            buildDarkColumn("<b>$dl[dlid]</b>");
            buildDarkColumn(ConfCat($dl['dlid']));
            buildDarkColumn("$dldesc");
            buildDarkColumn(stripslashes(htmlspecialchars($dl['dlauthor']))." / $date[mday].$date[mon].$date[year]");
            buildDarkColumn("<a href=\"".$sess->adminUrl("prog.php?action=update&catid=".$dl[catid]."&dlid=".$dl[dlid])."\"><img src=\"images/okcom.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"$a_lang[afunc_12]\"></a>&nbsp;
                            <a href=\"".$sess->adminUrl("prog.php?action=del&dlid=".$dl[dlid])."\"><img src=\"images/delcom.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"$a_lang[afunc_13]\"></a>&nbsp;
                            <a href=\"".$sess->adminUrl("prog.php?step=edit&dlid=".$dl[dlid])."\"><img src=\"images/detcom.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\" $a_lang[afunc_14]\"></a>",0,1);
            $no++;
        }	
        echo "<tr class=\"table_footer\">\n<td colspan=\"6\" align=\"left\">\n";
        echo "<img src= \"images/arrow.gif\" border=\"0\" align=\"absmiddle\">";
        echo "<input type=\"submit\" name=\"public\" value=\"   ".$a_lang['afunc_12']."   \" class=\"button\">\n";
        echo "<input type=\"submit\" name=\"delete\" value=\"   ".$a_lang['afunc_13']."   \" class=\"button\">\n";
    	echo "&nbsp;\n</td>\n</tr>\n</table>\n";
    	echo "</td>\n</tr>\n</table>\n";
    	echo "</form><br />\n";         
	} else {
		buildDarkColumn($a_lang['afunc_332'],1,1,6);
        buildTableFooter("",6);	
	}
}      
		 	 		 
function ConfCat($dlid) {
    global $cat_table, $dl_table, $config,$a_lang,$sess,$db_sql;
    $result = $db_sql->sql_query("SELECT catid,dltitle FROM $dl_table WHERE dlid='$dlid'");
    list($catid,$dltitle) = mysql_fetch_row($result);
    
    $result2 = $db_sql->sql_query("SELECT titel FROM $cat_table WHERE catid='$catid'");
    list($titel) = mysql_fetch_row($result2);
    
    $conf_cat = $a_lang['afunc_15'].": <b>".stripslashes(htmlspecialchars($dltitle))."</b><br>";
    $conf_cat .= "<font size=\"1\">$a_lang[afunc_16]: <a class=\"menu\" target=\"_blank\" href=\"".$sess->url("index.php?subcat=".$catid)."\"> $titel</a></font>";
    return $conf_cat;
}


function DeadLink() {
	global $config, $dl_table, $dlcomment_table, $sBBcode, $a_lang, $db_sql,$sess;
	$no = 1;
	$result = $db_sql->sql_query("SELECT * FROM $dl_table WHERE status='2'");    
    if($db_sql->num_rows($result) >= 1) buildFormHeader("prog.php", "post", "conf_multi");
    echo "<table bgcolor=\"#000000\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n<tr>\n<td>\n";
    echo "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
    echo "<tr>\n<td class=\"menu_desc\">&nbsp;</td>";
    echo "<td class=\"menu_desc\" width=\"5%\"><b>$a_lang[afunc_1]:</b></td>";
    echo "\n<td width=\"45%\" class=\"menu_desc\"><b>$a_lang[afunc_2]</b></td>";
    echo "\n<td class=\"menu_desc\"><b>$a_lang[afunc_17]</b></td>";
    echo "\n<td class=\"menu_desc\"><b>$a_lang[afunc_4]</b></td>";
    echo "\n<td width=\"12%\" class=\"menu_desc\"><b>$a_lang[afunc_5]</b></td>";
    echo "\n</td>\n</tr>\n";		 
		 
	if($db_sql->num_rows($result) >= 1) { 	
		while ($file = $db_sql->fetch_array($result)) {
			$file = stripslashes_array($file);
            
            $dl_date = $file['dl_date'];
            $date = getdate($dl_date);
            
            if ($file['dlpoints'] != 0) {
                $dl_divp = $file['dlpoints'] / $file['dlvotes'];
                $points = round($dl_divp,2);
            }
            
            if ($file['dlpoints'] == 0) {
                $vote = $a_lang['afunc_18'];
            } else {
                $vote = "<b>$points</b> - <b>$file[dlvotes]</b> $a_lang[afunc_19]";
            }                
			
			$kom_i = 0;
			$zahler = $db_sql->sql_query("SELECT comid from $dlcomment_table where dlid='$file[dlid]' and com_status='1'");
			while (list($comid) = mysql_fetch_row($zahler)) {
				$kom_i++;
			}
			
			if ($kom_i == 0) {
				$comment = $a_lang['afunc_20'];
			} else {
				$comment = "<b>$kom_i</b> $a_lang[afunc_21]";
			}
			
            buildDarkColumn("<input type=\"checkbox\" name=\"dlid[$file[dlid]]\">",1);
            buildDarkColumn("<b>$file[dlid]</b>");
            buildDarkColumn(ConfCat($file['dlid']));
            buildDarkColumn($vote."<br><b>".$file['dlhits']."</b> ".$a_lang['afunc_22'].", ".$comment);
            buildDarkColumn(htmlspecialchars($file['dl_author'])." / $date[mday].$date[mon].$date[year]");
            buildDarkColumn("<a href=\"".$sess->adminUrl("prog.php?dlid=".$file['dlid']."&action=dead")."\"><img src=\"images/okcom.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"$a_lang[afunc_23]\"></a>&nbsp;
												 <a href=\"".$sess->adminUrl("prog.php?dlid=".$file['dlid']."&step=edit")."\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"$a_lang[afunc_14]\"></a>",0,1);							 			
			$no++;
		}	
        echo "<tr class=\"table_footer\">\n<td colspan=\"6\" align=\"left\">\n";
        echo "<img src= \"images/arrow.gif\" border=\"0\" align=\"absmiddle\">";
        echo "<input type=\"submit\" name=\"dead\" value=\"   ".$a_lang['afunc_23']."   \" class=\"button\">\n";
    	echo "&nbsp;\n</td>\n</tr>\n</table>\n";
    	echo "</td>\n</tr>\n</table>\n";
    	echo "</form><br />\n";        		 
	} else {
		buildDarkColumn($a_lang['afunc_333'],1,1,6);
        buildTableFooter("",6);	
	}
}    

function GetCatInfo($catid) {
    global $cat_table,$a_lang,$db_sql;
    if ($catid == 0) {
        $cat['titel'] = $a_lang['afunc_38'];
    } else {
        $cat = $db_sql->query_array("SELECT * FROM $cat_table WHERE catid='$catid'");
    }
    return $cat;
} 
		 
function addChildlist($catid,$add_element) {
    global $cat_table, $dl_childtable,$db_sql;
    $result = $db_sql->sql_query("SELECT subcat FROM $cat_table WHERE catid='$catid'");
    list($subcat) = mysql_fetch_row($result);
    
    if($subcat != "0") {
        $result2 = $db_sql->sql_query("SELECT childlist FROM $dl_childtable WHERE catid='$subcat'");
        if(mysql_num_rows($result2) != "0") {
            list($childlist) = mysql_fetch_row($result2);
            $new_list = $childlist.",".$add_element;
            $db_sql->sql_query("UPDATE $dl_childtable SET childlist='".modifyChildList($new_list)."' WHERE catid='$subcat'");
            addChildlist($subcat,$add_element);
        } else {
            $db_sql->sql_query("INSERT INTO $dl_childtable (catid,childlist) VALUES ('".$subcat."','".modifyChildList($add_element)."')");
            addChildlist($subcat,$add_element);
        }
    }				
}	

function modifyChildList($var) {
    $t = substr($var,0,1);
    if($t == ",") {
        $var_modified = substr($var,1);
    } else {
        $var_modified = $var;
    }
    
    $var_reverse = strrev($var_modified);
    $s = substr($var_reverse,0,1);
    
    if($s == ",") {
        return strrev(substr($var_reverse,1));
    } else {
        return strrev($var_reverse);
    }  
}	 
		 
function delChildlist($catid,$del_element) {
    global $cat_table, $dl_childtable,$db_sql;
    $result = $db_sql->sql_query("SELECT subcat FROM $cat_table WHERE catid='$catid'");
    list($subcat) = mysql_fetch_row($result);
    
    if($subcat != "0") {
        $result2 = $db_sql->sql_query("SELECT childlist FROM $dl_childtable WHERE catid='$subcat'");
        list($childlist) = mysql_fetch_row($result2);
        $childarray = explode(",",$childlist);			
        foreach($childarray as $schluessel => $wert) {
            if($wert == $del_element) {
                $element = $childarray[$schluessel];
                unset($childarray[$schluessel]);
            }	
        }        
        $new_list = implode(",",$childarray);			
        $db_sql->sql_query("UPDATE $dl_childtable SET childlist='$new_list' WHERE catid='$subcat'");
        delChildlist($subcat,$del_element);
    }		
}
		 
function FileCatSearch($mainid) {
    global $cat_table,$config,$dl_table,$a_lang,$db_sql,$sess;         
    buildHeaderRow($a_lang['afunc_111'],"newdet.gif");  
	buildTableDescription(array("Kategoriename (ID)","Optionen","Unterkategorie"),1);        
         
    $result = $db_sql->sql_query("SELECT catid,titel,subcat FROM $cat_table WHERE subcat='$mainid'");
    while(list($catid,$titel,$subcat) = mysql_fetch_row($result)) {
        $count = 0;
        $result2 = $db_sql->sql_query("SELECT catid FROM $cat_table WHERE subcat='$catid'");
        while(list($dcatid,$dtitel,$dsubcat) = mysql_fetch_row($result2)) $count++;
				
        if ($count == "0") {
            $ucat = "&nbsp;";
        } else {
            $ucat = "<a class=\"menu\" href=\"".$sess->adminUrl("prog.php?catid=".$catid)."\"><img src=\"images/subcat.gif\" alt=\"".$a_lang['afunc_27']."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">$a_lang[afunc_27]</a>";
        }
					
        $no = 0;
        $result3 = mysql_query("SELECT dlid FROM $dl_table WHERE catid='$catid'");
        while(list($dlid,$dltitle) = mysql_fetch_row($result3)) $no++;
				
        if ($no == "0") {
            $file = "&nbsp;";
        } else {
            $file = "&nbsp;&nbsp;&nbsp;&nbsp;<a class=\"menu\" href=\"".$sess->adminUrl("prog.php?step=choose&catid=".$catid)."\"><img src=\"images/edit.gif\" alt=\"".$a_lang['afunc_113']."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">$a_lang[afunc_113]</a>";
        }        
        
        echo "<tr class=\"".switchBgColor()."\">\n";
        echo "<td valign=\"top\"><p>".stripslashes($titel)." <span class=\"smalltext\">(ID: <b>$catid</b>)</span></p></td>\n";
        echo "<td valign=\"top\"><p><b><a class=\"menu\" href=\"".$sess->adminUrl("prog.php?step=add&catid=".$catid)."\"><img src=\"images/add.gif\" alt=\"".$a_lang['afunc_114']."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">$a_lang[afunc_114]</a>".$file."</b></p></td>\n";
        echo "<td valign=\"top\"><p>".$ucat."</p></td>\n";        
        echo "</tr>\n";    
    }   
    buildTableFooter(); 
    
    if($mainid != 0) {
        $subjump = $db_sql->query_array("SELECT subcat FROM $cat_table WHERE catid='$mainid' LIMIT 1");
        $linktarget = ($subjump['subcat'] == 0) ? "" : "&catid=".$subjump['subcat'] ;
        buildExternalItems("Kategoriestufe zur&uuml;ck","prog.php?step=cat".$linktarget,"back.gif");     
    }        
}
		 
function copyFile($dlid,$catid,$edit_file,$comment_file,$date_file) {
    global $dl_table,$cat_table,$stats_day_table,$stats_month_table,$dlcomment_table,$db_sql;
    $result = $db_sql->sql_query("SELECT * FROM $dl_table WHERE dlid='".$dlid."'");
    $old = mysql_fetch_array($result);
    $db_sql->sql_query("INSERT INTO $dl_table (catid,dltitle,dldesc,status,dlurl,dl_date,dlhits,dlvotes,hplink,dlsize,dlpoints,dlauthor,authormail,thumb,comment_count,onlyreg)
                VALUES ('".$catid."','".addslashes($old[dltitle])."','".addslashes($old[dldesc])."','".$old[status]."','".$old[dlurl]."','".$old[dl_date]."','".$old[dlhits]."','".$old[dlvotes]."','".addslashes($old[hplink])."','".$old[dlsize]."','".$old[dlpoints]."','".addslashes($old[dlauthor])."','".addslashes($old[authormail])."','".$old[thumb]."','".$old[comment_count]."','".$old[onlyreg]."')");      
    $new_dlid = $db_sql->insert_id();
    
    $db_sql->sql_query("UPDATE $cat_table SET download_count=download_count+1 WHERE catid='$catid'");
    
    if($comment_file == 1) {
        $db_sql->sql_query("UPDATE $dlcomment_table SET dlid='".$new_dlid."' WHERE dlid='$dlid'");
        $db_sql->sql_query("UPDATE $dl_table SET comment_count='0' WHERE dlid='$dlid'");
    } else {
        $db_sql->sql_query("UPDATE $dl_table SET comment_count='0' WHERE dlid='".$new_dlid."'");
    }
    
    if($date_file == 1) {
        $db_sql->sql_query("UPDATE $dl_table SET dl_date='".time()."' WHERE dlid='".$new_dlid."'");		
    } else {
        $db_sql->sql_query("UPDATE $dl_table SET dl_date='".time()."' WHERE dlid='$dlid'");		
    }	
    
    if($edit_file == 1) {		
        return $new_dlid;        
    } else {
        return $dlid;
    }
		
}         
		 
function memberList($username="",$start=0) {
    global $config,$group_table,$avat_table,$user_table,$dlcomment_table, $a_lang,$db_sql,$sess,$_ENGINE,$start;
    $count = 0;
    
    if($username == "") {    
        $over_all = $db_sql->query_array("SELECT Count(*) as total FROM $user_table WHERE groupid != '8'");
        
        if(!class_exists(Nav_Link)) include_once($_ENGINE['eng_dir']."admin/enginelib/class.nav.php");
        if(!isset($start)) $start = 0;
        $nav = new Nav_Link();
        $nav->overAll = $over_all['total'];
        $nav->perPage = "15";
        $url_neu = $sess->adminUrl("member.php?step=change")."&";
        $nav->MyLink = $url_neu;
        $nav->LinkClass = "smalltext";
        $nav->start = $start;
        $pagecount = $nav->BuildLinks();
        
        if(!$pagecount) $pagecount = "<b>1</b>";         
        $query = "SELECT * FROM $user_table WHERE groupid != '8' ORDER BY username DESC LIMIT $start,15"; 
        $page_link = "<br><span class=\"smalltext\">&nbsp;$a_lang[afunc_252]: $pagecount</span>";  
    } else {
        $query = "SELECT * FROM $user_table WHERE username LIKE '%$username%' && groupid != '8' ORDER BY username DESC";
    }
    
    buildHeaderRow($a_lang['afunc_167']." ".$page_link,"user.gif",1);
    buildInfo($a_lang['info1'][0],$a_lang['info1'][1]);	
	buildTableDescription(array($a_lang['afunc_168'],$a_lang['afunc_337'],$a_lang['afunc_170'],$a_lang['afunc_171'],$a_lang['afunc_172'],$a_lang['afunc_173']),1);	
	/*echo "<table bgcolor=\"#000000\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n<tr>\n<td>\n";
	echo "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";    
    echo "<tr class=\"menu_desc\">";
    buildListColumn("<b>$a_lang[afunc_168]</b>");
    buildListColumn("<b>$a_lang[afunc_337]</b>");
    buildListColumn("<b>$a_lang[afunc_170]</b>");
    buildListColumn("<b>$a_lang[afunc_171]</b>");
    buildListColumn("<b>$a_lang[afunc_172]</b>");
    buildListColumn("<b>$a_lang[afunc_173]</b>");
    echo "</tr>";*/
         
            $result2 = $db_sql->sql_query($query);
            while($user = $db_sql->fetch_array($result2)) {
				$count++;
				$user = stripslashes_array($user);

				$kom_i = 0;
				$zahler = $db_sql->sql_query("SELECT comid FROM $dlcomment_table where userid='$user[userid]' and com_status='1'");
	   			while (list($comid) =$db_sql->fetch_row($zahler)) {
	 				  $kom_i++;
	   			}
                
       		   	if ($user['useremail'] == "") {
       		      $email = "&nbsp;";
       		    } else {
       		   	  $email = "<a href=\"mailto:".trim(stripslashes($user['useremail']))."\"><img src=\"$config[engine_mainurl]/admin/images/user_mail.gif\" border=\"0\" width=\"14\" height=\"13\" alt=\"".trim(stripslashes($user['useremail']))."\"></a>";
       		    }
                
       		   	if ($user['userhp'] == "") {
       		      $u_hp = "&nbsp;";
       		    } else {
       		   	  $u_hp = "<a target=\"_blank\" href=\"".trim(stripslashes($user['userhp']))."\"><img src=\"$config[engine_mainurl]/admin/images/user_hp.gif\" border=\"0\" width=\"12\" height=\"13\" alt=\"".trim(stripslashes($user['userhp']))."\"></a>";
       		    }                
         		
         		if ($user['blocked'] == "1") {
         		   $block = "<strong><font color=\"#ff0000\">$a_lang[afunc_61]</font></strong>";
         		} else {
         		   $block = $a_lang['afunc_62'];
         		}
         		
         		$result4 = $db_sql->sql_query(" SELECT title FROM $group_table WHERE groupid='$user[groupid]'");
         		list($titel) = $db_sql->fetch_row($result4);
				if ($user['groupid'] == 1) {
					$result5 = $db_sql->sql_query("SELECT * FROM $user_table WHERE groupid='1'");
					$anz = $db_sql->num_rows($result5);
					if ($anz == 1) {
						$erase = "<img src=\"images/no_delete.gif\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\" alt=\"".$a_lang['afunc_174']."\">".$a_lang[afunc_175]."";
					} else {
						$erase = "<a class=\"menu\" href=\"".$sess->adminUrl("member.php?step=del&memberid=".$user['userid'])."\"><img src=\"images/delete.gif\" alt=\"".$a_lang[afunc_175]."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">".$a_lang[afunc_175]."</a>";
					}
				} else {
					$erase = "<a class=\"menu\" href=\"".$sess->adminUrl("member.php?step=del&memberid=".$user['userid'])."\"><img src=\"images/delete.gif\" alt=\"".$a_lang[afunc_175]."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">".$a_lang[afunc_175]."</a>";
				}
                
    echo "<tr class=\"".switchBgColor()."\">";
    buildListColumn($user['username']);
    buildListColumn($email."&nbsp;".$u_hp);
    buildListColumn($titel);
    buildListColumn($kom_i);
    buildListColumn($block);
    buildListColumn("<a class=\"menu\" href=\"".$sess->adminUrl("member.php?step=edit&memberid=".$user['userid'])."\"><img src=\"images/edit.gif\" alt=\"".$a_lang[afunc_176]."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">".$a_lang[afunc_176]."</a>&nbsp;&nbsp;&nbsp;$erase",1);
    echo "</tr>";
    
    $no++;
    }    
    
    if ($no == 0) {
        echo "<tr class=\"".switchBgColor()."\">";  
        echo "<td colspan=\"6\">";
        buildMessageRow("$a_lang[afunc_177]  <b>$username</b>"); 
        echo "</table>\n";
        echo "</td>";
        echo "</tr>";
        echo "</table><br />\n";   
    } else {
    	echo "</table>\n";
    	echo "</td>\n</tr>\n";
    	echo "</table><br />\n";    
    }
    
    buildExternalItems($a_lang['afunc_334'],"member.php?step=add","add.gif");

}
		 
function GetGroup($usergroup) {
    global $config, $group_table, $a_lang,$db_sql;
    $group_line = "<select class=\"input\" name=\"groupid\">\n";
    $result3 = $db_sql->sql_query(" SELECT * FROM $group_table WHERE groupid!='4' AND groupid!='8'");
    while($main = $db_sql->fetch_array($result3)) {
        $main = stripslashes_array($main);
        if ($main['groupid'] == $usergroup) {
            $select = "selected";
        } else {
            $select = "";
        }
        $group_line .= "<option value=\"$main[groupid]\" $select>$main[title] (ID: $main[groupid])</option>\n";
    }		 
    $group_line .= "</select>";
    return $group_line;
}

function massConfirmation($comid) {
    global $db_sql, $dlcomment_table, $dl_table;   
    $dlid = $db_sql->query_array("SELECT dlid FROM $dlcomment_table WHERE comid='".$comid."'");
    $db_sql->sql_query("UPDATE $dl_table SET comment_count=comment_count+1 WHERE dlid='".$dlid['dlid']."'");
}

function buildAdminBreadCrumb($bread,$startcat) {
    global $config, $lang;
    $no = count($bread);
    $i = 1;
    foreach($bread as $val => $key) {
        if($i == 0) $breadcrumb .= "<a href=\"".$key."\">".$val."</a>";
        if($i == $no) {
            $breadcrumb .= "<b>".$val."</b>";
        } else {
            $breadcrumb .= "<a href=\"".$key."\">".$val."</a>";
        }
        
        if($i != $no && $startcat != 0) $breadcrumb .= "&nbsp;&raquo;&nbsp;";
        $i++;
    }
    return $breadcrumb;
}
		 
function memberForm($userid="") {
    global $config,$group_table,$avat_table,$user_table,$dlcomment_table, $a_lang,$lang,$db_sql,$_ENGINE;
    
    if($userid != "") {
        $result = $db_sql->sql_query("SELECT *,FROM_UNIXTIME(lastvisit) AS lastvisit,FROM_UNIXTIME(regdate) AS regdate FROM $user_table WHERE userid='$userid'");
        $user = $db_sql->fetch_array($result);
        $user = stripslashes_array($user);
    }
    
    buildHeaderRow($a_lang['afunc_178'],"user.gif",1);
    buildInfo($a_lang['info2'][0],$a_lang['info2'][1]);
    if($userid != "") buildFormHeader("member.php","", "edit");
    if($userid == "") buildFormHeader("member.php","", "add");
    if($userid != "") buildHiddenField("memberid",$userid);
    if($userid != "") buildHiddenField("userpassword",$user['userpassword']);
    if($userid != "") buildTableHeader($a_lang['afunc_179']." ".$user['username']);
    if($userid == "") buildTableHeader($a_lang['afunc_192']." ".$user['username']);
    buildStandardRow("<b>Member-ID</b>","<b>".$user['userid']."</b>");
    buildInputRow($a_lang[afunc_180], "username", $user['username'], "35");
    if($userid == "") buildInputRow($a_lang['afunc_193'], "userpassword", $user['userpassword'], "35");
    buildInputRow($a_lang['afunc_169'], "useremail", $user['useremail'], "35");
	include_once($_ENGINE['eng_dir']."admin/enginelib/class.calendar.php");
	$calendar = new DHTML_Calendar($_ENGINE['main_url'].'/admin/includes/calendar/', $lang['php_mailer_lang']);
	$calendar->load_files();
	$calendar->make_input_field(
	           array('firstDay'=>1,'showsTime'=>true,'showOthers'=>true,'ifFormat'=>'%Y-%m-%d %H:%M:%S','timeFormat'=> '24'),
	           array('size'=>'40','name'=>'regdate','value'=>$user['regdate']),
			   $a_lang['afunc_181']);
	$calendar->make_input_field(
	           array('firstDay'=>1,'showsTime'=>true,'showOthers'=>true,'ifFormat'=>'%Y-%m-%d %H:%M:%S','timeFormat'=> '24'),
	           array('size'=>'40','name'=>'lastvisit','value'=>$user['lastvisit']),
			   $a_lang['afunc_182']);		
    buildInputRow($a_lang['afunc_183'], "userhp", $user['userhp'], "35");		 
    $tmp_group = GetGroup($user['groupid']);
    buildStandardRow($a_lang['afunc_184'],$tmp_group);
    echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$a_lang['afunc_186']."</p></td>\n";
    echo "<td nowrap><p><input type=\"text\" size=\"40\" name=\"avatarid\" value=\"".$user['avatarid']."\">&nbsp;<a href=\"Javascript: Avatar(".$user['avatarid'].");\"><img src=\"images/avatar.gif\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">".$a_lang['member_choose_avatar']."</a></p></td>\n</tr>\n";
    
    //buildInputRow($a_lang['afunc_186']."<br><a class=\"menu\" href=\"JavaScript:Avatar(".$user['avatarid'].")\">$a_lang[afunc_201]</a>", "avatarid", $user['avatarid'], "35");
    buildInputYesNo($a_lang['afunc_187'], "show_email_global", $user['show_email_global']);
    buildInputYesNo($a_lang['afunc_188'], "blocked", $user['blocked']);
    buildInputYesNo($a_lang['afunc_189'], "canuploadfile", $user['canuploadfile']);
    buildFormFooter($a_lang['afunc_190'], $a_lang['afunc_209']);		 
}

function AddMemberData($username,$userpassword,$useremail,$userhp,$groupid,$avatarid,$show_email_global,$blocked,$regdate,$lastvisit) {
    global $user_table,$config,$db_sql;
    $userpassword = addslashes(md5($userpassword));
    $regdatestamp = ($regdate != "") ? "UNIX_TIMESTAMP('".trim($regdate)."')" : time();    
    $lastvisitstamp = ($lastvisit != "") ? "UNIX_TIMESTAMP('".trim($lastvisit)."')" : time();
    
    $db_sql->sql_query("INSERT INTO $user_table (username,userpassword,useremail,regdate,lastvisit,userhp,groupid,avatarid,show_email_global,blocked,activation,canuploadfile)
                VALUES ('".addslashes(htmlspecialchars($username))."','$userpassword','".addslashes($useremail)."',".$regdatestamp.",".$lastvisitstamp.",'".reBuildURL(addslashes($userhp))."','$groupid','$avatarid','$show_email_global','$blocked','1','$canuploadfile')");
    return mysql_insert_id();
}

function rebuildFileSize($fsize) {
	global $config,$lang;
	//$fsize = intval($fsize);
	$length = strlen($fsize);
	if($length <= 3) {
		$fsize = number_format($fsize,2,",",".");
		return $fsize." Bytes";
	} elseif($length >= 4 && $length <= 6) {
		$fsize = number_format($fsize/1024,2,",",".");
		return $fsize." kB";
	} elseif($length >= 7 && $length <= 9) {	
		$fsize = number_format($fsize/1048576,2,",",".");
		return $fsize." MB";
	} else {
		$fsize = number_format($fsize/1073741824,2,",",".");
		return $fsize." GB";
	}	
}

function makeACatLink($catid,$subcat,$depth=1) {
    global $cat_table,$db_sql;
    $result2 = mysql_query("SELECT * FROM $cat_table WHERE subcat='$subcat'");
    while ($dl_cat = mysql_fetch_array($result2)) {
    	if ($depth != "1") $limiter = str_repeat("--",$depth-1);
    
        if ($dl_cat['catid'] == $catid) {
        	$cat_link .= "<option value=\"$dl_cat[catid]\" selected>".$limiter.stripslashes($dl_cat['titel'])."</option>\n";
        } else {
        	$cat_link .= "<option value=\"$dl_cat[catid]\">".$limiter.stripslashes($dl_cat['titel'])."</option>\n\t\t\t\t\t\t\t";				
        }
    
        $newcat = $dl_cat['catid'];
        $cat_link .= makeACatLink($catid,$newcat,$depth+1);
    }
    
    return $cat_link;
}		 
		 
function buildAdminHeader($head="",$hide="") {
	global $a_lang, $config,$sess,$HTTP_USER_AGENT;
	
	header ("Cache-Control: no-store, no-cache, must-revalidate");
	header ("Cache-Control: pre-check=0, post-check=0, max-age=0", false);
	header ("Pragma: no-cache");
	header ("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  
	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Download Engine - Admin Center</title>
<meta content="text/html; charset=windows-1252" http-equiv="Content-Type">
<link rel="stylesheet" href="<?php echo $config['engine_mainurl']; ?>/admin/acstyle.css">
<script type="text/javascript" src="includes/js/menu.js"></script>
<script type="text/javascript" src="includes/js/utilities.js"></script>
<script type="text/javascript" src="includes/js/progress.js"></script>
<script language="JavaScript">
<!--
var neu = null;
function Avatar(avatarid) {
    neu = window.open('', 'Neues', 'width=600,height=500,resizable=0');
    if (neu != null) {
    if (neu.opener == null) {
        neu.opener = self;
        }
    neu.location.href = '<?php echo str_replace("&amp;","&",$sess->adminUrl("member.php?step=avatar&avatarid='+avatarid+'")); ?>';
    }
}

var statusWin, toppos2, leftpos2;
toppos2 = (screen.height - 401)/2;
leftpos2 = (screen.width - 401)/2;
function LoadUser() {
    neu = window.open('', 'Neues', 'width=300,height=200,top='+toppos2+',left='+leftpos2+',resizable=no, status=no');
    if (neu != null) {
    if (neu.opener == null) {
        neu.opener = self;
        }
    neu.location.href = '<?php echo str_replace("&amp;","&",$sess->adminUrl("member.php?step=load_user")); ?>';
    }
}

function CommentInfo(comid) {
    neu = window.open('', 'Neues', 'width=500,height=300,resizable=0,scrollbars=1');
    if (neu != null) {
    if (neu.opener == null) {
        neu.opener = self;
        }
    neu.location.href = '<?php echo str_replace("&amp;","&",$sess->adminUrl("comment.php?step=details&comid='+comid+'")); ?>';
    }
}

function Uploadfile() {
    neu = window.open('', 'Neues', 'width=600,height=500,resizable=0');
    if (neu != null) {
    if (neu.opener == null) {
        neu.opener = self;
        }
    neu.location.href = '<?php echo str_replace("&amp;","&",$sess->adminUrl("upload.php?step=file")); ?>';
    }
}

function Uploadimage() {
    neu = window.open('', 'Neues', 'width=600,height=500,resizable=0');
    if (neu != null) {
    if (neu.opener == null) {
        neu.opener = self;
        }
    neu.location.href = '<?php echo str_replace("&amp;","&",$sess->adminUrl("upload.php?step=thumb")); ?>';
    }
}

function Helpfile() {
    neu = window.open('', 'Neues', 'width=750,height=500,resizable=0,scrollbars=1');
    if (neu != null) {
    if (neu.opener == null) {
        neu.opener = self;
        }
    neu.location.href = '<?php echo str_replace("&amp;","&",$sess->adminUrl("bbhelp.php")); ?>';
    }
}

//-->
</script>	
<?php
echo $head;
?>
</head>
<?php
    if (eregi('msie',$HTTP_USER_AGENT)) {
    ?>
    <body leftmargin="20" topmargin="0" marginwidth="20" marginheight="20" bgcolor="#FFFFFF" text="#000000" onLoad=FrameStat="Show";>
    <?php
    } elseif(eregi('opera',$HTTP_USER_AGENT)) {
    ?>
    <body leftmargin="20" topmargin="0" marginwidth="20" marginheight="0" bgcolor="#FFFFFF" text="#000000" onLoad=FrameStat="Show";>
    <?php    
    } elseif(eregi('mozilla',$HTTP_USER_AGENT)) {
    ?>
    <body leftmargin="20" topmargin="0" marginwidth="20" marginheight="0" bgcolor="#FFFFFF" text="#000000" onLoad=FrameStat="Show";>
    <?php    
    } else {
    ?>
    <body leftmargin="20" topmargin="0" marginwidth="20" marginheight="20" bgcolor="#FFFFFF" text="#000000" onLoad=FrameStat="Show";>
    <?php    
    }
    
    if(!$hide) {
?>
<a href="JavaScript:Frame()"><img style="margin-left: -20px" src="images/toggle_frame.gif" width="50" height="18" border="0" alt="Men&uuml; verbergen/anzeigen"></a>
	<?php
        }
	}
    
function buildAdminFooter() {
?>
</body></html>
<?php
}

function switchBgColor() {
	global $bgcount;
	if ($bgcount++%2==0) {
		return "firstcolumn";
	} else {
		return "othercolumn";
	}
}

function buildMessageRow2($message) {
	echo "<p>\n<table bgcolor=\"#000000\" width=\"100%\" cellspacing=\"1\" cellpadding=\"0\" border=\"0\">\n<tr>\n<td>";
	echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n<tr>\n";
    echo "<td class=\"message\" width=\"26\">&nbsp;<img src=\"images/caution.gif\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">&nbsp;</td>\n";
	echo "<td class=\"message\">".$message."</td>\n";
	echo "</tr>\n</table>\n";
	echo "</td>\n</tr>\n</table>\n</p>\n";
}	

function buildMessageRow($message,$additionall='') {
    global $sess,$a_lang;
    if($additionall['is_top'] == 1) echo "<p>&nbsp;</p>";
    if($additionall['back_button'] != '' || $additionall['next_action']) echo "<form action=\"".$additionall['next_script']."\" name=\"ase\" method=\"post\"><br />\n";
    echo "<table bgcolor=\"#000000\" width=\"95%\" cellspacing=\"1\" cellpadding=\"0\" align=\"center\" border=\"0\">\n<tr>\n<td>";
    echo "<table cellpadding=\"4\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr>\n";
	echo "<td class=\"message_desc\" align=\"center\" colspan=\"2\"><b>";
	echo ($additionall['headline']=='') ? "Engine-Message" : $additionall['headline'];
    echo "</b></td></tr>\n";
    echo "<tr valign=\"top\"><td class=\"message\" colspan=\"2\">".$message."<br /></td>\n";
    echo "</tr>\n";
   	if($additionall['back_button'] != '' || $additionall['next_action']) echo "<tr>\n<td class=\"message_footer\" colspan=\"2\" align=\"center\">";
	if($additionall['back_button'] != '') echo "<input type=\"button\" class=\"button\" value=\"Zurück\" tabindex=\"1\" onclick=\"window.location='javascript:history.back(1)';\" />\n";
	if($additionall['next_action']) echo "<input type=\"submit\" class=\"button\" value=\"".$additionall['next_action'][2]."\" tabindex=\"2\" />\n";
	if($additionall['back_button'] != '' || $additionall['next_action']) echo "</td></tr>\n";
    echo "</table>\n";
    echo "</td>\n</tr>\n</table>\n";
    if($additionall['next_action']) echo "<input type=\"hidden\" name=\"".$additionall['next_action'][0]."\" value=\"".$additionall['next_action'][1]."\" /></form>\n";
    echo "<p>&nbsp;</p>\n";
    if($additionall['auto_redirect'] != '') buildRedirectJS($sess->adminUrl($additionall['auto_redirect']));
}

function buildRedirectJS($targetpage, $timeout = 2) {
	global $a_lang;
	$targetpage = str_replace('&amp;', '&', $targetpage);
	echo '<p align="center" class="smallfont"><a href="' . $targetpage . '" onclick="javascript:clearTimeout(timerID);">'.$a_lang['afunc_338'].'</a></p>';
	echo "\n<script type=\"text/javascript\">\n";
	if ($timeout == 0) {
		echo "window.location=\"$targetpage\";";
	} else {
		echo "myvar = \"\"; timeout = " . ($timeout*10) . ";
		function perform_refresh() {
			window.status=\"".$a_lang['afunc_338']."\"+myvar; myvar = myvar + \" .\";
			timerID = setTimeout(\"perform_refresh();\", 100);
			if (timeout > 0){ 
                timeout -= 1; 
            } else { 
                clearTimeout(timerID); window.status=\"\"; window.location=\"$targetpage\"; 
            }
		}
		perform_refresh();";
	}
	echo "\n</script>\n";
}

function buildTransferRow($head,$text,$js_name,$data_1, $data_2="") {
	global $config;
	echo "<div class=\"infomessage\">";
	echo "<img class=\"infomessage\" src=\"".$config['engine_mainurl']."/admin/images/clipboard.gif\" align=\"absmiddle\" border=\"0\"></a><span class=\"info\"><b>".$head."</b></span><br>\n";
	echo $text."<br><br><img src=\"".$config['engine_mainurl']."/admin/images/transfer.gif\" align=\"absmiddle\" border=\"0\">";
	echo "<a class=\"menu\" href=\"javascript:".$js_name."('".$data_1."'";
	if($data_2) echo ",'".$data_2."'";
	echo ")\">Daten &uuml;bertragen<br></div><br><br>\n";

}	
    
function buildHeaderRow($text,$graphic="standard.gif", $help=0) {	
	echo "<p>\n<table bgcolor=\"#000000\" width=\"100%\" cellspacing=\"1\" cellpadding=\"0\" border=\"0\">\n<tr>\n<td>\n";
	echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n<tr>\n";
	echo "<td class=\"page_head\">";
    if($help) echo "<div style=\"float:right\"><a class=\"help\" href=\"javascript:;\" onClick=\"blocking('info', 'block'); return false;\">Help&nbsp;<img src=\"images/help.gif\" alt=\"Hilfe/Help\" width=\"16\" height=\"16\" border=\"0\" style=\"vertical-align:middle\"></a></div>\n";
	echo "&nbsp;<img src=\"images/".$graphic."\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">&nbsp;".$text."&nbsp;</td>\n";
	echo "</tr>\n</table>\n";
	echo "</td>\n</tr>\n</table>\n</p>";
}

function buildTableHeader($headline, $colspan = 2) {
	echo "<table bgcolor=\"#000000\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n<tr>\n<td>\n";
	echo "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
	echo "<tr>\n<td colspan=\"$colspan\" class=\"menu_desc\">&raquo;&nbsp;";
	echo $headline;
	echo "\n</td>\n</tr>\n";
}

function buildTableDescription($name="",$headeruse="") {
	if($headeruse) {
		echo "<table bgcolor=\"#000000\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n<tr>\n<td>\n";
		echo "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
	}
	echo "<tr>\n";
	if(is_array($name)) {
		for($i=0;$i<count($name);$i++) {
			echo "<th>".$name[$i]."</th>\n";
		}
	} else {
		echo "<th>".$name."</th>\n";
	}
	echo "</tr>";	
}
	
function buildTableSeparator($title, $colspan = 2) {
	echo "<tr>\n<td colspan=\"$colspan\" class=\"menu_desc\">&raquo;&nbsp;";
	echo $title;
	echo "\n</td>\n</tr>\n";
}	
	
function buildTableFooter($extra="",$colspan=2) {
	if ($extra!="") echo "<tr class=\"table_footer\">\n<td colspan=\"$colspan\" align=\"center\">$extra</td></tr>\n";
	echo "</table>\n";
	echo "</td>\n</tr>\n";
	echo "</table><br />\n";
}	
	
function buildFormHeader($scripturl, $method="post", $action="", $name="alp", $uploadform=0) {
	global $config,$sess;
	if ($uploadform) {
		$upload = " ENCTYPE=\"multipart/form-data\"";
	} else {
		$upload = "";
	}	
	echo "<form action=\"".$config['engine_mainurl']."/admin/".$scripturl."\" ".$upload." name=\"".$name."\" method=\"".$method."\">\n";
	
	if ($action != "") echo "<input type=\"hidden\" name=\"action\" value=\"".$action."\">\n";
	buildHiddenField($sess->sess_name,$sess->sess_id);
}

function buildFormFooter($submitname = "Submit", $resetname = "Reset", $colspan = 2, $goback = "", $addsubmit="") {
	echo "<tr class=\"table_footer\">\n<td colspan=\"".$colspan."\" align=\"center\">\n&nbsp;";
	
	if ($submitname != "") echo "<input type=\"submit\" value=\"   ".$submitname."   \" class=\"button\" ".$addsubmit.">\n";	
	if ($resetname != "") echo "<input type=\"reset\" value=\"   ".$resetname."   \" class=\"button\">\n";	
	if ($goback != "") echo "<input type=\"button\" value=\"   ".$goback."   \" onclick=\"history.go(-1)\" class=\"button\">\n";

	echo "&nbsp;\n</td>\n</tr>\n</table>\n";
	echo "</td>\n</tr>\n</table>\n";
	echo "</form><br />\n";
}

function buildDarkColumn($name,$start=0,$end=0,$colspan=0) {
	if ($start) echo "<tr>";
    if ($colspan) $col = "colspan=\"".$colspan."\"";
	echo "<td class=\"firstcolumn\" ".$col.">".$name."</td>";
	if ($end) echo "</tr>";
}

function buildLightColumn($name,$start=0,$end=0,$colspan=0)	{
	if ($start) echo "<tr>";
    if ($colspan) $col = "colspan=\"".$colspan."\"";
	echo "<td class=\"othercolumn\" ".$col.">".$name."</td>";
	if ($end) echo "</tr>";
}	

function buildListColumn($name,$center=0)	{
    if($center) $cent = " align=\"center\"";
	echo "<td ".$cent."><p>".$name."</p></td>";
}	
	
function buildHiddenField($name,$value="",$html=0) {
	if ($html) $value=htmlspecialchars($value);
	echo "<input type=\"hidden\" name=\"$name\" value=\"$value\">\n";	
}	

function buildInputRow($title, $name, $value="", $size="40",$html=0,$calendar=0,$max_length="") {
	global $config,$bgcount;
	if ($html) $value=htmlspecialchars($value);
    if ($max_length) $max = "maxlength=\"".$max_length."\"";
	if ($calendar) $cal = "&nbsp;<a href=\"Javascript:displayCalender('".$name."')\"><img src=\"images/calender.gif\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\"></a>";
	echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
	echo "<td><p><input ".$max." type=\"text\" size=\"".$size."\" name=\"".$name."\" value=\"".$value."\">".$cal."</p></td>\n</tr>\n";
}

function buildDateRow($title, $name, $value="", $size="40",$html=0,$max_length="") {
	global $config,$bgcount;
	if ($html) $value=htmlspecialchars($value);
    if ($max_length) $max = "maxlength=\"".$max_length."\"";
	$cal = "&nbsp;<a href=\"#\" onclick=\"return showCalendar('".$name."', 'y-mm-dd 00:00:00');\"><img src=\"images/calender.gif\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\"></a>";
	echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
	echo "<td><p><input ".$max." type=\"text\" size=\"".$size."\" name=\"".$name."\" id=\"".$name."\" value=\"".$value."\">".$cal."</p></td>\n</tr>\n";
}


function buildUploadInput($title, $name, $value="", $size="40",$html=0,$upload_js="") {
	global $config,$bgcount,$a_lang;
	if ($html) $value=htmlspecialchars($value);
	echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
	echo "<td nowrap><p><input type=\"text\" size=\"".$size."\" name=\"".$name."\" value=\"".$value."\">&nbsp;<a href=\"Javascript:".$upload_js."\"><img src=\"images/upload.gif\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">$a_lang[afunc_253]</a></p></td>\n</tr>\n";
}

function buildUploadRow($title, $name, $size="40",$html=0) {
	global $config,$bgcount;
	if ($html) $value=htmlspecialchars($value);
	echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
	echo "<td nowrap><p><input type=\"file\" size=\"".$size."\" name=\"".$name."\" value=\"\"></p></td>\n</tr>\n";
}

function buildStandardRow($title, $value="",$html=0) {
	global $config,$bgcount;
	if ($html) $value=htmlspecialchars($value);
	echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
	echo "<td><p>".$value."</p></td>\n</tr>\n";
}
	
function buildTextareaRow($title, $name, $value="", $cols="", $rows=10,$html=0,$extra="") {
	global $config,$bgcount;
	if ($html) $value=htmlspecialchars($value);
	echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
	echo "<td><p><textarea name=\"".$name."\" rows=\"".$rows."\" cols=\"".$cols."\" $extra>".$value."</textarea></p></td>\n</tr>\n";
}	
	
function buildRadioRow($title, $name, $value = 1) {
	global $a_lang,$config,$bgcount;
	echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
	echo "<td><p>";
	echo "<input type=\"radio\" name=\"".$name."\" value=\"1\"";
	
	if ($value == 1) echo " checked=\"checked\"";
	echo "> ".$a_lang['afunc_61']."&nbsp;&nbsp;&nbsp;\n";
	
	echo "<input type=\"radio\" name=\"".$name."\" value=\"0\"";
	
	if ($value != 1) echo " checked=\"checked\"";
	echo "> ".$a_lang['afunc_62']." ";
	
	echo "</p></td>\n</tr>";
}	

function buildCheckBoxRow($title, $name, $value = 1, $add_info = "") {
	global $a_lang,$config,$bgcount;
	echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
	echo "<td><p>";
	echo "<input type=\"Checkbox\" name=\"".$name."\" value=\"1\"";
	if ($value == 1) echo " checked=\"checked\"";
	echo "> ".$add_info;
	echo "</p></td>\n</tr>";
}	
	
function buildInputYesNo($title, $name, $value=1, $flip=0) {
	global $a_lang,$config,$bgcount;
	if(!$flip) {
		echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
		echo "<td><p>";
		echo "<select class=\"input\" name=\"".$name."\">";
		echo "<option value=\"1\"";
		
		if ($value == 1) echo " selected";
		echo "> ".$a_lang['afunc_61']."</option>\n";
		
		echo "<option value=\"0\"";
		
		if ($value != 1) echo " selected";
		echo "> ".$a_lang['afunc_62']."</option>\n";
		
		echo "</p></td>\n</tr>";
	} else {
		echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
		echo "<td><p>";
		echo "<select class=\"input\" name=\"".$name."\">";
		echo "<option value=\"0\"";
		
		if ($value == 0) echo " selected";
		echo "> ".$a_lang['afunc_61']."</option>\n";
		
		echo "<option value=\"1\"";
		
		if ($value != 0) echo " selected";
		echo "> ".$a_lang['afunc_62']."</option>\n";
		
		echo "</p></td>\n</tr>";	
	}
}	  

function buildNewItem($name,$link,$search_name="",$search_link="") {
    global $config,$sess;
    echo "<table width=\"10%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"right\">\n<tr>\n<td align=\"right\" nowrap>\n";
    echo "<a class=\"menu\" href=\"".$sess->adminUrl($link)."\"><img src=\"images/add.gif\" alt=\"".$name."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">".$name."</a>\n";
    echo "</td>\n";
    if($search_name && $search_link) {
        echo "<td align=\"right\" nowrap>&nbsp;&nbsp;&nbsp;\n";
        echo "<a class=\"menu\" href=\"".$sess->adminUrl($search_link)."\"><img src=\"images/search.gif\" alt=\"".$search_name."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">".$search_name."</a>\n";
        echo "</td>\n";
    }
    echo "</tr>\n</table>\n<br><br>";
}  	  

function buildExternalItems($name,$link,$img,$parenttarget=0) {
    global $config,$sess;
	echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n<tr>\n<td>";
	echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"MenuTable\" height=\"22\" align=\"right\">";
	echo "<tr>\n<td rowspan=\"3\"><img hspace=\"1\" src=\"images/gif.gif\" width=\"1\" height=\"22\" border=\"0\"></td>";
	echo "<td><img hspace=\"1\" src=\"images/gif.gif\" width=\"1\" height=\"1\" border=\"0\"></td>";
	echo "<td rowspan=\"3\"><img hspace=\"1\" src=\"images/gif.gif\" width=\"1\" height=\"22\" border=\"0\"></td>\n</tr>\n<tr>\n<td>";
	echo "<table width=\"50%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"MenuInnerTable\">\n<tr>";
	echo "<td><img hspace=\"1\" src=\"images/gif.gif\" width=\"4\" border=\"0\"></td>";

	if(is_array($name)) {
		$totalitems = count($name)-1;
		for($i=0;$i<count($name);$i++) {
			echo "<td class=\"MenuOut\" onmouseover=\"this.attributes['class'].value='MenuOver';\" onmouseout=\"this.attributes['class'].value='MenuOut';\">\n";
			echo "<a href=\"".$sess->adminUrl($link[$i])."\" class=\"menu\"";
			if($parenttarget) echo " target=\"_parent\"";
			echo "><img class=\"img_button\" align=\"absmiddle\" src=\"images/".$img[$i]."\" alt=\"".$name[$i]."\" width\"16\" height=\"16\" border=\"0\">".$name[$i]."</a>\n";
		    echo "</td>\n";
			if($i<$totalitems) echo "<td style=\"padding-left: 2px;\">|</td>";
		}
	} else {
		echo "<td class=\"MenuOut\" onmouseover=\"this.attributes['class'].value='MenuOver';\" onmouseout=\"this.attributes['class'].value='MenuOut';\">\n";
		echo "<a href=\"".$sess->adminUrl($link)."\" class=\"menu\"";
		if($parenttarget) echo " target=\"_parent\"";
		echo "><img class=\"img_button\" align=\"absmiddle\" src=\"images/".$img."\" alt=\"".$name."\" width\"16\" height=\"16\" border=\"0\">".$name."</a>\n";
	    echo "</td>\n";	
	}
	echo "<td><img hspace=\"1\" src=\"images/gif.gif\" width=\"4\" border=\"0\"></td>\n</tr>\n</table>";
	echo "</td>\n</tr>\n<tr>";
	echo "<td><img hspace=\"1\" src=\"images/gif.gif\" width=\"1\" height=\"1\" border=\"0\"></td>\n</tr>\n</table>";
	echo "</td>\n</tr>\n</table>\n<br>";
}  	

function buildInfo($head,$text,$pic=1) {
    global $config;
    $icon = array(
        1 => "info_set",
        2 => "security"
    );
    echo "<div id=\"info\" style=\"display: none;\"><div class=\"infomessage\"><img class=\"infomessage\" src=\"".$config['engine_mainurl']."/admin/images/".$icon[$pic].".gif\" align=\"absmiddle\"><span class=\"info\"><b>".$head."</b></span><br>\n";
    echo $text."<br></div></div>\n";
}

function closeWindowRow() {
    global $a_lang;
	echo "<div align=\"center\">[<a class=\"menu\" href=\"javascript: self.close();\">$a_lang[comment_close]</a>]</div>";
}     
		
?>