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
|   > Hilfe zu den möglichen BBCodes
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: bbhelp.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","bbhelp.php");

include_once('adminfunc.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");

function buildBBSeparator($title, $value) {
	echo "<tr>\n<td class=\"menu_desc\">";
	echo $title;
	echo "\n</td>\n";
	echo "<td class=\"menu_desc\">";
	echo $value;
	echo "\n</td>\n</tr>\n";	
}	

buildAdminHeader();
buildHeaderRow($a_lang[bbhelp_2],"user.gif");
buildTableHeader($a_lang[bbhelp_3]);
?>
 		 		 		 
<tr>
    <td colspan="2" class="<?php echo switchBgColor() ?>"><span class="smalltext"><?php echo "$a_lang[bbhelp_4]"; ?></span></td>
</tr>
<?php
buildBBSeparator($a_lang[bbhelp_5], $a_lang[bbhelp_6]);
buildStandardRow("[b]$a_lang[bbhelp_7][/b]", "<b>$a_lang[bbhelp_7]</b>");
buildStandardRow("[i]$a_lang[bbhelp_8][/i]", "<i>$a_lang[bbhelp_8]</i>");
buildStandardRow("[u]$a_lang[bbhelp_9][/u]", "<u>$a_lang[bbhelp_9]</u>");
buildStandardRow("[url=http://www.link.de]$a_lang[bbhelp_10][/url]", "<a class=\"post\" href=\"http://www.link.de\" target=_blank>$a_lang[bbhelp_10]</a>");
buildStandardRow("[url]http://www.link.de[/url]", "<a class=\"post\" href=\"http://www.link.de\" target=_blank>http://www.link.de</a>");
buildStandardRow("[email=die@adresse.de]$a_lang[bbhelp_11][/email]", "<a class=\"post\" href=\"mailto:die@adresse.de\">$a_lang[bbhelp_11]</a>");
buildStandardRow("[email]die@adresse.de[/email]", "<a class=\"post\" href=\"mailto:die@adresse.de\">die@adresse.de</a>");
buildStandardRow("[code]$a_lang[bbhelp_12][/code]", "<blockquote><font size=1>Quellcode:</font><hr><pre><font size=1>$a_lang[bbhelp_12]</font></pre><hr></blockquote>  <hr>");
buildStandardRow("[quote]$a_lang[bbhelp_13][/quote]", "<blockquote><font size=1>Zitat:</font><hr><font size=1>$a_lang[bbhelp_13]</font><hr></blockquote>");
buildTableFooter("",2);
closeWindowRow();
?>
</body>
</html>