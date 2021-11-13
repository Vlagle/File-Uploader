<?php 
/*
+--------------------------------------------------------------------------
|   Alex Install Engine
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
|   > Installationsdatei
|
|   > Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|   > bedingungen (read-me.html) für weitere Informationen.
|   >-----------------------------------------------------------------
|   > This script is NOT freeware! Please read the Copyright Notice
|   > (read-me_eng.html) for further information. 
|
|   > $Id: installer.php 16 2005-10-08 10:52:59Z alex $
|
+--------------------------------------------------------------------------
*/

@error_reporting  (7);
@set_magic_quotes_runtime(0);

if (@get_magic_quotes_gpc() == 0) {
  $HTTP_GET_VARS = addslashes_array($HTTP_GET_VARS);
  $HTTP_POST_VARS = addslashes_array($HTTP_POST_VARS);
}

$old_version = "1.3.5";
$new_version = "1.4.2";
$app_name = "Download Engine";
$eng_type = "dl";

$filename = "installer.php";

if (@file_exists("include/config.inc.php")) {
  @include("include/config.inc.php");
}

include_once("admin/enginelib/class.db.php");

if (!empty($_GET))                   { extract($_GET); }
else if (!empty($HTTP_GET_VARS))     { extract($HTTP_GET_VARS); }

if (!empty($_POST))                  { extract($_POST); }
else if (!empty($HTTP_POST_VARS))    { extract($HTTP_POST_VARS); }

if (!empty($_COOKIE))                { extract($_COOKIE); }
else if (!empty($HTTP_COOKIE_VARS))  { extract($HTTP_COOKIE_VARS); }

if (!empty($_ENV))                   { extract($_ENV); }
else if (!empty($HTTP_ENV_VARS))     { extract($HTTP_ENV_VARS); }

if (!empty($_SERVER))                { extract($_SERVER); }
else if (!empty($HTTP_SERVER_VARS))  { extract($HTTP_SERVER_VARS); }

if (!empty($_SESSION))               { extract($_SESSION); }
else if (!empty($HTTP_SESSION_VARS)) { extract($HTTP_SESSION_VARS); }

if(!$_POST['language']) $_POST['language'] = 1;

if($_POST['language']==1) {
    $inst_lang[1] = "Prüfung der PHP-Installation";
    $inst_lang[2] = "Die aufgelistete Mindestandforderung muß jeweils erfüllt werden, andernfalls ist der einwandfreie Betrieb der Engine nicht möglich:<br>";
    $inst_lang[3] = "Systemkomponente";
    $inst_lang[4] = "erfüllt ja/nein";
    $inst_lang[5] = "Schreibrechte avatar Ordner";
    $inst_lang[6] = "Schreibrechte templates/default Ordner";
    $inst_lang[7] = "Schreibrechte files Ordner";
    $inst_lang[8] = "Schreibrechte include/config.inc.php Datei";
    $inst_lang[9] = "Auswahl der Installationsart";
    $inst_lang[10] = "Nachfolgend muß die Art der Installation gewählt werden. Bitte darauf achten, dass alle o. g. Anforderungen erüllt sind.<br>Nachdem auf Start Installation geklickt wurde erscheint ein Formular, in dem die entsprechenden Daten für MySQL und zur Installation anzugeben sind.<br><br>Die Unterschiede der jeweiligen Installation sind in der Read-me Datei genau beschrieben. Bitte diese beachten, da bei bestimmten Routinen Tabellen überschrieben werden!";
    $inst_lang[11] = "Vollinstallation, es werden alle Tabellen neu angelegt";
    $inst_lang[12] = "Teilinstalltion, es ist bereits eine Engine vorhanden";
    $inst_lang[13] = "Engine mit Forensoftware kombinieren (Beta!)";
    $inst_lang[14] = "Update der $app_name von V$old_version auf V$new_version";
    $inst_lang[15] = "Bitte wählen";
    $inst_lang[16] = "Vollinstallation";
    $inst_lang[17] = "Angaben zur Serverumgebung";
    $inst_lang[18] = "Hier muß die Url zur Engine angegeben werden. Sollten Zweifel bestehen, bitte unbedingt vorher mit dem Hoster in Verbindung setzen.<br><br>";
    $inst_lang[19] = "<b>Url zum Script</b><br><span class=\"smalltext\">ohne abschliessenden /, muß mit http:// beginnen</span>";
    $inst_lang[20] = "Angaben zur Engine";
    $inst_lang[21] = "Nachfolgend muß die Sprache für das Admin-Center gewählt werden und falls gewünscht der absolute Pfad zur Header bzw. Footer Datei. Dieser kann auch nachträglich in der Datei config.inc.php eingegeben werden.<br><br>";
    $inst_lang[22] = "<b>Sprache des AdminCenters</b>";
    $inst_lang[23] = "<b>Pfad zur Header-Datei</b><br><span class=\"smalltext\">Achtung absoluter Pfad, nicht Url! - optional - Die Header Datei wird vor dem Script ausgegeben</span>";
    $inst_lang[24] = "<b>Pfad zur Footer-Datei</b><br><span class=\"smalltext\">Achtung absoluter Pfad, nicht Url! - optional - Die Footer Datei wird nach dem Script ausgegeben</span>";
    $inst_lang[25] = "Angaben zu MySQL";
    $inst_lang[26] = "In die folgenden Felder müssen die Daten der MySQL-Datenbank eingetragen werden. Wenn diese nicht bekannt sind, kann Ihr Webhoster weiterhelfen. Es muß eine existierende Datenbank angegeben werden, sollte noch keine entsprechende MySQL-Datenbank vorhanden sein, müssen Sie diese zuerst anlegen.";
    $inst_lang[27] = "<b>MySQL-Host</b>";
    $inst_lang[28] = "<b>MySQL-Datenbank Name</b>";
    $inst_lang[29] = "<b>MySQL-Benutzername</b>";
    $inst_lang[30] = "<b>MySQL-Passwort</b>";
    $inst_lang[31] = "Bitte vergewissern Sie sich zudem, daß falls gewünscht die Tabellen richtig benannt sind. Sie finden diese in der Datei config.inc.php.";
    $inst_lang[32] = "Administrator Account";
    $inst_lang[33] = "Hier bitte die Daten für den Administrator-Account eingeben. Bitte diese vor dem Absenden nochmals prüfen, da sonst kein Zugang zum Admin-Center gewährt wird.";
    $inst_lang[34] = "<b>Username</b>";
    $inst_lang[35] = "<b>Passwort</b>";
    $inst_lang[36] = "<b>Passwort wiederholen</b>";
    $inst_lang[37] = "<b>Email-Adresse</b>";
    $inst_lang[38] = "Daten installieren";
    $inst_lang[39] = "Mit den angegebenen Daten konnte keine Verbindung zur MySQL-Datenbank hergestellt werden.<br>";
    $inst_lang[40] = "Die Konfigurationsdatei config.inc.php im Ordner include besitzt die erforderlichen Schreibrechte.<br>";
    $inst_lang[41] = "Es wurden nicht alle Daten f&uuml;r den Administrator-Account ausgef&uuml;llt.<br>";
    $inst_lang[42] = "Die angegebenen Passw&ouml;rter f&uuml;r den Administrator-Account stimmen nicht &uuml;berein.<br>";
    $inst_lang[43] = "Es wurde keine Url zum Script angegeben.<br>";
    $inst_lang[44] = "Fehler aufgetreten";
    $inst_lang[45] = "Folgender Fehler wurde während des Installationsprozesses entdeckt:<br><br>";
    $inst_lang[46] = "Bitte klicke <a href='javascript:history.back()'>hier</a> um die angegebenen Daten zu korrigieren";
    $inst_lang[47] = "Tabellen werden erstellt";
    $inst_lang[48] = "- Tabelle wurde erfolgreich erzeugt";
    $inst_lang[49] = "Fortfahren";
    $inst_lang[50] = "Engine mit Forensystem kombinieren - es ist momentan nur das Forum installiert, keine Engine";
    $inst_lang[51] = "Angaben zum Forum";
    $inst_lang[52] = "Es ist wichtig, dass bereits das WBB 1.X installiert ist und dies die erste Engine ist, die zum WBB installiert wird. Sollte bereits eine Engine installiert sein, bitte zurück gehen und nur den Punkt Teilinstallation wählen<br><br>";
    $inst_lang[53] = "<b>Board-Nummer</b><br><span class=\"smalltext\">Die Prefix-Nummer, die während der WBB-Installation angegeben wurde. Standardmäßig 1</span>";
    $inst_lang[54] = "Es wurde als Installationsart WBB-Erweiterung gew&auml;hlt. Um diese Installation erfolgreich abzuschliessen, muss die Usertabelle in der Datei config.inc.php der Usertabelle des WBB entsprechen (z. B. bb1_user_table)<br>";
    $inst_lang[55] = "Update $app_name - von Version $old_version auf die Version $new_version";
    $inst_lang[56] = "Unbedingt Beachten !";
    $inst_lang[57] = "Bitte bei diesem Installationsschritt unbedingt vorher die Tabellen in der Datei <b>config.inc.php</b> prüfen, da hier die Standardeinstellung vorhanden ist. Dies ist besonders wichtig, wenn bereits mehrere Engine miteinander kombiniert bzw. mit einem Woltlab Burning Board kombiniert sind.";
    $inst_lang[58] = "wurde erfolgreich erweitert";
    $inst_lang[59] = "Installation der $app_name";
    $inst_lang[60] = "Herzlichen Gl&uuml;ckwunsch, die Installation ist abgeschlossen. Bitte l&ouml;schen Sie unbedingt die Datei installer.php.<br><br>Klicken Sie <a href=\"%s/admin/index.php\">hier</a> um zum AdminCenter zu gelangen";
    $inst_lang[61] = "Die $app_name wurde erfolgreich installiert. Bitte klicke";
    $inst_lang[62] = "hier</a>, um zum AdminCenter zu gelangen<br><br><br>";
    $inst_lang[63] = "Vielen Dank und viel Spass";
    $inst_lang[64] = "Teilinstallation - es ist bereits eine oder mehrere Engine(s) installiert";
    $inst_lang[65] = "Es wurde als Installationsart Teilinstallation gew&auml;hlt. Um diese Installation erfolgreich abzuschliessen, m&uuml;ssen vorher in der Datei config.inc.php die Tabelle f&uuml;r User, Avatare und Gruppen an die bereits installierte Engine angepasst werden.<br>";
    $inst_lang[66] = "Besucher";
    $inst_lang[67] = "Schreibrechte admin/css_style.txt Datei";
    $inst_lang[68] = "Schreibrechte thumbnail Ordner";
    $inst_lang[lang] = "german";
    $inst_lang[100] = "Schreibrechte Site_Images-Ordner";
    $inst_lang[101] = "Schritt %s von %s";
    $inst_lang[102] = "Bitte ausw&auml;hlen";
    $inst_lang[103] = "<b>Installationsart</b>";
    $inst_lang[104] = "Weiter zu Schritt %s";
    $inst_lang[105] = "Zur&uuml;cksetzen";
    $inst_lang[106] = "Keine Installationsart gew&auml;hlt";
    $inst_lang[107] = "Es wurden nicht alle Felder der MySQL Zugangsdaten gef&uuml;llt<br>";
    $inst_lang[108] = "Um das Script als Erweiterung f&uuml;r das WBB zu installieren, mu&szlig; unbedingt die Board-Nummer angegeben werden.<br>";         
    $inst_lang[109] = "Hinweise der Installation";      
    $inst_lang[110] = "Abschliessen der Installation";      
    $inst_lang[111] = "Wichtig um mit der Installation fortzufahren!";
    $inst_lang[112] = "WICHTIG !!!";
    $inst_lang[113] = "Das Installationsscript konnte die Konfigurationsdatei nicht ordnungsgem&auml;&szlig; schreiben. Um mit der Installation fortzufahren, laden Sie sich bitte durch klick auf den nachfolgenden Button die Datei config.inc.php manuell herunter und speichern diese via FTP im Verzeichnis 'include'.";
    $inst_lang[114] = "Konfigurationsdatei herunterladen";
    $inst_lang[115] = "SafeMode deaktiviert";
    $inst_lang[116] = "Konfigurationsdatei herunterladen";
    $inst_lang[117] = "Installation abschliessen";
    $inst_lang[118] = "<b>Engine-Usergruppe:</b>";
    $inst_lang[119] = "<b>Board-Usergruppe:</b>";
    $inst_lang[120] = "Nachfolgend finden Sie alle verf&uuml;gbaren Gruppen der Engine sowie des gew&uuml;nschten Forums. Bitte sortieren Sie die gew&uuml;nschten Gruppen zueinander (z. B. Admin zu Admin). Sollten Sie weitere Gruppen im Forum ben&ouml;tigen, legen Sie diese bitte JETZT im Forum fest und laden Sie diese Seite neu, damit die Gruppen richtig zugeordnet werden.";
    $inst_lang[121] = "Mitglied / Moderator";
    $inst_lang[122] = "Mitglied";
    $inst_lang[123] = "Installation mit einem Forum";
    $inst_lang[124] = "Bitte beachten Sie, dass das gew&auml;hlte Forensystem bereits komplett installiert und eingerichtet sein muss. Sollten Sie bereits eine Engine installiert haben, gehen Sie bitte zur&uuml;ck und w&auml;hlen Sie den Punkt Teilinstallation aus";
    $inst_lang[125] = "<b>Board-Treiber:</b><br><span class=\"smalltext\">Bitte w&auml;hlen Sie hier den gew&uuml;nschten Treiber f&uuml;r Ihr Forum aus.</span>";
    $inst_lang[126] = "<b>Board-Nummer</b><br><span class=\"smalltext\">Die Prefix-Nummer der Tabellen, die während der Board-Installation angegeben wurde. F&uuml;r VBB2 Installation nicht n&ouml;tig.</span>";
} elseif($language==2) {
    $inst_lang[1] = "Checking your PHP-Installation";
    $inst_lang[2] = "Your Webserver has to fullfill the minimum requirements listet below otherwise some errors will poss. occur:<br>";
    $inst_lang[3] = "Systemcomponents";
    $inst_lang[4] = "perform yes/no";
    $inst_lang[5] = "writable avatar Folder";
    $inst_lang[6] = "writable templates/default Folder";
    $inst_lang[7] = "writable files Folder";
    $inst_lang[8] = "writable include/config.inc.php File";
    $inst_lang[9] = "Choose the kind of installation";
    $inst_lang[10] = "Please choose the kind of installation. Please be carefull that your webserver fullfill all minimum requirements.<br>After you have clicked on Start Installation a form will be displayed in which you have to give the data's for MySQL etc.<br><br>A detailed description of the different installation levels can be found within the read-me file . Please read it carefully because some tables can be overwritten!";
    $inst_lang[11] = "Fullinstallation, all tables will be installed";
    $inst_lang[12] = "Partial installation, an engine is already installed";
    $inst_lang[13] = "Combine the Engine with a discussion board (Beta!)";
    $inst_lang[14] = "Update the $app_name from V$old_version to V$new_version";
    $inst_lang[15] = "Please Choose";
    $inst_lang[16] = "Fullinstallation";
    $inst_lang[17] = "Server Environment";
    $inst_lang[18] = "Please insert the url to the Engine. If you are in doubt please contact your Webhoster.<br><br>";
    $inst_lang[19] = "<b>Url to the Engine</b><br><span class=\"smalltext\">without trailing /, start with http://</span>";
    $inst_lang[20] = "Engine Environment";
    $inst_lang[21] = "Please choose the language of the Admin-Center. If you want you can give a header and a footer file (this option can be changed later within the file config.inc.php)<br><br>";
    $inst_lang[22] = "<b>Admin Center Language</b>";
    $inst_lang[23] = "<b>Path to the Header</b><br><span class=\"smalltext\">Absolute path, not url!</span>";
    $inst_lang[24] = "<b>Path to the Footer</b><br><span class=\"smalltext\">Absolute path, not url!</span>";
    $inst_lang[25] = "MySQL Environment";
    $inst_lang[26] = "Please insert the required information concerning your MySQL installation. If these are unknown, please contact your Webhoster. You have to give an existing database. If no database exists, please create one first.";
    $inst_lang[27] = "<b>MySQL-Host</b>";
    $inst_lang[28] = "<b>MySQL-Database Name</b>";
    $inst_lang[29] = "<b>MySQL-Username</b>";
    $inst_lang[30] = "<b>MySQL-Password</b>";
    $inst_lang[31] = "Please be sure, that all table-names are correct. That is very important if you want to combine different engines or if you want to install two or more engines on the same server and database";
    $inst_lang[32] = "Administrator Account";
    $inst_lang[33] = "Please insert the required information for the admin account. Check the correctnes otherwise you will not have access to the Admin Center";
    $inst_lang[34] = "<b>Username</b>";
    $inst_lang[35] = "<b>Password</b>";
    $inst_lang[36] = "<b>Password again</b>";
    $inst_lang[37] = "<b>Email-Address</b>";
    $inst_lang[38] = "Install data";
    $inst_lang[39] = "It was not possible to start a MySQL connection with the given data";
    $inst_lang[40] = "Not able to write configuration file (check write permissions of config.inc.php)<br>";
    $inst_lang[41] = "Incomplete statement (UserName, Password, Passwort again or Email is missing)<br>";
    $inst_lang[42] = "Password is not correct (Password and Password again are not the same)<br>";
    $inst_lang[43] = "Url missing<br>";
    $inst_lang[44] = "An error occurred";
    $inst_lang[45] = "The following error occurred during the installation:<br><br>";
    $inst_lang[46] = "Please click <a href='javascript:history.back()'>here</a> to correct the data";
    $inst_lang[47] = "Build up tables";
    $inst_lang[48] = "- Table successfully created";
    $inst_lang[49] = "Progress";
    $inst_lang[50] = "Combine Engine with discussion board - currently there is only a discussion board installed, but no Engine";
    $inst_lang[51] = "Board Environment";
    $inst_lang[52] = "It is necessary that you have only installed a discussion board. If you have already installed an Engine, please go back and choose partialy installation.<br><br>";
    $inst_lang[53] = "<b>Board-Number</b><br><span class=\"smalltext\">The board prefix of the WBB. Standard: 1</span>";
    $inst_lang[54] = "You have chosen to combine the Engine with the WBB. The tables you have given are not ready for this installation, please change to proceed.";
    $inst_lang[55] = "Update $app_name - from Version $old_version to the Version $new_version";
    $inst_lang[56] = "Please take notice:";
    $inst_lang[57] = "Within these installation step you have to adjust the tables within the <b>config.inc.php</b> file because you have chosen the standard tables. This option is very important if you combine the engine with another or/with a WBB";
    $inst_lang[58] = "successfully enlarged";
    $inst_lang[59] = "Installation of $app_name";
    $inst_lang[60] = "Congratulations, Installation successfully<br><br>The $app_name installation has been completed. Please delete the file installer.php from your webserver. Click <a href=\"%s/admin/index.php\">hier</a>, to access the Admin Center";
    $inst_lang[61] = "The $app_name installation has been completed. Please delete the file installer.php from your webserver. Click <a href=\"%s/admin/index.php\">hier</a>, to access the Admin Center";
    $inst_lang[62] = "here</a>, to access the Admin Center<br><br><br>";
    $inst_lang[63] = "Thanks and have fun...";
    $inst_lang[64] = "Partially Installation - you have already installed one or more Engines";
    $inst_lang[65] = "You have chosen partially installation. Please check the table names for avatars, groups and users, because the table names are not changed!";    
    $inst_lang[66] = "Guest";   
    $inst_lang[67] = "writable admin/css_style.txt File";
    $inst_lang[68] = "writable thumbnail folder";
    $inst_lang[lang] = "english";
    $inst_lang[100] = "writeable Site_Images-Folder";
    $inst_lang[101] = "Step %s of %s";
    $inst_lang[102] = "Please make your choice";
    $inst_lang[103] = "<b>Kind of Installation</b>";
    $inst_lang[104] = "Go to step %s";
    $inst_lang[105] = "Reset";
    $inst_lang[106] = "No kind of installation chosen";
    $inst_lang[107] = "Es wurden nicht alle Felder der MySQL Zugangsdaten gef&uuml;llt<br>";
    $inst_lang[108] = "Um das Script als Erweiterung f&uuml;r das WBB zu installieren, mu&szlig; unbedingt die Board-Nummer angegeben werden.<br>";         
    $inst_lang[109] = "Installation Notice";        
    $inst_lang[110] = "Complete the Installation";  
    $inst_lang[111] = "Important to go on with the installation process";
    $inst_lang[112] = "IMPORTANT !!!";
    $inst_lang[113] = "The Installscript could not write the config-File accordingly. If you want to go on with the installation process, download the file config.inc.php manually and save the file via FTP in the folder 'include'.";
    $inst_lang[114] = "Download Config-File";   
    $inst_lang[115] = "deactivated SafeMode";
    $inst_lang[116] = "Download config-File";
    $inst_lang[117] = "Complete the Installation";
    $inst_lang[118] = "<b>Engine-Usergroup:</b>";
    $inst_lang[119] = "<b>Board-Usergroup:</b>";
    $inst_lang[120] = "At the following you will find all existing Engine and Board Usergroups. Please sort the groups (e. g. Administrator = Administrator). If you need more Usergroups, please go to the Board and create the Usergroup. After that reload this page.";
    $inst_lang[121] = "Member / Moderator";
    $inst_lang[122] = "Member";
    $inst_lang[123] = "Installation with discussion Board";
    $inst_lang[124] = "Important: The discussion Board must be already installed. If you have already an Engine installed, please go back and choose Partial installation";
    $inst_lang[125] = "<b>Board-Driver:</b><br><span class=\"smalltext\">Please select the Board you have already installed.</span>";
    $inst_lang[126] = "<b>Board-Number / Prefix</b><br><span class=\"smalltext\">The prefix of the Board Tables you gave during the board installation. Not necessary for VBB2.</span>";    
}   

if($step == "downloadconfig") {
    header("Content-Type: text/x-delimtext; name=\"config.inc.php\"");
    header("Content-disposition: attachment; filename=config.inc.php"); 
    echo urldecode($_POST['config_data']);
    exit;
}

/* Start Installer */
buildSetupHeader();

if(!$step) {
    buildFormHeader("2");
    buildHeaderRow();
    buildTableSeparator("Schritt 1 von 5");
    buildOneRow("<b>".$app_name." V".$new_version."</b>", "2", "right");
    buildOneRow("Willkommen zur Installation der ".$app_name." V".$new_version." - Starten Sie die Installation<br>Welcome to the installation of the ".$app_name." V".$new_version." - start the installation");
    buildTableFooter();
    buildTableHeader("Sprache wählen / Choose your language");
    $option .= "<select name=\"language\">";
    $option .= "<option value=\"1\" selected>Deutsch</option>";
    $option .= "<option value=\"2\">Englisch</option>";
    $option .= "</select>"; 
    
    buildStandardRow("<b>Sprache/Language</b><br><span class=\"smalltext\">Bitte hier die Sprache wählen, in der die Installation stattfinden soll / Please choose the language for the installation-file: </span>", $option);
    buildFormFooter("Start", "");  
}

if($_POST['step'] == 2) {
    buildFormHeader("3");
    buildHiddenField("language",$_POST['language']);
    buildHeaderRow();
    buildTableSeparator(sprintf($inst_lang[101],2,5));
    buildOneRow("<b>".$app_name." V".$new_version."</b>", "2", "right");
    buildTableFooter();
    buildTableHeader($inst_lang[1]);  
    buildOneRow($inst_lang[2]); 
    @chmod("./files/", 0777);
    @chmod("./thumbnails/", 0777);
    @chmod("./avatar/", 0777);
    @chmod("./templates/default/", 0777);
    @chmod("./include/config.inc.php", 0777);
    $reg_global = @get_cfg_var('register_globals');
    $akt_php = phpversion();
    $akt_size = ini_get("upload_max_filesize");
    
    $php_image = ($akt_php > "4.1.0") ? "on" : "off";
    buildStandardRow("<b>PHP-Version > 4.1.0</b>", "<img src=\"templates/default/images/".$php_image.".gif\" width=\"13\" height=\"13\" border=\"0\" align=\"middle\"> PHP ".$akt_php);     
    $file_image = ($akt_size > "0") ? "on" : "off";
    buildStandardRow("<b>upload_max_filesize > 0</b>", "<img src=\"templates/default/images/".$file_image.".gif\" width=\"13\" height=\"13\" border=\"0\" align=\"middle\"> Upload Max. ".$akt_size);
    buildStandardRow("<b>$inst_lang[115]</b>", "<img src=\"templates/default/images/".(!@ini_get("safe_mode") ? "on" : "off").".gif\" width=\"13\" height=\"13\" border=\"0\" align=\"middle\">");  
    buildStandardRow("<b>$inst_lang[5]</b>", "<img src=\"templates/default/images/".(@is_writeable("./avatar") ? "on" : "off").".gif\" width=\"13\" height=\"13\" border=\"0\" align=\"middle\">");
    buildStandardRow("<b>$inst_lang[6]</b>", "<img src=\"templates/default/images/".(@is_writeable("./templates/default/") ? "on" : "off").".gif\" width=\"13\" height=\"13\" border=\"0\" align=\"middle\">");
    buildStandardRow("<b>$inst_lang[8]</b>", "<img src=\"templates/default/images/".(@is_writeable("./include/config.inc.php") ? "on" : "off").".gif\" width=\"13\" height=\"13\" border=\"0\" align=\"middle\">");
    buildStandardRow("<b>$inst_lang[7]</b>", "<img src=\"templates/default/images/".(@is_writeable("./files/") ? "on" : "off").".gif\" width=\"13\" height=\"13\" border=\"0\" align=\"middle\">"); 
    buildStandardRow("<b>$inst_lang[68]</b>", "<img src=\"templates/default/images/".(@is_writeable("./thumbnail/") ? "on" : "off").".gif\" width=\"13\" height=\"13\" border=\"0\" align=\"middle\">");    
    
    buildTableSeparator($inst_lang[9]);
    buildOneRow($inst_lang[10]);
    $option .= "<select name=\"kind\">";
    $option .= "<option value=\"0\" selected> ---- $inst_lang[102] ----</option>";
    $option .= "<option value=\"1\">$inst_lang[11]</option>";
    $option .= "<option value=\"2\">$inst_lang[12]</option>";
    $option .= "<option value=\"3\">$inst_lang[13]</option>";
    $option .= "<option value=\"4\">$inst_lang[14]</option>";
    $option .= "</select>";
    buildStandardRow($inst_lang[103], $option);
    buildFormFooter(sprintf($inst_lang[104],3), $inst_lang[105]);
}

if($_POST['step'] == '3' && $_POST['kind']) {
    buildFormHeader("4");
    buildHiddenField("kind",$_POST['kind']);
    buildHiddenField("language",$_POST['language']);    
    if($_POST['kind'] != 3) buildHiddenField("board_driver","default");
    buildHeaderRow();
    if($_POST['kind'] == 1) $installation = $inst_lang[16]; 
    if($_POST['kind'] == 2) $installation = $inst_lang[64]; 
    if($_POST['kind'] == 3) $installation = $inst_lang[50]; 
    if($_POST['kind'] == 4) $installation = $inst_lang[55]; 
    buildTableSeparator(sprintf($inst_lang[101],3,5)." ".$installation);
    buildOneRow("<b>".$app_name." V".$new_version."</b>", "2", "right");
    buildTableFooter();
    
    if($_POST['kind'] == 4) {
        buildTableHeader($inst_lang[56]);
        buildOneRow($inst_lang[57]);
        buildTableFooter();
    }
    
    if($_POST['kind'] == 1 || $_POST['kind'] == 2 || $_POST['kind'] == 3) { 
        buildTableHeader($inst_lang[17]);
        buildOneRow($inst_lang[18]);
        if($_POST['url2script']) {
            $this_url = $_POST['url2script'];
        } else {
            $this_url = str_replace("/installer.php","",$_SERVER['HTTP_REFERER']);
        }        
        buildInputRow($inst_lang[19], "url2script", $this_url);
        buildTableFooter();
    }       
    if($_POST['kind'] == 1 || $_POST['kind'] == 2 || $_POST['kind'] == 3 || $_POST['kind'] == 4) {
        buildTableHeader($inst_lang[20]);   
        $option .= "<select name=\"admin_language\">";
        $option .= "<option value=\"1\" selected>Deutsch</option>";
        $option .= "<option value=\"2\">English</option>";
        $option .= "</select>"; 
        buildOneRow($inst_lang[21]);
        buildStandardRow($inst_lang[22], $option);
        buildInputRow($inst_lang[23], "header_path", $own_header);
        buildInputRow($inst_lang[24], "footer_path", $own_footer);
        buildTableFooter();
    }
    
    if($_POST['kind'] == 3) {
        buildTableHeader($inst_lang[123]);
        buildOneRow($inst_lang[124]);
        $option2 .= "<select name=\"board_driver\">";
        $option2 .= "<option value=\"wbblite\" selected>Woltlab Burning Board Lite</option>";
        $option2 .= "<option value=\"wbb2\">Woltlab Burning Board 2.x</option>";
        $option2 .= "<option value=\"vbb2\">VBulletin 2.x</option>";
        $option2 .= "<option value=\"vbb3\">VBulletin 3.x</option>";
        $option2 .= "<option value=\"phpbb2\">PHPBB 2.x</option>";
        $option2 .= "</select>";                    
        buildStandardRow($inst_lang[125], $option2);
        buildInputRow($inst_lang[126], "board_prefix");
        buildTableFooter();
    }
    
    if($_POST['kind'] == 1 || $_POST['kind'] == 2 || $_POST['kind'] == 3 || $_POST['kind'] == 4) {  
        buildTableHeader($inst_lang[25]);   
        buildOneRow($inst_lang[26]);
        buildInputRow($inst_lang[27], "db_host",$hostname);
        buildInputRow($inst_lang[28], "db_name",$dbName);
        buildInputRow($inst_lang[29], "db_user",$dbUname);
        buildInputRow($inst_lang[30], "db_password",$dbPasswort);   
        buildOneRow($inst_lang[31]);
        if($_POST['kind'] == 1) buildTableFooter();
    }
    if($_POST['kind'] == 1) {   
        buildTableHeader($inst_lang[32]);   
        buildOneRow($inst_lang[33]);
        buildInputRow($inst_lang[34], "username");
        buildPasswordRow($inst_lang[35],"pass");
        buildPasswordRow($inst_lang[36],"pass2");
        buildInputRow($inst_lang[37], "email");
    }
    buildFormFooter(sprintf($inst_lang[104],4), $inst_lang[105]);
} elseif($_POST['step'] == '3' && !$_POST['kind']) {
    buildHeaderRow(2);
    buildTableSeparator($inst_lang[44]);
    buildTableFooter();
    buildErrorRow($inst_lang[106]);
}

if($_POST['step'] == '4') {
    $installation_go = false;
    $download_config = false;
    if(empty($_POST['db_host']) || empty($_POST['db_name']) || empty($_POST['db_user'])) {
        $installation_go = false;
        $error_text .= $inst_lang[107];
    } else {
        $try_connect = @mysql_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_password']);
        if(!$try_connect) {
            $installation_go = false;
            $error_text .= $inst_lang[39];
        } else {
            $installation_go = true;
        }
        mysql_close($try_connect);  
    }
    
    if(!$set_table) $set_table = "dl1_config";
    if($_POST['kind'] != 3) {
        if(!$user_table) $user_table = "dl1_user";
    } else {
        switch($_POST['board_driver']) {
            case "wbblite":
                $user_table = "bb".$_POST['board_prefix']."_users";
                break;
            case "wbb1":
                $user_table = "bb".$_POST['board_prefix']."_user_table";
                break;
            case "wbb2":
                $user_table = "bb".$_POST['board_prefix']."_users";
                break;
            case "vbb2":
                $user_table = "user";
                break;
            case "vbb3":
                $user_table = $_POST['board_prefix']."user ";
                break;
            case "phpbb2":
                $user_table = $_POST['board_prefix']."users";
                break;
        }           
    }
    if(!$cat_table) $cat_table = "dl1_cat";
    if(!$dlcomment_table) $dlcomment_table = "dl1_commments";
    if(!$dl_table) $dl_table = "dl1_downloads";
    if(!$stats_day_table) $stats_day_table = "dl1_stats_day";
    if(!$stats_month_table) $stats_month_table = "dl1_stats_month";
    if(!$dl_iptable) $dl_iptable = "dl1_iptable";
    if(!$dl_childtable) $dl_childtable = "dl1_childlist";            
    if(!$mirror_table) $mirror_table = "dl1_mirror";
    if(!$licence_table) $licence_table = "dl1_licence";
    if(!$avat_table) $avat_table = "dl1_avatar";
    if(!$group_table) $group_table = "dl1_groups";  
    if(!$style_table) $style_table = "dl1_style";
    
    if($_POST['kind'] == 1 || $_POST['kind'] == 2 || $_POST['kind'] == 3) {
        if(empty($_POST['url2script'])) {
            $installation_go = false;
            $error_text .= $inst_lang[43];      
        } else {
            $installation_go = true;
        }
    }
    
    if($_POST['kind'] == 1) {       
        if(empty($_POST['username']) || empty($_POST['pass']) || empty($_POST['pass2']) || empty($_POST['email'])) {
            $installation_go = false;
            $error_text .= $inst_lang[41];          
        } else {
            if($_POST['pass'] == $_POST['pass2']) {
                $installation_go = true;    
            } else {
                $installation_go = false;
                $error_text .= $inst_lang[42];
            }
        }
    }
    
    if($_POST['kind'] == 3) {
        if($_POST['board_driver'] != "vbb2" && empty($_POST['board_prefix'])) {
            $installation_go = false;
            $error_text .= $inst_lang[108];     
        } else {
            $installation_go = true;
        }
    }
    
    if($_POST['kind'] == 2) {   
        if($group_table == "dl1_groups" || $user_table == "dl1_user" || $avat_table == "dl1_avatar") {
            $installation_go = false;
            $error_text .= $inst_lang[65];      

        }   
    }
    
    if($_POST['kind'] == 3) {   
        if($user_table == "dl1_user") {
            $installation_go = false;
            $error_text .= $inst_lang[54];      

        }   
    }   
    
    if($installation_go == true) {      
        $config_data = config_file($_POST['db_host'],$_POST['db_user'],$_POST['db_password'],$_POST['db_name'],$_POST['header_path'],$_POST['footer_path'],$set_table,$user_table,$group_table,$avat_table,$cat_table,$dlcomment_table,$dl_table,$stats_day_table,$stats_month_table,$dl_iptable,$dl_childtable,$mirror_table,$licence_table,$_POST['admin_language'],$style_table);
        
        $fp = @fopen('./include/config.inc.php', 'w+');
        $ok = @fwrite($fp, $config_data);
        if (!$ok) {
            $error_text .= $inst_lang[40];
            $download_config = true;
        }
        @fclose($fp);       
    }
    
    if($installation_go == false) {
        buildHeaderRow(2);
        buildTableSeparator($inst_lang[44]);
        buildTableFooter();
        buildErrorRow($error_text); 
    } else {
        $db_sql = new db_sql($_POST['db_name'],$_POST['db_host'],$_POST['db_user'],$_POST['db_password']);
        $install_message .= "<ul>";
        if($_POST['kind'] != 4) {
            if($_POST['kind'] == 1) {
                @mysql_query("DROP TABLE $user_table");
                install_user_table($user_table);
                $install_message .= "<li>".$user_table." $inst_lang[48]</li>";
            }
            
            if($_POST['kind'] == 1 ||$_POST['kind'] == 3) {
                @mysql_query("DROP TABLE $avat_table");
                install_avatar_table($avat_table);
                $install_message .= "<li>".$avat_table." $inst_lang[48]</li>";
                @mysql_query("DROP TABLE $group_table");
                install_group_table($group_table);
                $install_message .= "<li>".$group_table." $inst_lang[48]</li>";
            }
            
            if($_POST['kind'] == 1 ||$_POST['kind'] == 2 ||$_POST['kind'] == 3) {
                @mysql_query("DROP TABLE $cat_table");          
                install_cat_table($cat_table);
                $install_message .= "<li>".$cat_table." $inst_lang[48]</li>";   
                @mysql_query("DROP TABLE $set_table");
                install_set_table($set_table);  
                $install_message .= "<li>".$set_table." $inst_lang[48]</li>";   
                @mysql_query("DROP TABLE $dlcomment_table");
                install_comment_table($dlcomment_table);
                $install_message .= "<li>".$dlcomment_table." $inst_lang[48]</li>";
                @mysql_query("DROP TABLE $dl_table");
                install_downloads_table($dl_table);
                $install_message .= "<li>".$dl_table." $inst_lang[48]</li>";
                @mysql_query("DROP TABLE $stats_day_table");
                install_daystats_table($stats_day_table);
                $install_message .= "<li>".$stats_day_table." $inst_lang[48]</li>";
                @mysql_query("DROP TABLE $stats_month_table");
                install_monthstats_table($stats_month_table);
                $install_message .= "<li>".$stats_month_table." $inst_lang[48]</li>";                
                @mysql_query("DROP TABLE $dl_iptable"); 
                install_ip_table($dl_iptable);
                $install_message .= "<li>".$dl_iptable." $inst_lang[48]</li>";
                @mysql_query("DROP TABLE $dl_childtable");  
                install_childlist_table($dl_childtable);
                $install_message .= "<li>".$dl_childtable." $inst_lang[48]</li>"; 
                @mysql_query("DROP TABLE $mirror_table");   
                install_mirror_table($mirror_table);
                $install_message .= "<li>".$mirror_table." $inst_lang[48]</li>";                               
                @mysql_query("DROP TABLE $licence_table");  
                install_licence_table($licence_table);
                $install_message .= "<li>".$licence_table." $inst_lang[48]</li>";   
                @mysql_query("DROP TABLE $style_table");    
                install_style_table($style_table);
                $install_message .= "<li>".$style_table." $inst_lang[48]</li>";                               
            }
                
        } else {
            // hier kommt nur das Update rein
            $old_cfg = $db_sql->query_array("SELECT * FROM dl1_settings WHERE styleid='1'");
            @mysql_query("DROP TABLE $set_table");
            install_set_table($set_table);  
            $install_message .= "<li>".$set_table." $inst_lang[48]</li>";            
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (1, 'dlscripturl', '".$old_cfg['dlscripturl']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (2, 'scriptname', '".$old_cfg['scriptname']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (3, 'admin_mail', '".$old_cfg['admin_mail']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (4, 'smilieurl', '".$old_cfg['smilieurl']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (5, 'grafurl', '".$old_cfg['dlscripturl']."/templates/default/images')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (6, 'avaturl', '".$old_cfg['avaturl']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (7, 'fileurl', '".$old_cfg['fileurl']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (8, 'thumburl', '".$old_cfg['thumburl']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (19, 'pagesort', '".$old_cfg['pagesort']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (20, 'dlperpage', '".$old_cfg['dlperpage']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (21, 'newindex', '".$old_cfg['newindex']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (22, 'newindex_q', '".$old_cfg['newindex_q']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (23, 'newlist', '".$old_cfg['newlist']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (24, 'newlist_q', '".$old_cfg['newlist_q']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (25, 'top_list', '".$old_cfg['top_list']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (26, 'top_list_q', '".$old_cfg['top_list_q']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (27, 'mainwidth', '".$old_cfg['mainwidth']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (28, 'commentmail', '".$old_cfg['commentmail']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (29, 'deadmail', '".$old_cfg['deadmail']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (30, 'deadlink', '".$old_cfg['deadlink']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (31, 'guestpost', '".$old_cfg['guestpost']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (32, 'cool', '".$old_cfg['cool']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (33, 'cool_percent', '".$old_cfg['cool_percent']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (34, 'lastcomment', '".$old_cfg['lastcomment']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (35, 'lastcomment_q', '".$old_cfg['lastcomment_q']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (36, 'stats', '".$old_cfg['stats']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (37, 'show_path', '".$old_cfg['show_path']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (38, 'mainurl', '".$old_cfg['mainurl']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (39, 'directpost', '".$old_cfg['directpost']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (40, 'showvisitorinfo', '".$old_cfg['showvisitorinfo']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (41, 'maxsize', '".$old_cfg['maxsize']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (42, 'userreg', '".$old_cfg['userreg']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (43, 'userlogin', '".$old_cfg['userlogin']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (44, 'language', '".$inst_lang[lang]."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (45, 'addtplname', '".$old_cfg['addtplname']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (46, 'activategzip', '".$old_cfg['activategzip']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (47, 'gziplevel', '".$old_cfg['gziplevel']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (48, 'max_comment_length', '".$old_cfg['max_comment_length']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (49, 'more_stats', '".$old_cfg['more_stats']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (50, 'more_stats_admin', '".$old_cfg['more_stats_admin']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (51, 'isoffline', '".$old_cfg['isoffline']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (52, 'offline_why', '".$old_cfg['offline_why']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (53, 'reg_withmail', '".$old_cfg['reg_withmail']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (54, 'allowedreferer', '".$old_cfg['allowedreferer']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (55, 'upload_extension', '".$old_cfg['upload_extension']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (56, 'filemail', '".$old_cfg['filemail']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (57, 'active_lock', '".$old_cfg['active_lock']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (58, 'kindoflock', '".$old_cfg['kindoflock']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (59, 'time_to_lock', '".$old_cfg['time_to_lock']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (60, 'user_rate_factor', '".$old_cfg['user_rate_factor']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (61, 'newmark', '".$old_cfg['newmark']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (62, 'lastcatcheck', '".$old_cfg['lastcatcheck']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (63, 'enable_quickjump', '".$old_cfg['enable_quickjump']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (64, 'alluser_upload', '".$old_cfg['alluser_upload']."')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (65, 'std_group', '7')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (66, 'timeoffset', '0')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (67, 'shortdate', 'd.m.Y')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (68, 'longdate', 'd.m.Y, H:i')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (69, 'timeformat', 'H:i')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (70, 'use_smtp', '0')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (71, 'smtp_server', '')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (72, 'smtp_username', '')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (73, 'smtp_password', '')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (74, 'active_image_resizer', '0')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (75, 'image_width', '130')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (76, 'image_height', '130')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (77, 'front_download', '1')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (78, 'template_folder', 'default')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (79, 'style_id', '1')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (80, 'row_top_border_color', '#333333')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (81, 'row_top_background_color', '#CCCCCC')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (82, 'content_border_color', '#333333')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (83, 'row_bottom_border_color', '#333333')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (84, 'row_bottom_background_color', '#CCCCCC')");
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (85, 'body_background_color', '#F4F7FE')");             
            $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (86, 'updatemark', '4')");
            @mysql_query("DROP TABLE dl1_settings");    
            $install_message .= "<li>".$set_table." $inst_lang[58]</li>";            
            $db_sql->sql_query("ALTER TABLE $cat_table ADD cat_style tinyint(1) NOT NULL default '0'");
            $install_message .= "<li>".$cat_table." $inst_lang[58]</li>";
            $db_sql->sql_query("ALTER TABLE $dl_table ADD licence_id int(11) NOT NULL default '0'");
            $db_sql->sql_query("ALTER TABLE $dl_table ADD update_date int(11) NOT NULL default '0'");
            $install_message .= "<li>".$dl_table." $inst_lang[58]</li>";
            $db_sql->sql_query("ALTER TABLE $group_table ADD maxgroupdownloadspeed int(5) NOT NULL default '0'");
            $install_message .= "<li>".$group_table." $inst_lang[58]</li>";
            @mysql_query("DROP TABLE $mirror_table");           
            install_mirror_table($mirror_table);
            $install_message .= "<li>".$mirror_table." $inst_lang[48]</li>";
            @mysql_query("DROP TABLE $licence_table");          
            install_licence_table($licence_table);
            $install_message .= "<li>".$licence_table." $inst_lang[48]</li>"; 
            @mysql_query("DROP TABLE $style_table");    
            install_style_table($style_table);
            $install_message .= "<li>".$style_table." $inst_lang[48]</li>";                                 
        }
        $install_message .= "</ul>";    
        buildFormHeader("5");
        buildHiddenField("kind",$_POST['kind']);
        if($_POST['kind'] == 3) buildHiddenField("board_driver",$_POST['board_driver']);
        if($_POST['kind'] == 3) buildHiddenField("board_prefix",$_POST['board_prefix']);        
        buildHiddenField("language",$_POST['language']);    
        buildHiddenField("username",$_POST['username']);
        buildHiddenField("pass",$_POST['pass']);
        buildHiddenField("email",$_POST['email']);
        buildHiddenField("url2script",$_POST['url2script']);
        buildHeaderRow();
        
        buildTableSeparator(sprintf($inst_lang[101],4,5));
        buildOneRow("<b>".$app_name." V".$new_version."</b>", "2", "right");
        buildTableFooter();     
        buildTableHeader($inst_lang[109]);
        buildOneRow($install_message);
        buildFormFooter(sprintf($inst_lang[104],5), $inst_lang[105]);
        
        if($download_config == true) {
            buildErrorRow($inst_lang[111]);
            buildFormHeader("downloadconfig");
            buildHiddenField("config_data",urlencode($config_data));
            buildTableHeader($inst_lang[112]);
            buildOneRow($inst_lang[113]);
            buildFormFooter($inst_lang[116],"");
        }
    }
}

if($_POST['step'] == '5') {
    $db_sql = new db_sql($dbName,$hostname,$dbUname,$dbPasswort);

    $passwort = md5($_POST['pass']);

    include_once('installer_css.php');  
    $db_sql->sql_query("INSERT INTO $style_table (style_id, style_name, template_folder, body_font_face, body_font_color, body_font_size, body_background_color, row_top_border_color, row_top_background_color, row_top_font_color, row_top_font_size, breadcrumb_font_color, breadcrumb_font_size, breadcrumb_font_color_hover, content_border_color, content_background_color_odd, content_background_color_even, content_font_color, content_font_color_hover, content_font_size, content_highlight_background_color, content_highlight_font_color, content_highlight_font_color_hover, row_bottom_border_color, row_bottom_background_color, row_bottom_font_color, row_bottom_font_size, css_file) VALUES (1, 'Default Style', 'default', 'Verdana, Arial, Helvetica, sans-serif', '#000000', '10px', '#F4F7FE', '#333333', '#CCCCCC', '#000000', '11px', '#0055A8', '11px', '#FF3300', '#333333', '#CCCCCC', '#A9A9A9', '#000000', '#FF3300', '10px', '#5C8EEB', '#FFFFFF', '#FFFFFF', '#333333', '#CCCCCC', '#000000', '11px','".addslashes($css_import)."');");         
    
    if($_POST['kind'] == 1) {
        $db_sql->sql_query("INSERT INTO $user_table (username,userpassword,useremail,regdate,lastvisit,groupid,show_email_global,blocked,activation,canuploadfile,canpostarticle)
                            VALUES ('".addslashes($_POST['username'])."','".$passwort."','".addslashes($_POST['email'])."','".time()."','".time()."','1','0','0','1','1','1')");
    } 
    
    if($_POST['kind'] == 1 || $_POST['kind'] == 2 || $_POST['kind'] == 3) {
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (1, 'dlscripturl', '".$_POST['url2script']."')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (2, 'scriptname', 'Download-Engine')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (3, 'admin_mail', '".addslashes($_POST['email'])."')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (4, 'smilieurl', '".$_POST['url2script']."/smilie')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (5, 'grafurl', '".$_POST['url2script']."/templates/default/images')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (6, 'avaturl', '".$_POST['url2script']."/avatar')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (7, 'fileurl', '".$_POST['url2script']."/files')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (8, 'thumburl', '".$_POST['url2script']."/thumbnail')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (19, 'pagesort', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (20, 'dlperpage', '10')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (21, 'newindex', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (22, 'newindex_q', '2')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (23, 'newlist', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (24, 'newlist_q', '5')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (25, 'top_list', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (26, 'top_list_q', '2')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (27, 'mainwidth', '90%')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (28, 'commentmail', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (29, 'deadmail', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (30, 'deadlink', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (31, 'guestpost', '2')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (32, 'cool', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (33, 'cool_percent', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (34, 'lastcomment', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (35, 'lastcomment_q', '2')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (36, 'stats', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (37, 'show_path', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (38, 'mainurl', '".$_POST['url2script']."')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (39, 'directpost', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (40, 'showvisitorinfo', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (41, 'maxsize', '5048576')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (42, 'userreg', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (43, 'userlogin', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (44, 'language', '".$inst_lang[lang]."')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (45, 'addtplname', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (46, 'activategzip', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (47, 'gziplevel', '5')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (48, 'max_comment_length', '22222')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (49, 'more_stats', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (50, 'more_stats_admin', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (51, 'isoffline', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (52, 'offline_why', '')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (53, 'reg_withmail', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (54, 'allowedreferer', '')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (55, 'upload_extension', 'zip')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (56, 'filemail', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (57, 'active_lock', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (58, 'kindoflock', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (59, 'time_to_lock', '1440')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (60, 'user_rate_factor', '5')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (61, 'newmark', '5')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (62, 'lastcatcheck', '1071696145')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (63, 'enable_quickjump', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (64, 'alluser_upload', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (65, 'std_group', '7')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (66, 'timeoffset', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (67, 'shortdate', 'd.m.Y')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (68, 'longdate', 'd.m.Y, H:i')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (69, 'timeformat', 'H:i')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (70, 'use_smtp', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (71, 'smtp_server', '')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (72, 'smtp_username', '')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (73, 'smtp_password', '')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (74, 'active_image_resizer', '0')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (75, 'image_width', '130')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (76, 'image_height', '130')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (77, 'front_download', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (78, 'template_folder', 'default')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (79, 'style_id', '1')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (80, 'row_top_border_color', '#333333')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (81, 'row_top_background_color', '#CCCCCC')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (82, 'content_border_color', '#333333')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (83, 'row_bottom_border_color', '#333333')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (84, 'row_bottom_background_color', '#CCCCCC')");
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (85, 'body_background_color', '#F4F7FE')");                                                 
        $db_sql->sql_query("INSERT INTO $set_table (settingid, find_word, replace_value) VALUES (86, 'updatemark', '4')");
    }       
    
    if($_POST['kind'] == 1 || $_POST['kind'] == 3) {    
        $db_sql->sql_query("INSERT INTO $group_table VALUES (1, 'Administrator', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1000)");
        $db_sql->sql_query("INSERT INTO $group_table VALUES (2, 'Moderator', 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000)");
        $db_sql->sql_query("INSERT INTO $group_table VALUES (3, '".$inst_lang[122]."', 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000)");
        $db_sql->sql_query("INSERT INTO $group_table VALUES (4, '".$inst_lang[66]."', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000)");
        $db_sql->sql_query("INSERT INTO $group_table VALUES (5, 'Super Moderator', 0, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000)");
        $db_sql->sql_query("INSERT INTO $group_table VALUES (6, 'News Poster', 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000)");
        $db_sql->sql_query("INSERT INTO $group_table VALUES (7, '".$inst_lang[122]."', 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000)");
        $db_sql->sql_query("INSERT INTO $group_table VALUES (8, '".$inst_lang[66]."', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000)");
        $db_sql->sql_query("INSERT INTO $group_table VALUES (9, 'Co-Administrator', 0, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000)");
    }
    
    if($_POST['kind'] == 1) {               
        $db_sql->sql_query("INSERT INTO $user_table (userid,username,groupid) VALUES ('2','".$inst_lang[66]."','8')");
    }       
    
    if($_POST['kind'] == 1) {                   
        $db_sql->sql_query("INSERT INTO $avat_table (avatardata) VALUES ('no_avatar.gif')");    
    }   
    
    if($_POST['kind'] == 2) {   
        $db_sql->sql_query("ALTER TABLE $group_table ADD maxgroupdownloadspeed int(5) NOT NULL default '0'");
        $db_sql->sql_query("UPDATE $group_table SET canuploadfiles='1',canuseadvancedstats='1',canseetopstatsfiles='1',canaccessregisteredfiles='1',maxgroupdownloadspeed='1000' WHERE groupid='1'");
        $db_sql->sql_query("UPDATE $group_table SET canuploadfiles='0',canuseadvancedstats='1',canseetopstatsfiles='1',canaccessregisteredfiles='1',maxgroupdownloadspeed='1000' WHERE groupid='2'");
        $db_sql->sql_query("UPDATE $group_table SET canuploadfiles='1',canuseadvancedstats='0',canseetopstatsfiles='1',canaccessregisteredfiles='1',maxgroupdownloadspeed='1000' WHERE groupid='3'");
        $db_sql->sql_query("UPDATE $group_table SET canuploadfiles='1',canuseadvancedstats='0',canseetopstatsfiles='1',canaccessregisteredfiles='0',maxgroupdownloadspeed='1000' WHERE groupid='4'");
        $db_sql->sql_query("UPDATE $group_table SET canuploadfiles='0',canuseadvancedstats='1',canseetopstatsfiles='1',canaccessregisteredfiles='1',maxgroupdownloadspeed='1000' WHERE groupid='5'");
        $db_sql->sql_query("UPDATE $group_table SET canuploadfiles='0',canuseadvancedstats='0',canseetopstatsfiles='1',canaccessregisteredfiles='1',maxgroupdownloadspeed='1000' WHERE groupid='6'");
        $db_sql->sql_query("UPDATE $group_table SET canuploadfiles='1',canuseadvancedstats='0',canseetopstatsfiles='1',canaccessregisteredfiles='1',maxgroupdownloadspeed='1000' WHERE groupid='7'");
        $db_sql->sql_query("UPDATE $group_table SET canuploadfiles='1',canuseadvancedstats='0',canseetopstatsfiles='1',canaccessregisteredfiles='0',maxgroupdownloadspeed='1000' WHERE groupid='8'");
        $db_sql->sql_query("UPDATE $group_table SET canuploadfiles='0',canuseadvancedstats='1',canseetopstatsfiles='1',canaccessregisteredfiles='1',maxgroupdownloadspeed='1000' WHERE groupid='9'");   
    }
    
    if($_POST['kind'] == 3) {                           
        install_driver_table();
        buildFormHeader("6");
        buildHiddenField("kind",$_POST['kind']);
        if($_POST['kind'] == 3) buildHiddenField("board_driver",$_POST['board_driver']);
        if($_POST['kind'] == 3) buildHiddenField("board_prefix",$_POST['board_prefix']);
        buildHiddenField("language",$_POST['language']);    
    }
    
    $end = $db_sql->query_array("SELECT replace_value FROM $set_table WHERE find_word='dlscripturl'"); 
    buildHeaderRow();
    buildTableSeparator(sprintf($inst_lang[101],5,5));
    buildOneRow("<b>".$app_name." V".$new_version."</b>", "2", "right");
    
    if($_POST['kind'] == 3) {   
        buildOneRow($inst_lang[120]);
        $usesql = false;

        switch($_POST['board_driver']) {
            case "wbblite":
                $usesql = true;
                $id = "groupid";
                $title = "title";
                $sql = "SELECT groupid, title FROM bb".$_POST['board_prefix']."_groups";
                $preselection_array = array(1 => 1, 2 => 3, 5 => 2, 6 => 4, 7 => 4, 8 => 5, 9 => 2);
                break;
            case "wbb1":
                $usesql = true;
                $id = "id";
                $title = "title";        
                $sql = "SELECT id, title FROM bb".$_POST['board_prefix']."_groups";
                $preselection_array = array(1 => 1, 2 => 2, 5 => 5, 6 => 3, 7 => 3, 8 => 4, 9 => 5);
                break;
            case "wbb2":
                $usesql = true;
                $id = "groupid";
                $title = "title";        
                $sql = "SELECT groupid, title FROM bb".$_POST['board_prefix']."_groups";
                $preselection_array = array(1 => 1, 2 => 3, 5 => 2, 6 => 4, 7 => 4, 8 => 5, 9 => 2);
                break;
            case "vbb2":
                $usesql = true;
                $id = "usergroupid";
                $title = "title";        
                $sql = "SELECT usergroupid, title FROM usergroup";
                $preselection_array = array(1 => 6, 2 => 7, 5 => 5, 6 => 2, 7 => 2, 8 => 1, 9 => 5);
                break;
            case "vbb3":
                $usesql = true;
                $id = "usergroupid";
                $title = "title";        
                $sql = "SELECT usergroupid, title FROM ".$_POST['board_prefix']."usergroup";
                $preselection_array = array(1 => 6, 2 => 7, 5 => 5, 6 => 2, 7 => 2, 8 => 1, 9 => 5);
                break;
            case "phpbb2":
                $usesql = false;
                $preselection_array = array(1 => 2, 2 => 3, 5 => 3, 6 => 3, 7 => 1, 8 => 3, 9 => 2);
                break;
        }
        
        $result2 = $db_sql->sql_query("SELECT groupid, title FROM $group_table");
        while($engine_group = $db_sql->fetch_array($result2)) {
            if($engine_group['groupid']==3 || $engine_group['groupid']==4) continue;
            buildStandardRow($inst_lang[118]." ".$engine_group['title'], $inst_lang[119]." ".buildBoardOption($usesql,$sql,$id,$title,$engine_group['groupid']));
        }
        
        buildFormFooter($inst_lang[117]);   
    } else {    
        buildTableFooter();     
        buildTableHeader($inst_lang[110]);
        buildOneRow(sprintf($inst_lang[60],$end['replace_value']));
        buildTableFooter();
    }
    
}

if($_POST['step'] == '6') {
    $db_sql = new db_sql($dbName,$hostname,$dbUname,$dbPasswort);
    $end = $db_sql->query_array("SELECT replace_value FROM $set_table WHERE find_word='dlscripturl'");      

    
    if($_POST['kind'] == '3') {
        $db_sql->sql_query("INSERT INTO groups_engine2board (engine_groupid,board_groupid) VALUES (1, ".intval($_POST[1]).")");
        $db_sql->sql_query("INSERT INTO groups_engine2board (engine_groupid,board_groupid) VALUES (2, ".intval($_POST[2]).")"); 
        $db_sql->sql_query("INSERT INTO groups_engine2board (engine_groupid,board_groupid) VALUES (3, ".intval($_POST[7]).")"); 
        $db_sql->sql_query("INSERT INTO groups_engine2board (engine_groupid,board_groupid) VALUES (4, ".intval($_POST[8]).")"); 
        $db_sql->sql_query("INSERT INTO groups_engine2board (engine_groupid,board_groupid) VALUES (5, ".intval($_POST[5]).")"); 
        $db_sql->sql_query("INSERT INTO groups_engine2board (engine_groupid,board_groupid) VALUES (6, ".intval($_POST[6]).")"); 
        $db_sql->sql_query("INSERT INTO groups_engine2board (engine_groupid,board_groupid) VALUES (7, ".intval($_POST[7]).")"); 
        $db_sql->sql_query("INSERT INTO groups_engine2board (engine_groupid,board_groupid) VALUES (8, ".intval($_POST[8]).")"); 
        $db_sql->sql_query("INSERT INTO groups_engine2board (engine_groupid,board_groupid) VALUES (9, ".intval($_POST[9]).")");
        editBoardUser($_POST['board_driver'],$_POST['board_prefix']);   
    }          
    
    buildHeaderRow();
    
    buildTableSeparator($inst_lang[117]);
    buildOneRow("<b>".$app_name." V".$new_version."</b>", "2", "right");
    buildTableFooter();     
    buildTableHeader($inst_lang[110]);
    buildOneRow(sprintf($inst_lang[60],$end['replace_value']));
    buildTableFooter();     

}


buildSetupFooter();
/* Ende Installer */

function buildBoardOption($usesql=false,$sql="",$id="",$title="",$id_array="") {
    global $db_sql,$_POST,$preselection_array,$inst_lang;
    if($usesql) {
        $result = $db_sql->sql_query($sql);
        $option .= "<select name=\"".$id_array."\">\n";
        while($board_group = $db_sql->fetch_array($result)) {
            $option .= "<option value=\"".$board_group[$id]."\"";
            if($board_group[$id] == $preselection_array[$id_array]) $option .= " selected"; 
            $option .= ">".$board_group[$title]."";
            $option .= "</option>\n";
        }
        $option .= "</select>\n";
    } elseif($_POST['board_driver']=="phpbb2") {
        $option .= "<select name=\"".$id_array."\">\n";           
        $option .= "<option value=\"1\"";
        if(0 == $preselection_array[$id_array]) $option .= " selected"; 
        $option .= ">".$inst_lang[66]."</option>\n";
        $option .= "<option value=\"2\"";
        if(1 == $preselection_array[$id_array]) $option .= " selected";
        $option .= ">Administrator</option>\n";
        $option .= "<option value=\"3\"";
        if(2 == $preselection_array[$id_array]) $option .= " selected";
        $option .= ">".$inst_lang[121]."</option>\n";
        $option .= "</select>\n";
    }
    return $option;
}

function buildSetupHeader($head="") {
    global $a_lang, $config, $app_name;
    ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta content="text/html; charset=windows-1252" http-equiv="Content-Type">
<html>
<head>
<title><?php echo $app_name; ?> - Setup</title>
<link rel="stylesheet" href="admin/acstyle.css">
<?php
echo $head;
?>
</head>
<body leftmargin="20" topmargin="0" marginwidth="20" marginheight="20"  bgcolor="#F4F7FE" text="#000000" align="center">
</br>

    <?php
    }
    
function buildSetupFooter() {
?>
</body></html>
<?php
}

function buildErrorRow($message) {
    
    echo "<p>\n<table bgcolor=\"#000000\" width=\"90%\" cellspacing=\"1\" cellpadding=\"0\" border=\"0\" align=\"center\">\n<tr>\n<td>";
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n<tr>\n";
    echo "<td class=\"message\" width=\"26\">&nbsp;<img src=\"admin/images/caution.gif\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">&nbsp;</td>\n";
    echo "<td class=\"message\">".$message."</td>\n";
    echo "</tr>\n</table>\n";
    echo "</td>\n</tr>\n</table>\n</p>\n";
}   

function switchBgColor() {
    global $bgcount;
    if ($bgcount++%2==0) {
        return "firstcolumn";
    } else {
        return "othercolumn";
    }
}

function buildHeaderRow($colspan = 2) {
    echo "<table bgcolor=\"#000000\" width=\"90%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\">\n<tr>\n<td>\n";
    echo "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
    echo "<tr>\n<td colspan=\"$colspan\" class=\"menu_desc\" align=\"center\">";
    echo "<img src=\"templates/default/images/installer_pic.gif\" width=\"390\" height=\"70\" border=\"0\">";
    echo "\n</td>\n</tr>\n";
}

function buildTableHeader($headline, $colspan = 2) {
    echo "<table bgcolor=\"#000000\" width=\"90%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\">\n<tr>\n<td>\n";
    echo "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
    echo "<tr>\n<td colspan=\"$colspan\" class=\"menu_desc\">&raquo;&nbsp;";
    echo $headline;
    echo "\n</td>\n</tr>\n";
}

function buildTableSeparator($title, $colspan = 2) {
    echo "<tr>\n<td colspan=\"$colspan\" class=\"menu_desc\">&raquo;&nbsp;";
    echo $title;
    echo "\n</td>\n</tr>\n";
}   
    
function buildTableFooter($extra="",$colspan=2) {
    if ($extra!="") echo "<tr class=\"table_footer\">\n<td colspan=\"$colspan\" align=\"center\">$extra</td></tr>\n";
    echo "</table>\n";
    echo "</td>\n</tr>\n";
    echo "</table><br />\n";
}   
    
function buildFormHeader($step="", $method="post") {
    global $config,$filename;
    echo "<form action=\"".$filename."\" name=\"ase\" method=\"".$method."\">\n";   
    if ($step != "") echo "<input type=\"hidden\" name=\"step\" value=\"".$step."\">\n";
}

function buildFormFooter($submitname = "Submit", $resetname = "Reset", $colspan = 2) {
    echo "<tr class=\"table_footer\">\n<td colspan=\"".$colspan."\" align=\"center\">\n&nbsp;";
    
    if ($submitname != "") echo "<input type=\"submit\" value=\"   ".$submitname."   \" class=\"button\">\n";   
    if ($resetname != "") echo "<input type=\"reset\" value=\"   ".$resetname."   \" class=\"button\">\n";  

    echo "&nbsp;\n</td>\n</tr>\n</table>\n";
    echo "</td>\n</tr>\n</table>\n";
    echo "</form><br />\n";
}

function buildHiddenField($name,$value="",$html=0) {
    if ($html) $value=htmlspecialchars($value);
    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\">\n";   
}   

function buildInputRow($title, $name, $value="", $size="40", $max_length="", $info=0 , $info_message="", $info_message_header="") {
    global $config,$bgcount;
    if ($max_length) $max = "maxlength=\"".$max_length."\"";
    if ($info) $help = "<a href=\"#\" onMouseOver=\"dcc('".$info_message_header."','".$info_message."'); return true;\" onMouseOut=\"nd(); return true;\"><img src=\"admin/images/info_set.gif\" width=\"15\" height=\"15\" border=\"0\" align=\"middle\"></a>";
    echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\" width=\"40%\"><p>".$title."</p></td>\n";
    echo "<td><p><input ".$max." type=\"text\" size=\"".$size."\" name=\"".$name."\" value=\"".$value."\">".$help."</p></td>\n</tr>\n";
}

function buildPasswordRow($title, $name, $value="", $size="40", $max_length="") {
    global $config,$bgcount;
    if ($max_length) $max = "maxlength=\"".$max_length."\"";
    echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
    echo "<td><p><input ".$max." type=\"password\" size=\"".$size."\" name=\"".$name."\" value=\"".$value."\"></p></td>\n</tr>\n";
}

function buildStandardRow($title, $value="",$html=0, $info=0 , $info_message="", $info_message_header="") {
    global $config,$bgcount;
    if ($html) $value=htmlspecialchars($value);
    if ($info) $help = "<a href=\"#\" onMouseOver=\"dcc('".$info_message_header."','".$info_message."'); return true;\" onMouseOut=\"nd(); return true;\"><img src=\"admin/images/info_set.gif\" width=\"15\" height=\"15\" border=\"0\" align=\"middle\"></a>";    
    echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$title."</p></td>\n";
    echo "<td><p>".$value." ".$help."</p></td>\n</tr>\n";
}

function buildOneRow($title, $colspan="2", $align="", $html=0, $info=0 , $info_message="", $info_message_header="") {
    global $config,$bgcount;
    if ($html) $value=htmlspecialchars($value);
    if ($align) $align_insert = "align=\"".$align."\"";
    if ($info) $help = "<a href=\"#\" onMouseOver=\"dcc('".$info_message_header."','".$info_message."'); return true;\" onMouseOut=\"nd(); return true;\"><img src=\"admin/images/info_set.gif\" width=\"15\" height=\"15\" border=\"0\" align=\"middle\"></a>";        
    echo "<tr class=\"".switchBgColor()."\">\n<td colspan=\"".$colspan."\" valign=\"top\" ".$align_insert."><p>".$title." ".$help."</p></td>\n</tr>\n";
}
    
function install_user_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    userid int(11) NOT NULL auto_increment,
    username varchar(30) NOT NULL default '',
    userpassword varchar(59) NOT NULL default '',
    useremail varchar(150) NOT NULL default '',
    regdate int(11) NOT NULL default '0',
    lastvisit int(11) NOT NULL default '0',
    usericq varchar(30) NOT NULL default '',
    aim varchar(30) NOT NULL default '',
    yim varchar(30) NOT NULL default '',
    userhp varchar(200) NOT NULL default '',
    interests varchar(250) NOT NULL default '',
    location varchar(255) NOT NULL default '',
    gender int(1) NOT NULL default '0',
    groupid int(7) NOT NULL default '0',
    avatarid int(11) NOT NULL default '0',
    show_email_global int(1) NOT NULL default '0',
    blocked int(1) NOT NULL default '0',
    activation int(10) unsigned NOT NULL default '0',
    canuploadfile int(1) NOT NULL default '0',
    canpostarticle int(1) NOT NULL default '0',
    PRIMARY KEY  (userid),
    KEY username (username)
    )");    
    }
    
function install_mirror_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    `mirror_id` int(11) NOT NULL auto_increment,
    `dlid` int(11) NOT NULL default '0',
    `mirror_url` varchar(250) NOT NULL default '',
    `mirror_text` varchar(250) NOT NULL default '',
    `mirror_date` int(11) NOT NULL default '0',
    PRIMARY KEY  (`mirror_id`)
    )");    
    }   
    
function install_downloads_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    `dlid` int(11) NOT NULL auto_increment,
    `catid` int(11) NOT NULL default '0',
    `dltitle` text NOT NULL,
    `dldesc` text NOT NULL,
    `status` int(11) NOT NULL default '0',
    `dlurl` varchar(255) NOT NULL default '',
    `dl_date` int(11) NOT NULL default '0',
    `dlhits` int(11) NOT NULL default '0',
    `dlvotes` int(11) NOT NULL default '0',
    `hplink` varchar(255) NOT NULL default '0',
    `dlsize` int(11) NOT NULL default '0',
    `dlpoints` int(14) NOT NULL default '0',
    `dlauthor` text NOT NULL,
    `authormail` varchar(255) NOT NULL default '',
    `thumb` varchar(255) NOT NULL default '0',
    `comment_count` int(11) NOT NULL default '0',
    `onlyreg` int(1) NOT NULL default '0',
    `licence_id` int(11) NOT NULL default '0',
    `update_date` int(11) NOT NULL default '0',
    PRIMARY KEY  (`dlid`),
    KEY `catid` (`catid`),
    KEY `dl_date` (`dl_date`),
    KEY `dlhits` (`dlhits`),
    KEY `licence_id` (`licence_id`)
    )");    
    }

function install_set_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    settingid int(11) unsigned NOT NULL auto_increment,
    find_word varchar(100) NOT NULL default '',
    replace_value text NOT NULL,
    PRIMARY KEY  (settingid)
    )");    
    }

function install_licence_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    `licence_id` int(11) NOT NULL auto_increment,
    `licence_title` varchar(250) NOT NULL default '',
    `licence` text NOT NULL,
    `licence_date` int(11) NOT NULL default '0',
    PRIMARY KEY  (`licence_id`)
    )");    
    }
    
function install_cat_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    `catid` int(11) NOT NULL auto_increment,
    `titel` text NOT NULL,
    `cat_desc` text NOT NULL,
    `download_count` int(11) NOT NULL default '0',
    `subcat` int(11) NOT NULL default '0',
    `startorder` varchar(10) NOT NULL default '',
    `catorder` tinyint(6) unsigned NOT NULL default '0',
    `direct_upload` tinyint(1) unsigned NOT NULL default '0',
    `cat_style` tinyint(1) NOT NULL default '0',
    PRIMARY KEY  (`catid`),
    KEY `subcat` (`subcat`)
    )");    
    }   

function install_comment_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    `comid` int(11) NOT NULL auto_increment,
    `userid` int(11) NOT NULL default '0',
    `com_headline` varchar(255) NOT NULL default '',
    `dl_comment` text NOT NULL,
    `com_date` int(11) NOT NULL default '0',
    `com_status` int(11) NOT NULL default '0',
    `dlid` int(11) NOT NULL default '0',
    `user_ip` varchar(16) default NULL,
    `user_comname` varchar(30) NOT NULL default '0',
    `posticon` varchar(255) NOT NULL default '',
    PRIMARY KEY  (`comid`),
    KEY `userid` (`userid`),
    KEY `dlid` (`dlid`)
    )");    
    }

function install_avatar_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    avatarid int(11) NOT NULL auto_increment,
    avatardata char(255) NOT NULL,
    PRIMARY KEY (avatarid)
    )");    
}

function install_daystats_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    `day_id` int(11) NOT NULL auto_increment,
    `day_no` int(11) NOT NULL default '0',
    `year` int(4) NOT NULL default '0',
    `dl_id` int(11) NOT NULL default '0',
    `timestamp` int(11) NOT NULL default '0',
    PRIMARY KEY  (`day_id`)
    )");    
}

function install_monthstats_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    `month_no` int(11) NOT NULL default '0',
    `day_id` int(11) NOT NULL default '0',
    `dl_id` int(11) NOT NULL default '0',
    KEY `day_id` (`day_id`)
    )");    
}

function install_ip_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    `dl_id` int(11) NOT NULL default '0',
    `user_ip` varchar(16) default NULL,
    `vote_time` int(11) NOT NULL default '0',
    KEY `dl_id` (`dl_id`),
    KEY `user_ip` (`user_ip`)
    )");    
}

function install_childlist_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    `catid` int(11) unsigned NOT NULL default '0',
    `childlist` char(250) NOT NULL default '0'
    )");    
}

function install_group_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    groupid int(11) NOT NULL auto_increment,
    title varchar(30) NOT NULL,
    canaccessadmincent tinyint(1) unsigned NOT NULL default '0',
    canaccessofflineengine tinyint(1) unsigned NOT NULL default '0',
    canuseenginesearch tinyint(1) unsigned NOT NULL default '0',
    canmodifyownprofile tinyint(1) unsigned NOT NULL default '0',
    canseemembers tinyint(1) unsigned NOT NULL default '0',
    canpostcomments tinyint(1) unsigned NOT NULL default '0',
    caneditcomments tinyint(1) unsigned NOT NULL default '0',
    candeletecomments tinyint(1) unsigned NOT NULL default '0',
    canpostnews tinyint(1) unsigned NOT NULL default '0',
    canuploadfiles tinyint(1) unsigned NOT NULL default '0',
    canuseadvancedstats tinyint(1) unsigned NOT NULL default '0',
    canseetopstatsfiles tinyint(1) unsigned NOT NULL default '0',
    canaccessregisteredfiles tinyint(1) unsigned NOT NULL default '0',
    canuploadpictures tinyint(1) unsigned NOT NULL default '0',
    canseetopstatspictures tinyint(1) unsigned NOT NULL default '0',
    canaccessregisteredpictures tinyint(1) unsigned NOT NULL default '0',
    canaccessadminpictures tinyint(1) unsigned NOT NULL default '0',
    canpostarticles tinyint(1) unsigned NOT NULL default '0',
    canuploadcontentpictures tinyint(1) unsigned NOT NULL default '0',
    canseeregisteredarticles tinyint(1) NOT NULL default '0',
    canusearticlerating tinyint(1) NOT NULL default '0',
    caneditownarticles tinyint(1) NOT NULL default '0',     
    canseeunpublishedarticles tinyint(1) NOT NULL default '0',  
    canstartnewpoll tinyint(1) unsigned NOT NULL default '0',
    caneditownpoll tinyint(1) unsigned NOT NULL default '0',
    caninsertguestcomment tinyint(1) unsigned NOT NULL default '0',
    canpostnewlink tinyint(1) unsigned NOT NULL default '0',
    caneditownlink tinyint(1) unsigned NOT NULL default '0',
    canseetopstatslinks tinyint(1) unsigned NOT NULL default '0',   
    canseefullpicture tinyint(1) unsigned NOT NULL default '0',     
    maxgroupdownloadspeed int(5) NOT NULL default '0',
    PRIMARY KEY (groupid)
    )");    
}

function install_driver_table() {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE `groups_engine2board` (
    `engine_groupid` int(11) NOT NULL default '0',
    `board_groupid` int(11) NOT NULL default '0'
    )");    
}

function install_style_table($table_name) {
    global $db_sql,$inst_lang;  
    $db_sql->sql_query("CREATE TABLE $table_name (
    style_id int(11) NOT NULL auto_increment,
    style_name varchar(100) NOT NULL default '',
    template_folder varchar(100) NOT NULL default '',
    body_font_face varchar(100) NOT NULL default '',
    body_font_color varchar(7) NOT NULL default '',
    body_font_size varchar(5) NOT NULL default '',
    body_background_color varchar(7) NOT NULL default '',
    row_top_border_color varchar(7) NOT NULL default '',
    row_top_background_color varchar(7) NOT NULL default '',
    row_top_font_color varchar(7) NOT NULL default '',
    row_top_font_size varchar(5) NOT NULL default '',
    breadcrumb_font_color varchar(7) NOT NULL default '',
    breadcrumb_font_size varchar(5) NOT NULL default '',
    breadcrumb_font_color_hover varchar(7) NOT NULL default '',
    content_border_color varchar(7) NOT NULL default '',
    content_background_color_odd varchar(7) NOT NULL default '',
    content_background_color_even varchar(7) NOT NULL default '',
    content_font_color varchar(7) NOT NULL default '',
    content_font_color_hover varchar(7) NOT NULL default '',
    content_font_size varchar(5) NOT NULL default '',
    content_highlight_background_color varchar(7) NOT NULL default '',
    content_highlight_font_color varchar(7) NOT NULL default '',
    content_highlight_font_color_hover varchar(7) NOT NULL default '',
    row_bottom_border_color varchar(7) NOT NULL default '',
    row_bottom_background_color varchar(7) NOT NULL default '',
    row_bottom_font_color varchar(7) NOT NULL default '',
    row_bottom_font_size varchar(5) NOT NULL default '',
    css_file mediumtext NOT NULL,
    PRIMARY KEY  (style_id)
    )");    
}

function config_file($n_dbhost,$n_dbuser,$n_dbpasswort,$n_dbname,$header,$footer,$set_table,$user_table,$group_table,$avat_table,$cat_table,$dlcomment_table,$dl_table,$stats_day_table,$stats_month_table,$dl_iptable,$dl_childtable,$mirror_table,$licence_table,$admin_language,$style_table) {
    global $app_name,$new_version,$eng_type,$_POST;
    
    $config_data = '<?php'."\n";
    $config_data .= '/*'."\n";
    $config_data .= '+--------------------------------------------------------------------------'."\n";
    $config_data .= '|   '.$app_name.''."\n";
    $config_data .= '|   ========================================'."\n";
    $config_data .= '|   by Alex Höntschel'."\n";
    $config_data .= '|   (c) 2002 AlexScriptEngine'."\n";
    $config_data .= '|   http://www.alexscriptengine.de'."\n";
    $config_data .= '|   ========================================'."\n";
    $config_data .= '|   Web: http://www.alexscriptengine.de'."\n";
    $config_data .= '|   Email: info@alexscriptengine.de'."\n";
    $config_data .= '+---------------------------------------------------------------------------'."\n";
    $config_data .= '|'."\n";
    $config_data .= '|   > Beschreibung'."\n";
    $config_data .= '|   > Konfigurationsdatei'."\n";
    $config_data .= '|'."\n";
    $config_data .= '+--------------------------------------------------------------------------'."\n";
    $config_data .= '*/'."\n\n\n";
    $config_data .= '#######################################'."\n";
    $config_data .= '# ab hier, die entsprechenden Daten   #'."\n";
    $config_data .= '# eingeben                            #'."\n";
    $config_data .= '#######################################'."\n";
    $config_data .= '# Please insert the required Data     #'."\n";
    $config_data .= '#######################################'."\n";
    $config_data .= '#// hier den entsprechenden Host'."\n";
    $config_data .= '#// your Hostname'."\n";
    $config_data .= '$hostname = "'.$n_dbhost.'";'."\n";
    $config_data .= '#// hier Deinen Usernamen zur Datenbank'."\n";
    $config_data .= '#// your Username to the Database'."\n";
    $config_data .= '$dbUname = "'.$n_dbuser.'";'."\n";
    $config_data .= '#// hier das Passwort zur Datenbank'."\n";
    $config_data .= '#// your Password to the Database'."\n";
    $config_data .= '$dbPasswort = "'.$n_dbpasswort.'";'."\n";
    $config_data .= '#// Bitte hier den Namen der Datenbank eingeben'."\n";
    $config_data .= '#// The Name of the Database'."\n";
    $config_data .= '$dbName = "'.$n_dbname.'";'."\n\n";
    $config_data .= '#// nachfolgend die $inst_lang[22] bestimmen (1= DEUTSCH; 2= ENGLISCH)'."\n";
    $config_data .= '#// choose the language for the admin area (1= GERMAN; 2= ENGLISH)'."\n";
    $config_data .= '$admin_lang = '.$admin_language.';'."\n\n";
    $config_data .= '#######################################'."\n";
    $config_data .= '# Header und Footer Setup.            #'."\n";
    $config_data .= '# Dies ist nur sinnvoll, wenn Du die  #'."\n";
    $config_data .= '# Engine in einen eigenen Rahmen ein- #'."\n";
    $config_data .= '# binden willst                       #'."\n";
    $config_data .= '#######################################'."\n";
    $config_data .= '# Header and Footer Setup.            #'."\n";
    $config_data .= '# It makes only sense, if you want to #'."\n";
    $config_data .= '# put the script into your own HTML-  #'."\n";
    $config_data .= '# frame.                              #'."\n";
    $config_data .= '#######################################'."\n\n";
    $config_data .= '#// legt die Kopfdatei fest, bitte keine URL, sondern den vollständigen Pfad angeben'."\n";
    $config_data .= '#// defines the file who will put into the head of the engine, please use the complete path'."\n";
    $config_data .= '#// z. B. /usr/local/httpd/htdocs/projekte/header.html'."\n";
    $config_data .= '$own_header = "'.$header.'";'."\n\n";
    $config_data .= '#// legt die Fußdatei fest, bitte keine URL, sondern den vollständigen Pfad angeben'."\n";
    $config_data .= '#// defines the file who will put into the bottom of the engine, please use the complete path'."\n";
    $config_data .= '#// z. B. /usr/local/httpd/htdocs/projekte/footer.html'."\n";
    $config_data .= '$own_footer = "'.$footer.'";'."\n\n";
    $config_data .= '#######################################'."\n";
    $config_data .= '# Tabellen Setup.                     #'."\n";
    $config_data .= '# Bitte nur änder, wenn mehrere       #'."\n";
    $config_data .= '# Engines verwendet werden            #'."\n";
    $config_data .= '#######################################'."\n";
    $config_data .= '# Table Setup.                        # '."\n";
    $config_data .= '# Please do not change the things     #'."\n";
    $config_data .= '# above if you have installed only    #'."\n";
    $config_data .= '# one engine.                         #'."\n";
    $config_data .= '#######################################'."\n\n";
    $config_data .= '// Tabelle für die Einstellungen - Table for settings'."\n";
    $config_data .= '$set_table = "'.$set_table.'";'."\n\n";
    $config_data .= '// Tabelle für die Userdaten - evtl. ändern, falls bereits eine Engine installiert ist. - Table for userdatas'."\n";
    $config_data .= '$user_table = "'.$user_table.'";'."\n\n";
    $config_data .= '// Tabelle für die Kategorien - table for categories'."\n";
    $config_data .= '$cat_table = "'.$cat_table.'";'."\n\n";
    $config_data .= '// Tabelle für die Kommentare - table for comments'."\n";
    $config_data .= '$dlcomment_table = "'.$dlcomment_table.'";'."\n\n";
    $config_data .= '// Tabelle für die Downloads - table to store the files'."\n";
    $config_data .= '$dl_table = "'.$dl_table.'";'."\n\n";
    $config_data .= '// Tabelle für die Avatars - evtl. ändern, falls bereits eine Engine installiert ist. - table for avatars'."\n";
    $config_data .= '$avat_table = "'.$avat_table.'";'."\n\n";
    $config_data .= '// Tabelle für die Usergruppen - evtl. ändern, falls bereits eine Engine installiert ist. - table for usergroups'."\n";
    $config_data .= '$group_table = "'.$group_table.'";'."\n\n";
    $config_data .= '// Tabelle für die Eintragung der Tagesstatistik - table for downloads by day'."\n";
    $config_data .= '$stats_day_table = "'.$stats_day_table.'";'."\n\n";
    $config_data .= '// Tabelle für die Eintragung der mtl. Downloads - table for monthly downloads'."\n";
    $config_data .= '$stats_month_table = "'.$stats_month_table.'";'."\n\n";
    $config_data .= '// Tabelle um die IP\'s fuer die Voting-Sperre zu speichern - table to save ip-addresses'."\n";
    $config_data .= '$dl_iptable = "'.$dl_iptable.'";'."\n\n";
    $config_data .= '// Tabelle um die Kind-Kategorien pro Kategorie aufzuzeigen - table to save childlist of a category'."\n";
    $config_data .= '$dl_childtable = "'.$dl_childtable.'";'."\n\n";
    $config_data .= '// Tabelle für Mirror-Dateien - table for mirror urls'."\n";
    $config_data .= '$mirror_table = "'.$mirror_table.'";'."\n\n";
    $config_data .= '// Tabelle für Lizenzen - table for licences'."\n";
    $config_data .= '$licence_table = "'.$licence_table.'";'."\n\n";  
    $config_data .= '// Tabelle für Style-Sets'."\n";
    $config_data .= '$style_table = "'.$style_table.'";'."\n\n";              
    $config_date .= '#######################################'."\n\n";
    $config_data .= 'define(\'APP_VERSION\', \''.$new_version.'\');'."\n\n";
    $config_data .= 'define(\'ENG_TYPE\', \''.$eng_type.'\');'."\n\n";
    $config_data .= 'define(\'ENGINE_INSTALLED\', true);'."\n\n";
    $config_data .= 'define(\'BOARD_DRIVER\', \''.$_POST['board_driver'].'\');'."\n\n";
    $config_data .= '?>'."\n";
    
    return $config_data;
}   
    
function addslashes_array(&$array) {
    reset($array);
    if(is_array($array)) {    
        foreach ($array as $key => $val) {
            $array[$key] = (is_array($val)) ? addslashes_array($val) : addslashes($val);
        }
        return $array;
    }
}

function editBoardUser($driver,$prefix) {
    global $db_sql,$inst_lang;
    //echo "Treiber: ".$driver."<br>Tabelle-Pr&auml;fix: ".$prefix."<br>";
    switch($driver) {
        case "wbblite":
            $existing = $db_sql->query_array("SELECT count(userid) AS noOfUsers FROM bb".$prefix."_users WHERE userid='2'"); 
            if($existing['noOfUsers']<>1) {
                $db_sql->sql_query("INSERT INTO bb".$prefix."_users (userid,username,groupid) VALUES ('2','".$inst_lang[66]."','5')");
            } else {
                $wbblite = $db_sql->query_array("SELECT * FROM bb".$prefix."_users WHERE userid='2'");
                $db_sql->sql_query("INSERT INTO bb".$prefix."_users 
                                        (username, password, email, userposts, groupid, rankid, title, regdate, lastvisit, lastactivity, usertext, signature, icq, aim, yim, msn, homepage, birthday, avatarid, gender, showemail, admincanemail, usercanemail, invisible, usecookies, styleid, activation, blocked, daysprune, timezoneoffset, startweek, dateformat, timeformat, emailnotify, buddylist, ignorelist, receivepm, emailonpm, pmpopup, umaxposts, showsignatures, showavatars, showimages, nosessionhash, ratingcount, ratingpoints, threadview)
                                    VALUES
                                        ('$wbblite[username]', '$wbblite[password]', '$wbblite[email]', '$wbblite[userposts]', '$wbblite[groupid]', '$wbblite[rankid]', '$wbblite[title]', '$wbblite[regdate]', '$wbblite[lastvisit]', '$wbblite[lastactivity]', '$wbblite[usertext]', '$wbblite[signature]', '$wbblite[icq]', '$wbblite[aim]', '$wbblite[yim]', '$wbblite[msn]', '$wbblite[homepage]', '$wbblite[birthday]', '$wbblite[avatarid]', '$wbblite[gender]', '$wbblite[showemail]', '$wbblite[admincanemail]', '$wbblite[usercanemail]', '$wbblite[invisible]', '$wbblite[usecookies]', '$wbblite[styleid]', '$wbblite[activation]', '$wbblite[blocked]', '$wbblite[daysprune]', '$wbblite[timezoneoffset]', '$wbblite[startweek]', '$wbblite[dateformat]', '$wbblite[timeformat]', '$wbblite[emailnotify]', '$wbblite[buddylist]', '$wbblite[ignorelist]', '$wbblite[receivepm]', '$wbblite[emailonpm]', '$wbblite[pmpopup]', '$wbblite[umaxposts]', '$wbblite[showsignatures]', '$wbblite[showavatars]', '$wbblite[showimages]', '$wbblite[nosessionhash]', '$wbblite[ratingcount]', '$wbblite[ratingpoints]', '$wbblite[threadview]')");
                $new_id = $db_sql->insert_id();
                $db_sql->sql_query("UPDATE bb".$prefix."_avatars SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_events SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_folders SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_moderators SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_posts SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_searchs SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_subscribeboards SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_threads SET starterid='".$new_id."' WHERE starterid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_threads SET lastposterid='".$new_id."' WHERE lastposterid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_userfields SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_votes SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_sessions SET userid='".$new_id."' WHERE userid = '2'");                
                $db_sql->sql_query("UPDATE bb".$prefix."_privatemessage SET senderid='".$new_id."' WHERE senderid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_privatemessage SET recipientid='".$new_id."' WHERE recipientid = '2'");
                $db_sql->sql_query("DELETE FROM bb".$prefix."_users WHERE userid = '2'");
                $db_sql->sql_query("INSERT INTO bb".$prefix."_users (userid,username,groupid) VALUES ('2','".$inst_lang[66]."','5')");
            }        
            break;
        case "wbb1":
            $existing = $db_sql->query_array("SELECT count(userid) AS noOfUsers FROM bb".$prefix."_user_table WHERE userid='2'"); 
            echo "User suchen<br>";
            if($existing['noOfUsers']<>1) {
                $db_sql->sql_query("INSERT INTO bb".$prefix."_user_table (userid,username,groupid) VALUES ('2','".$inst_lang[66]."','8')");
                echo "User 2 einf&uuml;gen<br>";
            } else {
                $wbb1 = $db_sql->query_array("SELECT * FROM bb".$prefix."_user_table WHERE userid='2'");
                $db_sql->sql_query("INSERT INTO bb".$prefix."_user_table 
                                        (username, userpassword, useremail, regemail, userposts, groupid, statusextra, regdate, lastvisit, lastactivity, session_link, signatur, usericq, aim, yim, userhp, age_m, age_d, age_y, avatarid, interests, location, work, gender, usertext, show_email_global, mods_may_email, users_may_email, invisible, hide_signature, hide_userpic, prunedays, umaxposts, bbcode, lastpmpopup, style_set, activation, blocked)
                                    VALUES
                                        ('$wbb1[username]', '$wbb1[userpassword]', '$wbb1[useremail]', '$wbb1[regemail]', '$wbb1[userposts]', '$wbb1[groupid]', '$wbb1[statusextra]', '$wbb1[regdate]', '$wbb1[lastvisit]', '$wbb1[lastactivity]', '$wbb1[session_link]', '$wbb1[signatur]', '$wbb1[usericq]', '$wbb1[aim]', '$wbb1[yim]', '$wbb1[userhp]', '$wbb1[age_m]', '$wbb1[age_d]', '$wbb1[age_y]', '$wbb1[avatarid]', '$wbb1[interests]', '$wbb1[location]', '$wbb1[work]', '$wbb1[gender]', '$wbb1[usertext]', '$wbb1[show_email_global]', '$wbb1[mods_may_email]', '$wbb1[users_may_email]', '$wbb1[invisible]', '$wbb1[hide_signature]', '$wbb1[hide_userpic]', '$wbb1[prunedays]', '$wbb1[umaxposts]', '$wbb1[bbcode]', '$wbb1[lastpmpopup]', '$wbb1[style_set]', '$wbb1[activation]', '$wbb1[blocked]')");
                $new_id = $db_sql->insert_id();
                $db_sql->sql_query("UPDATE bb".$prefix."_posts SET userid = ".$new_id." WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_threads SET authorid = ".$new_id." WHERE authorid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_folders SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_notify SET userid='".$new_id."' WHERE userid = '2'");
                                $db_sql->sql_query("UPDATE bb".$prefix."_object2board SET objectid='".$new_id."' WHERE objectid = '2' AND mod = '1'");
                $db_sql->sql_query("UPDATE bb".$prefix."_object2user SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_object2user SET objectid = '".$new_id."' WHERE objectid = '2' AND (buddylist = 1 OR ignorelist = 1)");
                $db_sql->sql_query("UPDATE bb".$prefix."_pms SET recipientid = '".$new_id."' WHERE recipientid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_pms SET senderid = '".$new_id."' WHERE senderid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_pmsend SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_useronline SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_vote SET userid='".$new_id."' WHERE userid = '2'"); 
                $db_sql->sql_query("DELETE FROM bb".$prefix."_user_table WHERE userid = '2'");
                $db_sql->sql_query("INSERT INTO bb".$prefix."_user_table (userid,username,groupid) VALUES ('2','".$inst_lang[66]."','4')");               
                
            }        
            break;
        case "wbb2":
            $existing = $db_sql->query_array("SELECT count(userid) AS noOfUsers FROM bb".$prefix."_users WHERE userid='2'"); 
            if($existing['noOfUsers']<>1) {
                $db_sql->sql_query("INSERT INTO bb".$prefix."_users (userid,username,groupcombinationid) VALUES ('2','".$inst_lang[66]."','5')");
            } else {
                $wbb2 = $db_sql->query_array("SELECT * FROM bb".$prefix."_users WHERE userid='2'");
                $db_sql->sql_query("INSERT INTO bb".$prefix."_users 
                                        (username, password, sha1_password, email, userposts, groupcombinationid, rankid, title, regdate, lastvisit, lastactivity, usertext, signature, disablesignature, icq, aim, yim, msn, homepage, birthday, avatarid, gender, showemail, admincanemail, usercanemail, invisible, usecookies, styleid, langid, activation, blocked, daysprune, timezoneoffset, startweek, dateformat, timeformat, emailnotify, buddylist, ignorelist, receivepm, emailonpm, pmpopup, umaxposts, showsignatures, showavatars, showimages, nosessionhash, ratingcount, ratingpoints, threadview, useuseraccess, isgroupleader, rankgroupid, useronlinegroupid, allowsigsmilies, allowsightml, allowsigbbcode, allowsigimages, emailonapplication, acpmode, acppersonalmenu, acpmenumarkfirst, acpmenuhidelast)
                                    VALUES
                                        ('$wbb2[username]', '$wbb2[password]', '$wbb2[sha1_password]','$wbb2[email]', '$wbb2[userposts]', '$wbb2[groupcombinationid]','$wbb2[rankid]','$wbb2[title]', '$wbb2[regdate]', '$wbb2[lastvisit]', '$wbb2[lastactivity]','$wbb2[usertext]', '$wbb2[signature]', '$wbb2[disablesignature]','$wbb2[icq]','$wbb2[aim]','$wbb2[yim]','$wbb2[msn]','$wbb2[homepage]','$wbb2[birthday]','$wbb2[avatarid]','$wbb2[gender]', '$wbb2[showemail]', '$wbb2[admincanemail]','$wbb2[usercanemail]','$wbb2[invisible]', '$wbb2[usecookies]','$wbb2[styleid]','$wbb2[langid]', '$wbb2[activation]','$wbb2[blocked]', '$wbb2[daysprune]', '$wbb2[timezoneoffset]','$wbb2[startweek]', '$wbb2[dateformat]', '$wbb2[timeformat]', '$wbb2[emailnotify]','$wbb2[buddylist]', '$wbb2[ignorelist]','$wbb2[receivepm]','$wbb2[emailonpm]','$wbb2[pmpopup]', '$wbb2[umaxposts]', '$wbb2[showsignatures]','$wbb2[showavatars]','$wbb2[showimages]', '$wbb2[nosessionhash]','$wbb2[ratingcount]', '$wbb2[ratingpoints]','$wbb2[threadview]', '$wbb2[useuseraccess]','$wbb2[isgroupleader]','$wbb2[rankgroupid]', '$wbb2[useronlinegroupid]','$wbb2[allowsigsmilies]','$wbb2[allowsightml]', '$wbb2[allowsigbbcode]', '$wbb2[allowsigimages]', '$wbb2[emailonapplication]','$wbb2[acpmode]', '$wbb2[acppersonalmenu]', '$wbb2[acpmenumarkfirst]', '$wbb2[acpmenuhidelast]')");
                          
                $new_id = $db_sql->insert_id();
                $db_sql->sql_query("UPDATE bb".$prefix."_avatars SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_events SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_folders SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_moderators SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_posts SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_searchs SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_subscribeboards SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_subscribethreads SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_threads SET starterid='".$new_id."' WHERE starterid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_threads SET lastposterid='".$new_id."' WHERE lastposterid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_userfields SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_votes SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_sessions SET userid='".$new_id."' WHERE userid = '2'");                
                $db_sql->sql_query("UPDATE bb".$prefix."_privatemessage SET senderid='".$new_id."' WHERE senderid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_privatemessage SET recipientid='".$new_id."' WHERE recipientid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_acpmenuitemgroupscount SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_acpmenuitemscount SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_applications SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_threadvisit SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_user2groups SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE bb".$prefix."_boardvisit SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("DELETE FROM bb".$prefix."_users WHERE userid = '2'");
                $db_sql->sql_query("INSERT INTO bb".$prefix."_users (userid,username,groupcombinationid) VALUES ('2','".$inst_lang[66]."','5')");
            }                
            break;
        case "vbb2":
            $existing = $db_sql->query_array("SELECT count(userid) AS noOfUsers FROM user WHERE userid='2'"); 
            if($existing['noOfUsers']<>1) {
                $db_sql->sql_query("INSERT INTO user (userid,usergroupid,username) VALUES ('2','1','".$inst_lang[66]."')");
            } else {
                $vbb2 = $db_sql->query_array("SELECT * FROM user WHERE userid='2'");
                $db_sql->sql_query("INSERT INTO user 
                                        (usergroupid, username, password, email, styleid, parentemail, coppauser, homepage, icq, aim, yahoo, signature, adminemail, showemail, invisible, usertitle, customtitle, joindate, cookieuser, daysprune, lastvisit, lastactivity, lastpost, posts, timezoneoffset, emailnotification, buddylist, ignorelist, pmfolders, receivepm, emailonpm, pmpopup, avatarid, options, birthday, maxposts, startofweek, ipaddress, referrerid, nosessionhash, inforum)
                                    VALUES
                                        ('$vbb2[usergroupid]', '$vbb2[username]', '$vbb2[password]', '$vbb2[email]', '$vbb2[styleid]', '$vbb2[parentemail]', '$vbb2[coppauser]', '$vbb2[homepage]', '$vbb2[icq]', '$vbb2[aim]', '$vbb2[yahoo]', '$vbb2[signature]', '$vbb2[adminemail]', '$vbb2[showemail]', '$vbb2[invisible]', '$vbb2[usertitle]', '$vbb2[customtitle]', '$vbb2[joindate]', '$vbb2[cookieuser]', '$vbb2[daysprune]', '$vbb2[lastvisit]', '$vbb2[lastactivity]', '$vbb2[lastpost]', '$vbb2[posts]', '$vbb2[timezoneoffset]', '$vbb2[emailnotification]', '$vbb2[buddylist]', '$vbb2[ignorelist]', '$vbb2[pmfolders]', '$vbb2[receivepm]', '$vbb2[emailonpm]', '$vbb2[pmpopup]', '$vbb2[avatarid]', '$vbb2[options]', '$vbb2[birthday]', '$vbb2[maxposts]', '$vbb2[startofweek]', '$vbb2[ipaddress]', '$vbb2[referrerid]', '$vbb2[nosessionhash]', '$vbb2[inforum]')");
                          
                $new_id = $db_sql->insert_id();
                $db_sql->sql_query("UPDATE post SET userid='".$new_id."' WHERE userid='2'");
                $db_sql->sql_query("UPDATE userfield SET userid='".$new_id."' WHERE userid='2'");
                $db_sql->sql_query("UPDATE access SET userid='".$new_id."' WHERE userid='2'");
                $db_sql->sql_query("UPDATE calendar_events SET userid='".$new_id."' WHERE userid='2'");
                $db_sql->sql_query("UPDATE customavatar SET userid='".$new_id."' WHERE userid='2'");
                $db_sql->sql_query("UPDATE moderator SET userid='".$new_id."' WHERE userid='2'");
                $db_sql->sql_query("UPDATE privatemessage SET userid='".$new_id."' WHERE userid='2'");
                $db_sql->sql_query("UPDATE subscribeforum SET userid='".$new_id."' WHERE userid='2'");
                $db_sql->sql_query("UPDATE subscribethread SET userid='".$new_id."' WHERE userid='2'");
                $db_sql->sql_query("UPDATE session SET userid='".$new_id."' WHERE userid='2'");
                $db_sql->sql_query("DELETE FROM user WHERE userid = '2'");
                $db_sql->sql_query("INSERT INTO user (userid,usergroupid,username) VALUES ('2','1','".$inst_lang[66]."')");
            }                   
            break;
        case "vbb3":
            $existing = $db_sql->query_array("SELECT count(userid) AS noOfUsers FROM ".$prefix."user WHERE userid='2'"); 
            if($existing['noOfUsers']<>1) {
                $db_sql->sql_query("INSERT INTO ".$prefix."user (userid,usergroupid,username) VALUES ('2','1','".$inst_lang[66]."')");
            } else {
                $vbb2 = $db_sql->query_array("SELECT * FROM ".$prefix."user WHERE userid='2'");
                $db_sql->sql_query("INSERT INTO ".$prefix."user 
                                        (usergroupid, membergroupids, displaygroupid, username, password, passworddate, email, styleid, parentemail, homepage, icq, aim, yahoo, showvbcode, usertitle, customtitle, joindate, daysprune, lastvisit, lastactivity, lastpost, posts, reputation, reputationlevelid, timezoneoffset, pmpopup, avatarid, avatarrevision, options, birthday, maxposts, startofweek, ipaddress, referrerid, languageid, msn, emailstamp, threadedmode, autosubscribe, pmtotal, pmunread, salt)
                                    VALUES
                                        ('$vbb3[usergroupid]', '$vbb3[membergroupids]', '$vbb3[displaygroupid]', '$vbb3[username]', '$vbb3[password]', '$vbb3[passworddate]', '$vbb3[email]', '$vbb3[styleid]', '$vbb3[parentemail]', '$vbb3[homepage]', '$vbb3[icq]', '$vbb3[aim]', '$vbb3[yahoo]', '$vbb3[showvbcode]', '$vbb3[usertitle]', '$vbb3[customtitle]', '$vbb3[joindate]', '$vbb3[daysprune]', '$vbb3[lastvisit]', '$vbb3[lastactivity]', '$vbb3[lastpost]', '$vbb3[posts]', '$vbb3[reputation]', '$vbb3[reputationlevelid]', '$vbb3[timezoneoffset]', '$vbb3[pmpopup]', '$vbb3[avatarid]', '$vbb3[avatarrevision]', '$vbb3[options]', '$vbb3[birthday]', '$vbb3[maxposts]', '$vbb3[startofweek]', '$vbb3[ipaddress]', '$vbb3[referrerid]', '$vbb3[languageid]', '$vbb3[msn]', '$vbb3[emailstamp]', '$vbb3[threadedmode]', '$vbb3[autosubscribe]', '$vbb3[pmtotal]', '$vbb3[pmunread]', '$vbb3[salt]')");
                          
                $new_id = $db_sql->insert_id();
                $db_sql->sql_query("UPDATE ".$prefix."post SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."usernote SET userid='".$new_id."' WHERE posterid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."usernote SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."userfield SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."usertextfield SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."access SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."event SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."customavatar SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."customprofilepic SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."moderator SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."subscribeforum SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."subscribethread SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."subscriptionlog SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."session SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."userban SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."pmreceipt SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("UPDATE ".$prefix."pm SET userid='".$new_id."' WHERE userid = '2'");
                $db_sql->sql_query("DELETE FROM ".$prefix."user WHERE userid = '2'");
                $db_sql->sql_query("INSERT INTO ".$prefix."user (userid,usergroupid,username) VALUES ('2','1','".$inst_lang[66]."')");
            }                  
            break;
        case "phpbb2":
            $existing = $db_sql->query_array("SELECT count(user_id) AS noOfUsers FROM ".$prefix."users WHERE user_id='2'"); 
            if($existing['noOfUsers']<>1) {
                $db_sql->sql_query("INSERT INTO ".$prefix."users (user_id,username) VALUES ('2','".$inst_lang[66]."')");
                $db_sql->sql_query("INSERT INTO ".$prefix."user_group (group_id,user_id,user_pending) VALUES ('1','2','0')");
            } else {
                $phpbb2 = $db_sql->query_array("SELECT * FROM ".$prefix."users WHERE user_id='2'");
                $max = $db_sql->query_array("SELECT MAX(user_id) AS total FROM ".$prefix."users");
                $user_id_new = $max['total'] + 1;
                $db_sql->sql_query("INSERT INTO ".$prefix."users 
                                        (user_id,user_active, username, user_password, user_session_time, user_session_page, user_lastvisit, user_regdate, user_level, user_posts, user_timezone, user_style, user_lang, user_dateformat, user_new_privmsg, user_unread_privmsg, user_last_privmsg, user_emailtime, user_viewemail, user_attachsig, user_allowhtml, user_allowbbcode, user_allowsmile, user_allowavatar, user_allow_pm, user_allow_viewonline, user_notify, user_notify_pm, user_popup_pm, user_rank, user_avatar, user_avatar_type, user_email, user_icq, user_website, user_from, user_sig, user_sig_bbcode_uid, user_aim, user_yim, user_msnm, user_occ, user_interests, user_actkey, user_newpasswd)
                                    VALUES
                                        ('".$user_id_new."', '".$phpbb2['user_active']."', '".$phpbb2['username']."', '".$phpbb2['user_password']."', '".$phpbb2['user_session_time']."', '".$phpbb2['user_session_page']."', '".$phpbb2['user_lastvisit']."', '".$phpbb2['user_regdate']."', '".$phpbb2['user_level']."', '".$phpbb2['user_posts']."', '".$phpbb2['user_timezone']."', '".$phpbb2['user_style']."', '".$phpbb2['user_lang']."', '".$phpbb2['user_dateformat']."', '".$phpbb2['user_new_privmsg']."', '".$phpbb2['user_unread_privmsg']."', '".$phpbb2['user_last_privmsg']."', '".$phpbb2['user_emailtime']."', '".$phpbb2['user_viewemail']."', '".$phpbb2['user_attachsig']."', '".$phpbb2['user_allowhtml']."', '".$phpbb2['user_allowbbcode']."', '".$phpbb2['user_allowsmile']."', '".$phpbb2['user_allowavatar']."', '".$phpbb2['user_allow_pm']."', '".$phpbb2['user_allow_viewonline']."', '".$phpbb2['user_notify']."', '".$phpbb2['user_notify_pm']."', '".$phpbb2['user_popup_pm']."', '".$phpbb2['user_rank']."', '".$phpbb2['user_avatar']."', '".$phpbb2['user_avatar_type']."', '".$phpbb2['user_email']."', '".$phpbb2['user_icq']."', '".$phpbb2['user_website']."', '".$phpbb2['user_from']."', '".$phpbb2['user_sig']."', '".$phpbb2['user_sig_bbcode_uid']."', '".$phpbb2['user_aim']."', '".$phpbb2['user_yim']."', '".$phpbb2['user_msnm']."', '".$phpbb2['user_occ']."', '".$phpbb2['user_interests']."', '".$phpbb2['user_actkey']."', '".$phpbb2['user_newpasswd']."')");
                $new_id = $user_id_new;
                $db_sql->sql_query("UPDATE ".$prefix."topics SET topic_poster='".$new_id."' WHERE topic_poster='2'");
                $db_sql->sql_query("UPDATE ".$prefix."vote_voters SET vote_user_id='".$new_id."' WHERE vote_user_id='2'");
                $db_sql->sql_query("UPDATE ".$prefix."groups SET group_moderator='".$new_id."' WHERE group_moderator='2'");
                $db_sql->sql_query("UPDATE ".$prefix."user_group SET user_id='".$new_id."' WHERE user_id='2'");
                $db_sql->sql_query("UPDATE ".$prefix."topics_watch SET user_id='".$new_id."' WHERE user_id='2'");
                $db_sql->sql_query("UPDATE ".$prefix."banlist SET ban_userid='".$new_id."' WHERE ban_userid='2'");
                $db_sql->sql_query("UPDATE ".$prefix."privmsgs SET privmsgs_from_userid='".$new_id."' WHERE privmsgs_from_userid='2'");
                $db_sql->sql_query("UPDATE ".$prefix."privmsgs SET privmsgs_to_userid='".$new_id."' WHERE privmsgs_to_userid='2'");
                $db_sql->sql_query("DELETE FROM ".$prefix."users WHERE user_id = '2'");
                $db_sql->sql_query("INSERT INTO ".$prefix."users (user_id,username) VALUES ('2','".$inst_lang[66]."')");
                $db_sql->sql_query("INSERT INTO ".$prefix."user_group (group_id,user_id,user_pending) VALUES ('1','2','0')");
                $db_sql->sql_query("UPDATE ".$prefix."posts SET poster_id='".$new_id."' WHERE poster_id='2'");
            }            
            break;
    }
}
 ?>