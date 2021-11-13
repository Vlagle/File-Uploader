<html>
<head>
	<title>Pfad Hilfe</title>
<style>
.standard {font-family : Verdana,Arial,sans-serif; font-size : 11px; color : #4665B5;}
A.standard_link, A.standard_link:LINK, A.standard_link:VISITED, A.standard_link:ACTIVE {font-family : Verdana,Arial,sans-serif; font-size : 11px; text-decoration : underline; color : #4665B5;}
A.standard_link:HOVER {font-family : Verdana,Arial,sans-serif; font-size : 10px; text-decoration : none; color : {hovercol};}
</style>	
</head>
<body bgcolor="#F4F7FE">

<?php
echo "<p class=\"standard\">Dieses Script muﬂ im Hauptverzeichnis der zu installierenden Engine kopiert werden (das Verzeichnis, in dem auch der Installer liegt)</p>";
echo "<p class=\"standard\">in der dlinfo.php - Datei sollte der folgende Pfad bei \$path2dl gesetzt sein:<br><b>".dirname($_SERVER['SCRIPT_FILENAME'])."</b></p>";
echo "<p class=\"standard\"><br><br>Achtung, dieses Hilfescript sollte erst ab PHP 4.1.0 verwendet werden!</p>"

?>
</body>
</html>