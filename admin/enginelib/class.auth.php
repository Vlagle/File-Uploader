<?php
// +----------------------------------------------------------------------+
// | EngineLib - Authentification Class                                   |
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
* class engineAuth
*
* Authentifizierungs Klasse aller Engines.
* Diese Klasse muß vor allen Seiten aufgerufen werden, andernfalls kann
* die Sicherheit nicht gewährleistet werden
* Benötigt die Session-Klasse, DB-Klasse und Template-Klasse!
* Loggt User ein, löscht und setzt Cookies, liest Userdaten aus der DB
*
* @access public
* @author Alex Höntschel <info@alexscriptengine.de>
* @version $Id: class.auth.php 6 2005-10-08 10:12:03Z alex $
* @copyright Alexscriptengine 2002,2003
* @link http://www.alexscriptengine.de
*/
class engineAuth {

    /**
    * engineAuth::$user
    *
    * Array, hält Userdaten vor
	* @var array
	* @access public
    */
    var $user = array();

    /**
    * engineAuth::$user_ip
    *
    * Speichert die IP-Adresse des Users
    * @var string
    */
    var $user_ip;

    /**
    * engineAuth::$guest_name
    *
    * Name eines Gastes, der nicht eingeloggt ist
	* @var string
    */
    var $guest_name = "";

    /**
    * engineAuth::$isIdentified
    *
    * True, wenn User identifiziert wurde
	* @var boolean
    */
    var $isIdentified = false;

    /**
    * engineAuth::$enableIPCheck
    *
    * Wahr, wenn IP-Check aktiviert ist.
    * @var integer
    */
    var $enableIPCheck = "";
	
	/**
	* engineAuth::$showAuthDebug
	*
	* Array, das die einzelnen Schritte aufnimmt
	* @var array
	*/
	var $showAuthDebug = array();
		
	/**
	* engineAuth::$enableDebug
	*
	* Aktiviert die Debug Funktion, wenn true
	* @var boolean
	*/
	var $enableDebug = true;
    
    var $usertable = "";
    
    var $permtable = "";


    /**
    * engineAuth::engineAuth()
    *
    * Konstrukter der Auth-Klasse
    * Initialisierung des Namens für Besucher und des IP-Checks
    * Werde Daten per $_POST übergeben, wird der Benutzer eingeloggt
    *
    * @param string $guest_name
    * @param boolean $enableIPCheck
	* @access public
    */
    function engineAuth($user_table,$perm_table,$guest_name="Besucher",$enableIPCheck=true) {
        global $_POST, $sess, $_ENGINE;
        $this->guest_name = $guest_name;
        $this->enableIPCheck = $enableIPCheck;
        $this->user_ip = $this->getUserIp();
        
        $this->usertable = $user_table;
        $this->permtable = $perm_table;

        if($_POST['username'] && $_POST['userpassword']) {
            if($this->getUserByName($_POST['username'], $_POST['userpassword'])) {
                $this->validatLogin();
                $this->isIdentified = true;
            } else {
                header("Location: ".$sess->url("misc.php?action=invalid_login")."");
                exit;                
            }
        } elseif($_REQUEST['action'] != "logout") {
            if($this->enableDebug) $this->showAuthDebug[] = "Kein Login per POST und kein Logout - normaler Vorgang";        
            $this->logUserIn();
        }
    }

    /**
    * engineAuth::getUserByName()
    *
    * User nach Username und Passwort auslesen
    * Kann mit und ohne Aktivierungsabfrage erfolgen
    *
	* @param string $name
	* @param string $pw
    * @param integer $enableactivation
	* @access public
	* @return boolean
    */
    function getUserByName($name, $pw,$enableactivation=1) {
        global $sess, $db_sql, $_ENGINE;
		$this->user = getUserByName($name,$pw,$enableactivation);
		
		if(!$this->user['userfound']) {
			if($this->enableDebug) $this->showAuthDebug[] = "Kein User gefunden - ";
			return false;
		} else {
		    return true;
		}		
    }

    /**
    * engineAuth::getUserByID()
    *
    * Auslesen der Userdaten nach ID
    * Mit und ohne Activation Check
    *
	* @param integer $id
    * @param integer $enableactivation
	* @access public
	* @return boolean
    */
    function getUserByID($id,$enableactivation=false) {
        global $sess, $db_sql, $_ENGINE;
		$this->user = getUserByID($id,$enableactivation);
		
		if(!$this->user['userfound']) {
			if($this->enableDebug) $this->showAuthDebug[] = "Kein User gefunden - ".$this->user['username'];
			return false;
		} else {
		    return true;
		}		
    }

    /**
    * engineAuth::getUserIp()
    *
    * IP-Adresse auslesen. Abprüfen verschiedener Variablen
	*
	* @access private
	* @return string
    */
    function getUserIp() {
        global $HTTP_SERVER_VARS, $HTTP_ENV_VARS;
        $ip = (!empty($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ((!empty($HTTP_ENV_VARS['REMOTE_ADDR'])) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : getenv("REMOTE_ADDR"));
        return substr($ip, 0, 50);
    }

    /**
    * engineAuth::logUserIn()
    *
    * User einloggen, Unterscheidung nach PHP-Version
    * Evtl. IP-Check durchführen
    * ACHTUNG bei AOL, hier werden rotierende IP's verwendet
	*
	* @access private
    * @return array
    */
    function logUserIn() {
        global $sess, $db, $_ENGINE;

        $ipcheck = false;
		
		if(BOARD_DRIVER != 'default') {
            unset($userdata);
			$userdata = getDriverCookie();
            $engine_userid = $userdata['engine_userid'];
            $engine_password = $userdata['engine_password'];
            $ipcheck = true;
            if($this->enableDebug) $this->showAuthDebug[] = "Board-Treiber ".BOARD_DRIVER." gefunden: BoardCookie ausgelesen (ID:  $engine_userid, $engine_password)";
		} else {        
            if(getPHPVersion() >= 410) {
    			$engine_userid = $_COOKIE['ase_userid'];
    			$engine_password = $_COOKIE['ase_passwort'];		
            } else {
                global $HTTP_COOKIE_VARS;
    			$engine_userid = $HTTP_COOKIE_VARS['ase_userid'];
    			$engine_password = $HTTP_COOKIE_VARS['ase_passwort'];	
            } 
            
            if($engine_userid && $engine_password){
    			 $ipcheck = true;
    			 if($this->enableDebug) $this->showAuthDebug[] = "Cookies gefunden (ID: $engine_userid, $engine_password)";
    		}
    
    		if(!$engine_userid || !$engine_password) {
                $engine_userid = $sess->getSessVar('engine_id');		
                $engine_password = $sess->getSessVar('engine_password');
    			if($this->enableDebug) $this->showAuthDebug[] = "Keine Cookies gefunden, Session auslesen";
            }
    
    		if(!$ipcheck) {
                if($sess->getSessVar('engine_user_ip') == $this->user_ip) {
                	$ipcheck = true;
                } else {
                	$ipcheck = false;
                }
    		}              
        }

        if(!$this->enableIPCheck) $ipcheck = true;

        if($this->getUserByID($engine_userid) && $ipcheck) {
            if($this->enableDebug) $this->showAuthDebug[] = "User via ID geholt (PW-Vergleich: $engine_userid)";
            if($engine_password === $this->user['userpassword']) {
				if($this->enableDebug) $this->showAuthDebug[] = "UserID ausgelesen, identifiziert (PW-Vergleich: $engine_password === ".$this->user['userpassword'].")";
                $this->isIdentified = true;
            } else {
				if($this->enableDebug) $this->showAuthDebug[] = "UserID nicht ausgelesen (PW-Vergleich: $engine_password === ".$this->user['userpassword'].")";
                $this->isIdentified = false;
            }
        }

        if($this->isIdentified) {
			if($this->enableDebug) $this->showAuthDebug[] = "Eingeloggt, Session-Variablen setzen";
            $this->validatLogin();
        } else {
            unset($this->user);	
			if($this->enableDebug) $this->showAuthDebug[] = "Gast-Session gesetzt";
            $sess->setSessVar("engine_id", 2);
            $sess->setSessVar("engine_name", $this->guest_name);
            $sess->setSessVar("engine_password", "");
            return $this->getUserByID("2",false);
        }
    }

    /**
    * engineAuth::validatLogin()
    *
    * Logindaten prüfen und Session-Variablen füllen
    * Bei aktiviertem IP-Check wird auch die Session-Variable für die IP gefüllt
	*
	* @access private
	* @return boolean
    */
    function validatLogin() {
        global $sess, $db, $_ENGINE;
        $sess->setSessVar("engine_id", $this->user['userid']);
        $sess->setSessVar("engine_name", $this->user['username']);
        $sess->setSessVar("engine_password", $this->user['userpassword']);

        if($this->enableIPCheck) $sess->setSessVar("engine_user_ip", $this->user_ip);

        //if(!$sess->getSessVar("engine_last_visit")) {
        $cookie_visit = "";
        
        if(getPHPVersion() >= 410) {
            $cookie_visit = $_COOKIE['ase_lastvisit'];
        } else {
            $cookie_visit = $HTTP_COOKIE_VARS['ase_lastvisit'];
        }        
        
        if(!$cookie_visit) {
            if($this->user['lastvisit'] < (time()-900)) {
                $this->updateVisit($this->user['userid']);
            }            
            setcookie("ase_lastvisit", $this->user['lastvisit'], time()+900,$_ENGINE['cookiepath'],$_ENGINE['cookiedomain']);
            $sess->setSessVar("engine_last_visit", $this->user['lastvisit']);
        } else {
            $sess->setSessVar("engine_last_visit", $cookie_visit);
        }
        $this->setEngineCookie($this->user['userid'],$this->user['userpassword']);

        return true;
    }
    
    /**
    * engineAuth::updateVisit()
    *
    * Datum updaten
	*
	* @access public
	* @return boolean
    */    
    function updateVisit($userid) {
        global $db_sql,$lastvisit_table_column,$userid_table_column;
        $db_sql->sql_query("UPDATE ".$this->usertable." SET ".$lastvisit_table_column."='".time()."' WHERE ".$userid_table_column."='".$userid."'");
    }
    
    /**
    * engineAuth::userLogin()
    *
    * Logindaten an DB senden und Session-Variablen füllen
    * Bei aktiviertem IP-Check wird auch die Session-Variable für die IP gefüllt
	*
	* @access private
	* @return boolean
    */
    function userLogin($username,$password,$dbInsert=false,$userid="") {
        global $sess, $db_sql, $_ENGINE;
        
        $password = md5($password);
        
        if($dbInsert) {
            $user2register = userLogin($username,$password,$useremail);
        } else {
            $user2register = $userid;
        }        
        
        $sess->setSessVar("engine_id", $user2register);
        $sess->setSessVar("engine_name", $username);
        $sess->setSessVar("engine_password", $password);

        if($this->enableIPCheck) $sess->setSessVar("engine_user_ip", $this->user_ip);

        $this->setEngineCookie($user2register,$password);

        return true;
    }    

	/**
    * engineAuth::userLogout()
    *
    * Alle Session-Variablen löschen und Cookies als Besucher setzen
	*
	* @access private
	* @return boolean
    *
    */
    function userLogout() {
		global $sess;
        if(BOARD_DRIVER != 'default') {
            deleteDriverCookie($sess->getSessVar('engine_id'));
        }        
		$sess->sessUnset();
		$this->deleteEngineCookie();
		$this->setEngineCookie("2","");
		return true;
	}	
	
	
	/**
    * engineAuth::setEngineCookie()
    *
    * Setzt Engine eigene Cookies für Passwort und Userid
	*
	* @access private
    */	
    function setEngineCookie($cookie_userid,$cookie_userpw) {
		global $_ENGINE;
		if($this->enableDebug) $this->showAuthDebug[] = "Cookies wird gesetzt(Userid: $cookie_userid, PW: $cookie_userpw)";
   		
		if(!setcookie("ase_userid", $cookie_userid, time()+(60 * 60 * 24 * 365),$_ENGINE['cookiepath'],$_ENGINE['cookiedomain'])) {
			if($this->enableDebug) $this->showAuthDebug[] = "Cookie f&uuml;r Userid konnte nicht gesetzt werden";
		}
   		
		if(!setcookie("ase_passwort", $cookie_userpw, time()+(60 * 60 * 24 * 365),$_ENGINE['cookiepath'],$_ENGINE['cookiedomain'])) {
			if($this->enableDebug) $this->showAuthDebug[] = "Cookie f&uuml;r Password konnte nicht gesetzt werden";
		}	
        
        if(BOARD_DRIVER != 'default' && $cookie_userid != 2) {
            setDriverCookie($cookie_userid,$cookie_userpw);
        }           
    }


	/**
    * engineAuth::deleteEngineCookie()
    *
    * Löscht Cookies der Engine
	*
	* @access private
    */
    function deleteEngineCookie() {
		global $_ENGINE;
		if(!setcookie("ase_userid","",time()-3600,$_ENGINE['cookiepath'],$_ENGINE['cookiedomain'])) {
			if($this->enableDebug) $this->showAuthDebug[] = "Cookie f&uuml;r Userid konnte nicht entfernt werden";
		}
		if(!setcookie("ase_passwort","",time()-3600,$_ENGINE['cookiepath'],$_ENGINE['cookiedomain'])) {
			if($this->enableDebug) $this->showAuthDebug[] = "Cookie f&uuml;r Passwort konnte nicht entfernt werden";
		}	
		if(!setcookie("ase_lastvisit","",time()-3600,$_ENGINE['cookiepath'],$_ENGINE['cookiedomain'])) {
			if($this->enableDebug) $this->showAuthDebug[] = "Cookie f&uuml;r Passwort konnte nicht entfernt werden";
		}        
	}

    /**
    * engineAuth::checkEnginePerm()
    *
    * Prüft die Berechtigung des Users ab,
    * ist die Berechtigung vorhanden, kann User die Seite betretten
    * Sonst wird die Meldung 'Zugriff verweigert' gezeigt
	*
	* @param string $mustHave
	* @access public
	* @include Datei misc.php um die Meldung und Weiterleitung für 'Zugriff verweigert' anzuzeigen
    * @return boolean
    */
    function checkEnginePerm($mustHave,$forceLogin=0) {
        global $sess, $db, $_ENGINE;
        if(!$this->isIdentified) $this->logUserIn();
        
        if($forceLogin) {
            if($this->user[$mustHave]) {
                return true;
            } else {
                return false;
            }        
        } else {
            if($this->user[$mustHave]) {
                return true;
            } else {
                header("Location: ".$sess->url("misc.php?action=perm_denied"));
                exit;            
            }
        }
    }
	
	/**
	* engineAuth::showEngineAuth()
	*
	* Debug Ausgabe
	* Zeigt alle Schritte während des Authentifizierungsvorgangs an
	* $enableDebug muss true sein, damit dieser Schritt arbeitet
	*
	* @access public
	* @return string
	*
	*/
    function showEngineAuth() {
		if($this->enableDebug) {
			$debug .= "<br /><b>Done during Identification:</b><br />-----------------------------------<br />\n";
			if(is_array($this->showAuthDebug)) {
				$step = 1;
				while(list($key,$value) = each($this->showAuthDebug)) {
				    $debug .= "<b>Step ".$step."</b>: $value<br />\n";
					$step++;
				}
			}		
			$debug .= "<b>Board Groupid:</b> ".$this->user['board_groupid']." entspricht ".$this->user['groupid'];
			return $debug;
		} else {
			return;
		}
	}
} // class end

?>