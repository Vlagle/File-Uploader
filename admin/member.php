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
|   > Memberverwaltung Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: member.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","member.php");

include_once('adminfunc.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
$auth->checkEnginePerm("canaccessadmincent");

$message = "";

if(isset($action) && $action=='add') {
	if (holeUser($username)) $message .= "$a_lang[member_mes1a] \"$username\" $a_lang[member_mes1b]";
    if (empty($useremail)) $message .= $a_lang['member_mes2'];
    if (!isEmail($useremail)) $message .= $a_lang['member_mes3'];
    if ($message=="") {
 	   	  $uid = AddMemberData($username,$userpassword,$useremail,$userhp,$groupid,$avatarid,$show_email_global,$blocked,$regdate,$lastvisit);
		  $message .= $a_lang['member_mes4'];
		  unset ($memberid);
    }
	$step = "change";
}
	
if(isset ($action) && $action=='del') {
	$com_poster = $db_sql->query_array("SELECT username FROM $user_table WHERE userid='".intval($memberid)."'");
	$db_sql->sql_query("DELETE FROM $user_table WHERE userid='".intval($memberid)."'");
	$db_sql->sql_query("UPDATE $dlcomment_table SET userid='0', user_comname='".$com_poster['username']."' WHERE userid='".intval($memberid)."'");
	$message .= $a_lang['member_mes5'];
	unset ($memberid);
	$step = "change";
}
	
if(isset ($action) && $action=='edit') {
	if ($show_email_global == '') $show_email_global = 0;
	if ($blocked == '') $blocked = 0;    
    $regdatestamp = ($regdate != "") ? "UNIX_TIMESTAMP('".trim($regdate)."')" : time();    
    $lastvisitstamp = ($lastvisit != "") ? "UNIX_TIMESTAMP('".trim($lastvisit)."')" : time();
    
    $db_sql->sql_query("UPDATE $user_table SET
                username='".addslashes($username)."',
                userpassword='$userpassword',
                useremail='".addslashes($useremail)."',
                regdate=".$regdatestamp.",
                lastvisit=".$lastvisitstamp.",
                userhp='".reBuildURL(addslashes($userhp))."',
                groupid='$groupid',
                avatarid='$avatarid',
                show_email_global='$show_email_global',
                blocked='$blocked',
				canuploadfile='".intval($canuploadfile)."'
                WHERE userid=".intval($memberid)."");    

	unset ($memberid);
	$message .= $a_lang['member_mes6'];
	$step = "change";
}

if(isset ($action) && $action=='edit_inactive') {
	$act_id = implode("','", $active_id);
	if($delete) {
		$db_sql->sql_query("DELETE FROM $user_table WHERE userid IN ('$act_id')");
		$message .= $a_lang['member_del_success'];
	}
	
	if($active) {
		$db_sql->sql_query("UPDATE $user_table SET activation='1' WHERE userid IN ('$act_id')");
		$message .= $a_lang['member_active_success'];
	}
}

if($step == 'load_user') {
	$hide = 1;
   	$head_js = "
   	<script language=\"JavaScript\">
   	<!--	
	var member;
   	function memberdata() { 
		member = mother.elements[\"box\"].value.split(\"_#_\");
		opener.document.alp.dlauthor.value = member[0];
		opener.document.alp.authormail.value = member[1];
		opener.document.alp.hplink.value = member[2];
		self.close(); 
   	} 	
   	//-->
   	</script>	
   	";	
}

if($step == 'avatar') {
	$hide = 1;
   	$head_js = "
   	<script language=\"JavaScript\">
   	<!--	
	var avat;
   	function avatardata() {
		var f = document.mother
		for (var i=0; i<f.avatar.length; i++) {
			if (f.avatar[i].checked) {
				avat = f.avatar[i].value;
			}
		}
	
		opener.document.alp.avatarid.value = avat;
		self.close(); 
		
   	} 	
   	//-->
   	</script>	
   	";	
}
	
buildAdminHeader($head_js,$hide);

if ($message != "") {
    if($active) {
        buildMessageRow($message,array('auto_redirect' => 'member.php', 'is_top' => 1, 'next_action' => array('step',$step,$a_lang['afunc_proceed']), 'next_script' => 'member.php'));    
    } else {
        buildMessageRow($message,array('is_top' => 1, 'next_action' => array('step',$step,$a_lang['afunc_proceed']), 'next_script' => 'member.php'));
    }
    buildAdminFooter();
    exit;
}

if(!isset ($step) && $action == '') {
    echo " <p><b>Es wurde keine Auswahl getroffen. Bitte wähle links aus der Navigation die gewünschte Option aus.</b></p>";
} else {
	if($step == 'load_user') {	
		echo "<br>";
		echo "<form action=\"\" onsubmit=\"return memberdata()\" name=\"mother\" method=\"post\">\n";
		buildHiddenField($sess->sess_name,$sess->sess_id);
		buildTableHeader($a_lang['member_found'], 1);
		$result = $db_sql->sql_query("SELECT * FROM $user_table");
		while($select = $db_sql->fetch_array($result)) {
			$select = stripslashes_array($select);
			$box .= "<option value=\"".$select['username']."_#_".$select['useremail']."_#_".$select['userhp']."\">".$select['username']."</option>\n";
		}
		$box = "<select style=\"width: 100%\" id=\"box\" name=\"box\" size=\"5\">\n".$box."</select>\n";			
		buildLightColumn($box,1,1,1);
		buildFormFooter($a_lang['take_over'], "", 1);
		closeWindowRow();
	}
	
    if($step == 'change') {
        memberList();
    }
  
    if($step == 'get') {
        if(!isset($start)) $start = 0;
        memberList($username,$start);
    }

    if($step == 'search') {
        buildHeaderRow($a_lang['member_u_search'],"search.gif");
        buildFormHeader("member.php"); 
        buildHiddenField("step","get");
        buildTableHeader($a_lang['member_insert']);
        buildDarkColumn("<input class=\"input\" name=\"username\" type=\"text\" size=\"40\">",1);
        buildLightColumn("<input class=\"button\" type=\"submit\" value=\"$a_lang[member_search]\">",0,1);
        buildTableFooter();  
    }

    if($step == 'add') {
        memberForm();
    }

    if($step == 'del') {
        $result = $db_sql->sql_query("SELECT * FROM $user_table WHERE userid='$memberid'");
        $del = $db_sql->fetch_array($result); 
        
        buildHeaderRow($a_lang['adminutil_4'],"delart.gif",1);
		buildInfo($a_lang['info8'][0],$a_lang['info8'][1]);	
        buildFormHeader("member.php","post","del"); 
        buildHiddenField("memberid",$memberid);
        buildTableHeader("$a_lang[adminutil_4]: <u>$del[username]</u>");
        buildDarkColumn("$a_lang[member_del1] (ID: $memberid) $a_lang[member_del2]",1,1,2);
        buildFormFooter($a_lang['afunc_61'], "", 2, $a_lang['afunc_62']);
    }
  
    if($step == 'edit') {
        memberForm($memberid);
    }
  
  if($step == 'activation') {
		$result = $db_sql->sql_query("SELECT * FROM $user_table WHERE activation!='1' AND groupid!='8' ORDER BY regdate DESC");
        
        buildHeaderRow($a_lang['member_activation'],"user.gif",1);
		buildInfo($a_lang['info9'][0],$a_lang['info9'][1]);
        buildFormHeader("member.php", "post", "edit_inactive");        
		
		echo "
        <table bgcolor=\"#000000\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
        <tr>
        <td>
		<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\">
		<tr>
		    <td class=\"menu_desc\">&nbsp;</td>
			<td class=\"menu_desc\">$a_lang[member_actname]</td>
		    <td class=\"menu_desc\">$a_lang[member_actmail]</td>
		    <td class=\"menu_desc\">$a_lang[member_actsince]</td>
		</tr>";
		$no = 1;
		while($registered_user = $db_sql->fetch_array($result)) {
			$date = getdate($registered_user['regdate']);
			$nickname = holeUserID($registered_user['userid']);
			
			if(trim(stripslashes($registered_user['useremail'])) == "") {
				$reg_mail = "&nbsp;";
			} else {
				$reg_mail = trim(stripslashes($registered_user['useremail']));
			}
			
            echo "<tr class=\"".switchBgColor()."\">";
            buildListColumn("<input type=\"checkbox\" name=\"active_id[]\" value=\"$registered_user[userid]\">");		
            buildListColumn(trim(stripslashes($registered_user['username'])));	
            buildListColumn($reg_mail);	
            buildListColumn("$date[mday].$date[mon].$date[year]",0,1);	
			
			$no++;
			}	
			
		echo "
        
		<tr class=\"table_footer\">
		    <td colspan=\"4\" align=\"center\"><input class=\"button\" type=\"submit\" name=\"active\" value=\" $a_lang[member_actyes] \"> <input class=\"button\" type=\"submit\" name=\"delete\" value=\" $a_lang[member_actdel] \"></td>
		</tr>		
		</table>
        </td>
        </tr>
        </table>	
		</form>";
    }
    
    if($step == 'avatar') {
		echo "<br>";
        
    	$over_all = $db_sql->query_array("SELECT Count(*) as total FROM $avat_table");
    	
    	include_once($_ENGINE['eng_dir']."admin/enginelib/class.nav.php");
    	if(!isset($_GET['start'])) {
            $start = 0;
        } else {
            $start = intval($_GET['start']);
        }
    	$nav = new Nav_Link();
    	$nav->overAll = $over_all['total'];
    	$nav->perPage = 12;
        $nav->DisplayLast = 1;
        $nav->DisplayFirst = 1;    
    	$url_neu = $sess->adminUrl("member.php?step=avatar")."&amp;";
    	$nav->MyLink = $url_neu;
    	$nav->LinkClass = "page_step";
    	$nav->start = $start;
    	$pagecount = $nav->BuildLinks();
    	if(!$pagecount) $pagecount = "<b>1</b>";
        $pages = intval($over_all['total'] / $nav->perPage);
        if($over_all['total'] % $nav->perPage) $pages++;	

        buildHeaderRow($a_lang['member_avatars']."<br><span class=\"smalltext\">&nbsp;$a_lang[afunc_203] ($pages): $pagecount</span>","avat.gif");		            
        $result = $db_sql->sql_query("SELECT * FROM $avat_table LIMIT $start,12");
        $no = 1;
        echo "<form action=\"\" onsubmit=\"return avatardata()\" name=\"mother\" method=\"post\">\n";
        buildTableHeader($a_lang['member_available_avatars'], 4);
        while($avt = $db_sql->fetch_array($result)) {
            if($no == 1 || $no == 5 || $no == 9) $display_avatar .= "<tr>";
            $display_avatar .= "<td class=\"firstcolumn\"><div align=\"center\"><img src=\"".$config['avaturl']."/".$avt['avatardata']."\" /><input type=\"radio\" id=\"avatar\" name=\"avatar\" value=\"".$avt['avatarid']."\" ";
            if($avt['avatarid'] == $avatarid) $display_avatar .= "checked";
            $display_avatar .= " /></div></td>";
            if($no == 4 || $no == 8 || $no == 12) $display_avatar .= "</tr>";
            $no++;
        }
        echo $display_avatar;
        buildFormFooter($a_lang['member_use_avatar'], "", 4);
    }
}

buildAdminFooter();
?>