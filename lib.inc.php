<?php
// +----------------------------------------------------------------------+
// | EngineLib - Configuration File for the Engines                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003,2004 AlexScriptEngine - e-Visions                 |
// +----------------------------------------------------------------------+
// | This code is not freeware. Please read our licence condition care-   |
// | fully to find out more. If there are any doubts please ask at the    |
// | Support Forum                                                        |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Author: Alex Höntschel <info@alexscriptengine.de>                    |
// | Web: http://www.alexscriptengine.de                                  |
// | IMPORTANT: No email support, please use the support forum at         |
// |            http://www.alexscriptengine.de                            |
// +----------------------------------------------------------------------+
//
// $Id: lib.inc.php 6 2005-10-08 10:12:03Z alex $

// Absoluter Pfad - Change this if the script doesn't work
$_ENGINE['eng_dir'] = str_replace ('\\', '/', dirname(__FILE__) . '/'); 
// Session-Name
$_ENGINE['session_name'] = "EngineSID";
// Session-Max-Lifetime 
$_ENGINE['sess_max_lifetime'] = "1440";
// Cookie-Domain
$_ENGINE['cookiedomain'] = "";
// Cookie-Pfad
$_ENGINE['cookiepath'] = "/";
// Template-Ordner
$_ENGINE['template_folder'] = "templates/default";

$develope = 0;
$query_count = 0;

// Bibliotheken laden
//include_once($_ENGINE['eng_dir']."admin/enginelib/class.template.php");
include_once($_ENGINE['eng_dir']."include/config.inc.php");

if(!defined("ENGINE_INSTALLED")) {
	header("Location: installer.php");
	exit;
}

if(!defined("IN_ADMIN_CENTER")) include_once($_ENGINE['eng_dir']."admin/enginelib/class.template.php");
include_once($_ENGINE['eng_dir']."admin/enginelib/class.db.php");
include_once($_ENGINE['eng_dir']."admin/enginelib/class.session.php");
include_once($_ENGINE['eng_dir']."admin/enginelib/class.auth.php");
include_once($_ENGINE['eng_dir']."admin/enginelib/driver/function.driver.".BOARD_DRIVER.".php");
include_once($_ENGINE['eng_dir']."admin/enginelib/function.global.php");
include_once($_ENGINE['eng_dir']."admin/enginelib/function.img.php");
include_once($_ENGINE['eng_dir']."admin/enginelib/global.php");

if(!defined("IN_ADMIN_CENTER")) {
    $tpl->initGZipLevel($config['gziplevel']);
}

/* prüfen ob Engine-Offline */
if(!defined("IN_ADMIN_CENTER")) {
    if($config['isoffline'] == 1 && $config['offline_why'] != "" && !$auth->user['canaccessofflineengine'] && $_REQUEST['action'] != 'logout') {
    	include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
        $tpl->loadFile('main', 'offline.html'); 
        $tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['title_offline'] => '')));
        $tpl->register(array(
                            'title_offline' => $lang['title_offline'],
                            'offline_why' => $config['offline_why']));        
    	
        $tpl->register('query', showQueries($develope));
        $tpl->register('header', $tpl->pget('header'));
        
        $tpl->register('footer', $tpl->pget('footer'));
        $tpl->pprint('main');
    	exit;
    }
    
    if($auth->user['blocked']) {
        $auth->userLogout();
        rideSite($sess->url('index.php'), $lang['misc_2']);
        exit();        
    }
}

?>