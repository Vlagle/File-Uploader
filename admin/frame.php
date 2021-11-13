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
|   > Frame-Site Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: frame.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","frame.php");

include_once('adminfunc.inc.php');
$auth->checkEnginePerm("canaccessadmincent");
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>DownloadEngine Admin Center</title>
</head>

<frameset framespacing="0" border="0" frameborder="no" rows="35,*">
  <frame name="Banner" scrolling="no" noresize src="<?php echo $sess->adminUrl("head.php") ?>" marginwidth="0" marginheight="0">
  <frameset id=frameset1 cols="200,*" border="0" frameborder="no" framespacing="0">
    <frame name="menue" marginwidth="0" marginheight="0" scrolling="yes" noresize src="<?php echo $sess->adminUrl("navi.php") ?>">
    <frame name="main" src="<?php echo $sess->adminUrl("main.php") ?>" scrolling="auto" frameborder="no">
  </frameset>
  <noframes>
  <body>

  <p>Diese Seite verwendet Frames. Frames werden von Ihrem Browser aber nicht 
  unterstützt.</p>

  </body>
  </noframes>
</frameset>

</html>