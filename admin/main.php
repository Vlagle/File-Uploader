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
|   > Hauptseite Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: main.php 28 2005-10-30 10:09:00Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","main.php");

include_once('adminfunc.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
$auth->checkEnginePerm("canaccessadmincent");
   
if($step == 'upl_file') {
	header("Location: ".$sess->adminUrl("upload.php?step=file"));
	exit;
	#### hier, wie ein Prog hinzugefügt wird ###
	}
if($step == 'upl_thumb') {
	header("Location: ".$sess->adminUrl("upload.php?step=thumb"));
	exit;
	#### hier, wie ein Prog hinzugefügt wird ###
	}

if($step == 'upl_avat') {
	header("Location: ".$sess->adminUrl("upload.php?step=avat"));
	exit;
	#### hier, wie ein Prog hinzugefügt wird ###
	}
   

if($step == 'filecat') {
	header("Location: ".$sess->adminUrl("prog.php?step=cat"));
	exit;
	#### hier, wie ein Prog hinzugefügt wird ###
	}
	
if($step == 'down') {
	header("Location: ".$sess->adminUrl("prog.php?step=down"));
	exit;
	#### hier, wie ein Prog gelöscht wird ###
	}

if($step == 'gen_set') {
	header("Location: ".$sess->adminUrl("settings.php?step=gen_set"));
	exit;
	#### hier, wenn die Haupteinstellungen verändert werden sollen ###
	}

if($step == 'col_set') {
	header("Location: ".$sess->adminUrl("settings.php?step=col_set"));
	exit;
	#### hier, wenn die Farben verändert werden sollen ###
	}

if($step == 'page_set') {
	header("Location: ".$sess->adminUrl("settings.php?step=page_set"));
	exit;
	#### hier, wenn die Seiteneinstellungen geändert werden ###
	}
	
if($step == 'member_add') {
	header("Location: ".$sess->adminUrl("member.php?step=add"));
	exit;
	#### hier, wie ein Mitglied hinzugefügt wird ###
	}

if($step == 'member_change') {
	header("Location: ".$sess->adminUrl("member.php?step=change"));
	exit;
	#### hier, wie ein Mitglied editiert gelöscht wird ###
	}
	
if($step == 'member_search') {
	header("Location: ".$sess->adminUrl("member.php?step=search"));
	exit;
	#### hier, wie ein Mitglied gesucht wird ###
	}

if($step == 'avat_upload') {
	header("Location: ".$sess->adminUrl("avatar.php?step=add"));
	exit;
	#### hier, wie ein Avatar hinzugefügt wird ###
	}

if($step == 'avat_edit') {
	header("Location: ".$sess->adminUrl("avatar.php?step=edit"));
	exit;
	#### hier, wie ein Avatar geändert wird ###
	}

if($step == 'cat') {
	header("Location: ".$sess->adminUrl("categories.php"));
	exit;
	#### hier, wie eine Kategorie hinzugefügt wird ###
	}

if($step == 'maincat') {
	header("Location: ".$sess->adminUrl("categories.php?step=maincat"));
	exit;
	#### hier, wie eine Kategorie hinzugefügt wird ###
	}
if($step == 'lang') {
	header("Location: ".$sess->adminUrl("language.php?step=lang"));
	exit;
	#### hier, wie eine Kategorie hinzugefügt wird ###
	}		
if($step == 'template_edit') {
	header("Location: ".$sess->adminUrl("templates.php?step=edithtml"));
	exit;
	#### hier, wie eine Kategorie hinzugefügt wird ###
	}		
	
function fileFolderSize() {
	$size = 0;
	$handle = @opendir('./../files/');
	while ($file = @readdir($handle)) {
		if (eregi("^\.{1,2}$",$file))  continue;
		$size += filesize('./../files/'.$file);
	}
	@closedir($handle);  
	return $size;
}    	
	

$result = $db_sql->sql_query("SELECT * FROM $dlcomment_table");
$kum_i = $db_sql->num_rows($result);	

$result2 = $db_sql->sql_query("SELECT * FROM $dl_table");
$dl_i = $db_sql->num_rows($result2);	

$result3 = $db_sql->sql_query("SELECT * FROM $user_table");
$user_i = $db_sql->num_rows($result3);	
$user_i = $user_i - 1;
	
if(BOARD_DRIVER == "default") {
	$result5 = $db_sql->sql_query("SELECT count(activation) as wartend FROM $user_table WHERE activation!='1' && userid!='2'");
	$unconfirmed = $db_sql->fetch_array($result5);
	$unconfirmed = $unconfirmed['wartend'];	
}

$result6 = $db_sql->sql_query("SELECT dlid, dl_date, dlhits, dlsize FROM $dl_table");	
$f_no = 0;
while($traffic = $db_sql->fetch_array($result6)) {
	$traffic_total += $traffic['dlhits'] * $traffic['dlsize'];
	$av_day += $traffic['dlhits']/(((time()-$traffic['dl_date'])/60)/24);
	$f_no++;
}
$average_dl = round($av_day/$f_no,4);

buildAdminHeader();

include_once($_ENGINE['eng_dir']."admin/enginelib/class.bbcode.php");
$bbcode = new engineBBCode(1,0,1,0,$config['smilieurl']);	

include_once($_ENGINE['eng_dir']."admin/enginelib/class.db_utils.php");
$db_utils = new engineMysqlUtils();

if(@file_exists('./../installer.php')) $warning = "<div align=\"center\"><font color=\"#FF0000\" size=\"2\"><b>$a_lang[main_installer]</b></font></div>";

?>
<table width="100%" align="center">
	<tr valign="middle">
		<td><table cellpadding="3" cellspacing="1" width="95%" bgcolor="000000" align="center">
			<tr>
				<td class="menu_desc"><b>&raquo; <?php echo $a_lang['main_welcome']." ".$auth->user['username']; ?></b></td>
			</tr>
			<tr>
				<td class="othercolumn"><b><?php echo $a_lang['main_head']; ?></b></td>
			</tr>
			<tr>
				<td class="menu_desc"><b>&raquo; <?php echo $a_lang['main_stat']; ?></b></td>
			</tr>
			<tr>
				<td class="othercolumn">
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td width="50%"><?php echo $a_lang['main_reguser']; ?>: <b><?php echo "$user_i </b>"; ?></td>
                    <td width="50%">
                    <?php 
					if(BOARD_DRIVER == "default") {
					echo $a_lang['main_notactive']; ?>: <b>
					<?php echo $unconfirmed; ?></b> [<a href="<?php echo $sess->adminUrl("member.php?step=activation") ?>"><?php echo $a_lang['main_activate']; ?></a>]
					<?php }?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $a_lang['main_avdl']; ?>: <b><?php echo $dl_i; ?></b></td>
                    <td><?php echo $a_lang['main_comoverall']; ?>: <b><?php echo $kum_i; ?></b></td>
                </tr>
                <tr>
                    <td><?php echo $a_lang['main_databasesize']; ?>: <b><?php echo rebuildFileSize($db_utils->buildDatabaseSize($dbName)); ?></b></td>
					<td><?php echo $a_lang['main_caused_traffic']; ?>: <b><?php echo rebuildFileSize($traffic_total); ?></b></td>
                </tr>
                <tr>
                    <td><?php echo $a_lang['main_average_downloads_per_day']; ?>: <b><?php echo $average_dl; ?></b></td>
					<td><?php echo $a_lang['main_total_size_of_all_files']; ?>: <b><?php echo rebuildFileSize(fileFolderSize()); ?></b></td>
                </tr>				
                </table>
                <?php echo "<br>".$warning; ?>
                </td>
			</tr>
		</table></td>
	</tr>
</table>
<br>
<br>
<table width="100%" align="center">
	<tr valign="middle">
		<td><table cellpadding="3" cellspacing="1" width="95%" bgcolor="000000" align="center">
			<tr>
				<td class="menu_desc"><b>&raquo; <?php echo $a_lang['main_confirm']; ?></b></td>
			</tr>
			<tr>
				<td class="othercolumn">
<?php

echo "
<p class=\"index\"><img class=\"nav\" src=\"images/new.gif\" width=\"24\" height=\"24\" border=\"0\" align=\"absmiddle\"> $a_lang[main_newfiles]:";
ConfirmFile();
echo"</p>
<p class=\"index\"><img class=\"nav\" src=\"images/new.gif\" width=\"24\" height=\"24\" border=\"0\" align=\"absmiddle\"> $a_lang[main_newcom]:";
ConfirmComment();
echo "</p>
<p class=\"index\"><img class=\"nav\" src=\"images/new.gif\" width=\"24\" height=\"24\" border=\"0\" align=\"absmiddle\"> $a_lang[main_deadlinks]:";
DeadLink();
?>
				</td>
			</tr>
		</table></td>
	</tr>
</table>
<br>

<table width="100%" align="center">
	<tr valign="middle">
		<td><table cellpadding="3" cellspacing="1" width="95%" bgcolor="000000" align="center">
			<tr>
				<td class="menu_desc"><b>&raquo; <?php echo $a_lang['main_imthings']; ?>:</b></td>
			</tr>
			<tr>
				<td class="othercolumn">
				<b>Homepage:</b> <a target="_blank" class="menu" href="http://www.alexscriptengine.de">http://www.alexscriptengine.de</a><br>
				<b>Support:</b> <a target="_blank" class="menu" href="http://www.alexscriptengine.de/v2/prog_engine/bug.php?pid=5">Bug-Tracker</a> | <a target="_blank" class="menu" href="http://www.alexscriptengine.de/v2/prog_engine/request.php?pid=5">Feature-Request</a> | <a target="_blank" class="menu" href="http://www.alexscriptengine.de/v2/prog_engine/faq.php?pid=5">FAQ</a><br>
				<b>PHP:</b> <a target="_blank" class="menu" href="http://www.php.net">PHP - Homepage</a><br>
				<b>MySQL:</b> <a target="_blank" class="menu" href="http://www.mysql.org">MySQL - Homepage</a></td>
			</tr>
			<tr>
				<td class="firstcolumn"><b><?php echo $a_lang['main_installed']; ?>:</b> V<?php echo APP_VERSION; ?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>


<?php

buildAdminFooter();
?>