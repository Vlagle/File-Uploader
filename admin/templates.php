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
|   > Templates online editieren
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: templates.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/
define("FILE_NAME","templates.php");

include_once('adminfunc.inc.php');
$auth->checkEnginePerm("canaccessadmincent");

$message = '';

function show_all_templates($template_folder = "") {
    global $template_file_name,$a_lang,$_ENGINE,$config;
    if ($template_folder == "") $template_folder = "./../templates/".$config['template_folder'];
    $file_list = array();
    $handle = @opendir($template_folder);
    while ($file = @readdir($handle)) {
        if (@is_file($template_folder."/".$file) && $file != "." && $file != "..") $file_list[] = $file;
    }
    closedir($handle);
    if (empty($file_list) || !is_array($file_list)) {
        $message = $a_lang['templates_mes3'];
        return false;
    } else {
        echo "<select class=\"input\" name=template_file_name>\n";
        for($i = 0; $i < sizeof($file_list); $i++) {
            echo "<option value=\"".$file_list[$i]."\"";
            if ($template_file_name == $file_list[$i]) echo " selected=\"selected\"";
            echo ">".$file_list[$i]."</option>\n";
        }
    }
}

function redo_specialchars($chars) {
    $chars = preg_replace("/(&)([0-9]*)(;)/esiU", "chr(intval('\\2'))", $chars);
    $chars = str_replace("&gt;", ">", $chars);
    $chars = str_replace("&lt;", "<", $chars);
    $chars = str_replace("&quot;", "\"", $chars);
    $chars = str_replace("&amp;", "&", $chars);
    return $chars;
}

$textarea_style = "<style>\n.tpl_textarea {\nwidth: 100%;\n}\n</style>\n";

if($action=='save_template') {
    if ($template != "" && $temp_content != "") {
        $temp_content = redo_specialchars($temp_content);
        $temp_content = stripslashes($temp_content);
        $fp = @fopen("./../templates/".$config['template_folder']."/".$template, "w+");

        if (@fwrite($fp, $temp_content)) {
            $message .= $a_lang['templates_mes1'];
        } else {
            $message .= $a_lang['templates_mes2'];
        }
        $action = 'template_edit';
    }
}

buildAdminHeader($textarea_style);

if ($message != "") {
    buildMessageRow($message,array('is_top' => 1, 'next_action' => array('action',$action,$a_lang['afunc_proceed']), 'next_script' => 'templates.php'));
    buildAdminFooter();
    exit;
}

if($action == 'transfer_settings') {
    $set = $db_sql->query_array("SELECT * FROM $set_table WHERE styleid='1'");
    
    foreach($set as $key => $val) {
        if(is_int($key) || $key == "styleid") continue;
        echo "Schl&uuml;ssel: ".$key." Wert: ".$val."<br>";
        $db_sql->sql_query("INSERT INTO gb1_config (find_word,replace_value) VALUES ('".$key."','".addslashes($val)."')");
    }
    exit;
}

if ($action == 'template_edit') {
    if(isset($load) && $load=='template') {
        $content = implode("", file($config['engine_mainurl']."/templates/".$config['template_folder']."/$template_file_name"));
    	$content=htmlspecialchars($content);
	}	

    $info = $a_lang['templates_info'];

    buildHeaderRow($a_lang['templates_edittpl'],"template.gif",1);
    buildInfo($a_lang['info13'][0],$a_lang['info13'][1]);	
    buildFormHeader("templates.php");
    buildHiddenField("load","template");
    buildHiddenField("action","template_edit");
    buildTableHeader($a_lang['templates_existtpl']);
    buildDarkColumn($a_lang['templates_choosetpl'],1);
    echo "
    <td class=\"othercolumn\">";
    show_all_templates();
    echo "
    </td>
    </tr>";
    buildFormFooter($a_lang['templates_loadtpl'],"");
    buildFormHeader("templates.php","post","save_template","tpl");
    buildHiddenField("template",$template_file_name);
    buildTableHeader($a_lang['templates_htmltpl']);
    buildLightColumn($info,1,1,2);
    ?>
<script language="JavaScript">
<!--
function HighlightAll() {
	var tempval=eval("document.tpl.temp_content")
	tempval.focus()
	tempval.select()
	if (document.all){
	therange=tempval.createTextRange()
	therange.execCommand("Copy")
	window.status="Inhalt angeleuchtet und in die Zwischenablage kopiert"
	setTimeout("window.status=''",1800)
	}
}

var NS4 = (document.layers);
var IE4 = (document.all);
var win = window;
var n   = 0;

function findInPage(str) {
  var txt, i, found;
  if (str == '')
    return false;
  if (NS4) {
    if (!win.find(str))
      while(win.find(str, false, true))
        n++;
    else
      n++;
    if (n == 0)
      alert('Not found.');
  }

  if (IE4) {
    txt = win.document.body.createTextRange();
    for (i = 0; i <= n && (found = txt.findText(str)) != false; i++) {
      txt.moveStart('character', 1);
      txt.moveEnd('textedit');
    }
    if (found) {
      txt.moveStart('character', -1);
      txt.findText(str);
      txt.select();
      txt.scrollIntoView();
      n++;
    } else {
      if (n > 0) {
        n = 0;
        findInPage(str);
      }
      else
        alert('Not found.');
    }
  }
  return false;
}
//-->
</script>	

    <?php
    buildDarkColumn("<textarea class=\"tpl_textarea\" name=\"temp_content\" rows=\"30\" cols=\"70\" wrap=\"off\">$content</textarea>",1,1,2);
    buildLightColumn("<input name=\"string\" type=\"text\" accesskey=\"t\" size=\"20\" onChange=\"n=0;\">\n<input class=\"button\" type=\"button\" value=\"Find\" accesskey=\"f\" onClick=\"javascript:findInPage(document.tpl.string.value)\">&nbsp;&nbsp;&nbsp;\n
    <input class=\"button\" type=\"button\" value=\"Preview\" accesskey=\"p\" onclick=\"javascript:displayHTML()\">&nbsp;&nbsp;&nbsp;<input class=\"button\" type=\"button\" value=\"Copy\" accesskey=\"c\" onclick=\"javascript:HighlightAll()\">",1,2);
    echo "
    </td>
    </tr>";

    buildFormFooter($a_lang['templates_savetpl'],"");
}

buildAdminFooter();
?>