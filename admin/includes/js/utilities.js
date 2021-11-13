/*
+--------------------------------------------------------------------------
|   Alex JS Engine
|   ========================================
|   by Alex Höntschel
|   (c) 2002 AlexScriptEngine
|   http://www.alexscriptengine.de
|   ========================================
|   Web: http://www.alexscriptengine.de
|   Email: info@alexscriptengine.de
+---------------------------------------------------------------------------
|
|   > Beschreibung
|   > Javascript Hilffunktionen, Urls, etc.
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: utilities.js 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

var neu = null;
function displayHTML() {
    var inf = document.tpl.temp_content.value;
    neu = window.open('', 'POPup', 'toolbar = no, status = no, scrollbars=yes');
    if (neu != null) {
    if (neu.opener == null) {
        neu.opener = self;
        }
    neu.document.write("" + inf + "");
    }
}

function changecolor(theelement,color) {
	theelement.style.backgroundColor = color;
}