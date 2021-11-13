<?php
// +----------------------------------------------------------------------+
// | EngineLib - Upload Class                                             |
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
* class upload
* 
* Upload Klasse der Engines, Basis für alle Engines die eine Upload
* Funktion verwenden
* 
* @access public
* @author Alex Höntschel <info@alexscriptengine.de>
* @version $Id: class.upload.php 6 2005-10-08 10:12:03Z alex $
* @copyright Alexscriptengine 2002,2003
* @link http://www.alexscriptengine.de
*/

class upload {
    
    var $filesdir;
    var $fieldname;
    var $lang = array();
    var $upload_file;
    var $uploadError = array();
    var $new_destname;
    var $change_filename = 0;
    var $destName;
    var $maxsize;
    var $uploadWrong = false;


    /**
     * upload::setFilesDir()
     * 
     * @access public
     * @param $filesdir
     * @return 
     */
    function setFilesDir($filesdir) { // Ordner setzen
        $this->filesdir = $filesdir;
    }   
    
    /**
     * upload::setChangeFilename()
     * 
     * @access privat
     * @param $change_filename
     * @return 
     */
    function setChangeFilename($change_filename=1) { // darf der Filename ge&auml;ndert werden
        $this->change_filename = $change_filename;
    }       
    
    /**
     * upload::setAllowedExtensions()
     * 
     * @access public
     * @param $allowedextensions
     * @return 
     */
    function setAllowedExtensions($allowedextensions = "gif,jpg,jpeg,png,zip,rar") { // g&uuml;ltige Erweiterungen
        $this->allowedextensions = $allowedextensions;
    }
    
    /**
     * upload::checkAllowedExtensions()
     * 
     * @access privat
     * @return 
     */
    function checkAllowedExtensions() {
        if(!$this->allowedextensions) { // keine m&ouml;gliche Dateigroesse gesetzt
            $this->setAllowedExtensions();
        }   
            
        $extens = explode(",",$this->allowedextensions);
        $file_extension = strtolower(substr(strrchr($this->HTTP_POST_FILES[$this->fieldname]['name'],"."),1));
        
        if(!in_array($file_extension,$extens)) { 
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * upload::setMaxFileSize()
     * 
     * @access public
     * @param $maxsize
     * @return 
     */
    function setMaxFileSize($maxsize = "2097152") { // max. Filegroesse initialisieren
        $this->maxsize = $maxsize;
    }       
    
    /**
     * upload::checkMaxFileSize()
     * 
     * @access private
     * @return 
     */
    function checkMaxFileSize() { // groesse pruefen
        if ($this->HTTP_POST_FILES[$this->fieldname]['size'] > $this->maxsize) {
            return false;
        } else {
            return true;
        }
    }   
    
    /**
     * upload::uploadFile()
     * 
     * @access public
     * @param $fieldname
     * @return 
     */
    function uploadFile($fieldname) { // Aufruf der Klasse mit dem entsprechenden Formularfeld
        global $_COOKIE, $_POST, $_GET, $_FILES, $a_lang;
        
        if (isset($_COOKIE[$field_name]) || isset($_POST[$field_name]) || isset($_GET[$field_name])) die("Abort, security risk!");
        
        $this->HTTP_POST_FILES = $_FILES;
        $this->fieldname = $fieldname;
        $this->lang = $a_lang;
        
        if(!$this->maxsize) { // keine maximale Gr&ouml;sse gesetzt
            $this->setMaxFileSize();
        }       
        
        if(!$this->change_filename) { // keine maximale Gr&ouml;sse gesetzt
            $this->setChangeFilename();
        }           
        
        if ($this->saveFile()) { // File hochladen, klappt, dann true ansonsten false
            return true;
        } else {
            return false;
        }
    }   
    
    /**
     * upload::saveFile()
     * 
     * @access private
     * @return 
     */
    function saveFile() {   // speichern und file hochladen, evtl. umbenennen
        global $lang;
        $this->upload_file = $this->HTTP_POST_FILES[$this->fieldname]['tmp_name'];
        $break_upload = false;
        
        if(empty($this->upload_file) || $this->HTTP_POST_FILES[$this->fieldname]['tmp_name'] == "none") { // File ist nicht dabei
            $this->setErrorCode($lang['uploads_copy']);
            $this->uploadWrong = true;
        }
        
        if (!$this->checkMaxFileSize()) {
            $this->setErrorCode($lang['uploads_size']." ".$this->maxsize." Bytes");
            $this->uploadWrong = true;
        }       
        
        if (!$this->checkAllowedExtensions()) {
            $this->setErrorCode($lang['uploads_extens']);
            $this->uploadWrong = true;
        }  
        
        if(file_exists($this->filesdir."/".$this->HTTP_POST_FILES[$this->fieldname]['name'])) {
            if($this->change_filename) {
                $this_extension = strtolower(substr(strrchr($this->HTTP_POST_FILES[$this->fieldname]['name'],"."),1));
                $build_destname = strtolower($this->randomName());
                $this->new_destname = $build_destname.".".$this_extension;                          
            } else {
                $this->setErrorCode($this->lang['uploads_stillexist']); 
                $this->uploadWrong = true;
            }           
        } else {
            $this->new_destname = $this->HTTP_POST_FILES[$this->fieldname]['name'];
        } 
        
        if(!$this->uploadWrong) {
            $this->new_destname = preg_replace('/[^a-z0-9_\-\.]/i', '_', $this->new_destname);
            if(@move_uploaded_file($this->upload_file,$this->filesdir."/".$this->new_destname)) { // Datei kopieren
                @chmod($this->filesdir."/".$this->new_destname, 0777);  
                $this->setErrorCode($lang['uploads_success']);  
                $this->setDestName($this->new_destname);
                return true;                    
            } else {
                $this->setErrorCode($lang['uploads_copy']);     
                return false;
            }               
        } 
        return false;
    }
    
    /**
     * upload::randomName()
     * 
     * @access private
     * @param $word_len
     * @return 
     */
    function randomName($word_len=10) { // zuf&auml;lliger Name
        $allchar = "ABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789" ; 
        $str = "" ; 
        mt_srand (( double) microtime() * 1000000 ); 
        
        for($i = 0; $i<$word_len ; $i++) { 
            $str .= substr($allchar,mt_rand(0,25),1) ; 
        }        
        return $str ; 
    }       
    
    /**
     * upload::getErrorCode()
     * Errors auslesen
     * 
     * @access public
     * @return 
     */
    function getErrorCode() { // Errors auslesen
        $error_msg .= "<br />";
        foreach ($this->uploadError[$this->HTTP_POST_FILES[$this->fieldname]['name']] as $msg) {
          $error_msg .= $msg."<br />";
        }    
        return $error_msg;    
    }
    
    /**
     * upload::setErrorCode()
     * Errors setzen und aneinanderfügen
     * 
     * @access private
     * @param $errorMsg
     * @return 
     */
    function setErrorCode($errorMsg) { // Errors setzen
        $this->uploadError[$this->HTTP_POST_FILES[$this->fieldname]['name']][] = $errorMsg;
    }    
    
    function setDestName($desName) { // neuen Namen setzen
        $this->destName = $desName;
    }  
    
    function getDestName() { // neuen Namen auslesen
        return $this->destName;    
    }   

}
?>