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
|   > Tree-Auflistung AdminCenter
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: tree.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","tree.php");

include_once('adminfunc.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");

function makeTreeLink($catid,$subcat,$item_name="") {
    global $cat_table,$db_sql,$config;
    $result2 = mysql_query("SELECT * FROM $cat_table WHERE subcat='$subcat'");
    while ($dl_cat = mysql_fetch_array($result2)) {
                
        if($catid == 0) {
            $folder_name = str_replace(" ","",$dl_cat['titel']);
            $folder_name = str_replace(".","",$folder_name);
            $folder_name = str_replace(",","",$folder_name);
            $folder_name = str_replace("-","",$folder_name);
            $folder_name = str_replace("/","",$folder_name);
            
            $folder_name = str_replace("1","a",$folder_name);
            $folder_name = str_replace("2","b",$folder_name);
            $folder_name = str_replace("3","c",$folder_name);
            $folder_name = str_replace("4","d",$folder_name);
            $folder_name = str_replace("5","e",$folder_name);
            $folder_name = str_replace("6","f",$folder_name);
            $folder_name = str_replace("7","g",$folder_name);
            $folder_name = str_replace("8","h",$folder_name);
            $folder_name = str_replace("9","i",$folder_name);
            $folder_name = str_replace("0","j",$folder_name);
            
            $folder_name = trim($folder_name);

            $cat_link .= "var ".$folder_name." = new WebFXTreeItem('".$dl_cat['titel']."','".$config['dlscripturl']."/tree.php?item=".$dl_cat['catid']."')\n";
            if(!$item_name) {
                $cat_link .= "tree.add(".$folder_name.");\n";
            } else {
                $cat_link .= $item_name.".add(".$folder_name.");\n";
            }
        }		
				
        $newcat = $dl_cat['catid'];
        $cat_link .= makeTreeLink($catid,$newcat,$folder_name);
    }
		
    return $cat_link;
}		

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>

<title></title>
<!-- The xtree script file -->
<script src="includes/tree/xtree.js"></script>

<!-- Modify this file to change the way the tree looks -->
<link type="text/css" rel="stylesheet" href="includes/tree/xtree.css">

<style>
	body { background: white; color: black; }
	input { width: 120px; }
    
.button {
  font-family : Verdana, Arial, sans-serif;
  FONT-SIZE: 10px;
  FONT-WEIGHT: bold;
  //padding: 2px 2px 2px 2px;
  COLOR: #000000;
  text-decoration: none;
}    
</style>

</head>
<body>
<p>
	<a href="javascript:void(0);" onclick="tree.expandAll();">Alle zeigen</a>
	<a href="javascript:void(0);" onclick="tree.collapseAll();">Alle ausblenden</a>
</p>

<script type="text/javascript">
<!--
var tree = new WebFXTree('Download-Engine');
/* Change the behavior of the tree */
tree.setBehavior('explorer');
/* Add tree item to tree */

<?php
echo makeTreeLink(0,0);
?>
document.write(tree);
-->
</script>


