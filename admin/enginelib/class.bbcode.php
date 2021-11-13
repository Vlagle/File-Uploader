<?php
// +----------------------------------------------------------------------+
// | EngineLib - BBCode Class                                             |
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
// $Id: class.bbcode.php 6 2005-10-08 10:12:03Z alex $

/**
* class engineBBCode
*
* Basisklasse der Engines um innerhalb der Engines BBCode-Tags
* zu formatieren bzw. formatiert auszugeben
*
* @access public
* @author Alex Höntschel <info@alexscriptengine.de>
* @version $Id: class.bbcode.php 6 2005-10-08 10:12:03Z alex $
* @copyright Alexscriptengine 2004,2005
* @link http://www.alexscriptengine.de
*/
class engineBBCode {

    /**
    * engineBBCode::$direct_url
    *
    * Urls und Emails automatisch ersetzen
    */
    var $direct_url;

    /**
    * engineBBCode::$disable_images
    *
    * Ersetzt Bilder automatisch
    * bzw. schaltet dies ab
    */
    var $disable_images;

    /**
    * engineBBCode::$disable_html
    *
    * HTML-Code deaktiviert
    */
    var $disable_html;

    /**
    * engineBBCode::$diable_smilies
    *
    * Smilies deaktiviert
    */
    var $diable_smilies;

    /**
    * engineBBCode::$smilie_url
    *
    * Url zu den Smilies
    */
    var $smilie_url;

    /**
    * engineBBCode::$replace
    *
    * Hält Array mit BBCodes nach denen gesucht
    * werden soll
    */
    var $replace = array();

    /**
    * engineBBCode::$search
    *
    * Hält Array mit BBCode Ersetzungen
    */
    var $search = array();

    /**
    * engineBBCode::engineBBCode()
    *
    * Konstruktor der Klasse
    *
    * @param integer $direct_url
    * @param integer $disable_images
    * @param integer $disable_html
    * @param integer $disable_smilies
    * @param string $smilie_url
    * @access public
    */
    function engineBBCode($direct_url=1,$disable_images=0,$disable_html=1,$disable_smilies=0,$smilie_url='') {
        $this->direct_url = $direct_url;
        $this->disable_images = $disable_images;
        $this->disable_html = $disable_html;
        $this->diable_smilies = $disable_smilies;
        $this->smilie_url = $smilie_url;
        $this->loadBBCodes();
    }

    /**
    * engineBBCode::loadBBCodes()
    *
    * Es werden alle BBCodes und die entsprechenden
    * Ersetzungen geladen
    * @access private
    */
    /**
    * engineBBCode::loadBBCodes()
    *
    * Es werden alle BBCodes und die entsprechenden
    * Ersetzungen geladen
    * @access private
    */
    function loadBBCodes() {
        global $lang;
        $this->search[]='/\[list=([\'\"]?)([^\"\']*)\\1](.*)\[\/list((=\\1[^\"\']*\\1])|(\]))/esiU';
        $this->replace[]="\$this->formatList('\\3', '\\2')";

        $this->search[]='/\[list](.*)\[\/list\]/esiU';	
        $this->replace[]="\$this->formatList('\\1')";

        $this->search[]='/\[url=([\'\"]?)([^\"\']*)\\1](.*)\[\/url\]/esiU';
        $this->replace[]="\$this->formatUrl('\\2','\\3')";

        $this->search[]='/\[url]([^\"]*)\[\/url\]/esiU';	
        $this->replace[]="\$this->formatUrl('\\1')";

        $this->search[]='#\[code\](.*)\[/code\]#esiU';	
        $this->replace[]="\$this->formatCodeTag('\\1')";

        $this->search[]='#\[php\](.*?)\[\/php\]#se';	
        $this->replace[]="\$this->phpHighlite('\\1')";        

        $this->search[]='/\[img]([^\"]*)\[\/img\]/siU';	
        $this->replace[]="<img src='\\1' border=0>";

        $this->search[]='/\[b](.*)\[\/b\]/siU';	
        $this->replace[]='<b>\1</b>';

        $this->search[]='/\[i](.*)\[\/i\]/siU';	
        $this->replace[]='<i>\1</i>';

        $this->search[] = '/\[hr]/miUs';
        $this->replace[] = '<hr>';

        $this->search[]='/\[email](.*)\[\/email\]/siU';	
        $this->replace[]='<a href="mailto:\1">\1</a>';

        $this->search[]='/\[email=([\'\"]?)([^\"\']*)\\1](.*)\[\/email\]/siU';	
        $this->replace[]='<a href="mailto:\2">\3</a>';

        $this->search[]='/\[size=([\'\"]?)([^\"\']*)\\1](.*)\[\/size\]/siU';	
        $this->replace[]='<font size="\2">\3</font>';

        $this->search[]='/\[quote](.*)\[\/quote\]/esiU';
        $this->replace[]="\$this->formatQuoteTag('\\1')";


        $this->search[]='/\[u](.*)\[\/u\]/siU';	
        $this->replace[]='<u>\1</u>';

        $this->search[]='/\[color=([\'\"]?)([^\"\']*)\\1](.*)\[\/color\]/siU';	
        $this->replace[]='<font color="\2">\3</font>';

        $this->search[]='/\[font=([\'\"]?)([^\"\']*)\\1](.*)\[\/font\]/siU';	
        $this->replace[]='<font face="\2">\3</font>';

        $this->search[]='/\[align=([\'\"]?)([^\"\']*)\\1](.*)\[\/align\]/siU';	
        $this->replace[]='<div align="\2">\3</div>';

        $this->search[]='/\[mark=([\'\"]?)([^\"\']*)\\1](.*)\[\/mark\]/siU';	
        $this->replace[]='<span style="background-color: \2">\3</span>';

        $this->search[]='/\[edit=([\'\"]?)([^\"\']*)\\1](.*)\[\/edit\]/siU';	
        $this->replace[]='<p><span style="font-size: 10px; font-style : italic;">'.sprintf($lang['comment_edited_from_at'],'\3','\2').'</font></p>';
    }

    /**
    * engineBBCode::rebuildText()
    *
    * Parst den eigentlichen Text und entfernt kritische Script-Tags aus dem Code
    * Zensur-Funktion wird noch nicht benutzt
    *
    * @return string
    * @access public
    */
    function rebuildText($text) {
        if($this->disable_html) {
            $text = htmlspecialchars($text);
        } else {
            $text = preg_replace("/<script[^>]*>/i","&lt;script\\1&gt;",$text);
        }

    	$script_find = array('/javascript:/si', '/about:/si', '/vbscript:/si', '/&(?![a-z0-9#]+;)/si');
    	$script_replace = array('java_script_:', 'about_:', 'vb_script_:', '&amp;');
    	$text = preg_replace($script_find, $script_replace, $text);

        $text = nl2br($text);
        if(!$this->disable_smilies) $text = $this->parseSmilies($text);
        $text = $this->parseBBCode($text);

        // Hier Funktion f&uuml;r Textzensur ansprechen
        // noch nicht verwendet
        //$text = parseCensor($text);
        $text = $this->ntWordwrap($text);
        return $text;
    }

    /**
    * engineBBCode::parseBBCode()
    *
    * Ersetzt die einzelnen BBCodes
    * und evtl. vorhandene Urls
    * @return string
    * @access private
    */
    function parseBBCode($text) {
        if($this->direct_url == 1) {
            $text = $this->parseURL($text);
        }
        $text = preg_replace($this->search, $this->replace, $text);
        $text = str_replace("\\'", "'", $text);
        return $text;
    }

    /**
    * engineBBCode::parseSmilies()
    *
    * Parst alle Smilies (kann evtl. erweitert werden)
    * @return string
    * @access private
    */
    function parseSmilies($text) {
        $text = str_replace(":-)","<image src=\"".$this->smilie_url."/smile.gif\" alt=\"\" />",$text);
        $text = str_replace(";-)","<image src=\"".$this->smilie_url."/wink.gif\" alt=\"\" />",$text);
        $text = str_replace(":O","<image src=\"".$this->smilie_url."/wow.gif\" alt=\"\" />",$text);
        $text = str_replace(";-(","<image src=\"".$this->smilie_url."/sly.gif\" alt=\"\" />",$text);
        $text = str_replace(":D","<image src=\"".$this->smilie_url."/biggrin.gif\" alt=\"\" />",$text);
        $text = str_replace("8-)","<image src=\"".$this->smilie_url."/music.gif\" alt=\"\" />",$text);
        $text = str_replace(":-O","<image src=\"".$this->smilie_url."/cry.gif\" alt=\"\" />",$text);
        $text = str_replace(":-(","<image src=\"".$this->smilie_url."/confused.gif\" alt=\"\" />",$text);
        $text = str_replace("(?)","<image src=\"".$this->smilie_url."/sneaky2.gif\" alt=\"\" />",$text);
        $text = str_replace("(!)","<image src=\"".$this->smilie_url."/notify.gif\" alt=\"\" />",$text);
        $text = str_replace(":!","<image src=\"".$this->smilie_url."/thumbs-up.gif\" alt=\"\" />",$text);
        $text = str_replace(":zzz:","<image src=\"".$this->smilie_url."/sleepy.gif\" alt=\"\" />",$text);
        $text = str_replace(":baaa:","<image src=\"".$this->smilie_url."/baaa.gif\" alt=\"\" />",$text);
        $text = str_replace(":blush:","<image src=\"".$this->smilie_url."/blush.gif\" alt=\"\" />",$text);
        $text = str_replace(":inlove:","<image src=\"".$this->smilie_url."/inlove.gif\" alt=\"\" />",$text);
        $text = str_replace(":stupid:","<image src=\"".$this->smilie_url."/withstupid.gif\" alt=\"\" />",$text);
        $text = str_replace(":xmas:","<image src=\"".$this->smilie_url."/xmas.gif\" alt=\"\" />",$text);
        return $text;
    }

    /**
    * engineBBCode::parseURL()
    *
    * Diese Funktion ersetzt Urls und email Adressen ohne BBCode durch die Adresse mit bbcode.
    *
    * @return string
    * @param text string
    * @access private
    */
    function parseURL($text) {
        $urlsearch[]="/([^]_a-z0-9-=\"'\/])((https?|ftp):\/\/|www\.)([^ \r\n\(\)\*\^\$!`\"'\|\[\]\{\};<>]*)/si";
        $urlsearch[]="/^((https?|ftp):\/\/|www\.)([^ \r\n\(\)\*\^\$!`\"'\|\[\]\{\};<>]*)/si";
        $urlreplace[]="\\1[url]\\2\\4[/url]";
        $urlreplace[]="[url]\\1\\3[/url]";
        $emailsearch[]="/([\s])([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,}))/si";
        $emailsearch[]="/^([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,}))/si";
        $emailreplace[]="\\1[email]\\2[/email]";
        $emailreplace[]="[email]\\0[/email]";
        $text = preg_replace($urlsearch, $urlreplace, $text);
        if (strpos($text, "@")) $text = preg_replace($emailsearch, $emailreplace, $text);
        return $text;
    }

    /**
    * engineBBCode::formatCodeTag()
    *
    * Formatiert [code] mit dem bbTemplate
    * @return string
    * @access private
    */
    function formatCodeTag($code) {
        global $lang;
        $code = str_replace("\\\"","\"",$code);
        return $this->bbTemplate('<pre>'.$code.'</pre>',$lang['comment_code']);
    }

    /**
    * engineBBCode::formatQuoteTag()
    *
    * Formatiert [quote] mit dem bbTemplate
    * @return string
    * @access private
    */
    function formatQuoteTag($quote) {
        global $lang;
        $quote = str_replace("\\\"","\"",$quote);
        return $this->bbTemplate('<blockquote>'.$quote.'</blockquote>',$lang['comment_quote']);
    }

    /**
    * engineBBCode::formatUrl()
    *
    * Ist der Titel länger als maxwidth und kommt kein IMG Tag darin vor, wird er "abgeschnitten".
    * @return string
    * @access private
    */
    function formatUrl($url, $title="", $maxwidth=60, $width1=40, $width2=-15) {
        if(!trim($title)) $title=$url;
        if(!preg_match("/[a-z]:\/\//si", $url)) $url = "http://$url";
        if(strlen($title) > $maxwidth && !stristr($title,"[img]")) {
            $title = substr($title,0,$width1)."...".substr($title,$width2);
        }
        return "<a href=\"$url\" target=\"_blank\">".str_replace("\\\"", "\"", $title)."</a>";
    }

    /**
    * engineBBCode::formatList()
    *
    * Formatierung der unterschiedlichen [list]-Tags
    * @return string
    * @access private
    */
    function formatList($list, $listtype="") {
        if(!trim($listtype)) {
            $listtype = "";
        } else {
            $listtype = " type=\"$listtype\"";
        }
        $list = str_replace("\\\"","\"",$list);
        if ($listtype) {
            return "<ol$listtype>".str_replace("[*]","<li>", $list)."</ol>";
        } else {
            return "<ul>".str_replace("[*]","<li>", $list)."</ul>";
        }
    }

    /**
    * engineBBCode::phpHighlite()
    *
    * Formatiert PHP-Code und parst das bbTemplate
    * @return string
    * @access private
    */
    function phpHighlite($code) {

        $regexfind = array(
            '#<br( /)?>#siU',
            '#(&amp;\w+;)#siU',
            '#&lt;!--(.*)--&gt;#siU'
        );
        $regexreplace = array(
            '',
            '<b><i>\1</i></b>',
            '<i>&lt;!--\1--&gt;</i>'
        );

        $code = preg_replace($regexfind, $regexreplace, $code);

        $code = str_replace("&lt;","<",$code);
        $code = str_replace("&gt;",">",$code);	
        $code = str_replace("&quot;","\"",$code);
        $code = str_replace("&amp;","&",$code);

        $code = stripslashes($code);
        if(!strpos($code,"<?") && substr($code,0,2)!="<?") $code="<?php\n".trim($code)."\n?>";
        $code = trim($code);
        ob_start();
            $oldlevel=error_reporting(0);
            highlight_string($code);
            error_reporting($oldlevel);
            $buffer = ob_get_contents();
        ob_end_clean();

        return $this->bbTemplate($buffer,'PHP');
    }

    /**
    * engineBBCode::ntWordwrap()
    *
    * Fügt nach width Zeichen einen Zeilenumbruch in text ein.
    * @return string
    * @access private
    */
    function ntWordwrap($text, $width=75) {
        if($text) return preg_replace("/([^\n\r ?&\.\/<>\"\\-]{".$width."})/i"," \\1\n",$text);
    }

    /**
    * engineBBCode::bbTemplate()
    *
    * Template für Code, Zitat und PHP-Tags
    * @return string
    * @access private
    */
    function bbTemplate($code,$parsetype) {
        $template = '<div style="margin:20px; margin-top:5px">
        	<div class="smallfont" style="margin-bottom:2px">'.$parsetype.':</div>
        	<table cellpadding="$stylevar[cellpadding]" cellspacing="0" border="0" width="100%">
        	<tr>
        		<td style="background-color : #FFFFFF; color : #000000; border:1px inset">
        			<code style="white-space:nowrap">
        				'.$code.'
        			</code>
        		</td>
        	</tr>
        	</table>
        </div>';
        return $template;
    }
}
?>

