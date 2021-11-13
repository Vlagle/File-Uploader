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
|   > externe Datei um überall einzubinden
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: dlstatsinfo.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

// Diese Zeile anpassen, um die News per include-Befehl einzubinden
$path2dl = "c:/inetpub/wwwroot/projekte/tpl_dl/";

// Das Design der Ausgabe wird in folgender Funktion angepasst.
// Zwischen <<<EOT und EOT; kann normaler HTML-Code stehen.
// die Platzhalter stehen immer in geschweiften Klammern {}
//-------------------------------------------------------------------
// Folgende Befehle können VOR dem include verwendet werden:
//
function parseDlStats($stats) {
    global $lang;
    return <<<EOT
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr> 
            <td><b>{$lang[index_statistics]}</b></td>
        </tr>
        <tr> 
            <td> 
                {$lang[index_number_of_files]} <b>{$stats[number_of_files]}</b><br />
                {$lang[index_total_size_of_files]} <b>{$stats[total_file_size]}</b><br />
                {$lang[index_number_of_categories]} <b>{$stats[number_of_categories]}</b><br />
                {$lang[index_last_file]} <b><a href="{$stats[dlurl]}">{$stats[dltitle]}</a></b><br />
                <hr width="95%" size="1" noshade="noshade" /> 
                {$lang[index_number_of_users]} <b>{$stats[number_of_registered_users]}</b><br />
            </td>
        </tr>
    </table>
EOT;
}

// ----------------------------------------------------------------
// --------------- ab hier keine Anpassungen mehr notwendig -------
// ----------------------------------------------------------------
error_reporting(E_ALL & ~E_NOTICE);

require_once($path2dl."include/config.inc.php");
if(!class_exists(db_sql)) require_once($path2dl."admin/enginelib/class.db.php");
if(!$info_added) require_once($path2dl."admin/enginelib/driver/function.driver.".BOARD_DRIVER.".php");

$dl_stat_sql = new db_sql($dbName,$hostname,$dbUname,$dbPasswort);
// ----------------------------------------------------------------
//----------------------------------- Functions Start -------------
// ----------------------------------------------------------------

/**
* loadEngineSettingDl()
*
* Einstellungen der Engine laden, und im Array
* $setting speichern, Url im Array $_ENGINE ablegen
*/
function loadEngineSettingDlStats() {
    global $dl_stat_sql,$set_table,$_ENGINE;
    
    $result = $dl_stat_sql->sql_query("SELECT * FROM $set_table");
    while($set = $dl_stat_sql->fetch_array($result)) {
        $set = stripslashesDlStatsArray($set);
        $setting[$set['find_word']] = $set['replace_value'];
    }
    
    $_ENGINE['main_url'] = $setting['dlscripturl'];
    $_ENGINE['languageurl'] = $_ENGINE['main_url']."/lang/".$setting['language']."/images";  
    $_ENGINE['std_group'] = $setting['std_group']; 
    $setting['engine_mainurl'] = $setting['dlscripturl'];
      
    return $setting;    
}

/**
* GetGerDayDl()
*
* Erstellt Datum - Tag
*/
function GetGerDayDlStats($day_number) {
    global $lang;
    $name_tag[0] = $lang['php_fu_day_0'];
    $name_tag[1] = $lang['php_fu_day_1'];
    $name_tag[2] = $lang['php_fu_day_2'];
    $name_tag[3] = $lang['php_fu_day_3'];
    $name_tag[4] = $lang['php_fu_day_4'];
    $name_tag[5] = $lang['php_fu_day_5'];
    $name_tag[6] = $lang['php_fu_day_6'];
    
    return $name_tag[$day_number];
}
	
/**
* GetGerMonthDl()
*
* Erstellt Datum - Monat
*/		
function GetGerMonthDlStats($month_number) {		
    global $lang;
    $name_monat[1] = $lang['php_fu_month_1'];
    $name_monat[2] = $lang['php_fu_month_2'];
    $name_monat[3] = $lang['php_fu_month_3'];
    $name_monat[4] = $lang['php_fu_month_4'];
    $name_monat[5] = $lang['php_fu_month_5'];
    $name_monat[6] = $lang['php_fu_month_6'];
    $name_monat[7] = $lang['php_fu_month_7'];
    $name_monat[8] = $lang['php_fu_month_8'];
    $name_monat[9] = $lang['php_fu_month_9'];
    $name_monat[10] = $lang['php_fu_month_10'];
    $name_monat[11] = $lang['php_fu_month_11'];
    $name_monat[12] = $lang['php_fu_month_12'];
    
    return $name_monat[$month_number];
}		

/**
* aseDateDl()
*
* Datum auf Basis der DB-Abfragen erstellen
* @param string $format
* @param integer $timeformat
* @param integer $month
*/
function aseDateDlStats($format,$timestamp,$month=0) {
	global $setting;
	$time = $timestamp+(3600*$setting['timeoffset']);	
	if($month && (eregi(m,$format) || eregi(n,$format))) {
		$month = GetGerMonthDlStats(date(n,$time));
		$output = date(d,$time).". ".$month." ".date(Y,$time);
	} else {
		$output = date("$format",$time);
	}
	return $output;
}	

/**
* dlUrl()
*
* Gibt die Url zu einer Seite inkl.Query-String zurück
* 
* @access public
* @param string $filename
* @return string
*/
function dlStatsUrl($filename) {
    global $_ENGINE;
    $return_url = $_ENGINE['main_url']."/".$filename;
    return $return_url;

}

/**
* stripslashesDlArray()
*
* Führt die Funktion stripslashes auf ein Array aus
* @param array $array
*/
function stripslashesDlStatsArray(&$array) {
    reset($array);
    if(is_array($array)) {
    	foreach ($array as $key => $val) {
    		$array[$key] = (is_array($val)) ? stripslashesDlStatsArray($val) : stripslashes($val);
   		}
      	return $array;
	}	
}	

function buildDLStatsFileSize($fsize) {
	$fsize = intval($fsize);
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

function realDlStatsFileSize() {
    global $path2dl;
	$size = 0;
	$handle = @opendir($path2dl.'files/');
	while ($file = @readdir($handle)) {
		if (eregi("^\.{1,2}$",$file))  continue;
		$size += filesize($path2dl.'files/'.$file);
	}
	@closedir($handle);  
	return $size;
}  

// ----------------------------------------------------------------
//----------------------------------- Functions END ---------------
// ----------------------------------------------------------------
$info_added = true;

$setting = loadEngineSettingDlStats();

$lang = array();
require_once($path2dl."lang/".$setting['language']."/".$setting['language'].".php");

$downloads = $dl_stat_sql->sql_query("SELECT dlid FROM $dl_table WHERE status!='3'");
$dls = $dl_stat_sql->num_rows($downloads);

$categories = $dl_stat_sql->sql_query("SELECT catid FROM $cat_table");
$categ = $dl_stat_sql->num_rows($categories);
$memb = $dl_stat_sql->sql_query("SELECT $userid_table_column FROM $user_table");
$member = $dl_stat_sql->num_rows($memb);
$member = $member-1;
if ($member >= 1 && BOARD_DRIVER == 'default') {
    $lm = list($username,$useremail) = $dl_stat_sql->sql_fetch_row("SELECT $username_table_column AS username, $useremail_table_column AS useremail FROM $user_table ORDER BY regdate DESC LIMIT 1");
    $username = stripslashes($username);
    $useremail = stripslashes($useremail);
} else {
    $username = "---";
}
$lfile = list($dlid,$dl_date,$dltitle,$dlurl) = $dl_stat_sql->sql_fetch_row("SELECT dlid,dl_date,dltitle,dlurl FROM $dl_table WHERE status!='3' ORDER BY dl_date DESC LIMIT 1");
$stats['dltitle'] = stripslashes($dltitle);
$stats['filedate'] = getdate($dl_date);
$stats['dlurl'] = urlencode($dlurl);
$stats['all_files'] = sprintf($lang['cat_stats_allfiles1'],$dls,$categ);
$stats['dlurl'] = $setting['dlscripturl']."/comment.php?dlid=".$dlid;
$stats['dltitle'] = $dltitle;
$stats['number_of_categories'] = $categ;
$stats['number_of_files'] = $dls;
$stats['number_of_registered_users'] = $member;
$stats['total_file_size'] = buildDLStatsFileSize(realDlStatsFileSize());

echo parseDlStats($stats);
?>
