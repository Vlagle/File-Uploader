<?php
// +----------------------------------------------------------------------+
// | EngineLib - Global Functions                                         |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003,2004 AlexScriptEngine - e-Visions                 |
// +----------------------------------------------------------------------+
// | This code is not freeware. Please read our licence condition care-   |
// | fully to find out more. If there are any doubts please ask at the    |
// | Support Forum                                                        |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Author: Alex Höntschel <info@alexscriptengine.de>                    |
// | Web: http://www.alexscriptengine.de                                  |
// | IMPORTANT: No email support, please use the support forum at         |
// |            http://www.alexscriptengine.de                            |
// +----------------------------------------------------------------------+
// $Id: function.global.php 6 2005-10-08 10:12:03Z alex $

/**
 * rideSite()
 * 
 * Weiterleitung der Seiten
 * Kann für alle Aktionen verwendet werden, um 
 * dem User eine Information anzuzeigen und auf eine bestimmte
 * Seite weiterzuleiten
 * 
 * @param string $ride_url 
 * @param string $info
 * @return 
 */
function rideSite($ride_url, $info) {
	global $lang, $tpl, $develope;
    $tpl->loadFile('main', 'action_ride.html');
    $tpl->register(array('url' => $ride_url,
                        'info'=>$info,
                        'action_ride_info' => $lang['action_ride'],
                        'title' => "Weiterleitung")); 
    $tpl->register('query', showQueries($develope));
    $tpl->pprint('main');
    //eval("dooutput(\"".gettemplate("action_ride")."\");");	 
}

/**
 * showLoginScreen()
 * 
 * Anzeigen des Login-Screens, kann aus allen 
 * Seiten aufgerufen werden.
 * 
 * @param string $message
 * @param string $info
 * @param string $add_info
 * @param string $action
 * @return 
 */
function showLoginScreen($message, $info, $add_info="&nbsp;", $action="") {
	global $tpl,$GLOBALS,$sess,$stylesheetUrl,$imageUrl;
	if(!$action) $action = $sess->adminUrl("index.php");
	$tpl->set_file(array(
					"main"=>"main_header.html",
					"content"=>"login.html"));
					
	$tpl->set_var(array(
					"PAGE_HEADER"=>"Authentifizierung",
					"CHARSET"=>$GLOBALS['charset'],
                    "STYLE"=>$stylesheetUrl,
					"MESSAGE"=>$message,
                    "IMAGEURL"=>$imageUrl,
					"INFO"=>$info,
					"ACTION_URL"=>$action,
					"ADDITIONAL_INFO"=>$add_info));					
	$tpl->parse("PAGE_CONTENT","content",true);
	
	$tpl->pparse('Output', 'main');
}

/**
* addslashes_array()
*
* Führt die Funktion addslashes auf ein Array aus
* @param array $array
*/
function addslashes_array(&$array) {
    reset($array);
    if(is_array($array)) {    
    	foreach ($array as $key => $val) {
    		$array[$key] = (is_array($val)) ? addslashes_array($val) : addslashes($val);
    	}
      	return $array;
    }
}
	
/**
* stripslashes_array()
*
* Führt die Funktion stripslashes auf ein Array aus
* @param array $array
*/
function stripslashes_array(&$array) {
    reset($array);
    if(is_array($array)) {
    	foreach ($array as $key => $val) {
    		$array[$key] = (is_array($val)) ? stripslashes_array($val) : stripslashes($val);
   		}
      	return $array;
	}	
}	

/**
* fill_new_vars()
*
* Bei PHP-Versionen < 4.1.0 sind die globalen Variablen
* _GET, _POST, _REQUEST, _FILES, _ENV, _SESSION, _SERVER
* nicht gesetzt. fill_new_vars wandelt die HTTP_*_VARS in
* die 'neuen' globalen Variablen um
*
*/
function fill_new_vars() {
	global $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_COOKIE_VARS, $HTTP_SESSION_VARS, $HTTP_POST_FILES, $HTTP_SERVER_VARS, $HTTP_ENV_VARS; // INPUT VARS
	global $_REQUEST, $_COOKIE, $_POST, $_GET, $_SERVER, $_FILES,$_ENV,$_SESSION; // OUTPUT VARS
	// Variablen des Post-Parameters einlesen
	if(is_array($HTTP_POST_VARS)) {
		foreach($HTTP_POST_VARS as $var=>$value) {
			$_REQUEST[$var] = $value;
			$_POST[$var] = $value;
		}			
	}
	// Variablen des GET-Parameters einlesen
	if(is_array($HTTP_GET_VARS)) {
		foreach($HTTP_GET_VARS as $var=>$value) {
			$_REQUEST[$var] = $value;
			$_GET[$var] = $value;
		}			
	}		
	// Variablen des COOKIE-Parameters einlesen
	if(is_array($HTTP_COOKIE_VARS)) {
		foreach($HTTP_COOKIE_VARS as $var=>$value) {
			$_REQUEST[$var] = $value;
			$_COOKIE[$var] = $value;
		}			
	}		
	// Variablen des SESSION-Parameters einlesen (ohne $_REQUEST)
	if(is_array($HTTP_SESSION_VARS)) {
		foreach($HTTP_SESSION_VARS as $var=>$value) {
			$_SESSION[$var] = $value;
		}			
	}		
	// Variablen des FILES-Parameters einlesen (ohne $_REQUEST)
	if(is_array($HTTP_FILES_VARS)) {
		foreach($HTTP_FILES_VARS as $var=>$value) {
			$_FILES[$var] = $value;
		}			
	}		
	// Variablen des SERVER-Parameters einlesen (ohne $_REQUEST)	
	if(is_array($HTTP_SERVER_VARS)) {
		foreach($HTTP_SERVER_VARS as $var=>$value) {
			$_SERVER[$var] = $value;
		}			
	}		
	// Variablen des ENV-Parameters einlesen (ohne $_REQUEST)			
	if(is_array($HTTP_ENV_VARS)) {
		foreach($HTTP_ENV_VARS as $var=>$value) {
			$_ENV[$var] = $value;
		}			
	}			
}
	
// Erweiterung der Upload Funktion
// siehe http://www.php.net/manual/de/function.is-uploaded-file.php	
if (!function_exists("is_uploaded_file") and get_cfg_var("safe_mode")==0) {
	function is_uploaded_file($filename) {
    		if (!$tmp_file = get_cfg_var('upload_tmp_dir')) $tmp_file = dirname(tempnam('', ''));
    		$tmp_file .= '/' . basename($filename);
    		return (ereg_replace('/+', '/', $tmp_file) == $filename);
  	} 
	function move_uploaded_file($filename, $destination) {
     		if (is_uploaded_file($filename))  {
       			if (copy($filename,$destination)) return true;
       			else return false;
       		}
      		else return false;
   	}
}	

/**
* reBuildURL()
*
* Durchläuft eine Internetadresse und fügt ggfs.
* http:// davor
*/
function reBuildURL($url) {
    if($url!="") {
        if(strtolower(substr($url,0,7))!="http://") $url="http://".$url;
    }
    return $url;
}	

/**
* getPHPVersion()
*
* PHP-Version auslesen und zurückgeben
* Format: z. B. 410
*/
function getPHPVersion() {
    $phpversion=(int)(str_replace(".","",phpversion()));
    return $phpversion;
}

/**
 * engineErrorHandler()
 * 
 * Eigener Error-Handler der Engines.
 * Wird mit trigger_error aufgerufen
 */
function engineErrorHandler($type, $msg, $file, $line, $context) {
	switch($type) {
		// user-triggered fatal error
		case E_USER_ERROR:
			$info = "<font color=red><b>A fatal error occurred</b> - Script	terminated</font><br>";
			displayError($type, $msg, $file, $line, $info);
			die();
			break;
		// user-triggered warning
		case E_USER_WARNING:
			$info = "A non-trivial, non-fatal error occurred<br>";
			displayError($type, $msg, $file, $line, $info);
			break;
		// user-triggered notice
		case E_USER_NOTICE:
			$info = "A trivial, non-fatal error occurred<br>";
			displayError($type, $msg, $file, $line, $info);
			break;
	}
}

/**
 * displayError()
 * 
 * Anzeigen der eigentlichen Fehlermeldung 
 */
function displayError($type, $msg, $file, $line, $info) {
	// read some environment variables
	// these can be used to provide some additional debug information
	global $HTTP_HOST, $HTTP_USER_AGENT, $REMOTE_ADDR,$REQUEST_URI;
	// define the log file
	$errorLog = "error.log";
	// construct the error string
	$errorString = "<b>Date:</b> " . date("d-m-Y H:i:s", mktime()) ."\n<br><br>";
	$errorString .= "<b>Error message:</b> ".$msg."\n<br><br>";
	$errorString .= "<b>Script:</b> ".$file." (<b> on 	Line:</b> ".$line.")\n<br>";
    $errorString .= "<b>Referer:</b> ".getenv("HTTP_REFERER")."\n<br>";	
	$errorString .= "<b>Client IP:</b> ".$REMOTE_ADDR."\n<br>";
	// log the error string to the specified log file
	//error_log($errorString, 3, $errorLog);
	echo "
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>:: ERROR ::</title>
</head>
<style type=\"text/css\">
body {
	background-color: #FFF;
	font-family: Verdana, Helvetica, sans-serif;
	font-size: 10pt;
}

div, li, p, td, th {
	font-family: Verdana, Helvetica, sans-serif;
	font-size: 10pt;
}
</style>
<body>

<table align=\"center\" bgcolor=\"#ff0000\" cellpadding=\"0\" cellspacing=\"0\">
	<tr valign=\"middle\" align=\"center\">
		<td>
		<table width=\"100%\" bgcolor=\"#ff0000\" cellpadding=\"5\" cellspacing=\"1\">
			<tr valign=\"middle\" align=\"center\">
				<td><font color=\"#FFFFFF\"><b>Following Error occured: (Type ".$type.")</b></font>
				</td>
			</tr>
			<tr bgcolor=\"#ffffff\" valign=\"middle\">
				<td align=\"center\">
				".$info."
				</td>
			</tr>			
			<tr bgcolor=\"#ffffff\" valign=\"middle\">
				<td>
				".$errorString."
				</td>
			</tr>
			<tr bgcolor=\"#ffffff\" valign=\"middle\">
				<td align=\"center\">
				<font size=\"1\">If you ask for support, please give us these information. Thank you.</font>
				</td>
			</tr>			
		</table>
		</td>
	</tr>
</table>	
</body>
</html>	
	";
	exit();
}

/**
* loadEngineSetting()
*
* Einstellungen der Engine laden, und im Array
* $config speichern, Url im Array $_ENGINE ablegen
*/
function loadEngineSetting() {
    global $db_sql,$set_table,$_ENGINE;
    
    $result = $db_sql->sql_query("SELECT * FROM $set_table");
    while($set = $db_sql->fetch_array($result)) {
        $set = stripslashes_array($set);
        $config[$set['find_word']] = $set['replace_value'];
    }
    
    $_ENGINE['main_url'] = $config['dlscripturl'];
    $_ENGINE['languageurl'] = $_ENGINE['main_url']."/lang/".$config['language']."/images";  
    $_ENGINE['std_group'] = $config['std_group']; 
    $config['engine_mainurl'] = $config['dlscripturl'];
      
    return $config;    
}

/**
* checkVariable()
*
* Prüft eingehende Variablen
*
* @param string $varname
* @param integer $isint
*/
function checkVariable($varname,$isint=0) {
    global $_POST, $_GET;
    if(isset($_POST[$varname]) || isset($_GET[$varname])) {
		if(isset($_GET[$varname])) {
			$varvalue = stripslashes(trim($_GET[$varname]));
			if($isint === 1) $varvalue = (int)$varvalue;
			$_GET[$varname] = $varvalue;
		} else {
			$varvalue = stripslashes(trim($_POST[$varname]));
			if($isint === 1) $varvalue = (int)$varvalue;
			$_POST[$varname] = $varvalue;
		}
    } else {
    	unset($varname);
    }
}

/**
* newUser()
*
* Fügt einen neuen User in die Datenbank ein
* Die GroupID wird im AdminCenter vorgegeben
* Zurückgeliefert wird die ID der Datenbank
*/
function newUser($login,$pass) {
    global $user_table,$db_sql,$config;
    $today = time();
    $pass = addslashes(md5($pass));
    $login = addslashes(htmlspecialchars(trim($login)));
    $result = $db_sql->sql_query("INSERT INTO $user_table (username, userpassword, regdate, lastvisit, groupid, activation) VALUES('$login','$pass', '$today', '$today', '$config[std_group]', '1')");
    return mysql_insert_id();
}

/**
* isEmail()
*
* Überprüft eine Email-Adresse im Format
* asdf123@asdf123.1234
* liefert 1 bei Erfolg zurück
*/
function isEmail($umail) {
  if (!eregi("^[0-9a-z]([-_.]*[0-9a-z]*)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|shop)$",$umail)) {
     $ismail = 0;
  } else {
     $ismail=1;
  }
  return $ismail;
}

/**
* GetGerDay()
*
* Erstellt Datum - Tag
*/
function GetGerDay($day_number) {
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
* GetGerMonth()
*
* Erstellt Datum - Monat
*/		
function GetGerMonth($month_number) {		
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
* aseDate()
*
* Datum auf Basis der DB-Abfragen erstellen
* @param string $format
* @param integer $timeformat
* @param integer $month
*/
function aseDate($format,$timestamp,$month=0) {
	global $config;
	$time = $timestamp+(3600*$config['timeoffset']);	
	if($month && (eregi(m,$format) || eregi(n,$format))) {
		$month = GetGerMonth(date(n,$time));
		$output = date(d,$time).". ".$month." ".date(Y,$time);
	} else {
		$output = date("$format",$time);
	}
	return $output;
}	

/**
* LoadTime()
*
* Erstellt Dateigrösse aus dem Bytewert der Datei
*/
function LoadTime($dlsize) {
    global $config,$lang;
    $dltime = round(($dlsize * 8)/64000,2);
    if ($dlsize > 250000) {
        $dlsize = round(($dlsize/1024)/1024,2);
        $dlsize = "$dlsize MB";
    } else {
        $dlsize = round($dlsize/1024,2);
        $dlsize = "$dlsize kB";
    }
    if ($dltime > 100) {
        $min = floor($dltime / 60);
        $sek = ($min*60)-$min;
        $loadtime = sprintf($lang['index_total_file_size'],$dlsize,$min.",".$sek,$lang['index_min_by_isdn']);
    } else {
        $loadtime = sprintf($lang['index_total_file_size'],$dlsize,$dltime,$lang['index_sek_by_isdn']);
    }
    return $loadtime;
}
	
/**
* initStandardVars()
*
* Initialisiert die Standard-Templatevariablen
*/
function initStandardVars() {
    global $tpl, $config, $sess, $auth, $lang, $_ENGINE;
    $tpl->register('STYLESHEET', $config['dlscripturl']."/templates/".$config['template_folder']."/style.css");    
    $tpl->register('GRAFURL', $config['grafurl']);
    $tpl->register('MAINURL', $_ENGINE['main_url']);
    $tpl->register('SMILIEURL', $config['smilieurl']);
    $tpl->register('DOCTYPE', $lang['doctype']);
    $tpl->register('CHARSET', $lang['charset']);
    $tpl->register('DIR', $lang['dir']);
    $tpl->register('LANG', $lang['lang']);    
    $tpl->register('SESS_NAME', $sess->sess_name);
    $tpl->register('SESS_ID', $sess->sess_id); 
    $tpl->register('USERNAME', $auth->user['username']);  
    $tpl->register('MAINWIDTH', $config['mainwidth']);     
    $tpl->register('AVATURL', $config['avaturl']);
    $tpl->register('LANGUAGEURL', $config['language']);
    $tpl->register('ROW_TOP_BORDER_COLOR', $config['row_top_border_color']);
    $tpl->register('ROW_TOP_BACKGROUND_COLOR', $config['row_top_background_color']);
    $tpl->register('CONTENT_BORDER_COLOR', $config['content_border_color']);
    $tpl->register('ROW_BOTTOM_BORDER_COLOR', $config['row_bottom_border_color']);
    $tpl->register('ROW_BOTTOM_BACKGROUND_COLOR', $config['row_bottom_background_color']);
    $tpl->register('BODY_BACKGROUND_COLOR', $config['body_background_color']);
}

function getUserOS() {
	global $HTTP_USER_AGENT, $HTTP_SERVER_VARS;
	if (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
		$HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
	} elseif (getenv("HTTP_USER_AGENT")) {
		$HTTP_USER_AGENT = getenv("HTTP_USER_AGENT");
	} elseif (empty($HTTP_USER_AGENT)) {
		$HTTP_USER_AGENT = "";
	}
	
	if (eregi("Win", $HTTP_USER_AGENT)) {
		$user_os = "WIN";
	} elseif (eregi("Mac", $HTTP_USER_AGENT)) {
		$user_os = "MAC";
	} else {
		$user_os = "OTHER";
	}
	return $user_os;
}

function getBrowserInfo() {
	global $HTTP_USER_AGENT, $HTTP_SERVER_VARS;
	if (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
		$HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
	} elseif (getenv("HTTP_USER_AGENT")) {
		$HTTP_USER_AGENT = getenv("HTTP_USER_AGENT");
	} elseif (empty($HTTP_USER_AGENT)) {
		$HTTP_USER_AGENT = "";
	}
	
	if (eregi("MSIE ([0-9].[0-9]{1,2})", $HTTP_USER_AGENT, $regs)) {
		$browser_info['browser_agent'] = "MSIE";
		$browser_info['browser_version'] = $regs[1];
	} elseif (eregi("Mozilla/([0-9].[0-9]{1,2})", $HTTP_USER_AGENT, $regs)) {
		$browser_info['browser_agent'] = "MOZILLA";
		$browser_info['browser_version'] = $regs[1];
	} elseif (eregi("Opera(/| )([0-9].[0-9]{1,2})", $HTTP_USER_AGENT, $regs)) {
		$browser_info['browser_agent'] = "OPERA";
		$browser_info['browser_version'] = $regs[2];
	} else {
		$browser_info['browser_agent'] = "OTHER";
		$browser_info['browser_version'] = 0;
	}
	return $browser_info['browser_agent'];
}			

// Benchmark Timer	
/**
* startTimer()
*
* Benchmark Timer aktivieren
*/
function startTimer() {
    global $starttime;
    $starttime = microtime();
    return $starttime;
}

/**
* endTimer()
*
* Benchmark Timer beenden
*/
function endTimer() {
    global $starttime;
    $pageendtime = microtime();
    $starttime = explode(" ",$starttime);
    $endtime = explode(" ",$pageendtime);
    $totaltime = $endtime[0]-$starttime[0]+$endtime[1]-$starttime[1];
    $totaltime = round($totaltime, 5);
    return $totaltime;
}
	
/**
* showQueries()
*
* Anzahl der Datenbankqueries und GZIP-Level ausgeben
* @param integer $develope
*/
function showQueries($develope=0) {
    global $query_count,$config,$db_sql,$sess;
    if ($develope == 1) {
        $totaltime = endTimer();
        if($config['activategzip'] == 0) {
            $gzip = "nicht aktiviert";
        } else {
            $gzip = "aktiviert - Level: $config[gziplevel]";
        }
		 
       	/*$q_print .= "<br><table width=\"{mainwidth}\" align=\"center\" cellpadding=\"0\" cellspacing=\"1\" bgcolor=\"{bordercol}\">
      					<tr>
      					 <td>
      					  <table width=\"100%\" align=\"center\" cellpadding=\"4\" cellspacing=\"1\" bgcolor=\"{primcol}\">
      					<tr>
      					  <td colspan=\"2\" class=\"navichain\" align=\"center\"><strong>Gesendete Queries</strong></td>
      					</tr>";
      					
       	foreach($db_sql->test["q_cache"] as $query)
       	{
       		$query = preg_replace( "/^SELECT/i" , "<font style=\"color:red;font-weight:bold\">SELECT</font>"   , $query );
       		$query = preg_replace( "/^UPDATE/i" , "<font style=\"color:blue;font-weight:bold\">UPDATE</font>"  , $query );
       		$query = preg_replace( "/^DELETE/i" , "<font style=\"color:orange;font-weight:bold\">DELETE</font>", $query );
       		$query = preg_replace( "/^INSERT/i" , "<font style=\"color:green;font-weight:bold\">INSERT</font>" , $query );
       		
       		$q_print .= "<tr><td class=\"incat\">$query</td></tr>";
       	}
       	
       	$q_print .= "</table></td></tr></table>";*/
		
		
	$query = "<br>\n<div class=\"footer\" align=\"center\">[ Script-Ausf&uuml;hrungszeit: $totaltime Sekunden ]  [ Ben&ouml;tigte Queries: $query_count ] [GZIP: $gzip ]</div>".$q_print;
    //$query .= "<br>".$sess->showEngineSess();
	}	
	
	return $query;
}	
?>
