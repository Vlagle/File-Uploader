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
|   > Artikelverwaltung Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: frameset.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","frameset.php");

include_once('adminfunc.inc.php');
$auth->checkEnginePerm("canaccessadmincent");

function makeTreeLink($catid,$subcat,$item_name="") {
	global $cat_table,$db_sql,$config,$sess;
	$result2 = $db_sql->sql_query("SELECT * FROM $cat_table WHERE subcat='$subcat' ORDER BY subcat,catorder,catid");
	while ($dl_cat = $db_sql->fetch_array($result2)) {
        $dl_cat = stripslashes_array($dl_cat);
		if($catid == 0) {
            $folder_name = ereg_replace("[^a-zA-Z]","",$dl_cat['titel']);
			
			$cat_link .= "var ".$folder_name." = new WebFXTreeItem('".addslashes($dl_cat['titel'])."','".$sess->adminUrl("prog.php?step=choose&catid=".$dl_cat['catid'])."','mainContent')\n";
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

$message = '';

if($step == 'catframe') {
	if($initialize_category) {
		$contentFrameTarget = $sess->adminUrl("prog.php?step=choose&catid=".$initialize_category);
	} else {
		$contentFrameTarget = $sess->adminUrl("frameset.php?step=mainframe_categories&hide=1");
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>Download Engine</title>
    <meta content="text/html; charset=windows-1252" http-equiv="Content-Type">
    <link rel="stylesheet" href="<?php echo $config['engine_mainurl']; ?>/admin/acstyle.css">
	
    <script type="text/javascript">
    <!--
    function Frame() {
        if (FrameStat == "Show") {
            FrameSize = "00,*";
            FrameStat = "Hide";
        }
        else {
            FrameSize = "200,*";
            FrameStat = "Show";
        }
        parent.frameset1.cols = FrameSize;
    }    
    //-->
    </script>  
</head>
<frameset id="contentFrame" cols="200,6,*" border="0" frameborder="0">

    <frameset rows="20,*" frameborder="0">
		<!-- Infoleiste über Kategorien -->
        <frame src="<?php echo $sess->adminUrl("frameset.php?step=sideframe_head"); ?>" name="left_top" border="0" marginwidth="0" marginheight="0" scrolling="no" />
		<!-- Anzeige der Kategorien -->
        <frame src="<?php echo $sess->adminUrl("frameset.php?step=sideframe_categories&hide=1"); ?>" name="left_bottom" border="0" scrolling="yes" />
    </frameset>
	<!-- Füllbereich für Absandshalter -->
    <frame src="<?php echo $config['engine_mainurl']; ?>/admin/images/filler.html" border="0" scrolling="no" noresize="noresize" />
	<!-- Inhaltsframe für normalen Content -->
    <frame src="<?php echo $contentFrameTarget; ?>" name="mainContent" border="0" scrolling="yes" noresize="noresize" />
</frameset><noframes></noframes>

<body>
</body>
</html>


<?php
    exit;
}

if($step == 'sideframe_head') {
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
    <html>
    <head>
        <title>Download Engine</title>
        <meta content="text/html; charset=windows-1252" http-equiv="Content-Type">
        <link rel="stylesheet" href="<?php echo $config['engine_mainurl']; ?>/admin/acstyle.css">
    </head>
    
    <body bgcolor="#4665B5" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad=parent.FrameStat="Show">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="50"><a href="JavaScript:parent.Frame()"><img src="images/toggle_frame2.gif" width="50" height="18" border="0" alt="<?php echo $a_lang['show_hide_menu']; ?>"></a></td>
        <td align="left" class="table_footer"><?php echo $a_lang['frameset_categories']; ?></td>
    </tr>
    </table>
    </body>
    </html>    
    <?php
    exit;
}

if($step == 'sideframe_categories') {
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
    <html>
    <head>
        <title>Download-Engine</title>
        <meta content="text/html; charset=windows-1252" http-equiv="Content-Type">
		<link type="text/css" rel="stylesheet" href="includes/tree/xtree.css">
        <!-- <link rel="stylesheet" href="<?php echo $config['engine_mainurl']; ?>/admin/acstyle.css"> -->
		<script src="includes/tree/xtree.js"></script>
		<script language="JavaScript">
		<!--
		function TreeItemTargetC()
		{
			WebFXTreeItem.apply( this, arguments );
			this.target = "mainContent";
		}
		TreeItemTargetC.prototype = new WebFXTreeItem;
		
		//-->
		</script>
		<style type="text/css">
			BODY {
				font-family : Verdana, Arial, sans-serif;
			  	SCROLLBAR-BASE-COLOR: #4665B5;
			  	SCROLLBAR-ARROW-COLOR: White;
				font-size : 11px;
			}		
		</style>
    </head>
    
    <body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad=parent.FrameStat="Show">
	<p align="center" class="webfx-tree-item">
	<a class="webfx-tree-item" href="javascript:void(0);" onclick="tree.expandAll();"><?php echo $a_lang['show_all']; ?></a> | 
	<a class="webfx-tree-item" href="javascript:void(0);" onclick="tree.collapseAll();"><?php echo $a_lang['hide_all']; ?></a>
	</p>
	<br>
	<table width="100%" cellspacing="2" cellpadding="2" border="0">
	<tr>
    <td nowrap>
	
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
	</td>
	</tr>
	</table>	
    </body>
    </html>    
    <?php
    exit;
    
}

if(!$hide) $hide="";
buildAdminHeader($head,$hide);	

if ($message != '') buildMessageRow($message);

if($step == 'sideframe_categories') {
    echo makeCategorieList(0,0);
}

if($step == 'mainframe_categories') {
    echo "<br><div align=\"center\">".$a_lang['choose_a_category']."</div>";
}


buildAdminFooter();

?>