<?php

define("IN_ADMIN_CENTER", true);
include_once("../../../../lib.inc.php");
include '../config/spaw_control.config.php';
include $spaw_root.'class/lang.class.php';

function buildArtLink($catid,$artid,$limiter,$subcat) {
    global $art_table_new,$db_sql,$art_cache,$config;
    
    
    if ( !isset($art_cache) ) {
        $result2 = mysql_query("SELECT * FROM $art_table_new WHERE subart='0' ORDER BY catid,artid,subart");
        while ($nartcache = mysql_fetch_array($result2)) {
            $nartcache = stripslashes_array($nartcache);
            $art_cache["$nartcache[catid]"]["$nartcache[artid]"]["$nartcache[subart]"] = $nartcache;
        }
    }
		
    $link_count = 1;
    while ( list($key1,$val1) = @each($art_cache["$catid"]) ) {
        while ( list($key2,$node) = each($val1) ) {
            $no = count($art_cache["$catid"]);
            $jumpcatid = $node['artid'];
            if($subcat == 0) {
                $limit_def = "&nbsp;&nbsp;";
            } else {
                $limit_def = "&nbsp;&nbsp;&nbsp;";
            }
            
            if($node['published'] != 1) {
                $article_title = "".$node['title']." *";
            } else {
                $article_title = $node['title'];
            }
            
            $path = $config['artscripturl']."/article.php?article=$jumpcatid";
            $cat_link .= "<option value=\"".$path."\">".$limiter."&nbsp;Artikel: ".$article_title."</option>";
            $link_count++;
        }
    }
					
    return $cat_link;
}		

function makeCategorieList($catid,$subcat,$limiter,$depth=1) {
    global $artcat_table,$db_sql,$cat_cache,$current_cat,$sess;
    
    if ( !isset($cat_cache) ) {
        $result2 = $db_sql->sql_query("SELECT catid,subcat,catorder,titel FROM $artcat_table ORDER BY subcat,catorder,catid");
        while ($ncatcache = $db_sql->fetch_array($result2)) {
            $ncatcache = stripslashes_array($ncatcache);
            $cat_cache["$ncatcache[subcat]"]["$ncatcache[catorder]"]["$ncatcache[catid]"] = $ncatcache;
        }
    }
    
    while ( list($key1,$val1) = @each($cat_cache["$catid"]) ) {
        while ( list($key2,$node) = each($val1) ) {					
            $jumpcatid = $node['catid'];
			$path = $config['artscripturl']."/index.php?article=$jumpcatid";
            $jumpcattitle = "<option value=\"".$path."\">".$limiter."Kategorie: ".$node[titel]."</option>";
            if ($current_cat == $jumpcatid) {
                $optionselected='selected';
            } else {
                $optionselected='';
            }	            
            $cat_link .= $jumpcattitle;
			$cat_link .= buildArtLink($jumpcatid,0,$limiter,$node['subcat']);
            $cat_link .= makeCategorieList($jumpcatid,$jumpcatid,$limiter."&nbsp;&nbsp;&nbsp;",$depth+1);
        }
    }
    
    return $cat_link;
}	

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD W3 HTML 3.2//EN">
<html id=dlgImage style="width: 40.1em; height: 18em">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="MSThemeCompatible" content="Yes">
    <title>Insert Link</title>
    <style type="text/css">
        html, body, button, div, input, select, fieldset { font-family: MS Shell Dlg; font-size: 8pt; position: absolute; };
    </style>
    <SCRIPT language="javascript">

        function Set() {

            link    = document.all.txtextern.value;
            ziel    = document.all.txtziel.value;
            /*cla     = document.all.txtstyle.value;*/
            tmail   = document.all.txtmail.checked;
            /*img     = document.all.selectimg.value;*/

            if ( tmail == true ) {
                hr = "mailto:" + link;
            } else {
                hr = "";
            }

            /*if ( cla != 0 ) {
                cla = 'class="' + cla + '"';
            } else {
                cla = "";
            }*/

            /*if ( img != 0 ){
               img = '<img src="' + img + '" border="0" alt=""></a>';
            } else {
               img = "";
            }*/
            
            if ( hr != 0 ) {
                link = hr;
            }

            if ( hr != 0 ) {            
                targ = "";
            } else {
                targ = " target=\"" + ziel + "\"";
            }

            ret = "<a href=\"" + link + "\"" + targ + ">";

            if ( link != '' ) {
                window.returnValue = ret;       // set return value
            } else {
                window.returnValue = 'none';        // set return value
            }

            window.close();                     // close dialog

        }

        function wechsel(pos) {
            document.all.txtextern.value = pos.value;
        }


    </SCRIPT>
</head>

<body id="bdy" style="background: threedface; color: windowtext;" scroll=no>
<div id=divconName style="left: 0.98em; top: 1.2168em; width: 7em; height: 1.2168em; ">Interner Link:</div>
<?php
echo "<select ID=\"selecttxtName\" SIZE=1 style=\"left: 8.54em; top: 1.0647em; width: 21.5em;height: 2.1294em;\" onchange=\"wechsel(this)\" onClick=\"wechsel(this)\">";
echo "<option value=\"\" selected>Bitte w&auml;hlen</option>";

if ($a_link_intern_value != 0) {
    echo "<option value=0>Kein Link</option>";
} else {
    echo "<option value=0 selected>Kein Link</option>";
}
//$cat_link = makeLinkView(0,0,"",0);
$cat_link = makeCategorieList(0,0);
echo $cat_link;
echo "</SELECT>";

?>
<DIV id=divFileName style="left: 0.98em; top: 4.2168em; width: 7em; height: 1.2168em; ">Externer Link:</DIV>
<INPUT ID="txtextern"  type="text" style="left: 8.54em; top: 4.0647em; width: 21.5em;height: 2.1294em; " tabIndex=10 onfocus="select()">

<DIV id=divAltText style="left: 0.98em; top: 7.1067em; width: 6.58em; height: 1.2168em; ">Ziel:</DIV>
<SELECT ID="txtziel" style="left: 8.54em; top:6.8025em; width: 21.5em;height:10px;">
<option value="_self">im gleichen Fenster &ouml;ffnen</option>
<option value="_blank">in neuem Fenster &ouml;ffnen</option>
</select>

<DIV id=divAltText style="left: 31.36em; top: 7.1067em; width: 6.58em; height: 1.2168em; ">Email:</DIV>
<input type="checkbox" ID="txtmail" name="hal" value="x" style="left: 34.54em; top: 6.75em;">
<BUTTON class="bt" ID=btnOK onClick="Set()" style="left: 31.36em; top: 1.0647em; width: 7em; height: 2.2em; " tabIndex=40>Link einf&uuml;gen</BUTTON>
<BUTTON class="bt" ID=btnCancel style="left: 31.36em; top: 3.6504em; width: 7em; height: 2.2em; " type=reset tabIndex=45 onClick="window.returnValue = 'none';window.close();">Abbrechen</BUTTON>



</BODY>
</HTML>
