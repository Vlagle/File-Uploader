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
|	> $Id: dlinfo.php 6 2005-10-08 10:12:03Z alex $
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
function parseDl($dl) {
    return <<<EOT
                  <table width="100%" cellspacing="2" cellpadding="2" border="0">
                    $dl
                  </table>
				  <hr />
EOT;
}


function parseDlLoop($dl) {
    return <<<EOT
                    <tr> 
                      <td>
					  	{$dl[no]} - <a href="{$dl[new_file_url]}"><b>{$dl[new_file_filename]}</b></a><br />{$dl[misc_top_category]}</td>
                      <td>{$dl[misc_new_file_hits]}</td>
                      <td>{$dl[misc_upload_dtd]}</td>
                    </tr>
EOT;
}

// ----------------------------------------------------------------
// --------------- ab hier keine Anpassungen mehr notwendig -------
// ----------------------------------------------------------------
error_reporting(E_ALL & ~E_NOTICE);

require_once($path2dl."include/config.inc.php");
if(!class_exists(db_sql)) require_once($path2dl."admin/enginelib/class.db.php");
if(!$info_added) require_once($path2dl."admin/enginelib/driver/function.driver.".BOARD_DRIVER.".php");

$dl_sql = new db_sql($dbName,$hostname,$dbUname,$dbPasswort);
// ----------------------------------------------------------------
//----------------------------------- Functions Start -------------
// ----------------------------------------------------------------

/**
* loadEngineSettingDl()
*
* Einstellungen der Engine laden, und im Array
* $setting speichern, Url im Array $_ENGINE ablegen
*/
function loadEngineSettingDl() {
    global $dl_sql,$set_table,$_ENGINE;
    $result = $dl_sql->sql_query("SELECT * FROM $set_table");
    while($set = $dl_sql->fetch_array($result)) {
        $set = stripslashesDlArray($set);
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
function GetGerDayDl($day_number) {
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
function GetGerMonthDl($month_number) {		
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
function aseDateDl($format,$timestamp,$month=0) {
	global $setting;
	$time = $timestamp+(3600*$setting['timeoffset']);	
	if($month && (eregi(m,$format) || eregi(n,$format))) {
		$month = GetGerMonthDl(date(n,$time));
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
function dlUrl($filename) {
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
function stripslashesDlArray(&$array) {
    reset($array);
    if(is_array($array)) {
    	foreach ($array as $key => $val) {
    		$array[$key] = (is_array($val)) ? stripslashesDlArray($val) : stripslashes($val);
   		}
      	return $array;
	}	
}	

// ----------------------------------------------------------------
//----------------------------------- Functions END ---------------
// ----------------------------------------------------------------
$info_added = true;
$setting = loadEngineSettingDl();

$lang = array();
require_once($path2dl."lang/".$setting['language']."/".$setting['language'].".php");

$result2 = $dl_sql->sql_query("SELECT $dl_table.*, $cat_table.titel FROM $dl_table 
                                LEFT JOIN $cat_table ON ($cat_table.catid = $dl_table.catid)
                                WHERE $dl_table.status!='3' ORDER BY $dl_table.dl_date DESC LIMIT 0,$setting[newlist_q]");
                                
if($dl_sql->num_rows($result2) >= 1) { 
    $no = 1;                               
    while($dow = $dl_sql->fetch_array($result2)) {
        $dow = stripslashesDlArray($dow);
   		$upl_date = aseDateDl($setting['longdate'],$dow['dl_date']); 
        $file = array('no' => $no,
                        'new_file_url' => dlUrl('comment.php?dlid='.$dow['dlid']),
                        'new_file_filename' => $dow['dltitle'],
                        'misc_upload_dtd' => sprintf($lang['misc_upload_dtd'],aseDateDl($setting['shortdate'],$dow['dl_date'])),
                        'misc_top_category' => sprintf($lang['misc_top_category'],dlUrl('index.php?subcat='.$dow['catid']),$dow['titel']),									
                        'misc_new_file_hits' => sprintf($lang['misc_new_file_hits'],$dow['dlhits']));        
        $parser .= parseDlLoop($file);
        $no++;
    }
	
} else {

}	

echo parseDl($parser);
?>
