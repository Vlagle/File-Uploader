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
|   > Javascript Progress-Balken
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: progress.js 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

var statusWin, toppos, leftpos;

toppos = (screen.height - 401)/2;

leftpos = (screen.width - 401)/2;

function showProgress() {
    statusWin = window.open('progress.php','Status','height=100,width=350,top='+toppos+',left='+leftpos+',location=no,scrollbars=no,menubars=no,toolbars=no,resizable=yes');
    statusWin.focus();
}

function hideProgress() {
    if (statusWin != null) {
        if (!statusWin.closed) {
            statusWin.close();
        }
    }
}