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
$UrlToBoard = "http://192.168.0.100/boards/wbblite";

/**
* Nummer des Boards
*/
$n = "3";

//-------------------------------------------------------------------------
//-------------- Ab hier keine Einstellungen notwendig --------------------
//-------------- No additional settings necessary -------------------------
//-------------------------------------------------------------------------

/**
* Definiert die Tabellenspalte in der die Timestamp für den
* letzten Besuch steht
*/
$lastvisit_table_column = "lastvisit";

/**
* Definiert die Tabellenspalte in der die UserID steht
*/
$userid_table_column = "userid";

/**
* Definiert die Tabellenspalte in der der Username steht
*/
$username_table_column = "username";

/**
* Definiert die Tabellenspalte in der die Email des Users steht
*/
$useremail_table_column = "email";

/**
* Definiert die Tabellenspalte in der die Homepage des Users steht
*/
$userhp_table_column = "homepage";

/**
* Definiert die Tabellenspalte in der steht ob die Email des Users angezeigt werden soll
*/
$showmail_table_column = "showemail";

/**
* Aktiviert bzw. deaktiviert das Engineeigene Avatarhandling
*/
define('USE_ENGINE_AVATARS',false);

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
	global $user_table, $group_table, $db_sql,$n;
	
	if($enableactivation) $more_sql = "AND activation='1'";	
	
	$sql = "
		SELECT 
			bb".$n."_users.userid AS userid,
			bb".$n."_users.username AS username,
			bb".$n."_users.password AS userpassword,
			bb".$n."_users.email AS useremail,
			bb".$n."_users.groupid AS board_groupid,
			bb".$n."_users.regdate AS regdate,
			bb".$n."_users.lastvisit AS lastvisit,
			bb".$n."_users.icq AS usericq,
			bb".$n."_users.aim AS aim,
			bb".$n."_users.yim AS yim,
			bb".$n."_users.homepage AS userhp,
			bb".$n."_users.avatarid AS avatarid,
			bb".$n."_users.gender AS gender,
			bb".$n."_users.showemail AS show_email_global,
			bb".$n."_users.activation AS activation,
			bb".$n."_users.blocked AS blocked
			
		FROM bb".$n."_users
		WHERE username = '".addslashes(htmlspecialchars($name))."' AND password = '".md5($pw)."' ". $more_sql;
		
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
	global $user_table, $group_table, $db_sql, $n;
	if($enableactivation) $more_sql = "AND activation='1'";	
	
	$sql = "
		SELECT 
			bb".$n."_users.userid AS userid,
			bb".$n."_users.username AS username,
			bb".$n."_users.password AS userpassword,
			bb".$n."_users.email AS useremail,
			bb".$n."_users.groupid AS board_groupid,
			bb".$n."_users.regdate AS regdate,
			bb".$n."_users.lastvisit AS lastvisit,
			bb".$n."_users.icq AS usericq,
			bb".$n."_users.aim AS aim,
			bb".$n."_users.yim AS yim,
			bb".$n."_users.homepage AS userhp,
			bb".$n."_users.avatarid AS avatarid,
			bb".$n."_users.gender AS gender,
			bb".$n."_users.showemail AS show_email_global,
			bb".$n."_users.activation AS activation,
			bb".$n."_users.blocked AS blocked
		FROM bb".$n."_users
		WHERE userid='".intval($id)."' ". $more_sql;	

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
	engineErrorHandler(E_USER_ERROR, "WBBlite Interface Error", __FILE__, __LINE__, "");
}

/**
* holeUserID()
*
* User auf Basis der User-ID aus der DB holen und
* zurückliefern (z. B. memberdetails.php)
*/
function holeUserID($uid,$pw="") {
    global $user_table,$db_sql,$n;
	if($pw) $add = "AND userpassword = '".md5($pw)."'";
    $sql = $db_sql->query_array("SELECT * FROM bb".$n."_users WHERE userid='".intval($uid)."' $add");
    return stripslashes_array($sql);
}

/**
* holeUser()
*
* User auf Basis des Usernamens aus der DB holen und
* zurückliefern (z. B. addmember.php)
*/
function holeUser($uname) {
    global $user_table,$db_sql,$n;
    $sql = $db_sql->query_array("SELECT * FROM bb".$n."_users WHERE username='".addslashes(htmlspecialchars($uname))."'");
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
    engineErrorHandler(E_USER_ERROR, "WBBlite Interface Error", __FILE__, __LINE__, "");
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
	engineErrorHandler(E_USER_ERROR, "WBBlite Interface Error", __FILE__, __LINE__, "");
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
	engineErrorHandler(E_USER_ERROR, "WBBlite Interface Error", __FILE__, __LINE__, "");
}

/**
* rewriteUser()
*
* Schreibt geänderte Userdaten in Datenbank
* Url Prüfung hinzugefügt
*/
function rewriteUser($uid,$umail,$uhp,$location,$gender,$uavatar,$global_mail,$icq,$aim,$yim,$interests) {
	engineErrorHandler(E_USER_ERROR, "WBBlite Interface Error", __FILE__, __LINE__, "");
}
	
/**
* rewritePW()
*
* Passwort in die DB eintragen
* Wird benötigt, wenn User eigenes Passwort ändert
*/
function rewritePW($password,$uid) {
	engineErrorHandler(E_USER_ERROR, "WBBlite Interface Error", __FILE__, __LINE__, "");
}

/**
* getCommentSQL()
* 
* SQL-Statement für Kommentare
*/
function getCommentSQL($table_name,$user_comment_column,$postid,$id,$status,$comment_date) {
	global $user_table,$group_table,$userid_table_column,$comments_per_page;
    if($comments_per_page >= 1) $add_sql = " LIMIT ".intval($_GET['start']).",".$comments_per_page;
	return "SELECT 
                d.*, 
                g.title, 
                g.groupid, 
                u.userid AS userid,
                u.username AS username,
                u.email AS useremail,
                u.regdate AS regdate,
                u.icq AS usericq,
                u.aim AS aim,
                u.yim AS yim,
                u.homepage AS userhp,
                u.showemail AS show_email_global
            FROM ".$table_name." d
			LEFT JOIN $user_table u ON  u.$userid_table_column = d.$user_comment_column
			LEFT JOIN groups_engine2board a ON a.board_groupid = u.groupid
			LEFT JOIN $group_table g ON g.groupid = a.engine_groupid
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
                g.title, 
                g.groupid, 
                u.userid AS userid,
                u.username AS username,
                u.email AS useremail,
                u.regdate AS regdate,
                u.icq AS usericq,
                u.aim AS aim,
                u.yim AS yim,
                u.homepage AS userhp,
                u.showemail AS show_email_global
            FROM ".$table_name." d
			LEFT JOIN $user_table u ON  u.$userid_table_column = d.$user_comment_column
			LEFT JOIN groups_engine2board a ON a.board_groupid = u.groupid
			LEFT JOIN $group_table g ON g.groupid = a.engine_groupid
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
			return $UrlToBoard."/memberslist.php";
			break;
		case "addmember":
			return $UrlToBoard."/register.php";		
			break;		
		case "remember":
			return $UrlToBoard."/forgotpw.php";		
			break;		
		case "memberdetail":
			return $UrlToBoard."/profile.php?userid=".$id;		
			break;		
		case "changeaccount":
			return $UrlToBoard."/usercp.php";		
			break;								
	}
}

//---------------------------------------- WBBlite Specific Functions


function setDriverCookie($cookie_userid,$cookie_userpw) {
    global $HTTP_COOKIE_VARS,$db_sql,$n; 
    setcookie("wbb_userid","$cookie_userid",time()+3600*24*365,"/");
    setcookie("wbb_userpassword","$cookie_userpw",time()+3600*24*365,"/");       
}

function getDriverCookie() {
    global $HTTP_COOKIE_VARS,$db_sql,$n;
    
    $HTTP_USER_AGENT=htmlspecialchars($HTTP_USER_AGENT);
    $REMOTE_ADDR = getIpForWBBAddress();
    $REQUEST_URI = $_SERVER['REQUEST_URI'];
    
    if(isset($HTTP_COOKIE_VARS['cookiehash'])) {
        $sid = $HTTP_COOKIE_VARS['cookiehash'];
    }
    
    if($sid && isset($HTTP_COOKIE_VARS['cookiehash']) && $HTTP_COOKIE_VARS['cookiehash'] && $sid != $HTTP_COOKIE_VARS['cookiehash']) {
        $falsecookiehash=1;
    }
    
    $createsession = 0;
    if($sid) {
        $session = $db_sql->query_array("SELECT * FROM bb".$n."_sessions WHERE hash = '".addslashes($sid)."' AND ipaddress = '".addslashes($REMOTE_ADDR)."'");
        if($session['hash']) {
            $wbb_userid = $session['userid'];
            $session['lastactivity'] = time();
            $db_sql->sql_query("UPDATE bb".$n."_sessions SET lastactivity = '".$session['lastactivity']."', request_uri = '".addslashes($REQUEST_URI)."', boardid = '0', threadid = '0' WHERE hash = '".$sid."'");
        } else {
            $createsession = 1;
        }
    } else {
        $createsession = 1;    
    }
    
    if($createsession == 1 || $session['userid'] == 0) {
        if(isset($HTTP_COOKIE_VARS['wbb_userid']) && isset($HTTP_COOKIE_VARS['wbb_userpassword'])) { 
            $wbbuserdata = $db_sql->query_array("SELECT bb".$n."_users.*, bb".$n."_groups.* FROM bb".$n."_users LEFT JOIN bb".$n."_groups USING (groupid) WHERE userid = '".$HTTP_COOKIE_VARS['wbb_userid']."'");
            if($HTTP_COOKIE_VARS['wbb_userpassword'] == $wbbuserdata['password']) { 
                $session = array();
                $session['hash'] = md5(uniqid(microtime()));
                $session['userid'] = $HTTP_COOKIE_VARS['wbb_userid'];
                $session['ipaddress'] = $REMOTE_ADDR;
                $session['useragent'] = addslashes($HTTP_USER_AGENT);
                $session['lastactivity'] = time();
                $session['request_uri'] = $REQUEST_URI;
                $engine_return = array('engine_userid' => $HTTP_COOKIE_VARS['wbb_userid'], 'engine_password' => $wbbuserdata['password']);
                $db_sql->sql_query("DELETE FROM bb".$n."_sessions WHERE userid = '".$session['userid']."'");
                $db_sql->sql_query("INSERT INTO bb".$n."_sessions VALUES ('".$session['hash']."','".$session['userid']."','".addslashes($session['ipaddress'])."','".addslashes($session['useragent'])."','".$session['lastactivity']."','".addslashes($session['request_uri'])."','0','0','0')");
                setcookie("cookiehash", "$session[hash]", 0, "/");
                
                return $engine_return;
            } else {
                if($createsession == 1) $guestsession = 1;
                unset($wbb_userid);
                unset($wbbuserdata);
                setcookie("wbb_userid", "", time()-31536000, "/");
                setcookie("wbb_userpassword", "", time()-31536000, "/");
            }
        } elseif($createsession == 1) {
            unset($wbb_userid);
            $guestsession = 1;
        }
        
        if(isset($guestsession)) { 
            $db_sql->sql_query("DELETE FROM bb".$n."_sessions WHERE userid='0' AND ipaddress = '".addslashes($REMOTE_ADDR)."'");
            $engine_return = array('engine_userid' => '2', 'engine_password' => '');
            $session = array();
            $session['hash'] = md5(uniqid(microtime()));
            $session['userid'] = 0;
            $session['engine_userid'] = 2;
            $session['engine_password'] = '';            
            $session['ipaddress'] = $REMOTE_ADDR;
            $session['useragent'] = addslashes($HTTP_USER_AGENT);
            $session['lastactivity'] = time();
            $session['request_uri'] = $REQUEST_URI;
            $db_sql->sql_query("INSERT INTO bb".$n."_sessions VALUES ('".$session['hash']."','0','".addslashes($session['ipaddress'])."','".addslashes($session['useragent'])."','$session[lastactivity]','".addslashes($session['request_uri'])."','0','0','0')");
            setcookie("cookiehash", "$session[hash]", 0, "/");
            
            return $engine_return;
        }
    }    
    
    if(!isset($wbbuserdata)) {
        $wbbuserdata = $db_sql->query_array("SELECT bb".$n."_users.*, bb".$n."_groups.* FROM bb".$n."_users LEFT JOIN bb".$n."_groups USING (groupid) WHERE userid = '".$wbb_userid."'");
        $engine_return = array('engine_userid' => $wbbuserdata['userid'], 'engine_password' => $wbbuserdata['password']);
        
        return $engine_return;
    }
}

function deleteDriverCookie($user_id) {
	global $HTTP_COOKIE_VARS,$db_sql,$n;
    setcookie("wbb_userid","",time()-31536000,"/");
    setcookie("wbb_userpassword","",time()-31536000,"/");
    setcookie("boardpasswords","",time()-31536000,"/");
    setcookie("hidecats","",time()-31536000,"/");
    setcookie("boardvisit","",time()-31536000,"/");
    setcookie("threadvisit","",time()-31536000,"/");
    setcookie("postvisit","",time()-31536000,"/");   
    
    $db_sql->sql_query("UPDATE bb".$n."_sessions SET userid = '0' WHERE hash = '".$HTTP_COOKIE_VARS['cookiehash']."'"); 
    setcookie("cookiehash", "", time()-31536000,"/"); 
	return true;
}

function getIpForWBBAddress() {
    global $_SERVER;
    
    $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $HTTP_X_FORWARDED_FOR = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $HTTP_X_FORWARDED_FOR = '';
    }
    
    if($HTTP_X_FORWARDED_FOR != "") {
        if(preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $HTTP_X_FORWARDED_FOR, $ip_match)) {
            $private_ip_list = array("/^0\./", "/^127\.0\.0\.1/", "/^192\.168\..*/", "/^172\.16\..*/", "/^10..*/", "/^224..*/", "/^240..*/");
            $REMOTE_ADDR = preg_replace($private_ip_list, $REMOTE_ADDR, $ip_match[1]);
        }
    }
    
    if(strlen($REMOTE_ADDR) > 16) $REMOTE_ADDR = substr($REMOTE_ADDR, 0, 16);
    return $REMOTE_ADDR;
}

?>