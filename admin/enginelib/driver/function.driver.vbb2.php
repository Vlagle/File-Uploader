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
$UrlToBoard = "http://192.168.0.100/boards/vbb2";

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
    
    //if($enableactivation) $more_sql = "AND activation='1'";   
    
    $sql = "
        SELECT 
            user.userid AS userid,
            user.usergroupid AS board_groupid,
            user.username AS username,
            user.password AS userpassword,
            user.email AS useremail,
            user.homepage AS userhp,
            user.icq AS usericq,
            user.aim AS aim,
            user.yahoo AS yim,
            user.showemail AS show_email_global,
            user.joindate AS regdate,
            user.lastvisit AS lastvisit,
            user.avatarid AS avatarid
        FROM user
        WHERE username = '".addslashes(htmlspecialchars($name))."' AND password = '".md5($pw)."'";
        
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
            user.userid AS userid,
            user.usergroupid AS board_groupid,
            user.username AS username,
            user.password AS userpassword,
            user.email AS useremail,
            user.homepage AS userhp,
            user.icq AS usericq,
            user.aim AS aim,
            user.yahoo AS yim,
            user.showemail AS show_email_global,
            user.joindate AS regdate,
            user.lastvisit AS lastvisit,
            user.avatarid AS avatarid
        FROM user
        WHERE userid = '".$id."'";

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
    engineErrorHandler(E_USER_ERROR, "VBB2 Interface Error", __FILE__, __LINE__, "");
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
    $sql = $db_sql->query_array("SELECT * FROM user WHERE userid='".intval($uid)."' $add");
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
    $sql = $db_sql->query_array("SELECT * FROM user WHERE username='".addslashes(htmlspecialchars($uname))."'");
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
    engineErrorHandler(E_USER_ERROR, "VBB2 Interface Error", __FILE__, __LINE__, "");
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
    engineErrorHandler(E_USER_ERROR, "VBB2 Interface Error", __FILE__, __LINE__, "");
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
    engineErrorHandler(E_USER_ERROR, "VBB2 Interface Error", __FILE__, __LINE__, "");
}

/**
* rewriteUser()
*
* Schreibt geänderte Userdaten in Datenbank
* Url Prüfung hinzugefügt
*/
function rewriteUser($uid,$umail,$uhp,$location,$gender,$uavatar,$global_mail,$icq,$aim,$yim,$interests) {
    engineErrorHandler(E_USER_ERROR, "VBB2 Interface Error", __FILE__, __LINE__, "");
}
    
/**
* rewritePW()
*
* Passwort in die DB eintragen
* Wird benötigt, wenn User eigenes Passwort ändert
*/
function rewritePW($password,$uid) {
    engineErrorHandler(E_USER_ERROR, "VBB2 Interface Error", __FILE__, __LINE__, "");
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
            return $UrlToBoard."/register.php";     
            break;      
        case "remember":
            return $UrlToBoard."/member.php?action=lostpw";     
            break;      
        case "memberdetail":
            return $UrlToBoard."/member.php?action=getinfo&userid=".$id;        
            break;      
        case "changeaccount":
            return $UrlToBoard."/usercp.php";       
            break;                              
    }
}

//---------------------------------------- VBB2 Specific Functions


function setDriverCookie($cookie_userid,$cookie_userpw) {
    global $HTTP_COOKIE_VARS,$db_sql;      
    setcookie("bbuserid","$cookie_userid",time()+3600*24*365,"/");
    setcookie("bbpassword","$cookie_userpw",time()+3600*24*365,"/");
}

function getDriverCookie() {
    global $HTTP_COOKIE_VARS,$HTTP_USER_AGENT,$REMOTE_ADDR,$db_sql;
    
    $HTTP_USER_AGENT=substr($HTTP_USER_AGENT,0,50);
    $REMOTE_ADDR=substr($REMOTE_ADDR,0,50);
    
    if(isset($HTTP_COOKIE_VARS['sessionhash'])) {
        $sessionhash = $HTTP_COOKIE_VARS['sessionhash'];
    }
    
    if($sessionhash && isset($HTTP_COOKIE_VARS['sessionhash']) && $HTTP_COOKIE_VARS['sessionhash'] && $sessionhash != $HTTP_COOKIE_VARS['sessionhash']) {
        $falsecookiehash=1;
    }
    
    $createsession = 0;
    
    if($sessionhash) {
        $session = $db_sql->query_array("SELECT sessionhash,userid,host,useragent,styleid FROM session WHERE sessionhash='".addslashes($sessionhash)."' AND host='".addslashes($REMOTE_ADDR)."' AND useragent='".addslashes($HTTP_USER_AGENT)."'");
        if($session['sessionhash']) {
            $vbb_userid = $session['userid'];
            $session['lastactivity'] = time();
            $db_sql->sql_query("UPDATE session SET lastactivity = '".$session['lastactivity']."', location = '".addslashes($REQUEST_URI)."' WHERE sessionhash = '".$sessionhash."'");
        } else {
            $createsession = 1;
        }
    } else {
        $createsession = 1;    
    }   

    if($createsession == 1 || $session['userid'] == 0) {
        /*print_r($HTTP_COOKIE_VARS);
        exit;*/
        if(isset($HTTP_COOKIE_VARS['bbuserid']) && isset($HTTP_COOKIE_VARS['bbpassword'])) {
            $bbuserinfo = $db_sql->query_array("SELECT user.*,userfield.* FROM user LEFT JOIN userfield ON userfield.userid=user.userid WHERE user.userid='".$HTTP_COOKIE_VARS['bbuserid']."'");
            if ($HTTP_COOKIE_VARS['bbpassword'] == $bbuserinfo['password']) {
                $session = array();
                $session['sessionhash'] = md5(uniqid(microtime()));
                $session['host'] = $REMOTE_ADDR;
                $session['useragent'] = $HTTP_USER_AGENT;
                $session['userid'] = $bbuserinfo['userid'];         
                $engine_return = array('engine_userid' => $HTTP_COOKIE_VARS['bbuserid'], 'engine_password' => $HTTP_COOKIE_VARS['bbpassword']);
                $db_sql->sql_query("DELETE FROM session WHERE userid = '".$session['userid']."'");
                $db_sql->sql_query("INSERT INTO session (sessionhash,userid,host,useragent,lastactivity) VALUES ('".addslashes($session['sessionhash'])."','$bbuserinfo[userid]','".addslashes($session['host'])."','".addslashes($session['useragent'])."','".time()."')");
                setcookie("sessionhash", "$session[sessionhash]", 0, "/");
                
                return $engine_return;              
            } else {
                if($createsession == 1) $guestsession = 1;
                unset($bbuserid);
                unset($bbuserinfo);
                setcookie("bbuserid", "", time()-31536000, "/");
                setcookie("bbpassword", "", time()-31536000, "/");
            }
        } elseif($createsession == 1) {
            unset($bbuserid);
            $guestsession = 1;
        }
        
        if(isset($guestsession)) { 
            $db_sql->sql_query("DELETE FROM session WHERE userid='0' AND host = '".addslashes($REMOTE_ADDR)."'");
            $engine_return = array('engine_userid' => '2', 'engine_password' => '');
            $session = array();
            $session['sessionhash'] = md5(uniqid(microtime()));
            $session['host'] = $REMOTE_ADDR;
            $session['useragent'] = $HTTP_USER_AGENT;
            $session['userid'] = $bbuserinfo['userid']; 
            $session['engine_userid'] = 2;
            $session['engine_password'] = '';            
            $db_sql->sql_query("INSERT INTO session (sessionhash,userid,host,useragent,lastactivity) VALUES ('".$session['sessionhash']."','0','".addslashes($session['host'])."','".addslashes($session['useragent'])."','".time()."')");
            setcookie("sessionhash", "$session[sessionhash]", 0, "/");
            
            return $engine_return;
        }       
        
    }
    
    if(!isset($bbuserinfo)) {
        $engine_return = array('engine_userid' => $wbbuserdata['userid'], 'engine_password' => $wbbuserdata['password']);
        return $engine_return;
    } 
}

function deleteDriverCookie($user_id) {
    global $HTTP_COOKIE_VARS,$db_sql;
    $db_sql->sql_query("DELETE FROM session WHERE userid='".$HTTP_COOKIE_VARS['bbuserid']."'");
    setcookie("bbuserid","",time()-31536000,"/");
    setcookie("bbpassword","",time()-31536000,"/");
    setcookie("bbstyleid","",time()-31536000,"/");
    setcookie("sessionhash","",time()-31536000,"/");
    return true;
}

?>