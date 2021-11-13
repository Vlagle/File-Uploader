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
$UrlToBoard = "";

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
$useremail_table_column = "useremail";

/**
* Definiert die Tabellenspalte in der die Homepage des Users steht
*/
$userhp_table_column = "userhp";

/**
* Definiert die Tabellenspalte in der steht ob die Email des Users angezeigt werden soll
*/
$showmail_table_column = "show_email_global";

/**
* Aktiviert bzw. deaktiviert das Engineeigene Avatarhandling
*/
define('USE_ENGINE_AVATARS',true);

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
	global $user_table, $group_table, $db_sql;
	if($enableactivation) $more_sql = "AND activation='1'";
	
	$sql = "SELECT ".$user_table.".*,".$group_table.".* FROM ".$user_table."
			LEFT JOIN ".$group_table." ON (".$group_table.".groupid = ".$user_table.".groupid)
			WHERE username = '".addslashes(htmlspecialchars($name))."' AND userpassword = '".md5($pw)."' ". $more_sql;
			
	$result = $db_sql->sql_query($sql);
	if ($db_sql->num_rows($result) != 1) {
		return false;
	} else {
		$user = $db_sql->fetch_array($result);
		$user['userfound'] = true;
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
	global $user_table, $group_table, $db_sql;
	if($enableactivation) $more_sql = "AND activation='1'";
	
	$sql = "SELECT ".$user_table.".*, ".$group_table.".* FROM ".$user_table."
			LEFT JOIN ".$group_table." ON (".$group_table.".groupid = ".$user_table.".groupid)
			WHERE userid='".intval($id)."' ". $more_sql;
	
	$result = $db_sql->sql_query($sql);
	if ($db_sql->num_rows($result) != 1) {
		return false;
	} else {
		$user = $db_sql->fetch_array($result);
		$user['userfound'] = true;
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
	global $db_sql, $user_table;
	$sql = $db_sql->query_array("SELECT * FROM $user_table WHERE userid='".intval($userid)."' AND activation='".$actcode."'");
	return stripslashes_array($sql);
}

/**
* holeUserID()
*
* User auf Basis der User-ID aus der DB holen und
* zurückliefern (z. B. memberdetails.php)
*/
function holeUserID($uid,$pw="") {
    global $user_table,$db_sql;
	if($pw) $add = "AND userpassword = '".md5($pw)."'";
    $sql = $db_sql->query_array("SELECT * FROM $user_table WHERE userid='".intval($uid)."' $add");
    return stripslashes_array($sql);
}

/**
* holeUser()
*
* User auf Basis des Usernamens aus der DB holen und
* zurückliefern (z. B. addmember.php)
*/
function holeUser($uname) {
    global $user_table,$db_sql;
    $sql = $db_sql->query_array("SELECT * FROM $user_table WHERE username='".addslashes(htmlspecialchars($uname))."'");
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
    $sql = $db_sql->query_array("SELECT $user_table.*, $group_table.* FROM $user_table 
    				LEFT JOIN $group_table ON ($group_table.groupid = $user_table.groupid) 
    				WHERE userid='".intval($userid)."'");
    return stripslashes_array($sql);
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
    global $db_sql, $_ENGINE, $user_table;
	
	if(!$act_code) {
		$db_activation_code = "1";
	} else {
		$db_activation_code = $act_code;
	}
	
	$result = $db_sql->sql_query("INSERT INTO ".$user_table." (username, userpassword, useremail, regdate, lastvisit, groupid, activation) 
									VALUES('".addslashes(htmlspecialchars(trim($username)))."','".$password."','".addslashes(trim($_POST['u_email']))."','".time()."', '".time()."', '".$_ENGINE['std_group']."', '".$db_activation_code."')");
    return $db_sql->insert_id();
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
	global $db_sql, $user_table;
	return $db_sql->query_array("SELECT COUNT(userid)as anzahl FROM $user_table WHERE userid='".intval($userid)."' AND activation='".$actcode."'");
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
	global $db_sql, $user_table;
	$db_sql->sql_query("UPDATE $user_table SET activation='1' WHERE userid='".intval($userid)."'");
}

/**
* rewriteUser()
*
* Schreibt geänderte Userdaten in Datenbank
* Url Prüfung hinzugefügt
*/
function rewriteUser($uid,$umail,$uhp,$location,$gender,$uavatar,$global_mail,$icq,$aim,$yim,$interests) {
    global $session,$user_table,$db_sql;
    if($uhp) $uhp = reBuildURL($uhp);		
    $db_sql->sql_query("UPDATE $user_table SET useremail='".addslashes(htmlspecialchars($umail))."', usericq='".addslashes($icq)."', aim='".addslashes($aim)."', yim='".addslashes($yim)."', interests='".addslashes(strip_tags($interests))."', userhp='".addslashes($uhp)."', location='".addslashes(strip_tags($location))."', gender='$gender',avatarid='$uavatar', show_email_global='$global_mail' WHERE userid='".intval($uid)."'");
}
	
/**
* rewritePW()
*
* Passwort in die DB eintragen
* Wird benötigt, wenn User eigenes Passwort ändert
*/
function rewritePW($password,$uid) {
    global $user_table,$db_sql;
    $db_sql->sql_query("UPDATE $user_table SET userpassword='".md5($password)."' WHERE userid='".intval($uid)."'");
}

/**
* getCommentSQL()
* 
* SQL-Statement für Kommentare
*/
function getCommentSQL($table_name,$user_comment_column,$postid,$id,$status,$comment_date) {
	global $user_table,$group_table,$userid_table_column,$avat_table,$comments_per_page;
    if($comments_per_page >= 1) $add_sql = " LIMIT ".intval($_GET['start']).",".$comments_per_page;
	return "SELECT d.*, a.avatardata, g.title, u.* FROM ".$table_name." d
			LEFT JOIN $user_table u ON  u.userid = d.$user_comment_column
			LEFT JOIN $group_table g ON  g.groupid = u.groupid
			LEFT JOIN $avat_table a ON a.avatarid = u.avatarid
			WHERE d.".$postid."='".intval($id)."' AND d.".$status."='1' ORDER BY d.".$comment_date." DESC".$add_sql;
}

/**
* getModeratorCommentSQL()
* 
* SQL-Statement für Kommentare
*/
function getModeratorCommentSQL($table_name,$user_comment_column,$postid,$id) {
	global $user_table,$group_table,$userid_table_column,$avat_table;
	return "SELECT d.*, a.avatardata, g.title, u.* FROM ".$table_name." d
			LEFT JOIN $user_table u ON  u.userid = d.$user_comment_column
			LEFT JOIN $group_table g ON  g.groupid = u.groupid
			LEFT JOIN $avat_table a ON a.avatarid = u.avatarid
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
			return $UrlToBoard."/memberlist.php?".$sess->sess_name."=".$sess->sess_id;
			break;
		case "addmember":
			return $UrlToBoard."/addmember.php?action=rules&amp;".$sess->sess_name."=".$sess->sess_id;		
			break;		
		case "remember":
			return $UrlToBoard."/remember.php?".$sess->sess_name."=".$sess->sess_id;		
			break;		
		case "memberdetail":
			return $UrlToBoard."/memberlist.php?action=userdetail&amp;nameid=".$id."&amp;".$sess->sess_name."=".$sess->sess_id;		
			break;		
		case "changeaccount":
			return $UrlToBoard."/memberdetails.php?change=1&amp;".$sess->sess_name."=".$sess->sess_id;		
			break;								
	}
}

?>