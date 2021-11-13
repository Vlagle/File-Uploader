<?php
// +----------------------------------------------------------------------+
// | EngineLib - Template Class                                           |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003,2004 AlexScriptEngine - e-Visions                 |
// +----------------------------------------------------------------------+
// | This code is not freeware. Please read our licence condition care-   |
// | fully to find out more. If there are any doubts please ask at the    |
// | Support Forum                                                        |
// | This class was published under LGPL-Licence, original                |
// | from Brent R. Matzelle                                               |
// +----------------------------------------------------------------------+
// | Author: Alex Höntschel <info@alexscriptengine.de>                    |
// | Web: http://www.alexscriptengine.de                                  |
// | IMPORTANT: No email support, please use the support forum at         |
// |            http://www.alexscriptengine.de                            |
// +----------------------------------------------------------------------+
// $Id: class.template.php 6 2005-10-08 10:12:03Z alex $

/**
* class engineTemplate
*
* Template Klasse aller Engines.
* Diese Klasse verarbeitet alle Template Kommandos und steuert
* die GZip-Klasse.
* Benötigt die GZip-Klasse
* Basiert auf der Template-Klasse von Richard Heyes (Version 1.4)
*
* @access public
* @author Alex Höntschel <info@alexscriptengine.de>
* @version $Id: class.template.php 6 2005-10-08 10:12:03Z alex $
* @copyright Alexscriptengine 2002,2003,2004
* @link http://www.alexscriptengine.de
*/
class engineTemplate{

    /**
    * template::$var_names
    *
    * Bezeichnung der Variablen innerhalb der
    * Templates
	* @var array
    */
    var $var_names	= array();
    
    /**
    * template::$var_global
    *
    * Bezeichnung der Variablen innerhalb der
    * Templates, die fuer alle Templates verwendet werden
	* @var array
    */
    var $var_global	= array();    
    
    /**
    * template::$files
    *
    * Hält Array() mit den einzelnen Template File_ID's
	* @var array
    */
    var $files		= array();
    
    /**
    * template::$folder
    *
    * Pfad zum Ordner in dem die Dateien liegen
    * @var string
    */
    var $folder		= '';    
    
    /**
    * template::$start
    *
    * Anfangs Identifier für Variablen
    * @var string
    */
    var $start		= '{';
    
       /**
    * template::$end
    *
    * Ende Identifier für Variablen
    * @var string
    */
    var $end		= '}';
    
    /**
    * template::$header_file
    *
    * Variable der eigenen Header-Datei
    * @var string
    */    
    var $header_file = '';
    
    /**
    * template::$footer_file
    *
    * Variable der eigenen Footer-Datei
    * @var string
    */    
    var $footer_file = '';
    
    /**
    * template::$key_cache
    *
    * Variablen in Templates
    * @var array
    */    
    var $key_cache = array();
    
    /**
    * template::$val_cache
    *
    * Wert fuer Variablen im Template
    * @var array
    */    
    var $val_cache = array();
    
    /**
    * template::$gzipLevel
    *
    * GZIP-Level
    * @var int
    */    
    var $gzipLevel = 0;
    
    /**
    * template::$activategzip
    *
    * GZIP aktivieren
    * @var boolean
    */    
    var $activategzip = false;
    
    /**
    * template::engineTemplate()
    *
    * Konstruktor
	* @access public
    */
    function engineTemplate($folder='') {
        $this->folder = $folder;
    }    

    /**
    * template::setOwnBorder()
    *
    * Setzt die Pfade für eigene Header und Footer Files
	* @access public
    */    
    function setOwnBorder($footer,$header) {
        if($header && is_file($header)) {
            $this->header_file = $header;
        } else {
            die("Pfad zur angegebenen Header Datei stimmt nicht. Es konnte keine Datei gefunden werden");
        }
        if($footer && is_file($footer)) {
            $this->footer_file = $footer;
        } else {
            die("Pfad zur angegebenen Footer Datei stimmt nicht. Es konnte keine Datei gefunden werden");
        }    
    }

    /**
    * template::load_file()
    *
    * Lädt ein Template File in die Klasse
	* @access public
    */
    function loadFile($file_id, $filename) {
        $loadfile = $this->folder.$filename;
        $this->files[$file_id] = fread($fp = fopen($loadfile, 'r'), filesize($loadfile));
        if(!fclose($fp)) {
            return false;
        }
    }

    /**
    * template::setIdentifiers()
    *
    * Setzt Anfang und Ende Identifiers für
    * die Variablendefinition
	* @access public
    */
    function setIdentifiers($start, $end) {
        $this->start = $start;
        $this->end = $end;
    }

    /**
    * template::traverseArray()
    *
    * Wird verwendet wenn ein Array als Variable
    * Registriert wird.
	* @access private
    */
    function traverseArray($file_id, $array) {
        while(list(,$value) = each($array)){
            if(is_array($value)) {
                $this->traverse_array($file_id, $value);
            } else {
                $this->var_names[$file_id][] = $value;
            }
        }
    }

    /**
    * template::register()
    *
    * Initialisierung eines Tags $var_name und hinterlegen
    * des entsprechenden Wertes $value. Auch übergabe eines
    * Arrays möglich mit entsprechender Paarung
    *
    * @param $var_name Name der Variable
    * @param $value Wert der Variable
    * @access public
    * @return
    */
    function register($var_name, $value="") {
        if (!is_array($var_name)) {
            if (!empty($var_name)) {
                $value = preg_replace(array('/\$([0-9])/', '/\\\\([0-9])/'), array('&#36;\1', '&#92;\1'), $value);
                $this->key_cache[$var_name] = "/".$this->addDelemiter($var_name)."/";
                $this->val_cache[$var_name] = $value;
            }
        } else {
            foreach ($var_name as $key => $val) {
                if (!empty($key)) {
                    $val = preg_replace(array('/\$([0-9])/', '/\\\\([0-9])/'), array('&#36;\1', '&#92;\1'), $val);
                    $this->key_cache[$key] = "/".$this->addDelemiter($key)."/";
                    $this->val_cache[$key] = $val;
                }
            }
        }
        return;
    }

    /**
    * template::add_delemiter()
    * Fügt Variablenlimitierung hinzu
    *
    * @param string $var_name Name der Variable
    * @access private
    * @return
    */
    function addDelemiter($var_name) {
        return preg_quote($this->start.$var_name.$this->end);
    }    
    
    /**
    * template::registerGlobal()
    *
    * Variable(n) registrieren
	* @access public
    */
    function registerGlobal($var_name) {
        $this->var_global[] = $var_name;
    }    
    
    /**
    * template::initGZipLevel()
    * Setzt den Komprimierungslevel wenn GZIP aktiviert ist.
    *
    * @param string $gzipLevel GZip-Level 0-9
    * @access public
    * @return
    */
    function initGZipLevel($gzipLevel = "") {
		global $config;
        $this->gzipLevel = $gzipLevel;
		if($this->gzipLevel >= 1 && $config['activategzip']) {
        	$this->activategzip = true;
		} else {
			$this->activategzip = false;
		}
    }    

    /**
    * template::includeFile()
    *
    * Bindet externe Datei ein, z. B. Header/Footer
 	* @access private
    */
    function includeFile($file_id, $filename) {
        if(file_exists($filename)) {
            $include = fread($fp = fopen($filename, 'r'), filesize($filename));
            fclose($fp);
        } else {
            $include = '[ERROR: "'.$filename.'" does not exist.]';
        }
        $tag = substr($this->files[$file_id], strpos(strtolower($this->files[$file_id]), '<include filename="'.$filename.'">'), strlen('<include filename="'.$filename.'">'));
        $this->files[$file_id] = str_replace($tag, $include, $this->files[$file_id]);
    }

    /**
    * template::parse()
    *
    * Template parsen und evtl. INCLUDE-TAGS durch Dateien
    * ersetzen; Variablen ersetzen
	* @access public
    */
    function parse($file_id) {
        $file_ids = explode(',', $file_id);
        for(reset($file_ids); $file_id = trim(current($file_ids)); next($file_ids)){
            while(is_long($pos = strpos(strtolower($this->files[$file_id]), '<include filename="'))){
            	$pos += 19;
            	$endpos = strpos($this->files[$file_id], '">', $pos);
            	$filename = substr($this->files[$file_id], $pos, $endpos-$pos);
            	$this->includeFile($file_id, $filename);
            }

            $this->parseAllVars($file_id);
            
            if(isset($this->var_global) AND count($this->var_global) > 0){
            	for($i=0; $i<count($this->var_global); $i++){
            		$temp_var = $this->var_global[$i];
            			global $$temp_var;
            			$this->files[$file_id] = str_replace($this->start.$temp_var.$this->end, $$temp_var, $this->files[$file_id]);
            	}
            }            
        }
    }
    
    /**
    * template::parseAllVars()
    *
    * Platzhalter in den Templates ersetzen
	* @access public
    */    
    function parseAllVars($file_id) {
        $this->files[$file_id] = preg_replace($this->key_cache, $this->val_cache, $this->files[$file_id]);
    }

    /**
    * template::parseLoop()
    *
    * Loop auf Basis eines Array's. Array key und
    * Platzhalter müssen übereinstimmen
	* @access public
    */
    function parseLoop($file_id, $array_name) {
        global $$array_name;
        $loop_code = '';

        $start_pos = strpos(strtolower($this->files[$file_id]), '<loop name="'.$array_name.'">') + strlen('<loop name="'.$array_name.'">');
        $end_pos = strpos(strtolower($this->files[$file_id]), '</loop name="'.$array_name.'">');

        $loop_code = substr($this->files[$file_id], $start_pos, $end_pos-$start_pos);

        $start_tag = substr($this->files[$file_id], strpos(strtolower($this->files[$file_id]), '<loop name="'.$array_name.'">'),strlen('<loop name="'.$array_name.'">'));
        $end_tag = substr($this->files[$file_id], strpos(strtolower($this->files[$file_id]), '</loop name="'.$array_name.'">'),strlen('</loop name="'.$array_name.'">'));

        if($loop_code != '') {
            $new_code = '';
            for($i=0; $i<count($$array_name); $i++) {
            	$temp_code = $loop_code;
            	while(list($key,) = each(${$array_name}[$i])) {
            		$temp_code = str_replace($this->start.$key.$this->end,${$array_name}[$i][$key], $temp_code);
            	}
            	$new_code .= $temp_code;
            }
            
            $this->files[$file_id] = str_replace($start_tag.$loop_code.$end_tag, $new_code, $this->files[$file_id]);
        }
        
    }

    /**
    * template::parseIf()
    *
    * Auswertung der IF-Bedingung und entsprechendes
    * parsen der Bedingung
	* @access public
    */
    function parseIf($file_id, $array_name){
        $var_names = explode(',', $array_name);

        for($i=0; $i<count($var_names); $i++) {
            $if_code	= '';
            $start_pos	= strpos(strtolower($this->files[$file_id]), '<if name="'.strtolower($var_names[$i]).'">') + strlen('<if name="'.strtolower($var_names[$i]).'">');
            $end_pos	= strpos(strtolower($this->files[$file_id]), '</if name="'.strtolower($var_names[$i]).'">');

            $if_code	= substr($this->files[$file_id], $start_pos, $end_pos-$start_pos);
            $start_tag	= substr($this->files[$file_id], strpos(strtolower($this->files[$file_id]), '<if name="'.strtolower($var_names[$i]).'">'),strlen('<if name="'.strtolower($var_names[$i]).'">'));
            $end_tag	= substr($this->files[$file_id], strpos(strtolower($this->files[$file_id]), '</if name="'.strtolower($var_names[$i]).'">'),strlen('</if name="'.strtolower($var_names[$i]).'">'));

            $new_code = '';
            if($if_code != '') {
                global ${$var_names[$i]};
                if(@${$var_names[$i]}) $new_code = $if_code;
                $this->files[$file_id] = str_replace($start_tag.$if_code.$end_tag, $new_code, $this->files[$file_id]);
            }
        }
    }
    
    /**
    * template::removeTags()
    * Leere Platzhalter entfernen
    * IF-Anweisungen entfernen
    *
    * @param $template Templatename
    * @access private
    * @return
    */
    function removeTags($file_id) {
        $search = array("/".$this->start."+[^ \t\r\n]+".$this->end."/","/&#36;([0-9])/","/&#92;([0-9])/","`<\!#.*?#\!>`s");
        $replace = array("","$\1","\\\1","");
        $file_id = preg_replace($search, $replace, $file_id);
        return $file_id;
    }    

    /**
    * template::printFile()
    *
    * Gibt geparstes Template im Browser aus
	* @access public
    */
    function printFile($file_id, $ispopup='') {
        if(is_long(strpos($file_id, ',')) == TRUE){
            $file_id = explode(',', $file_id);
            for(reset($file_id); 
			$current = current($file_id); 
			next($file_id)) $template = $this->removeTags($this->files[trim($current)]);
        } else {
            $template = $this->removeTags($this->files[$file_id]);
        }
		
		$this->printGzipFile($template);
    }

    /**
    * template::printGzipFile()
    *
    * Leitet geparstes Template in die GZIP-Ausgabe, sofern aktiviert und verf&uuml;gbar
	* @access private
    */	
	function printGzipFile($template) {
		if(!$template) {
			trigger_error("Nothing to parse in GZip-Class, please go back...",E_USER_WARNING);
		} else {
			$this->text = $template;
		}
		
		if(!defined("DISABLE_GZIP")) {
		    if ($this->activategzip && !headers_sent()) {
	            ob_start();
	            $oldlevel=error_reporting(0);
	            include($this->header_file);
	            error_reporting($oldlevel);
	            $buffer1 = ob_get_contents();
	            ob_end_clean();		
	            
	            ob_start();
	            $oldlevel=error_reporting(0);
	            include($this->footer_file);
	            error_reporting($oldlevel);
	            $buffer2 = ob_get_contents();
	            ob_end_clean();	
		        
		        $output = $buffer1.$this->text.$buffer2; 
		        $output = $this->makeGzipOut($output);        
		        if (!headers_sent()) @header("Content-Length: ".strlen($output));
		    } else {
				$output = $this->text;
			}
		} else {
			$output = $this->text;
		}
		
		if(!defined("IS_POPUP")) define('IS_POPUP',false);
		
	    if ($this->header_file != "" && !IS_POPUP && !$this->activategzip) include($this->header_file);
	    echo $output;
	    if ($this->footer_file != "" && !IS_POPUP && !$this->activategzip) include($this->footer_file);
	    flush();	
	}
	
	/**
	* template::makegzipout()
	*
	* G-ZIP aktivieren wenn Browser akzeptiert
	* Auswahl zwischen x-gzip und gzip
	* @param string $text
	* @access private	
	*/
	function makeGzipOut($text) {
	    global $_SERVER,$config;
	    
		$returntext = $text;
		
		if (function_exists('crc32') AND function_exists('gzcompress')) {
			if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
				$encoding = 'x-gzip';
			}
			if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
				$encoding = 'gzip';
			}
	
			if ($encoding) {
				//@header("Content-Encoding: $encoding");
	
				if (function_exists('gzencode') AND getPHPVersion() > '420') {
					$text = $text."\n<!-- GZip-Info: PHP > 4.2.0 ".$encoding."; GZip-Level: ".$this->gzipLevel."; used natural PHP gzencode function -->\n";
					$returntext = gzencode($text, $this->gzipLevel);
				} else {
					$text = $text."\n<!-- GZip-Info: PHP < 4.2.0 ".$encoding."; GZip-Level: ".$this->gzipLevel."; used self compress function -->\n";
					$size = strlen($text);
					$crc = crc32($text);
	
					$returntext = "\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\xff";
					$returntext .= substr(gzcompress($text, $this->gzipLevel), 2, -4);
					$returntext .= pack('V', $crc);
					$returntext .= pack('V', $size);
				}
				$returntext = $text;
			}
		}
		return $returntext;		
	}				

    /**
    * template::returnFile()
    *
    * Gib geparstes Template zurück ohne dieses an den
    * Browser zu senden.
	* @access public
    */
    function returnFile($file_id){
        $ret = '';
        if(is_long(strpos($file_id, ',')) == TRUE) {
            $file_id = explode(',', $file_id);
            for(reset($file_id); $current = current($file_id); next($file_id)) $ret .= $this->files[trim($current)];
        } else {
            $ret .= $this->files[$file_id];
        }
        return $this->removeTags($ret);
    }

    /**
    * template::pprint()
    *
    * Registriert Variable und parst das angegebene
    * Template; Template wird anschl. im Browser
    * ausgegeben und kann weiterverarbeitet werden
	* @access public
    * @param string $replacements
    */
    function pprint($file_id, $replacements = ''){
        $this->register($file_id, $replacements);
        $this->parse($file_id);
        $this->printFile($file_id);
    }

    /**
    * template::pget()
    *
    * Registriert Variable und parst das angegebene
    * Template; Template wird anschl. nicht im Browser
    * ausgegeben und kann weiterverarbeitet werden
	* @access public
    * @param string $replacements
    */
    function pget($file_id, $replacements = ''){
        $this->register($file_id, $replacements);
        $this->parse($file_id);
        return $this->returnFile($file_id);
    }

    /**
    * template::pprintFile()
    *
    * Lädt Template, Parst Variablen im Template und gibt
    * das geparste Template zurück (wird im Browser ausgegeben)
	* @access public
    * @param string $replacements
    */
    function pprintFile($filename, $replacements = ''){
        for($file_id=1; isset($this->files[$file_id]); $file_id++);
        $this->loadFile($file_id, $filename);
        $this->pprint($file_id, $replacements);
        unset($this->files[$file_id]);
    }

    /**
    * template::pgetFile()
    *
    * Lädt Template, Parst Variablen im Template und gibt
    * das geparste Template zurück (wird nicht im Browser
    * ausgegeben)
	* @access public
    * @param string $replacements
    */
    function pgetFile($filename, $replacements = ''){
        for($file_id=1; isset($this->files[$file_id]); $file_id++);
        $this->loadFile($file_id, $filename);
        return $this->pget($file_id, $replacements);
    }
}
?>
