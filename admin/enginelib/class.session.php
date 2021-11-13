<?php
// +----------------------------------------------------------------------+
// | EngineLib - Session Class                                            |
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

//define("ENGINE_SESS_LIFE_TIME", 3600);

/**
* class engineSession
*
* Session Klasse der Engines, Basis für alle Engines
* Übergreifende Funktion, arbeitet mit $HTTP_SESSION_VARS und mit $_SESSION
* Benötigt die Session-Klasse, DB-Klasse und Template-Klasse!
*
* @access public
* @author Alex Höntschel <info@alexscriptengine.de>
* @version $Id: class.session.php 6 2005-10-08 10:12:03Z alex $
* @copyright Alexscriptengine 2002,2003
* @link http://www.alexscriptengine.de
*/

class engineSession {
    /**
    * engineSession::$sess_name
    *
    * Name der Session
    * @ var string
    */
    var $sess_name;

    /**
    * engineSession::$sess_id
    *
    * Session-ID der Session
    * @var string
    */
    var $sess_id;

    /**
    * engineSession::$engineUrl
    *
    * Haupturl zur Engine
    * @var string
    */
    var $engineUrl;

    /**
    * engineSession::$disableTransSID
    *
    * Automatisches anhängen der Session-Daten deaktiviert
    * Momentan nicht genutzt
    * @var boolean
    */
    var $disableTransSID;

    /**
    * engineSession::$noCache
    *
    * Bei true wird ein Header gesendet, der das Cachen der Seiten verhindert
    * @var boolean
    */
    var $noCache;

    /**
    * engineSession::engineSession()
    *
    * Konstruktor der Engine Session
    * Die SID wird überprüft, entspricht diese nicht der normalen Länge
    * Oder ist die aufrufende URL/IP nicht die eigene wird die Session
    * zerstört
    *
    * @access public
    * @param string $sess_name
    * @param boolean $disableTransSID
    * @param boolean $noCache
    */
    function engineSession($sess_name="ENGINEsessID", $disableTransSID=true, $noCache=false) {
        global $_SERVER, $HTTP_SERVER_VARS,$_ENGINE, $_POST, $auth;
        if ($this->disableTransSID) @ini_set("session.use_trans_sid","0");

        if($this->noCache)$this->sendNoCacheHeader();

        $this->sess_name = $sess_name;

        $this->engineUrl = $_ENGINE['main_url'];

        @ini_set("session.gc_maxlifetime", $_ENGINE['sess_max_lifetime']);

        session_name($this->sess_name);
        @session_start();

        // Session-ID prüfen ob 32 stellen, wenn nicht, neue ID erzeugen
        if (strlen(session_id()) != 32) {
                mt_srand ((double)microtime()*1000000);
                session_id(md5(uniqid(mt_rand())));
        }

        $this->sess_id = session_id();

        $passed=true;
        $_SERVER = $this->readServerVars();

            // Eine Session kann nur 'legal' durch den eigenen Server verlinkt worden sein,
            // daher muss der Referrer den eigenen Servernamen beinhalten
        if (strpos($_SERVER["HTTP_REFERER"],$_SERVER["HTTP_HOST"])===false) {
            $passed=false;
        }

        if(!$passed) {
            $this->destEngineSess();
        }

    }


    /**
    * engineSession::readServerVars()
    *
    * Globale Server-Variablen auslesen, wenn PHP kleiner 4.1.0
    *
    * @access privatr
    * @return string
    */
    function readServerVars() {
        global $HTTP_SERVER_VARS;
        if(getPHPVersion() <= 410) {
            $_SERVER = &$GLOBALS['HTTP_SERVER_VARS'];
        }
        return $_SERVER;
    }


    /**
    * engineSession::setSessVar()
    *
    * Session Variable Setzen
    *
    * @access public
    * @param string $varname
    * @param string $varvalue
    */
    function setSessVar($varname, $varvalue="") {
        global $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_COOKIE_VARS, $HTTP_SESSION_VARS;
        if(!isset($varname) || !isset($varvalue)) trigger_error("Function setSessVar( String \$varname, mixed \$value ) expects two parameters!",E_USER_ERROR);

        if(getPHPVersion() >= 410) {
            $_SESSION[$varname] = $varvalue;
            if(!isset($GLOBALS[$varname])) $GLOBALS[$varname] = $varvalue;
        } else {
            global $HTTP_SESSION_VARS;
            session_register($varname);
            $GLOBALS['HTTP_SESSION_VARS'][$varname] = $varvalue;
            if(!isset($GLOBALS[$varname])) $GLOBALS[$varname] = $varvalue;
        }
    }

    /**
    * engineSession::getSessVar()
    *
    * Wert einer Session-Variable auslesen
    *
    * @access public
    * @link http://wwww.php.net/session
    * @param string $varname
    * @return string
    */
    function getSessVar($varname) {
        if(!isset($varname)) trigger_error("Function getSessVar( String \$varname ) expects a parameter!",E_USER_ERROR);

        if(getPHPVersion() >= 410) {
            if (isset($GLOBALS[$varname])) {
                return $GLOBALS[$varname];
            } elseif(isset($GLOBALS['_SESSION'][$varname])) {
                $GLOBALS[$varname] = $GLOBALS['_SESSION'][$varname];
                return $GLOBALS['_SESSION'][$varname];
            }
        } else {
            if (isset($GLOBALS[$varname])) {
                return $GLOBALS[$varname];
            } elseif(isset($GLOBALS['HTTP_SESSION_VARS'][$varname])) {
                $GLOBALS[$varname] = $GLOBALS['HTTP_SESSION_VARS'][$varname];
                return $GLOBALS['HTTP_SESSION_VARS'][$varname];
            }
        }
    }

    /**
    * engineSession::sendNoCacheHeader()
    *
    * Header senden, wenn kein Caching nötig/gewünscht
    *
    * @access private
    */
    function sendNoCacheHeader()    {
        header("Expires: Sat, 28 May 1999 22:27:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Cache-Control: post-check=0, pre-check=0");
    }


    /**
    * engineSession::getEngineSID()
    *
    * SID-String übertragen
    *
    * @access public
    * @return string
    */
    function getEngineSID() {
        return $this->sess_name . "=" . $this->sess_id;
    }


    /**
    * engineSession::getSid()
    *
    * Session-ID zurückgeben
    *
    * @access public
    * @return string
    */
    function getSid() {
        return $this->sess_id;
    }


    /**
    * engineSession::varSessUnset()
    *
    * Session-Variable löschen
    *
    * @access public
    * @param string $varname
    */
    function varSessUnset($varname) {
        if(!isset($varname)) trigger_error("Function varSessUnset( String \$varname ) expects a parameter!",E_USER_ERROR);

        if(getPHPVersion() >= 410) {
            if (isset($GLOBALS[$varname])) unset($GLOBALS[$varname]);
            if (isset($GLOBALS['_SESSION'][$varname])) unset($GLOBALS['_SESSION'][$varname]);
        } else {
            if (isset($GLOBALS[$varname])) unset($GLOBALS[$varname]);
            if (isset($GLOBALS['HTTP_SESSION_VARS'][$varname])) unset($GLOBALS['HTTP_SESSION_VARS'][$varname]);
        }
    }

    /**
    * engineSession::sessUnset()
    *
    * Alle Session-Variablen löschen
    *
    * @access public
    */
    function sessUnset() {
        if(getPHPVersion() >= 410) {
            if(isset($GLOBALS['_SESSION'])) $a = $GLOBALS['_SESSION'];
            while(list($key,) = each($a))
                $this->varSessUnset($key);
        } else {
            if(isset($GLOBALS['HTTP_SESSION_VARS'])) $a = $GLOBALS['HTTP_SESSION_VARS'];
            while(list($key,) = each($a))
                $this->varSessUnset($key);
        }
    }

    /**
    * engineSession::destEngineSess()
    *
    * Alle Session Variablen löschen und Session killen
    *
    * @access public
    * @return boolean
    */
    function destEngineSess() {
        $this->sessUnset();
        if(@session_destroy()) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * engineSession::closeSess()
    *
    * Beendet die aktuelle Session und speichert die
    * Session Daten
    *
    * Alias zu session_write_close()
    *
    * @access public
    */
    function closeSess() {
        session_write_close();
    }

    /**
    * engineSession::url()
    *
    * Gibt die Url zu einer Seite inkl.Query-String und
    * Session-ID/Session-Name zurück
    *
    * @access public
    * @param string $filename
    * @return string
    */
    function url($filename) {
        global $_ENGINE;
        $return_url = $_ENGINE['main_url']."/".$filename;
        $return_url .= ( strpos($filename, "?") != false ?  "&" : "?" );
        $return_url .= $this->getEngineSID();
        $return_url = str_replace('&', '&amp;', $return_url);
        return $return_url;
    }

    /**
    * engineSession::adminUrl()
    *
    * Gibt die Url zu einer Seite inkl.Query-String und
    * Session-ID/Session-Name zurück
    *
    * @access public
    * @param string $filename
    * @return string
    */
    function adminUrl($filename) {
        global $_ENGINE;
        $return_url = $_ENGINE['main_url']."/admin/".$filename;
        $return_url .= ( strpos($filename, "?") != false ?  "&" : "?" );
        $return_url .= $this->getEngineSID();
        $return_url = str_replace('&', '&amp;', $return_url);
        return $return_url;
    }

    /**
    * engineSession::self_url()
    *
    * Liest die Url des momentanen Scripts aus und gibt dieses
    * als formatierte Url inkl. Session-Daten zurück
    *
    * @access public
    * @return string
    */
    function self_url() {
        global $HTTP_SERVER_VARS;

        return $this->url($HTTP_SERVER_VARS["PHP_SELF"] .
        ((isset($HTTP_SERVER_VARS["QUERY_STRING"]) && ("" != $HTTP_SERVER_VARS["QUERY_STRING"]))
        ? "?" . $HTTP_SERVER_VARS["QUERY_STRING"] : ""));
    }

    /**
    * engineSession::showEngineSess()
    *
    * Registrierte Session-Variablen anzeigen
    * Debug Modus für die Session-Kontrolle
    *
    * @access public
    * @return string
    */
    function showEngineSess() {
        $debug .= "<br /><b>Variables set in current session:</b><br />-----------------------------------<br />\n";
        if(getPHPVersion() >= 410) {
            if(isset($GLOBALS['_SESSION'])) $a = $GLOBALS['_SESSION'];
            while(list($key,$value) = each($a))
                $debug .= "Variable1: <b>$key</b> - Value: <b>$value</b><br />\n";

        } else {
            if(isset($GLOBALS['HTTP_SESSION_VARS'])) $a = $GLOBALS['HTTP_SESSION_VARS'];
            while(list($key,$value) = each($a))
                $debug .= "Variable2: <b>$key</b> - Value: <b>$value</b><br />\n";
        }

        $debug .= "<br /><b>Cookies:</b><br />-----------------------------------<br />\n\nUserid: $_COOKIE[enginecookieuserid]<br />\nPasswort: $_COOKIE[enginecookiepasswort]<br />";

        return $debug;
    }
}
?>