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
$UrlToBoard = "http://192.168.0.100/boards/vbb3";

/**
* Tabellenprefix der VBulletin Tabellen
*/
$tableprefix = 'vb3_';

/**
* Cookieprefix der VBulletin Cookies
*/
$cookieprefix = 'bb';

//-------------------------------------------------------------------------
//-------------- Ab hier keine Einstellungen notwendig --------------------
//-------------- No additional settings necessary -------------------------
//-------------------------------------------------------------------------

/**
* Definiert, ob allen Mitgliedern Emails via Formmailer geschrieben
* werden d&uuml;rfen. Email-Adressen werden generell nicht angezeigt
* 1=ja; 0=nein
*/
$email_option = 1;

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
$showmail_table_column = "";

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
    global $user_table, $group_table, $db_sql, $email_option, $tableprefix;
    
    //if($enableactivation) $more_sql = "AND activation='1'";   
    $hash = $db_sql->query_array("SELECT salt FROM ".$tableprefix."user WHERE username = '".$name."'");
    $hashedpassword = md5(md5($pw).$hash['salt']);
    
    $sql = "
        SELECT 
            ".$tableprefix."user.userid AS userid,
            ".$tableprefix."user.usergroupid AS board_groupid,
            ".$tableprefix."user.username AS username,
            ".$tableprefix."user.password AS userpassword,
            ".$tableprefix."user.email AS useremail,
            ".$tableprefix."user.homepage AS userhp,
            ".$tableprefix."user.icq AS usericq,
            ".$tableprefix."user.aim AS aim,
            ".$tableprefix."user.yahoo AS yim,
            ".$tableprefix."user.joindate AS regdate,
            ".$tableprefix."user.lastvisit AS lastvisit,
            ".$tableprefix."user.avatarid AS avatarid
        FROM ".$tableprefix."user
        WHERE username = '".addslashes(htmlSpecialCharsUni($name))."' AND password = '".$hashedpassword."'";
        
    $result = $db_sql->sql_query($sql);
    if ($db_sql->num_rows($result) != 1) {
        return false;
    } else {
        $user = $db_sql->fetch_array($result);
        $engine_group = $db_sql->query_array("SELECT engine_groupid AS groupid, $group_table.* FROM groups_engine2board 
                                            LEFT JOIN ".$group_table." ON (".$group_table.".groupid = groups_engine2board.engine_groupid)
                                            WHERE board_groupid='".$user['board_groupid']."'");     
        $user['userfound'] = true;
        $user['show_email_global'] = $email_option;
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
    global $user_table, $group_table, $db_sql, $email_option, $tableprefix;
    if($enableactivation) $more_sql = "AND activation='1'"; 
    
    $sql = "
        SELECT 
            ".$tableprefix."user.userid AS userid,
            ".$tableprefix."user.usergroupid AS board_groupid,
            ".$tableprefix."user.username AS username,
            ".$tableprefix."user.password AS userpassword,
            ".$tableprefix."user.email AS useremail,
            ".$tableprefix."user.homepage AS userhp,
            ".$tableprefix."user.icq AS usericq,
            ".$tableprefix."user.aim AS aim,
            ".$tableprefix."user.yahoo AS yim,
            ".$tableprefix."user.joindate AS regdate,
            ".$tableprefix."user.lastvisit AS lastvisit,
            ".$tableprefix."user.avatarid AS avatarid
        FROM ".$tableprefix."user
        WHERE userid = '".intval($id)."'";

    $result = $db_sql->sql_query($sql);
    if ($db_sql->num_rows($result) != 1) {
        return false;
    } else {
        $user = $db_sql->fetch_array($result);
        $engine_group = $db_sql->query_array("SELECT engine_groupid AS groupid, $group_table.* FROM groups_engine2board 
                                            LEFT JOIN ".$group_table." ON (".$group_table.".groupid = groups_engine2board.engine_groupid)
                                            WHERE board_groupid='".$user['board_groupid']."'");     
        $user['userfound'] = true;
        $user['show_email_global'] = $email_option;
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
    engineErrorHandler(E_USER_ERROR, "VBB3 Interface Error", __FILE__, __LINE__, "");
}

/**
* holeUserID()
*
* User auf Basis der User-ID aus der DB holen und
* zurückliefern (z. B. memberdetails.php)
*/
function holeUserID($uid,$pw="") {
    global $user_table,$db_sql,$tableprefix;
    if($pw) {
        $salt = fetch_user_salt(3);
        $hashedpassword = md5(md5($pw) . $salt);    
        $add = "AND userpassword = '".$hashedpassword."'";
    }
    $sql = $db_sql->query_array("SELECT * FROM ".$tableprefix."user WHERE userid='".intval($uid)."' $add");
    return stripslashes_array($sql);
}

/**
* holeUser()
*
* User auf Basis des Usernamens aus der DB holen und
* zurückliefern (z. B. addmember.php)
*/
function holeUser($uname) {
    global $user_table,$db_sql,$tableprefix;
    $sql = $db_sql->query_array("SELECT * FROM ".$tableprefix."user WHERE username='".addslashes(htmlspecialchars_uni($uname))."'");
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
    engineErrorHandler(E_USER_ERROR, "VBB3 Interface Error", __FILE__, __LINE__, "");
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
    engineErrorHandler(E_USER_ERROR, "VBB3 Interface Error", __FILE__, __LINE__, "");
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
    engineErrorHandler(E_USER_ERROR, "VBB3 Interface Error", __FILE__, __LINE__, "");
}

/**
* rewriteUser()
*
* Schreibt geänderte Userdaten in Datenbank
* Url Prüfung hinzugefügt
*/
function rewriteUser($uid,$umail,$uhp,$location,$gender,$uavatar,$global_mail,$icq,$aim,$yim,$interests) {
    engineErrorHandler(E_USER_ERROR, "VBB3 Interface Error", __FILE__, __LINE__, "");
}
    
/**
* rewritePW()
*
* Passwort in die DB eintragen
* Wird benötigt, wenn User eigenes Passwort ändert
*/
function rewritePW($password,$uid) {
    engineErrorHandler(E_USER_ERROR, "VBB3 Interface Error", __FILE__, __LINE__, "");
}

/**
* getCommentSQL()
* 
* SQL-Statement für Kommentare
*/
function getCommentSQL($table_name,$user_comment_column,$postid,$id,$status,$comment_date) {
    global $user_table,$group_table,$userid_table_column,$tableprefix,$comments_per_page;
    if($comments_per_page >= 1) $add_sql = " LIMIT ".intval($_GET['start']).",".$comments_per_page;
    return "SELECT 
                d.*, 
                u.userid AS userid,
                u.usergroupid AS board_groupid,
                u.username AS username,
                u.password AS userpassword,
                u.email AS useremail,
                u.homepage AS userhp,
                u.icq AS usericq,
                u.aim AS aim,
                u.yahoo AS yim,
                u.joindate AS regdate,
                u.lastvisit AS lastvisit
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
                u.userid AS userid,
                u.usergroupid AS board_groupid,
                u.username AS username,
                u.password AS userpassword,
                u.email AS useremail,
                u.homepage AS userhp,
                u.icq AS usericq,
                u.aim AS aim,
                u.yahoo AS yim,
                u.joindate AS regdate,
                u.lastvisit AS lastvisit
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
            return $UrlToBoard."/register.php?do=signup";       
            break;      
        case "remember":
            return $UrlToBoard."/login.php?do=lostpw";      
            break;      
        case "memberdetail":
            return $UrlToBoard."/member.php?u=".$id;        
            break;      
        case "changeaccount":
            return $UrlToBoard."/usercp.php";       
            break;                              
    }
}

//---------------------------------------- VBB3 Specific Functions


function setDriverCookie($cookie_userid,$cookie_userpw) {
    global $HTTP_COOKIE_VARS,$db_sql;      
    doCookie("userid", $cookie_userid,time()+3600*24*365);
    doCookie("password", md5($cookie_userpw . 'nullified'),time()+3600*24*365);
}

function getDriverCookie() {
    global $HTTP_COOKIE_VARS,$db_sql,$tableprefix,$cookieprefix;
    if(isset($HTTP_COOKIE_VARS['sessionhash'])) {
        $sessionhash = $HTTP_COOKIE_VARS['sessionhash'];
    }
    
    if($sessionhash && isset($HTTP_COOKIE_VARS['sessionhash']) && $HTTP_COOKIE_VARS['sessionhash'] && $sessionhash != $HTTP_COOKIE_VARS['sessionhash']) {
        $falsecookiehash=1;
    }
    
    $createsession = 0;
    
    if($sessionhash) {
        $session = $db_sql->query_array("SELECT sessionhash,userid,host,useragent FROM ".$tableprefix."session WHERE sessionhash='".addslashes($sessionhash)."' AND host='".addslashes($REMOTE_ADDR)."' AND useragent='".addslashes($HTTP_USER_AGENT)."'");
        if($session['sessionhash']) {
            $vbb_userid = $session['userid'];
            $session['lastactivity'] = time();
        } else {
            $createsession = 1;
        }
    } else {
        $createsession = 1;    
    }   

    if($createsession == 1 || $session['userid'] == 0) {
        if(isset($HTTP_COOKIE_VARS[$cookieprefix.'userid']) && isset($HTTP_COOKIE_VARS[$cookieprefix.'password'])) {
            $bbuserinfo = $db_sql->query_array("SELECT * FROM ".$tableprefix."user WHERE userid='".$HTTP_COOKIE_VARS[$cookieprefix.'userid']."'");
            if (md5($bbuserinfo['password'] . 'nullified') == $HTTP_COOKIE_VARS[$cookieprefix . 'password']) {
                $engine_return = array('engine_userid' => $HTTP_COOKIE_VARS[$cookieprefix.'userid'], 'engine_password' => $bbuserinfo['password']);
                return $engine_return;              
            } else {
                if($createsession == 1) $guestsession = 1;
                unset($bbuserid);
                unset($bbuserinfo);
                doCookie("userid", "", time()-31536000);
                doCookie("password", "", time()-31536000);
            }
        } elseif($createsession == 1) {
            unset($bbuserid);
            $guestsession = 1;
        }
        
        if(isset($guestsession)) { 
            $db_sql->sql_query("DELETE FROM session WHERE userid='0'");
            $engine_return = array('engine_userid' => '2', 'engine_password' => '');    
            return $engine_return;
        }       
        
    }
    
    if(!isset($bbuserinfo)) {
        $engine_return = array('engine_userid' => $wbbuserdata['userid'], 'engine_password' => $wbbuserdata['password']);
        return $engine_return;
    }    
}

function deleteDriverCookie($user_id) {
    global $HTTP_COOKIE_VARS,$db_sql,$tableprefix,$cookieprefix;
    $db_sql->sql_query("DELETE FROM ".$tableprefix."session WHERE userid='".$HTTP_COOKIE_VARS[$cookieprefix.'userid']."'");
    $prefix_length = strlen($cookieprefix);
    foreach ($_COOKIE AS $key => $val) {
        $index = strpos($key, $cookieprefix);
        if ($index !== false) {
            $key = substr($key, $prefix_length);
            if (trim($key) == '') continue;
            doCookie($key, '', time()-31536000);
        }
    }   
    return true;
}

function doCookie($name, $value = '', $time='') {
    global $cookieprefix;
    if ($name != 'sessionhash') {
        $name = $cookieprefix.$name;
    }
    setcookie($name, $value, $time, "/");
}

function htmlSpecialCharsUni($text) {
    $text = preg_replace('/&(?!#[0-9]+;)/si', '&amp;', $text);
    $text = str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $text);
    return $text;
}

?>