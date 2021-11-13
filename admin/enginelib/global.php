<?php
// +----------------------------------------------------------------------+
// | EngineLib - Image Functions                                          |
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
// $Id: global.php 6 2005-10-08 10:12:03Z alex $

$starttime = startTimer();

error_reporting(E_ALL & ~E_NOTICE);

set_error_handler("engineErrorHandler");

//Session Fehlermeldung deaktivieren
@ini_set("session.bug_compat_42", 1);
@ini_set("session.bug_compat_warn", 0);

// PHP-Version prüfen
//$curver = intval(str_replace(".","", phpversion()));
$ini_val = ( getPHPVersion() >= '400' ) ? 'ini_get' : 'get_cfg_var';

// bei PHP-Version älter als 4.1.0 neue superglobale Variablen füllen
if(getPHPVersion() < 410) {
	$_POST = array();
	$_GET = array();
	$_COOKIE = array();
	$_SESSION = array();
	$_FILES = array();
	$_SERVER = array();
	$_ENV = array();
	$_REQUEST = array();
	fill_new_vars();	
}
	
if (get_magic_quotes_gpc()) {
  if(is_array($_REQUEST)) $_REQUEST=stripslashes_array($_REQUEST);
  if(is_array($_POST)) $_POST=stripslashes_array($_POST);
  if(is_array($_GET)) $_GET=stripslashes_array($_GET);
  if(is_array($_COOKIE)) $_COOKIE=stripslashes_array($_COOKIE);
}
@set_magic_quotes_runtime(0);
	
// register_globals prüfen 
if(@$ini_val("register_globals")) {
	$register_globals = TRUE;
} else {
	$register_globals = FALSE;
}

$db_sql = new db_sql($dbName,$hostname,$dbUname,$dbPasswort);

$config = loadEngineSetting();

if(!defined("IN_ADMIN_CENTER")) {
    checkVariable('subcat',1);
    checkVariable('dlid',1);
    checkVariable('nameid',1);
    checkVariable('comid',1);
	checkVariable('start',1);
    $lang = array();
    include_once($_ENGINE['eng_dir']."lang/".$config['language']."/".$config['language'].".php");
} else {
    $a_lang = array();
    if($admin_lang == 1) include_once($_ENGINE['eng_dir']."admin/lang/german_admin.php");
    if($admin_lang == 2) include_once($_ENGINE['eng_dir']."admin/lang/english_admin.php");
    if($admin_lang != 1 && $admin_lang != 2) {
    	echo "No Language File available, you can not use the AdminCenter without!!!";
    	exit;
	}
}

// Session-Klasse initialisieren
$sess = new engineSession();

// Authentification-Klasse initialisieren
$auth = new engineAuth($user_table,$group_table);

if(!defined("IN_ADMIN_CENTER")) {
    $tpl = new engineTemplate($_ENGINE['eng_dir']."templates/".$config['template_folder']."/");
    
    if($own_header || $own_footer) {
        $tpl->setOwnBorder($own_footer,$own_header);
    }
    initStandardVars();
    $tpl->loadFile('header', 'header.html'); 
    $tpl->loadFile('footer', 'footer.html'); 
}

if(!defined("IN_ADMIN_CENTER")) {    
    if ($auth->user['userid'] != 2 || $auth->user['userid'] == 0) {
        $registered_member = true;
        if($auth->user['canmodifyownprofile']) {
            $modify_own_profile = true;
            $tpl->register('change_account_url', definedBoardUrls("changeaccount"));
        }
        $tpl->parseIf('header', 'modify_own_profile');
        $tpl->register('logout_url', $sess->url("misc.php?action=logout"));
    } else {
        $guest_member = true;
        $activate_login = false;
        $activate_registering = false;
        if ($config['userreg'] != '1') {
            $tpl->register('register_url', definedBoardUrls("addmember"));
            $activate_registering = true;
        }
        
        if ($config['userlogin'] != '1') {
            $tpl->register('login_url', $sess->url("misc.php?action=login"));
            $activate_login = true;
        }
    }
    $tpl->parseIf('header', 'activate_registering');
    $tpl->parseIf('header', 'activate_login');
    $tpl->parseIf('header', 'registered_member');
    $tpl->parseIf('header', 'guest_member');

    
    if ($auth->user['canseemembers']) {
        $use_memberlist = true;
        $tpl->register('memberlist_url', definedBoardUrls("memberlist"));
    }
    $tpl->parseIf('header', 'use_memberlist');
    
    if ($auth->user['canuseenginesearch']) {
        $use_search = true;
        $tpl->register('search_url', $sess->url("search.php?action=search"));
    }
    $tpl->parseIf('header', 'use_search');
    $tpl->parseIf('footer', 'use_search');
    
    if($auth->user['canuploadfile'] == 1 || $auth->user['canuploadfiles']) {
        $upload_new_file = true;
        $tpl->register('upload_file_url', $sess->url("uploadfile.php"));    
    }
    $tpl->parseIf('header', 'upload_new_file');
	
    if($config['top_list'] && $auth->user['canseetopstatsfiles']) {
        $top_stats_file = true;
        $tpl->register('top_stats_url', $sess->url("misc.php?action=toplist"));    
    }
    $tpl->parseIf('header', 'top_stats_file');	
    
    if ($auth->user['canaccessadmincent'] == 1 ) {
        $access_admin_center = true;
        $tpl->register('admin_url', $sess->adminUrl("index.php"));       
    }
    $tpl->parseIf('footer', 'access_admin_center');
	
	if($config['enable_quickjump'] == "1") {
	    if($auth->user['canuseenginesearch']) $option .= "<option value=\"search\">".$lang['footer_search']."</option>";
	    if($auth->user['canmodifyownprofile']) $option .= "<option value=\"memberdetails\">".$lang['footer_edit_profile']."</option>";
	    
	    $current_cat = $_GET['subcat'];
		$option .= "<option value=\"main\">".$lang['footer_downloads_mainpage']."</option>\n<option value=\"0\">----------------</option>\n";		
	    $option .= makeQuickLink(0,0,"",0);
		$tpl->register('option',$option);
		$parse_quickjump_block = true;
	}	
	$tpl->parseIf('footer', 'parse_quickjump_block');	
    
    $tpl->register(array('header_welcome' => $lang['header_welcome'],
                        'header_edit_profile' => $lang['header_edit_profile'],
                        'header_log_out' => $lang['header_log_out'],
                        'header_log_in' => $lang['header_log_in'],
                        'header_register' => $lang['header_register'],
                        'header_search' => $lang['header_search'],
                        'header_memberlist' => $lang['header_memberlist'],
                        'header_add_entry' => $lang['header_add_entry'],
                        'footer_quick_search' => $lang['footer_quick_search'],
                        'footer_admin_center' => $lang['footer_admin_center'],
						'footer_quick_jump' => $lang['footer_quick_jump'],
						'footer_please_choose' => $lang['footer_please_choose'],						
                        'header_upload' => $lang['header_upload'],
                        'header_stats' => $lang['header_stats']));
}

function makeQuickLink($catid,$subcat,$limiter,$depth=1) {
    global $cat_table,$db_sql,$cat_cache,$current_cat;
    
    if ( !isset($cat_cache) ) {
        $result2 = $db_sql->sql_query("SELECT catid,subcat,catorder,titel FROM $cat_table ORDER BY subcat,catorder,catid");
        while ($ncatcache = $db_sql->fetch_array($result2)) {
            $ncatcache = stripslashes_array($ncatcache);
            $cat_cache["$ncatcache[subcat]"]["$ncatcache[catorder]"]["$ncatcache[catid]"] = $ncatcache;
        }
    }
    
    while ( list($key1,$val1) = @each($cat_cache["$catid"]) ) {
        while ( list($key2,$node) = each($val1) ) {					
            $jumpcatid = $node['catid'];
            $jumpcattitle = $limiter." $node[titel]";
            if ($current_cat == $jumpcatid) {
                $optionselected='selected="selected"';
            } else {
                $optionselected='';
            }	            
            $cat_link .= "<option value=\"$jumpcatid\" $optionselected>$jumpcattitle</option>";
            $cat_link .= makeQuickLink($jumpcatid,$jumpcatid,$limiter."--",$depth+1);
        }
    }
    
    return $cat_link;
}
?>