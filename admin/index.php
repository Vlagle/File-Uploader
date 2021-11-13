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
|   > Startseite Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: index.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","index.php");

include_once('adminfunc.inc.php');

function buildPasswordRow($title, $name, $value="", $size="40",$html=0) {
	global $config,$bgcount;
	if ($html) $value=htmlspecialchars($value);
	echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
	echo "<td><p><input type=\"Password\" size=\"".$size."\" name=\"".$name."\" value=\"".$value."\"></p></td>\n</tr>\n";
}

if(!$auth->user['canaccessadmincent']) {

    buildAdminHeader('',1);
    ?>
<br><br><br><br>
<table bgcolor="#000000" width="500" cellspacing="0" cellpadding="0" border="0" align="center">
<tr>
    <td>
	<table align="center" border="0" cellspacing="1" cellpadding="15" width="100%" bgcolor="#333333">
    <tr>
        <td bgcolor="#C0C0C0">
        <img src="images/ac_logo.gif" alt="" align="right" width="255" height="35" border="0" />
        <br><br><br>
    <?php
    
    buildHeaderRow($a_lang[index_head],"lock.gif");
    if ($message != "") buildMessageRow($message);
    
    buildFormHeader("frame.php");
    buildHiddenField("admin","enter");
    buildTableHeader("Login");
    buildInputRow("<b>".$a_lang[index_login]."</b>", "username", $form[login]);
    buildPasswordRow("<b>".$a_lang[index_pw]."</b>", "userpassword");
    buildFormFooter($a_lang[index_login]);
    buildAdminFooter();
    ?>
        </td>
    </tr>
    </table>    
    </td>
</tr>
</table>    
    <?php
} else {
    header("Location: ".$sess->adminUrl("frame.php"));
    exit;
}
buildAdminFooter();
?>
