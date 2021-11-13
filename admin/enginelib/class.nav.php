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
// $Id: class.nav.php 6 2005-10-08 10:12:03Z alex $

/**
* class Nav_Link
* 
* Basisklasse der Engines um Links zu erstellen
* 
* @access public
* @author Alex Höntschel <info@alexscriptengine.de>
* @version $Id: class.nav.php 6 2005-10-08 10:12:03Z alex $
* @copyright Alexscriptengine 2002,2003
* @link http://www.alexscriptengine.de
*/

class Nav_Link {

	var $perPage = "";  // Anzeige pro Seite
	var $overAll = ""; // Anzahl Datensätze insgesamt
	var $DisplayNext = 1; // Soll der Link 'nächste Seite' angezeigt werden
	var $DisplayLast = 0; // Soll der Link 'letzte Seite' angezeigt werden
	var $DisplayFirst = 0; // Soll der Link 'erste Seite' angezeigt werden
	var $DisplayPrev = 1; // Soll der Link 'vorhergehende Seite' angezeigt werden
	var $start = ""; // Startseite, hier immer leer, muß auf Seite gesetzt werden!
	var $MyLink = ""; // der Link Name auf die Seite auf die bei Klick referenziert wird wenn ohne weitere Variablen, muß mit ? enden, sonst mit &
	var $LinkClass = ""; // Name einer CSS Klasse, die beim Link verwendet wird

	/* Einzelne Links bauen */
	/* externe Funktion     */
	function BuildLinks() {
			/* erste Seite und eine Seite zurück */
			if($this->start > 0) $link .= $this->FirstLink($this->start);
			
			/* einzelne Seitenlinks */
			if($this->overAll > $this->perPage) {
				$pages = intval($this->overAll / $this->perPage);
				if($this->overAll % $this->perPage) $pages++;
			}
            
            $current_page = ($this->start + $this->perPage) / $this->perPage;
            
			for ($i=1; $i<=$pages;$i++) {
				$fwd = ($i-1) * $this->perPage;
				if ($fwd == $this->start) { 
                    $link .= "<b>".$i."</b>&nbsp;"; 
                } else {
                    if($i >= ($current_page-2) && $i < $current_page) {
                        $link .= "<a class=\"".$this->LinkClass."\" href=\"".$this->MyLink."start=".$fwd."\">$i</a>&nbsp;";
                    } elseif($i >= ($current_page +1) && $i < ($current_page+3)) {
                        $link .= "<a class=\"".$this->LinkClass."\" href=\"".$this->MyLink."start=".$fwd."\">$i</a>&nbsp;";
                    }                    
                }                    
			}
			
			/* letzte Seite und eine Seite vor */
			if($this->start < $this->overAll-$this->perPage) $link .= $this->LastLink($this->start,$fwd);		

	return $link;
	}
	
	
	
	/* Link für die Anzeige erste Seite */
	/* interne Funktion                 */	
	function FirstLink($start) {
	
		if($this->DisplayFirst) {
			$link .= "<a class=\"".$this->LinkClass."\" href=\"".$this->MyLink."start=0\">&laquo;</a>&nbsp;";
			}
			
			$one_back = $start - $this->perPage;
			if($one_back < 0) $one_back = 0;
			
		if ($this->DisplayPrev) {
			$link .= "<a class=\"".$this->LinkClass."\" href=\"".$this->MyLink."start=$one_back\">&lt;</a>&nbsp;";
			}
			
			return $link;	
	}
	
	/* Link für die Anzeige letzte Seite */
	/* interne Funktion                  */
	function LastLink($start,$last) {
	
		if($this->DisplayNext) {
			$fwd = $start + $this->perPage;
			$link .= "<a class=\"".$this->LinkClass."\" href=\"".$this->MyLink."start=".$fwd."\">&gt;</a>&nbsp;";
			}
			
		if ($this->DisplayLast) {
			$link .= "<a class=\"".$this->LinkClass."\" href=\"".$this->MyLink."start=".$last."\">&raquo;</a>&nbsp;";
			}
			
			return $link;	
	}
	
	
} // Klasse Ende

/* Usage:
erst Abfrage aus Datenbank, Start bei $start = 0 was als Limiter für die SQL-Abfrage eingesetzt werden soll
Aufruf: 

if(!isset($start)) $start = 0;

$nav = new Nav_Link();
$nav->overAll = ""; hier die Anzahl aller Datensätze
$nav->perPage = ""; Anzahl der Datensätze, die angezeigt werden sollen
$nav->MyLink = ""; hier die Url auf die referenziert werden soll, muß mit ? oder & enden 
$nav->start = $start;
*/

?>
