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
|   > Weiterleitung zum Download inkl. Pruefung des Referers
|
|   > Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|   > bedingungen (read-me.html) für weitere Informationen.
|   >-----------------------------------------------------------------
|   > This script is NOT freeware! Please read the Copyright Notice
|   > (read-me_eng.html) for further information.
|
|   > $Id: redirect.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/
define('DISABLE_GZIP', true);

include_once('lib.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");

function getFileExtension($filename) {
    $filedata = explode(".", $filename);
    return strtolower($filedata[count($filedata)-1]);
}

$load_file = 0;

if($_GET['mirror_id']) {
    if ($_GET['mirror_id']=='') {
        rideSite($sess->url('index.php'), $lang['php_link_missed']);
        exit();
    } else {
        $loadfile = $db_sql->query_array("SELECT $mirror_table.mirror_url as dlurl, $dl_table.dlid, $dl_table.catid, $dl_table.dltitle, $dl_table.onlyreg, $dl_table.licence_id, $cat_table.titel FROM $mirror_table
                                        LEFT JOIN $dl_table ON $mirror_table.dlid = $dl_table.dlid
                                        LEFT JOIN $cat_table ON $dl_table.catid = $cat_table.catid
                                        WHERE $mirror_table.mirror_id='".$_GET['mirror_id']."'");
    }

    $base_name = "mirror_id";
    $file_id = $_GET['mirror_id'];

    if(!$_GET['licence_accepted']) {
        if($loadfile['licence_id'] != 0) {
            $tpl->loadFile('main', 'redirect.html');
            $licence = $db_sql->query_array("SELECT licence_title, licence FROM $licence_table WHERE licence_id = '".$loadfile['licence_id']."'");
            $tpl->register('title', $licence['licence_title']);
            $tpl->register(array('licence_text' => $licence['licence'],
                                'redirect_yes_i_have_read_licence' => $lang['redirect_yes_i_have_read_licence'],
                                'base_name' => $base_name,
                                'file_id' => $file_id,
                                'redirect_btn_download' => $lang['redirect_btn_download']));
            $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $loadfile['titel'] => $sess->url('index.php?subcat='.$loadfile['catid']) , $loadfile['dltitle'] => $sess->url("comment.php?dlid=".$loadfile['dlid']), $licence['licence_title'] => '')));
            $tpl->register('query', showQueries($develope));
            $tpl->register('header', $tpl->pget('header'));

            $tpl->register('footer', $tpl->pget('footer'));
            $tpl->pprint('main');
            exit;
        }
    }

    // Refererpr&uuml;fung
    $httpref = $HTTP_SERVER_VARS["HTTP_REFERER"];
    if (!isset ($httpref)) $httpref = $HTTP_REFERER;

    if (eregi("?", $httpref)){
        $referer_std = explode("?",$httpref);
        $referer = $referer_std[0];
    } else {
        $referer = $httpref;
    }

    $load_file = 0;
    if($config['allowedreferer'] != "") {
        $allowed_sites = explode("\r\n",$config['allowedreferer']);
        $load_file = 0;
        for($i=0;$i < count($allowed_sites);$i++) {
            if(eregi($allowed_sites[$i],$referer))   $load_file = 1;
        }
    } else {
        $load_file = 1;
    }

    if($loadfile['onlyreg'] ==  1) {
        if($auth->user['canaccessregisteredfiles'] != "1") $load_file = 0;
    }

    if($load_file==1) {
        header("Location: ".$loadfile['dlurl']);
    } else {
        rideSite($sess->url('index.php'), $lang['rec_error54']);
        exit();
    }

} else {

    if ($_GET['dlid']=='') {
        rideSite($sess->url('index.php'), $lang['php_link_missed']);
        exit();
    } else {
        $loadfile = $db_sql->query_array("SELECT $dl_table.catid, $dl_table.dltitle, $dl_table.dlurl, $dl_table.onlyreg, $dl_table.licence_id, $cat_table.titel FROM $dl_table
                                        LEFT JOIN $cat_table ON $dl_table.catid = $cat_table.catid
                                        WHERE $dl_table.dlid='".$_GET['dlid']."'");
    }

    $base_name = "dlid";
    $file_id = $_GET['dlid'];


    if(!$_GET['licence_accepted']) {
        if($loadfile['licence_id'] != 0) {
            $tpl->loadFile('main', 'redirect.html');
            $licence = $db_sql->query_array("SELECT licence_title, licence FROM $licence_table WHERE licence_id = '".$loadfile['licence_id']."'");
            $tpl->register('title', $licence['licence_title']);
            $tpl->register(array('licence_text' => $licence['licence'],
                                'redirect_yes_i_have_read_licence' => $lang['redirect_yes_i_have_read_licence'],
                                'base_name' => $base_name,
                                'file_id' => $file_id,
                                'redirect_btn_download' => $lang['redirect_btn_download']));
            $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $loadfile['titel'] => $sess->url('index.php?subcat='.$loadfile['catid']) , $loadfile['dltitle'] => $sess->url("comment.php?dlid=".$_GET['dlid']), $licence['licence_title'] => '')));
            $tpl->register('query', showQueries($develope));
            $tpl->register('header', $tpl->pget('header'));

            $tpl->register('footer', $tpl->pget('footer'));
            $tpl->pprint('main');
            exit;
        }
    }

    // Refererpr&uuml;fung
    $httpref = $HTTP_SERVER_VARS["HTTP_REFERER"];
    if (!isset ($httpref)) $httpref = $HTTP_REFERER;

    if (eregi("?", $httpref)){
        $referer_std = explode("?",$httpref);
        $referer = $referer_std[0];
    } else {
        $referer = $httpref;
    }

    $load_file = 0;
    if($config['allowedreferer'] != "") {
        $allowed_sites = explode("\r\n",$config['allowedreferer']);
        $load_file = 0;
        for($i=0;$i < count($allowed_sites);$i++) {
            if(eregi($allowed_sites[$i],$referer))   $load_file = 1;
        }
    } else {
        $load_file = 1;
    }

    if($loadfile['onlyreg'] ==  1) {
        if($auth->user['canaccessregisteredfiles'] != "1") $load_file = 0;
    }


    if($load_file==1) {
        $db_sql->sql_query("UPDATE $dl_table SET dlhits=dlhits+1 WHERE dlid='".intval($_GET['dlid'])."'");

        if(eregi($config['fileurl'], $loadfile['dlurl'])) {
            $sess->closeSess();
            include_once($_ENGINE['eng_dir']."admin/enginelib/code.mime.php");
            include_once($_ENGINE['eng_dir']."admin/enginelib/class.download.php");

            $filesdir = substr(strrchr($config['fileurl'],303),1);
            $filename = substr(strrchr($loadfile['dlurl'],303),1);

            $dl_object = new engineDownload();
            $dl_object->setByFile($filesdir."/".$filename);
            $dl_object->download();
        } else {
            header("Location: ".$loadfile['dlurl']);
        }
    } else {
        rideSite($sess->url('index.php'), $lang['rec_error54']);
        exit();
    }
}

?>