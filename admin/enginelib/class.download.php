<?php
// +----------------------------------------------------------------------+
// | EngineLib - Download Class                                           |
// +----------------------------------------------------------------------+
// | Copyright (c) 2005,2006 AlexScriptEngine - e-Visions                 |
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
* class engineDownload
*
* Klasse um einen Download anzustoßen und diesen über den Browser
* auszugeben. Die Klasse unterstützt Download-Manager und Resuming
* Benötigt die Auth-Klasse um die notwendige Geschwindigkeit auszulesen
*
* Die Klasse ist eine Weiterentwicklung der Klasse von Nguyen Quoc Bao
*
* @access public
* @author Alex Höntschel <info@alexscriptengine.de>
* @version $Id: class.download.php 352 2006-06-26 19:46:20Z Alex $
* @copyright Alexscriptengine 2005,2006
* @link http://www.alexscriptengine.de
**********************************************************************
* Original Copyright of the base class
**********************************************************************
* @author Nguyen Quoc Bao <quocbao.coder@gmail.com>
* @version 1.3
* @desc A simple object for processing download operation , support section downloading
* Please send me an email if you find some bug or it doesn't work with download manager.
* I've tested it with
*   - Reget
*   - FDM
*   - FlashGet
*   - GetRight
*   - DAP
* @copyright It's free as long as you keep this header .
*/
class engineDownload {

    /**
    * Hält je nach data_type entweder den
    * Pfad zur Datei, die Daten selbst oder
    * die Url zur Datei
    * @var string
    */
    var $data = null;

    /**
    * Grösse der Datei in Bytes
    * @var int
    */
    var $data_len = 0;

    /**
    * Datum wann Datei zuletzt modifiziert wurde
    */
    var $data_mod = 0;

    /**
    * Typ des Downloads der verwendet werden soll
    * 0 = Datei im Filesystem
    * 1 = Rohdaten
    * 2 = Verweis auf URL
    * @var int
    */
    var $data_type = 0;

    /**
    * 1, wenn es sich um teilweisen Download
    * bei Resuming handelt; wird vom Script gesetzt
    * @var int
    */
    var $data_section = 0;

    /**
    * True, wenn Resuming erlaubt werden soll
    * @var bool
    */
    var $use_resume = false;

    /**
    * Wenn true, expliziter Aufruf von
    * exit(); nach Beendigung des Downloads
    * @var bool
    */
    var $use_autoexit = false;

    /**
    * Dateiname der angezeigt wird
    * @var string
    */
    var $filename = null;

    /**
    * MIME-Type
    * @var string
    */
    var $mime = null;

    /**
    * Maximal zu lesende Bytes
    * pro Datei
    * @var int
    */
    var $bufsize = 2048;

    /**
    * Dateizeiger Start
    * Wird für Resuming verwendet
    * @var int
    */
    var $seek_start = 0;

    /**
    * Dateizeiger Ende
    * Wird für Resuming verwendet
    * @var int
    */
    var $seek_end = -1;

    /**
    * Total bandwidth has been used for this download
    * @var int
    */
    var $bandwidth = 0;

    /**
    * Speed limit
    * @var float
    */
    var $speed = 0;

    /**
    * Dateierweiterung, falls $data_type 0 ist
    * @var string
    */
    var $extension;

    /**
    * Konstruktor der Klasse.
    *
    * @return engineDownload
    */
    function engineDownload() {
        global $auth;
        if($auth->user['maxgroupdownloadspeed'] != 0) {
            $this->speed = $auth->user['maxgroupdownloadspeed'];
        }

        $this->use_resume = true;

    }

    /**
    * Initialisiert den Download und berechnet die Resuming Position
    *
    * @access private
    * @return bool
    */
    function initialize() {
        global $HTTP_SERVER_VARS;

        if ($this->mime == null) $this->mime = "application/octet-stream"; //default mime

        if (isset($_SERVER['HTTP_RANGE']) || isset($HTTP_SERVER_VARS['HTTP_RANGE'])) {

            if (isset($HTTP_SERVER_VARS['HTTP_RANGE'])) {
                $seek_range = substr($HTTP_SERVER_VARS['HTTP_RANGE'] , strlen('bytes='));
            } else {
                $seek_range = substr($_SERVER['HTTP_RANGE'] , strlen('bytes='));
            }

            $range = explode('-',$seek_range);

            if ($range[0] > 0) {
                $this->seek_start = intval($range[0]);
            }

            if ($range[1] > 0) {
                $this->seek_end = intval($range[1]);
            } else {
                $this->seek_end = -1;
            }

            if (!$this->use_resume) {
                $this->seek_start = 0;
            } else {
                $this->data_section = 1;
            }

        } else {
            $this->seek_start = 0;
            $this->seek_end = -1;
        }

        return true;
    }

    /**
    * Setzt den benötigten MIME-Typ bzw. den Default-MIMI
    * falls kein passender gefunden wurde
    *
    * @access private
    */
    function handleMIMEType() {
        global $mimetypes;
        if($this->data_type == 0) $this->extension = $this->getFileExtension();
        if($this->data_type == 0) {
            if($mimetypes[$this->extension] != "") {
                $this->mime = $mimetypes[$this->extension];
            } else {
                $this->mime = "application/force-download";
            }
        } else {
            $this->mime = "application/force-download";
        }
    }


    /**
    * Liest aus der Datei die Dateierweiterung
    * aus und gibt die Erweiterung zurück
    *
    * @acces private
    * @return string
    */
    function getFileExtension() {
        if($this->filename == null) {
            trigger_error("Required Filename missing", E_USER_ERROR);
        }
        ereg("(.+)\.(.+)", $this->filename, $regs);
        return strtolower($regs[2]);
    }

    /**
    * Sendet die notwendigen Header und überarbeitet bei
    * Verwendung des IE den Filenamen falls notwendig
    *
    * @param int $size Grösse der Datei in Bytes
    * @param int $seek_start Dateizeiger Start
    * @param int $seek_end Dateizeiger Ende
    * @access private
    */
    function setHeader($size, $seek_start=null, $seek_end=null) {
        $this->handleMIMEType();

        if(strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
            $filename = preg_replace('/\./', '%2e', $this->filename, substr_count($this->filename, '.') - 1);
        } else {
            $filename = $this->filename;
        }

        header('Content-type: ' . $this->mime);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Last-Modified: ' . date('D, d M Y H:i:s \G\M\T' , $this->data_mod));

        if ($this->data_section && $this->use_resume) {
            header("HTTP/1.0 206 Partial Content");
            header("Status: 206 Partial Content");
            header('Accept-Ranges: bytes');
            header("Content-Range: bytes $seek_start-$seek_end/$size");
            header("Content-Length: " . ($seek_end - $seek_start + 1));
        } else {
            header("Content-Length: $size");
        }
    }

    /**
    * Externe Download-Möglichkeit. Berechnet die Resuming-Daten
    * und sendet über $this->setHeader die benötigten Header an den
    * Client. Der Download selbst muss extern ausgelesen werden
    *
    * @param $size int Dateigrösse der Daten, die zum
    *                  Download angeboten werden soll
    * @acces public
    * @return bool
    */
    function downloadExternal($size) {
        if (!$this->initialize()) return false;
        ignore_user_abort(true);
        if ($this->seek_start > ($size - 1)) $this->seek_start = 0;
        if ($this->seek_end <= 0) $this->seek_end = $size - 1;
        $this->setHeader($size,$seek,$this->seek_end);
        $this->data_mod = time();
        return true;
    }

    /**
    * Startet den eigentlichen Download und sendet die
    * header über $this->setHeader()
    *
    * @access public
    * @return bool
    */
    function download() {
        if (!$this->initialize()) return false;

        $seek = $this->seek_start;
        $speed = $this->speed;
        $bufsize = $this->bufsize;
        $packet = 1;

        @ob_end_clean();
        $old_status = ignore_user_abort(true);
        @set_time_limit(0);
        $this->bandwidth = 0;

        $size = $this->data_len;

        if ($this->data_type == 0) {

            $size = filesize($this->data);
            if ($seek > ($size - 1)) $seek = 0;
            if ($this->filename == null) $this->filename = basename($this->data);

            $res = fopen($this->data,'rb');
            if ($seek) fseek($res , $seek);
            if ($this->seek_end < $seek) $this->seek_end = $size - 1;

            $this->setHeader($size,$seek,$this->seek_end);
            $size = $this->seek_end - $seek + 1;

            while (!(connection_aborted() || connection_status() == 1) && $size > 0) {
                if ($size < $bufsize) {
                    echo fread($res , $size);
                    $this->bandwidth += $size;
                } else {
                    echo fread($res , $bufsize);
                    $this->bandwidth += $bufsize;
                }

                $size -= $bufsize;
                flush();
                ob_flush();

                if ($speed > 0 && ($this->bandwidth > $speed*$packet*1024)) {
                    sleep(1);
                    $packet++;
                }
            }
            fclose($res);

        } elseif ($this->data_type == 1) {
            if ($seek > ($size - 1)) $seek = 0;
            if ($this->seek_end < $seek) $this->seek_end = $this->data_len - 1;
            $this->data = substr($this->data , $seek , $this->seek_end - $seek + 1);
            if ($this->filename == null) $this->filename = time();
            $size = strlen($this->data);
            $this->setHeader($this->data_len,$seek,$this->seek_end);
            while (!connection_aborted() && $size > 0) {
                if ($size < $bufsize) {
                    $this->bandwidth += $size;
                } else {
                    $this->bandwidth += $bufsize;
                }

                echo substr($this->data , 0 , $bufsize);
                $this->data = substr($this->data , $bufsize);

                $size -= $bufsize;
                flush();

                if ($speed > 0 && ($this->bandwidth > $speed*$packet*1024)) {
                    sleep(1);
                    $packet++;
                }
            }
        } else if ($this->data_type == 2) {
            header('location: ' . $this->data);
        }

        if ($this->use_autoexit) exit();

        //restore old status
        ignore_user_abort($old_status);
        set_time_limit(ini_get("max_execution_time"));

        return true;
    }

    /**
    * Setzt den Download auf eine Datei
    * im normalen Filesystem
    *
    * @param $dir string Pfad zur Datei
    * @access public
    * @return bool
    */
    function setByFile($dir) {
        if (is_readable($dir) && is_file($dir)) {
            $this->data_len = 0;
            $this->data = $dir;
            $this->data_type = 0;
            $this->data_mod = filemtime($dir);
            return true;
        } else {
            return false;
        }
    }

    /**
    * Setzt den Download auf Rohdaten
    *
    * @param $data string Daten die zum Download
    *                     gesendet werden sollen
    * @access public
    * @return bool
    */
    function setByData($data) {
        if ($data == '') return false;
        $this->data = $data;
        $this->data_len = strlen($data);
        $this->data_type = 1;
        $this->data_mod = time();
        return true;
    }

    /**
    * Setzt den Download auf eine URL
    * extern oder intern
    *
    * @param $data string URL zum Download
    * @access public
    * @return bool
    */
    function setByUrl($data) {
        $this->data = $data;
        $this->data_len = 0;
        $this->data_type = 2;
        return true;
    }

    /**
    * Manuelles anpassen des Last-modified Datums
    * im Header
    *
    * @param $time int Unix Zeitstempel
    * @access public
    */
    function setLastModtime($time) {
        $time = intval($time);
        if ($time <= 0) $time = time();
        $this->data_mod = $time;
    }
}

?>