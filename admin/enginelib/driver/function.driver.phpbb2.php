<?php
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
//

/**
* Definiert die Url zum Board, sofern es sich um einen Treiber
* für VB2, VB3, WBB2 oder ähnlichem handelt
* OHNE ABSCHLIESSENDEN /
*/
$UrlToBoard = "http://127.0.0.1/conn/phpbb2";

/**
* Tabellenprefix der PHPBB2 Tabellen
*/
$table_prefix = 'phpbb_';

//-------------------------------------------------------------------------
//-------------- Ab hier keine Einstellungen notwendig --------------------
//-------------- No additional settings necessary -------------------------
//-------------------------------------------------------------------------

/**
* Definiert die Tabellenspalte in der die Timestamp für den
* letzten Besuch steht
*/
$lastvisit_table_column = "user_lastvisit";

/**
* Definiert die Tabellenspalte in der die UserID steht
*/
$userid_table_column = "user_id";

/**
* Definiert die Tabellenspalte in der der Username steht
*/
$username_table_column = "username";

/**
* Definiert die Tabellenspalte in der die Email des Users steht
*/
$useremail_table_column = "user_email";

/**
* Definiert die Tabellenspalte in der die Homepage des Users steht
*/
$userhp_table_column = "user_website";

/**
* Definiert die Tabellenspalte in der steht ob die Email des Users angezeigt werden soll
*/
$showmail_table_column = "user_viewemail";

/**
* Aktiviert bzw. deaktiviert das Engineeigene Avatarhandling
*/
define('USE_ENGINE_AVATARS',false);

define('DELETED', -1);
define('ANONYMOUS', -1);

define('USER', 0);
define('ADMIN', 1);
define('MOD', 2);

// Table names
define('AUTH_ACCESS_TABLE', $table_prefix.'auth_access');
define('CONFIG_TABLE', $table_prefix.'config');
define('GROUPS_TABLE', $table_prefix.'groups');
define('SESSIONS_TABLE', $table_prefix.'sessions');
define('USER_GROUP_TABLE', $table_prefix.'user_group');
define('USERS_TABLE', $table_prefix.'users');

/**
 * getUserByName()
 * 
 * Holt den User anhand von Username und Passwort aus
 * der Datenbank, ggfs. nur wenn aktiviert
 * Ersetzt gleichnamige Funktion aus der Klasse Auth
 * 
 * @param $name 
 * @param $pw
 * @param $enableactivation
 * @return 
 */
function getUserByName($name,$pw,$enableactivation) {
	global $user_table, $group_table, $db_sql, $email_option, $table_prefix;
	
	if($enableactivation) $more_sql = "AND user_active='1'";	
    
    $username = trim(htmlspecialchars($name));
    $username = substr(str_replace("\\'", "'", $username), 0, 25);
    $username = str_replace("'", "\\'", $username);    
	
	$sql = "
		SELECT 
			".$table_prefix."users.user_id AS userid,
			".$table_prefix."users.username AS username,
			".$table_prefix."users.user_password AS userpassword,
            ".$table_prefix."users.user_lastvisit AS lastvisit,
            ".$table_prefix."users.user_regdate AS regdate,
            ".$table_prefix."users.user_level AS user_level,
            ".$table_prefix."users.user_viewemail AS show_email_global,
            ".$table_prefix."users.user_email AS useremail,
            ".$table_prefix."users.user_icq AS usericq,
			".$table_prefix."users.user_website AS userhp,
			".$table_prefix."users.user_from AS location,
			".$table_prefix."users.user_aim AS aim,
			".$table_prefix."users.user_yim AS yim,
			".$table_prefix."users.user_interests AS interests,
            ".$table_prefix."user_group.group_id AS board_groupid
		FROM ".$table_prefix."users
        LEFT JOIN ".$table_prefix."user_group ON (".$table_prefix."user_group.user_id = ".$table_prefix."users.user_id)
		WHERE username = '" . str_replace("\\'", "''", $username) . "' AND user_password = '".md5($pw)."'";
		
	$result = $db_sql->sql_query($sql);
	if ($db_sql->num_rows($result) != 1) {
		return false;
	} else {
		$user = $db_sql->fetch_array($result);
		$engine_group = $db_sql->query_array("SELECT engine_groupid AS groupid, $group_table.* FROM groups_engine2board 
											LEFT JOIN ".$group_table." ON (".$group_table.".groupid = groups_engine2board.engine_groupid)
											WHERE board_groupid='".$user['board_groupid']."'");		
		$user['userfound'] = true;
		$user = array_merge($user,$engine_group);
		return stripslashes_array($user);
	}
}

/**
 * getUserByID()
 * 
 * Holt den User anhand der User-ID aus
 * der Datenbank, ggfs. nur wenn aktiviert 
 * Ersetzt gleichnamige Funktion aus der Klasse Auth
 * 
 * @param $id
 * @param $enableactivation
 * @return 
 */
function getUserByID($id,$enableactivation=false) {
	global $user_table, $group_table, $db_sql, $email_option, $table_prefix;
	if($enableactivation) $more_sql = "AND activation='1'";	
	
	$sql = "
		SELECT 
			".$table_prefix."users.user_id AS userid,
			".$table_prefix."users.username AS username,
			".$table_prefix."users.user_password AS userpassword,
            ".$table_prefix."users.user_lastvisit AS lastvisit,
            ".$table_prefix."users.user_regdate AS regdate,
            ".$table_prefix."users.user_level AS user_level,
            ".$table_prefix."users.user_viewemail AS show_email_global,
            ".$table_prefix."users.user_email AS useremail,
            ".$table_prefix."users.user_icq AS usericq,
			".$table_prefix."users.user_website AS userhp,
			".$table_prefix."users.user_from AS location,
			".$table_prefix."users.user_aim AS aim,
			".$table_prefix."users.user_yim AS yim,
			".$table_prefix."users.user_interests AS interests,
            ".$table_prefix."user_group.group_id AS board_groupid
		FROM ".$table_prefix."users, ".$table_prefix."user_group
		WHERE ".$table_prefix."users.user_id = '".intval($id)."' AND ".$table_prefix."user_group.user_id = '".intval($id)."'";

	$result = $db_sql->sql_query($sql);
	if ($db_sql->num_rows($result) != 1) {
		return false;
	} else {
		$user = $db_sql->fetch_array($result);
		$engine_group = $db_sql->query_array("SELECT engine_groupid AS groupid, $group_table.* FROM groups_engine2board 
											LEFT JOIN ".$group_table." ON (".$group_table.".groupid = groups_engine2board.engine_groupid)
											WHERE board_groupid='".$user['board_groupid']."'");		
		$user['userfound'] = true;
		$user = array_merge($user,$engine_group);
		return stripslashes_array($user);
	}
}

/**
 * getUserByActivationCode()
 * 
 * @param $userid
 * @param $actcode
 * @return 
 */
function getUserByActivationCode($userid,$actcode) {
	engineErrorHandler(E_USER_ERROR, "PHPBB2 Interface Error", __FILE__, __LINE__, "");
}

/**
* holeUserID()
*
* User auf Basis der User-ID aus der DB holen und
* zurückliefern (z. B. memberdetails.php)
*/
function holeUserID($uid,$pw="") {
    global $user_table,$db_sql,$table_prefix;
	if($pw) {
		$add = "AND userpassword = '".md5($pw)."'";
	}
    $sql = $db_sql->query_array("SELECT * FROM ".$table_prefix."users WHERE user_id='".intval($uid)."' $add");
    return stripslashes_array($sql);
}

/**
* holeUser()
*
* User auf Basis des Usernamens aus der DB holen und
* zurückliefern (z. B. addmember.php)
*/
function holeUser($uname) {
    global $user_table,$db_sql,$table_prefix;
    $username = trim(htmlspecialchars($uname));
    $username = substr(str_replace("\\'", "'", $username), 0, 25);
    $username = str_replace("'", "\\'", $username);     
    $sql = $db_sql->query_array("SELECT * FROM ".$table_prefix."users WHERE username='" . str_replace("\\'", "''", $username) . "'");
    return stripslashes_array($sql);
}

/**
 * getGroupNameByGroupID()
 * 
 * @param $groupid
 * @return 
 */
function getGroupNameByGroupID($groupid) {
    global $group_table,$db_sql;
    $sql = $db_sql->query_array("SELECT title FROM $group_table WHERE groupid='".intval($groupid)."'");
    return stripslashes_array($sql);
}

/**
* CheckUserID()
*
* Holt den User anhand der ID aus der Datenbank
* inkl. Gruppenrechte
*/
function CheckUserID($userid) {
    global $user_table, $group_table, $db_sql;
    $sql = holeUserID($userid);
    return $sql;
}

/**
 * userLogin()
 * 
 * User in die DB schreiben
 * Ersetzt gleichnamige Funktion aus der Klasse Auth
 * 
 * @param $username
 * @param $password
 * @return 
 */
function userLogin($username,$password,$useremail="",$act_code="") {
    engineErrorHandler(E_USER_ERROR, "PHPBB2 Interface Error", __FILE__, __LINE__, "");
}   

/**
 * countActivationCode()
 * 
 * Zählt alle User mit gleicher Userid und gleichem
 * Aktivierungscode in der Datenbank
 * 
 * @param $userid
 * @param $actcode
 * @return 
 */
function countActivationCode($userid,$actcode) {
	engineErrorHandler(E_USER_ERROR, "PHPBB2 Interface Error", __FILE__, __LINE__, "");
} 

/**
 * updateActivationCode()
 * 
 * Update des Aktivierungscodes bei
 * erfolgreicher Freischaltung
 * 
 * @param $userid
 * @return 
 */
function updateActivationCode($userid) {
	engineErrorHandler(E_USER_ERROR, "PHPBB2 Interface Error", __FILE__, __LINE__, "");
}

/**
* rewriteUser()
*
* Schreibt geänderte Userdaten in Datenbank
* Url Prüfung hinzugefügt
*/
function rewriteUser($uid,$umail,$uhp,$location,$gender,$uavatar,$global_mail,$icq,$aim,$yim,$interests) {
	engineErrorHandler(E_USER_ERROR, "PHPBB2 Interface Error", __FILE__, __LINE__, "");
}
	
/**
* rewritePW()
*
* Passwort in die DB eintragen
* Wird benötigt, wenn User eigenes Passwort ändert
*/
function rewritePW($password,$uid) {
	engineErrorHandler(E_USER_ERROR, "PHPBB2 Interface Error", __FILE__, __LINE__, "");
}

/**
* getCommentSQL()
* 
* SQL-Statement für Kommentare
*/
function getCommentSQL($table_name,$user_comment_column,$postid,$id,$status,$comment_date) {
	global $user_table,$group_table,$userid_table_column,$table_prefix,$comments_per_page;
    if($comments_per_page >= 1) $add_sql = " LIMIT ".intval($_GET['start']).",".$comments_per_page;
	return "SELECT DISTINCT
                d.*, 
			    u.username AS username,
                u.user_regdate AS regdate,
                u.user_viewemail AS show_email_global,
                u.user_email AS useremail,
                u.user_icq AS usericq,
                u.user_website AS userhp,
                u.user_from AS location,
                u.user_aim AS aim,
                u.user_yim AS yim              
            FROM ".$table_name." d
			LEFT JOIN $user_table u ON  u.$userid_table_column = d.$user_comment_column
			WHERE d.".$postid."='".intval($id)."' AND d.".$status."='1' ORDER BY d.".$comment_date." DESC".$add_sql;
}

/**
* getModeratorCommentSQL()
* 
* SQL-Statement für Kommentare
*/
function getModeratorCommentSQL($table_name,$user_comment_column,$postid,$id) {
	global $user_table,$group_table,$userid_table_column,$avat_table;
	return "SELECT 
                d.*, 
			    u.username AS username,
                u.user_regdate AS regdate,
                u.user_viewemail AS show_email_global,
                u.user_email AS useremail,
                u.user_icq AS usericq,
                u.user_website AS userhp,
                u.user_from AS location,
                u.user_aim AS aim,
                u.user_yim AS yim              
            FROM ".$table_name." d
			LEFT JOIN $user_table u ON  u.$userid_table_column = d.$user_comment_column
			WHERE d.".$postid."='".intval($id)."'";            
}

/**
* useShowMailGlobal()
* 
* Setzt SQL-Statement wenn ShowEmail Spalte vorhanden ist
*/
function useShowMailGlobal() {
    global $showmail_table_column, $user_table;
    if($showmail_table_column) {
        return ", $user_table.$showmail_table_column AS show_email_global";
    } else {
        return "";
    }
}

/**
 * definedBoardUrls()
 * 
 * Handelt es sich um einen Treiber für VB2, VB3,
 * WBB2 oder ähnlichem müssen hier die Url's zu
 * den jeweiligen Seiten vermerkt werden
 * 
 * @param $usage
 * @param $id
 * @return 
 */
function definedBoardUrls($usage,$id="") {
	global $UrlToBoard,$sess,$_ENGINE;
    
    if($UrlToBoard == "") $UrlToBoard = $_ENGINE['main_url'];
	
	switch($usage) {
		case "memberlist":
			return $UrlToBoard."/memberlist.php";
			break;
		case "addmember":
			return $UrlToBoard."/profile.php?mode=register";		
			break;		
		case "remember":
			return $UrlToBoard."/profile.php?mode=sendpassword";		
			break;		
		case "memberdetail":
			return $UrlToBoard."/profile.php?mode=viewprofile&u=".$id;		
			break;		
		case "changeaccount":
			return $UrlToBoard."/profile.php?mode=editprofile";		
			break;								
	}
}

//--------------------------------- PHPBB2 Specific Functions

function setDriverCookie($cookie_userid,$cookie_userpw) {
    global $HTTP_COOKIE_VARS,$db_sql;
    
    $board_config = loadPhpbbConfig();
    
	$cookiename = $board_config['cookie_name'];
	$cookiepath = $board_config['cookie_path'];
	$cookiedomain = $board_config['cookie_domain'];
	$cookiesecure = $board_config['cookie_secure'];
    
    $auto_login_key = $userdata['user_password'];
    $current_time = time();
    
    $user_ip = encodeIp();
    
    $session_id = md5(uniqid($user_ip));
    
	$userdata['session_id'] = $session_id;
	$userdata['session_ip'] = $user_ip;
	$userdata['session_user_id'] = $cookie_userid;
	$userdata['session_logged_in'] = 1;
	$userdata['session_page'] = 0;
	$userdata['session_start'] = $current_time;
	$userdata['session_time'] = $current_time;
    
    $sql = "INSERT INTO " . SESSIONS_TABLE . " (session_id, session_user_id, session_start, session_time, session_ip, session_page, session_logged_in)
                    VALUES ('$session_id', $cookie_userid, $current_time, $current_time, '$user_ip', 0, 1)";
    $db_sql->sql_query($sql); 

	setcookie($cookiename . '_data', serialize($sessiondata), $current_time + 31536000, $cookiepath, $cookiedomain, $cookiesecure);
	setcookie($cookiename . '_sid', $session_id, 0, $cookiepath, $cookiedomain, $cookiesecure);        

}

function getDriverCookie() {
    global $HTTP_COOKIE_VARS,$db_sql;
    
    $current_time = time();
	unset($userdata);    
    
    $board_config = loadPhpbbConfig();
    $cookiename = $board_config['cookie_name'];
    $cookiepath = $board_config['cookie_path'];
    $cookiedomain = $board_config['cookie_domain'];
    $cookiesecure = $board_config['cookie_secure'];
	
    if(isset($HTTP_COOKIE_VARS[$cookiename . '_sid']) || isset($HTTP_COOKIE_VARS[$cookiename . '_data'])) {
        $sessiondata = isset($HTTP_COOKIE_VARS[$cookiename . '_data']) ? unserialize(stripslashes($HTTP_COOKIE_VARS[$cookiename . '_data'])) : array();
        $session_id = isset( $HTTP_COOKIE_VARS[$cookiename . '_sid'] ) ? $HTTP_COOKIE_VARS[$cookiename . '_sid'] : '';
    } 
    
	if(!empty($session_id)) {
		$result = $db_sql->sql_query("SELECT u.*, u.user_id AS engine_userid, u.user_password AS engine_password, s.* FROM " . SESSIONS_TABLE . " s, " . USERS_TABLE . " u WHERE s.session_id = '$session_id' AND u.user_id = s.session_user_id");
		$userdata = $db_sql->fetch_array($result);        
        
		if(isset($userdata['user_id'])) {
            $engine_return = array('engine_userid' => $userdata['user_id'], 'engine_password' => $userdata['user_password']);        
        
			$ip_check_s = substr($userdata['session_ip'], 0, 6);
			$ip_check_u = substr(encodeIp(), 0, 6);
            
			if($ip_check_s == $ip_check_u) {
				if($current_time - $userdata['session_time'] > 60) {
					$sql = "UPDATE " . SESSIONS_TABLE . " SET session_time = $current_time, session_page = '0' WHERE session_id = '" . $userdata['session_id'] . "'";
					$db_sql->sql_query($sql);

					if($userdata['user_id'] != ANONYMOUS && $userdate['user_id'] != 2) {
						$sql = "UPDATE " . USERS_TABLE . " SET user_session_time = $current_time, user_session_page = '0' WHERE user_id = " . $userdata['user_id'];
						$db_sql->sql_query($sql);
					}

					$expiry_time = $current_time - $board_config['session_length'];
					$sql = "DELETE FROM " . SESSIONS_TABLE . " WHERE session_time < $expiry_time AND session_id <> '$session_id'";
					$db_sql->sql_query($sql);

					setcookie($cookiename . '_data', serialize($sessiondata), $current_time + 31536000, $cookiepath, $cookiedomain, $cookiesecure);
					setcookie($cookiename . '_sid', $session_id, 0, $cookiepath, $cookiedomain, $cookiesecure);
				}
                
                if($userdata['user_id'] == -1) {
                    $returndata['engine_userid'] = '2';
                    $returndata['engine_password'] = '';
                    return $returndata;                
                } else {
                    return $engine_return;
                }
				
			}
		}
	}

    $userdata['engine_userid'] = '2';
    $userdata['engine_password'] = '';
    return $userdata;	 
}

function deleteDriverCookie($user_id) {
	global $HTTP_COOKIE_VARS,$db_sql;

    $board_config = loadPhpbbConfig();
    
	$cookiename = $board_config['cookie_name'];
	$cookiepath = $board_config['cookie_path'];
	$cookiedomain = $board_config['cookie_domain'];
	$cookiesecure = $board_config['cookie_secure'];

	$current_time = time();

	if(isset($HTTP_COOKIE_VARS[$cookiename . '_sid'])) {
		$session_id = isset($HTTP_COOKIE_VARS[$cookiename . '_sid']) ? $HTTP_COOKIE_VARS[$cookiename . '_sid'] : '';
	}

	$db_sql->sql_query("DELETE FROM " . SESSIONS_TABLE . " WHERE session_id = '$session_id' AND session_user_id = $user_id");

	setcookie($cookiename . '_data', '', $current_time - 31536000, $cookiepath, $cookiedomain, $cookiesecure);
	setcookie($cookiename . '_sid', '', $current_time - 31536000, $cookiepath, $cookiedomain, $cookiesecure);

	return true;
}

function loadPhpbbConfig() {
    global $db_sql,$table_prefix;
    $board_config = array();
    $sql = "SELECT * FROM " . PHPBB_CONFIG_TABLE;
    $result = $db_sql->sql_query("SELECT * FROM ".$table_prefix."config");
    while($row = $db_sql->fetch_array($result)) {
      $board_config[$row['config_name']] = $row['config_value'];
    }
    return $board_config;
}

function encodeIp() {
    global $HTTP_SERVER_VARS,$HTTP_ENV_VARS,$REMOTE_ADDR;
    $client_ip = ( !empty($HTTP_SERVER_VARS['REMOTE_ADDR']) ) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ( ( !empty($HTTP_ENV_VARS['REMOTE_ADDR']) ) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR );
	$ip_sep = explode('.', $client_ip);
	return sprintf('%02x%02x%02x%02x', $ip_sep[0], $ip_sep[1], $ip_sep[2], $ip_sep[3]);
}

?>