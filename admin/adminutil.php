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
|   > Admin Utilities
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: adminutil.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","adminutil.php");

include_once('adminfunc.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
include_once($_ENGINE['eng_dir']."admin/enginelib/class.db_utils.php");
$auth->checkEnginePerm("canaccessadmincent");
$message = "";

function show_all_languages($language_folder = "") {
    global $language_folder_name,$a_lang;
    if ($language_folder == "") {
        $language_folder = './../lang';
    }
    $folder_list = array();
    $handle = opendir($language_folder);
    while ($file = @readdir($handle)) {
        
        if (@is_dir($language_folder."/".$file) && $file != "." && $file != "..") {
            if(!strstr($file,"CVS")) {
                $folder_list[] = $file;
            } else {
                continue;
            }
        }
    }
    closedir($handle);
    if (empty($folder_list) || !is_array($folder_list)) {
        $message = $a_lang['language_nopacks'];
        return false;
    } else {
        echo "<select class=\"input\" name=language_file_name>\n";
        for($i = 0; $i < sizeof($folder_list); $i++) {
            echo "<option value=\"".$folder_list[$i]."\"";
            if ($language_folder_name == $folder_list[$i]) {
                echo " selected=\"selected\"";
            }
            echo ">".$folder_list[$i]."</option>\n";
        }
    }
}

if (isset ($action) && $action=='language') {
   $db_sql->sql_query("UPDATE $set_table SET replace_value='$language_file_name' WHERE find_word='language'");
   $message .= $a_lang['language_done'];
   $action = 'lang';
}

$extra = "
<script language=\"Javascript\" type=\"text/javascript\">
<!--
function changeOptions(do_check)
{
    var selectObject = document.forms['alp'].elements['db_tables[]'];
    var selectCount  = selectObject.length;

    for (var i = 0; i < selectCount; i++) {
        selectObject.options[i].selected = do_check;
    }
    return true;
}
//-->
</script>
";

if($action == "php_info") {
	phpinfo();
}

if($action!="download") buildAdminHeader($extra);

// --------------------------------------------------------------------
// ----------------------------- AC Email -----------------------------
// --------------------------------------------------------------------

if($action == "send_mail") {
    include_once($_ENGINE['eng_dir']."admin/enginelib/class.phpmailer.php");
    $mail = new PHPMailer();
    if($admin_lang == 1) {
        $mailer_lang = "de";
    } else {
        $mailer_lang = "en";
    }
    $mail->SetLanguage($mailer_lang, $_ENGINE['eng_dir']."lang/".$config['language']."/");
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
    $mail->FromName = $user['username'];
    for ($x=0; $x<sizeof($mail_addr); $x++) {
        $mail->AddCC($mail_addr[$x]);
    }           
    $mail->Subject = $betreff;
    $mail->Body = $mail_text;  
    $mail->WordWrap = 50;  
    
    if(!$mail->Send()) {
       $message = $a_lang['adminutil_10'];
       $message .= $a_lang['adminutil_11'].": ".$mail->ErrorInfo;
    } else {
       $message = $a_lang['adminutil_12'];
    }  
    buildMessageRow($message);
    $action = "email";  
}

if($action == "email") {
    $option_line = "<select class=\"input\" name=\"mail_addr[]\" multiple size=\"12\">\n";
    $result = $db_sql->sql_query(" SELECT * FROM $group_table WHERE groupid!='4' AND groupid!='8'");
    while($main = $db_sql->fetch_array($result)) {
        $main = stripslashes_array($main);
        $option_line .= "<optgroup label=\"".$main['title']."\">";
        $result2 = $db_sql->sql_query("SELECT * FROM $user_table WHERE groupid='".$main['groupid']."' AND useremail!='' ORDER BY username");
        while($memberlist = $db_sql->fetch_array($result2)) {
            $memberlist = stripslashes_array($memberlist);
            $option_line .= "<option value=\"".trim(htmlspecialchars($memberlist['useremail']))."\">&raquo;&nbsp;".trim(htmlspecialchars($memberlist['username']))." (".trim(htmlspecialchars($memberlist['useremail'])).")</option>\n";
        }
        $option_line .= "</optgroup>\n";
    }	
    $option_line .= "</select>";    

    buildHeaderRow($a_lang['adminutil_13'],"mail.gif",1);
    buildInfo($a_lang['info4'][0],$a_lang['info4'][1]);
    buildFormHeader("adminutil.php", "post", "send_mail");
    buildTableHeader($a_lang['adminutil_14']);
    buildInputRow($a_lang['adminutil_15'], "betreff");
    buildTextareaRow($a_lang['adminutil_16'], "mail_text", "", 60, 20);
    buildStandardRow($a_lang['adminutil_17'], $option_line);
    buildFormFooter($a_lang['adminutil_18'], $a_lang['adminutil_19']);
}

// ------------------------------------------------------------------------
// ----------------------------- AC DB Backup -----------------------------
// ------------------------------------------------------------------------

if($action=="conf_delete" && $bckfile!="") {
    $db_utils = new engineMysqlUtils();
    if($db_utils->deleteBackup($bckfile)) {
		$message = $a_lang['adminutil_8'];
	} else {
		$message = $a_lang['adminutil_9'];
	}
	buildMessageRow($message);
	$action = "pre_backup";
}

if($action == "backup") {
	if(!empty($db_tables)) {
        $db_utils = new engineMysqlUtils();
        $db_utils->buildBackup($dbName, $structure_only);
	} else {
		buildMessageRow($a_lang['adminutil_20']);
	}
	
	$action = "pre_backup";
}

if($action=="restore" && $bckfile!="") {
    $db_utils = new engineMysqlUtils();
	if($db_utils->restoreBackup($bckfile)) {
		$message = $a_lang['adminutil_21'];
	} else {
		$message = $a_lang['adminutil_22'];	
	}
	buildMessageRow($message);

	$action = "pre_backup";
}

if($action == "pre_backup") {
    $db_utils = new engineMysqlUtils();
	buildHeaderRow($a_lang['adminutil_23'],"setting.gif",1);
    buildInfo($a_lang['info5'][0],$a_lang['info5'][1]);
	buildFormHeader("adminutil.php", "post", "backup");
	buildTableHeader($a_lang['adminutil_24']);
	$result = mysql_list_tables($dbName);
	$db_list = "<select class=\"input\" name=\"db_tables[]\" multiple size=\"12\">\n";
	while($tbl_name = $db_sql->fetch_array($result)) {
    	$db_list .= "<option value=\"".$tbl_name[0]."\" selected=\"selected\">".$tbl_name[0]."</option>\n";		
	}
	$db_list .= "</select>";
	$addon_desc = "<br><br>
	<a class=\"menu\" href=\"\" onclick=\"changeOptions(true); return false;\">$a_lang[adminutil_25]</a>
    &nbsp;|&nbsp;
    <a class=\"menu\" href=\"\" onclick=\"changeOptions(false); return false;\">$a_lang[adminutil_26]</a>
	<br><br><b>".$a_lang['adminutil_37']."</b>: ".rebuildFileSize($db_utils->buildDatabaseSize($dbName));
	buildStandardRow($a_lang['adminutil_27'].$addon_desc, $db_list);
	buildInputYesNo($a_lang['adminutil_28'], "structure_only", 0);
	buildFormFooter($a_lang['adminutil_29'], $a_lang['adminutil_19']);	
	buildTableHeader($a_lang['adminutil_30']);
	$db_utils->readBackups();
	buildTableFooter();

}

if($action=="delete" && $bckfile!="") {
	buildHeaderRow($a_lang['adminutil_31'],"delart.gif");
	buildFormHeader("adminutil.php","post","conf_delete"); 
	buildHiddenField("bckfile",$bckfile);
	buildTableHeader("$a_lang[adminutil_32]: <u>$bckfile</u>");
	buildDarkColumn($a_lang['adminutil_33'],1,1,2);
	buildFormFooter($a_lang['afunc_61'], "", 2, $a_lang['afunc_62']);
}

if($action=="download" && $bckfile!="") {
    $db_utils = new engineMysqlUtils();
    $db_utils->downloadBackup($bckfile);
}

if($action=="optimize") {
    $db_utils = new engineMysqlUtils();
    echo $db_utils->optimizeTables($_POST['operation'],$dbName);
}

// -----------------------------------------------------------------------
// ----------------------------- AC Language -----------------------------
// -----------------------------------------------------------------------

if ($action == "lang") {
    $result = $db_sql->sql_query("SELECT * FROM $set_table WHERE find_word='language'");
    $current_lang = $db_sql->fetch_array($result);  
    $language_folder_name = $current_lang['replace_value']; 
    
    buildHeaderRow($a_lang['language_head'],"language.gif");
    buildFormHeader("adminutil.php","post", "language");
    buildHiddenField("load","language");
    buildTableHeader($a_lang['language_exist']);
    buildDarkColumn("$a_lang[language_choose]:<br><span class=\"smalltext\">$a_lang[language_current]: <b>$current_lang[replace_value]</b></span>",1);
    echo "
    <td class=\"othercolumn\">";
    show_all_languages();
    echo "
    </td>
    </tr>";
    buildFormFooter($a_lang['language_button']);    
}

buildAdminFooter();
?>