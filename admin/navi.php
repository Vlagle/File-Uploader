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
|   > Navigation Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: navi.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","navi.php");

include_once('adminfunc.inc.php');
$auth->checkEnginePerm("canaccessadmincent");

function switchMenuColor() {
	global $bgcount2;
	if ($bgcount2++%2==0) {
		return "#C0C0C0";
	} else {
		return "#D3D3D3";
	}
}

function buildMenuItem($name,$site,$target="main",$add_sid=1) {
    global $sess, $bgcount, $bgcount2;
    $menu_color = switchMenuColor();
	echo "<tr bgcolor=\"".$menu_color."\">\n<td class=\"menu\" onMouseOver=\"window.status='".$name."'; this.style.backgroundColor='#E9E9E9'; return true\" onMouseOut=\"this.style.backgroundColor='".$menu_color."'; window.status='';\">";
	echo "<a target=\"".$target."\" href=\"";
    if(!$add_sid) {
        echo $sess->url($site);
    } else {
        echo $sess->adminUrl($site);
    }
	echo "\">".$name."</a>";
	echo "</td>\n</tr>";

}

function buildMenuHeader($name) {
	echo "<tr>\n<td class=\"leftmenu\">&raquo;&nbsp;".$name."</td>\n</tr>";
}
?>

<html>
<head>
<title>Download Engine - Admin Center</title>
<style>
BODY {
	font-family : Verdana, Arial, sans-serif;
	font-size : 11px;
  	SCROLLBAR-BASE-COLOR: #4665B5;
  	SCROLLBAR-ARROW-COLOR: White;    
}

A, A:ACTIVE {
	font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size : 11px;
	color : Black;
	text-decoration : none;
}
A:HOVER {
	font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size : 11px;
	color : #FF3300;
	text-decoration : underline;
}

.leftmenu {
	background-color :#4665B5;
	color : White;
	font-size : 11px;
	font-weight : bold;
	border : 1px outset;	
}

/* Dark, first column */
.firstcolumn {
	background-color : Silver;
	font-size : 11px;
}

/* Light, other columns */
.othercolumn {
	background-color : #D3D3D3;
	font-size : 11px;
}

TD.menu {
	padding-left : 4px;
	padding-bottom : 4px;
	padding-top : 4px;
}

</style>
</head>
<body bgcolor="#E6E6E6">
<table bgcolor="#000000" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
<tr>
    <td>
	<table align="center" border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#333333">
    
    <?php
    buildMenuHeader($a_lang['navi_cat1']);
    buildMenuItem($a_lang['navi_p1'],"main.php");
	buildMenuItem($a_lang['navi_p2'],"index.php","_blank",false);
    buildMenuHeader($a_lang['navi_cat2']);
    buildMenuItem($a_lang['navi_p3'],"main.php?step=cat");
    buildMenuItem($a_lang['navi_p4'],"main.php?step=maincat");
    buildMenuHeader($a_lang['navi_cat3']);
    buildMenuItem($a_lang['navi_p5'],"frameset.php?step=catframe");
    buildMenuItem($a_lang['navi_p6'],"main.php?step=down");
    buildMenuItem($a_lang['navi_p22'],"upload.php?action=batch_start");
    buildMenuItem($a_lang['navi_p28'],"prog.php?step=read_dir");
    buildMenuItem($a_lang['navi_p27'],"prog.php?step=licence");
	if(BOARD_DRIVER == "default") {    
        buildMenuHeader($a_lang['navi_cat4']);
        buildMenuItem($a_lang['navi_p7'],"main.php?step=member_add");
        buildMenuItem($a_lang['navi_p8'],"main.php?step=member_change");
        buildMenuItem($a_lang['navi_p9'],"main.php?step=member_search");
    	buildMenuItem($a_lang['navi_p23'],"adminutil.php?action=email");
    }
    buildMenuHeader($a_lang['navi_cat8']);
    buildMenuItem($a_lang['navi_p19'],"groups.php?step=change");
    buildMenuItem($a_lang['navi_p20'],"groups.php?step=edit&egroupid=add");
	if(BOARD_DRIVER == "default") {    
        buildMenuHeader($a_lang['navi_cat5']);
        buildMenuItem($a_lang['navi_p10'],"avatar.php?step=add");
        buildMenuItem($a_lang['navi_p11'],"main.php?step=avat_edit");
    }
    buildMenuHeader($a_lang['navi_cat9']);
    buildMenuItem($a_lang['navi_p29'],"style.php?step=new_style");
    buildMenuItem($a_lang['navi_p30'],"style.php?step=edit");
    buildMenuItem($a_lang['navi_p13'],"templates.php?action=template_edit");	
    buildMenuHeader($a_lang['navi_cat7']);
    buildMenuItem($a_lang['navi_p14'],"main.php?step=gen_set");
    buildMenuItem($a_lang['navi_p16'],"main.php?step=page_set");
    buildMenuItem($a_lang['navi_p17'],"settings.php?step=onoff");    
    buildMenuItem($a_lang['navi_p12'],"adminutil.php?action=lang");
	buildMenuHeader($a_lang['navi_cat10']);
	buildMenuItem($a_lang['navi_p32'],"settings.php?step=update_counter");
	buildMenuItem($a_lang['navi_p31'],"settings.php?step=info");
	buildMenuItem($a_lang['navi_p24'],"adminutil.php?action=pre_backup");	
	buildMenuItem("PHP-Info","adminutil.php?action=php_info");
    ?>    
	</table>
	</td>
</tr>
</table>
<p class="menu2" align="center">&copy; by <a target="_blank" class="menu2" href="http://www.alexscriptengine.de">AlexScriptEngine.de</a></p>
</body>
</html>
