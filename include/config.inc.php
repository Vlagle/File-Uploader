<?php
/*
+--------------------------------------------------------------------------
|   Alex Download Engine
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
|   > Konfigurationsdatei
|
|   > Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|   > bedingungen (read-me.html) für weitere Informationen.
|   >-----------------------------------------------------------------
|   > This script is NOT freeware! Please read the Copyright Notice
|   > (read-me_eng.html) for further information. 
|
|   > $Id: config.inc.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/


#######################################
# ab hier, die entsprechenden Daten   #
# eingeben                            #
#######################################
# Please insert the required Data     #
#######################################

#// hier den entsprechenden Host
#// your Hostname
$hostname = "localhost";
#// hier Deinen Usernamen zur Datenbank
#// your Username to the Database
$dbUname = "root";
#// hier das Passwort zur Datenbank
#// your Password to the Database
$dbPasswort = "";
#// Bitte hier den Namen der Datenbank eingeben
#// The Name of the Database
$dbName = "engine_dl";


#// nachfolgend die Sprache des Admincenters bestimmen (1= DEUTSCH; 2= ENGLISCH)
#// choose the language for the admin area (1= GERMAN; 2= ENGLISH)
$admin_lang = 1;

#######################################
# Header und Footer Setup.            #
# Dies ist nur sinnvoll, wenn Du die  #
# Engine in einen eigenen Rahmen ein- #
# binden willst                       #
#######################################
# Header and Footer Setup.            #
# It makes only sense, if you want to #
# put the script into your own HTML-  #
# frame.                              #
#######################################

#// legt die Kopfdatei fest, bitte keine URL, sondern den vollständigen Pfad angeben
#// defines the file who will put into the head of the engine, please use the complete path
#// z. B. /usr/local/httpd/htdocs/projekte/header.html
$own_header = "";

#// legt die Fußdatei fest, bitte keine URL, sondern den vollständigen Pfad angeben
#// defines the file who will put into the bottom of the engine, please use the complete path
#// z. B. /usr/local/httpd/htdocs/projekte/footer.html
$own_footer = "";

#######################################
# Tabellen Setup.                     #
# Bitte nur änder, wenn mehrere       #
# Engines verwendet werden            #
#######################################
# Table Setup.                        # 
# Please do not change the things     #
# above if you have installed only    #
# one engine.                         #
#######################################

// Tabelle für die Einstellungen - Table for settings
$set_table = "dl1_config";

// Tabelle für die Userdaten - evtl. ändern, falls bereits eine Engine installiert ist. - Table for userdatas
$user_table = "dl1_user";

// Tabelle für die Kategorien - table for categories
$cat_table = "dl1_cat";

// Tabelle für die Kommentare - table for comments
$dlcomment_table = "dl1_commments";

// Tabelle für die Downloads - table to store the files
$dl_table = "dl1_downloads";

// Tabelle für die Avatars - evtl. ändern, falls bereits eine Engine installiert ist. - table for avatars
$avat_table = "dl1_avatar";

// Tabelle für die Usergruppen - evtl. ändern, falls bereits eine Engine installiert ist. - table for usergroups
$group_table = "dl1_groups";

// Tabelle für die Eintragung der Tagesstatistik - table for downloads by day
$stats_day_table = "dl1_stats_day";

// Tabelle für die Eintragung der mtl. Downloads - table for monthly downloads
$stats_month_table = "dl1_stats_month";

// Tabelle um die IP's fuer die Voting-Sperre zu speichern - table to save ip-addresses
$dl_iptable = "dl1_iptable";

// Tabelle um die Kind-Kategorien pro Kategorie aufzuzeigen - table to save childlist of a category
$dl_childtable = "dl1_childlist";

// Tabelle für Mirror-Dateien - table for mirror urls
$mirror_table = "dl1_mirror";

// Tabelle für Lizenzen - table for licences
$licence_table = "dl1_licence";

$style_table = "dl1_style";

define('APP_VERSION', '1.4.2');

define('ENG_TYPE', 'dl');

define('BOARD_DRIVER', 'default');

// Kommentarzeichen entfernen, wenn diese Datei manuell eingestellt wird
//define('ENGINE_INSTALLED', true);
?>
