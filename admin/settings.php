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
|   > Einstellungen Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: settings.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","settings.php");

include_once('adminfunc.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
$auth->checkEnginePerm("canaccessadmincent");

$message = '';

function reloadSetting() {
    global $db_sql, $set_table, $config;
    $config = $db_sql->query_array("SELECT * FROM $set_table WHERE styleid='1'");
    $config['engine_mainurl'] = $config['dlscripturl'];
    return $config;
}

if(isset ($action) && $action=='gen') {
    while (list($key,$val)=each($setting)) {
        $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($val)."' WHERE find_word='$key'");
    }
	$message .= $a_lang['settings_mes2'];
    $config = loadEngineSetting();
    $step = 'gen_set';    
}
	
if(isset ($action) && $action=='page') {
    while (list($key,$val)=each($setting)) {
        $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($val)."' WHERE find_word='$key'");
    }   
	$message .= $a_lang['settings_mes3'];
    $config = loadEngineSetting();
    $step = "page_set"; 
}

if(isset ($action) && $action=='onoff') {
    $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($isoffline)."' WHERE find_word='isoffline'");
    $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($offline_why)."' WHERE find_word='offline_why'");
	$message .= $a_lang['settings_onoff'];
    $config = loadEngineSetting();
    $step = 'onoff';
} 

if(isset ($action) && $action=='comment_count') {
	$process_redirect = false;
	$step = "update_counter";
	if(!$start) $start = 0;
	if(!$i) $i = 1;
	$j = 0;
	$start_interval = $i;
	$result = $db_sql->sql_query("SELECT dlid, comment_count FROM $dl_table LIMIT $start,$interval");
	while($walk_through = $db_sql->fetch_array($result)) {
		$res = $db_sql->query_array("SELECT count(*) AS total_comments FROM $dlcomment_table WHERE dlid='".$walk_through['dlid']."' AND com_status='1'");
		//echo "Datei-ID: ".$walk_through['dlid']." Z&auml;hler alt: ".$walk_through['comment_count']." neu: ".$res['total_comments']."<br>";
		$db_sql->sql_query("UPDATE $dl_table SET comment_count='".$res['total_comments']."' WHERE dlid='".$walk_through['dlid']."'");
		$j++;
	}
	$i = $i+1;
	$start = $start+$interval;
	
	$redirect = 'settings.php?action=comment_count&start='.$start.'&interval='.$interval.'&i='.$i;
	if($j == $interval) {
		$process_redirect = true;
		$message = sprintf($a_lang['settings_cycle_done_proceed'],$start_interval,$i);
	} else {
		$message = $a_lang['settings_comment_count_updated'];
	}
}

if(isset ($action) && $action=='file_count') {
	$process_redirect = false;
	$step = "update_counter";
	if(!$start) $start = 0;
	if(!$i) $i = 1;
	$j = 0;
	$start_interval = $i;
	$result = $db_sql->sql_query("SELECT catid, download_count FROM $cat_table LIMIT $start,$interval");
	while($walk_through = $db_sql->fetch_array($result)) {
		$res = $db_sql->query_array("SELECT count(*) AS total_files FROM $dl_table WHERE catid='".$walk_through['catid']."' AND status!='3'");
		//echo "Kategorie-ID: ".$walk_through['catid']." Z&auml;hler alt: ".$walk_through['download_count']." neu: ".$res['total_files']."<br>";
		$db_sql->sql_query("UPDATE $cat_table SET download_count='".$res['total_files']."' WHERE catid='".$walk_through['catid']."'");
		$j++;
	}
	$i = $i+1;
	$start = $start+$interval;
	
	$redirect = 'settings.php?action=file_count&start='.$start.'&interval='.$interval.'&i='.$i;
	if($j == $interval) {
		$process_redirect = true;
		$message = sprintf($a_lang['settings_cycle_done_proceed'],$start_interval,$i);
	} else {
		$message = $a_lang['settings_file_count_updated'];
	}
}
    
buildAdminHeader();

if ($message != "") {
	if($process_redirect) {
		buildMessageRow($message,array('is_top' => 1, 'auto_redirect' => $redirect));
		buildAdminFooter();
		exit;
	} else {
	    buildMessageRow($message, array('is_top' => 1, 'next_script' => 'settings.php', 'next_action' => array('step',$step,$a_lang['afunc_proceed'])));
	    buildAdminFooter();
	    exit;
	}
}

if(!isset ($step) && $change == '') {
  echo " <p><b>Es wurde kein Einstelltyp gewählt. Bitte wähle links aus der Navigation die gewünschte Option aus. $step</b></p>";
} else {

    if($step == 'gen_set') {
        buildHeaderRow($a_lang['afunc_84'],"setting.gif",1);
        buildInfo($a_lang['info10'][0],$a_lang['info10'][1]);	
        buildFormHeader("settings.php", "post", "gen");
        buildHiddenField("styleid","1");
        buildTableHeader($a_lang['afunc_85']);		
		buildInputRow($a_lang['afunc_86'], "setting[mainurl]", $config['mainurl']);
		buildInputRow($a_lang['afunc_87'], "setting[dlscripturl]", $config['dlscripturl']);
		//buildInputRow($a_lang['afunc_88'], "setting[smilieurl]", $config['smilieurl']);
		//buildInputRow($a_lang['afunc_89'], "setting[grafurl]", $config['grafurl']);
		buildInputRow($a_lang['afunc_90'], "setting[avaturl]", $config['avaturl']);
		buildInputRow($a_lang['afunc_91'], "setting[fileurl]", $config['fileurl']);
        buildInputRow($a_lang['afunc_92'], "setting[thumburl]", $config['thumburl']);
		buildTableSeparator($a_lang['afunc_93']);
		buildInputRow($a_lang['afunc_94'], "setting[scriptname]", $config['scriptname']);
		buildInputRow($a_lang['afunc_95'], "setting[admin_mail]", $config['admin_mail']);
        buildInputYesNo($a_lang['settings_7'], "setting[use_smtp]", $config['use_smtp']);
        buildInputRow($a_lang['settings_8'], "setting[smtp_server]", $config['smtp_server'], "40");
        buildInputRow($a_lang['settings_9'], "setting[smtp_username]", $config['smtp_username'], "40");
        buildInputRow($a_lang['settings_10'], "setting[smtp_password]", $config['smtp_password'], "40");        
		buildInputRow($a_lang['afunc_96'], "setting[mainwidth]", $config['mainwidth']);
		buildTableSeparator($a_lang['afunc_97']);
		buildInputYesNo($a_lang['afunc_98'], "setting[commentmail]", $config['commentmail']);
		if ($config['guestpost'] == 1) {
			$check8 = "selected";
		} else {
			$check9 = "selected";
		}
        $visitor = $db_sql->query_array("SELECT canpostcomments FROM $group_table WHERE groupid='4'");
		$guest_option = "<select class=\"input\" name=\"setting[guestpost]\" size=\"2\">\n<option value=\"1\" $check8>$a_lang[afunc_101]\n<option value=\"2\" $check9>$a_lang[afunc_102]\n</select>";	
		
		if($visitor['canpostcomments']) buildStandardRow($a_lang['afunc_99'], $guest_option);
		buildInputYesNo($a_lang['afunc_103'], "setting[directpost]", $config['directpost']);
		buildInputRow($a_lang['afunc_257'], "setting[max_comment_length]", $config['max_comment_length']);
		buildTableSeparator($a_lang['afunc_104']);
		buildInputYesNo($a_lang['afunc_105'], "setting[deadmail]", $config['deadmail']);
		buildInputYesNo($a_lang['afunc_106'], "setting[deadlink]", $config['deadlink']);
		buildTableSeparator($a_lang['afunc_210']);
		buildInputYesNo($a_lang['afunc_211'], "setting[activategzip]", $config['activategzip']);
		buildInputRow($a_lang['afunc_212'], "setting[gziplevel]", $config['gziplevel']);
		buildTableSeparator($a_lang['settings_1']);
		$timeoffset_select .= "<select class=\"input\" name=\"setting[timeoffset]\">\n";
		for($i = -12; $i <= 12; $i++) {
			$timeoffset_select .= "<option value=\"$i\"";
			if($i == $config['timeoffset']) $timeoffset_select .= " selected";
			$timeoffset_select .= ">GMT $i:00 $a_lang[settings_2]</option>\n";
		}	
		$timeoffset_select .= "</select>\n";
			
		buildStandardRow($a_lang['settings_3'], $timeoffset_select);
		buildInputRow($a_lang['settings_4'], "setting[longdate]", $config['longdate']);
		buildInputRow($a_lang['settings_5'], "setting[shortdate]", $config['shortdate']);
		buildInputRow($a_lang['settings_6'], "setting[timeformat]", $config['timeformat']);		
		buildTableSeparator($a_lang['afunc_107']);
		buildInputRow($a_lang['afunc_335'], "setting[std_group]", $config['std_group']);
		//buildInputYesNo($a_lang['afunc_213'], "setting[addtplname]", $config['addtplname']);
		buildInputYesNo($a_lang['afunc_108'], "setting[showvisitorinfo]", $config['showvisitorinfo'],1);
		buildInputYesNo($a_lang['afunc_109'], "setting[userreg]", $config['userreg'],1);
		buildInputYesNo($a_lang['afunc_110'], "setting[userlogin]", $config['userlogin'],1);
		buildInputYesNo($a_lang['afunc_280'], "setting[reg_withmail]", $config['reg_withmail']);
		buildFormFooter($a_lang['afunc_57'], "");

    }

    if($step == 'page_set') {
        buildHeaderRow($a_lang['afunc_58'],"setting.gif");
        buildFormHeader("settings.php", "post", "page");
        buildHiddenField("styleid","1");
        buildTableHeader( $a_lang['afunc_59']);
        buildInputYesNo($a_lang['afunc_60'], "setting[pagesort]", $config['pagesort']);
        buildInputRow($a_lang['afunc_63'], "setting[dlperpage]", $config['dlperpage']);
        buildTableSeparator($a_lang['afunc_64']);
        buildInputYesNo($a_lang['afunc_65'], "setting[newindex]", $config['newindex']);
        buildInputRow($a_lang['afunc_66'], "setting[newindex_q]", $config['newindex_q']);
        buildInputYesNo($a_lang['afunc_67'], "setting[lastcomment]", $config['lastcomment']);
        buildInputRow($a_lang['afunc_68'], "setting[lastcomment_q]", $config['lastcomment_q']);
        buildInputYesNo($a_lang['afunc_69'], "setting[stats]", $config['stats']);
        //buildInputYesNo($a_lang['afunc_76'], "setting[more_stats]", $config['more_stats']);
        buildTableSeparator($a_lang['afunc_70']);
        buildInputYesNo($a_lang['afunc_71'], "setting[newlist]", $config['newlist']);
        buildInputRow($a_lang['afunc_72'], "setting[newlist_q]", $config['newlist_q']);
        buildInputYesNo($a_lang['afunc_73'], "setting[top_list]", $config['top_list']);
        buildInputRow($a_lang['afunc_74'], "setting[top_list_q]", $config['top_list_q']);
        buildInputRow($a_lang['afunc_321'], "setting[newmark]", $config['newmark']);
        buildInputRow($a_lang['update_mark'], "setting[updatemark]", $config['updatemark']);
        buildTableSeparator($a_lang['afunc_288']);
        buildTextareaRow($a_lang['afunc_289'], "setting[allowedreferer]", $config['allowedreferer'], 20, 5); 
        buildTableSeparator($a_lang['afunc_290']);
        $server_limit = intval(@ini_get('upload_max_filesize'));
        buildInputRow(sprintf($a_lang['afunc_291'],$server_limit,$server_limit*1024,$server_limit*1024*1024), "setting[maxsize]", $config['maxsize']);
        buildTextareaRow($a_lang['afunc_292'], "setting[upload_extension]", $config['upload_extension'], 15, 5);
        buildInputYesNo($a_lang['afunc_293'], "setting[filemail]", $config['filemail']);
        //buildInputYesNo($a_lang['afunc_322'], "setting[alluser_upload]", $config['alluser_upload']);
        buildTableSeparator($a_lang['afunc_294']);
        buildInputYesNo($a_lang['afunc_295'], "setting[active_lock]", $config['active_lock']);
        $lock_value = "<select class=\"input\" name=\"setting[kindoflock]\">\n<option value=\"0\""; 
        if($config['kindoflock'] == 0) $lock_value .= "selected";
        $lock_value .= ">$a_lang[afunc_299]</option>\n<option value=\"1\""; 
        if($config['kindoflock'] == 1) $lock_value .= "selected";
        $lock_value .= ">$a_lang[afunc_300]</option>\n<option value=\"2\""; 
        if($config['kindoflock'] == 2) $lock_value .= "selected";
        $lock_value .= ">$a_lang[afunc_301]</option>\n</select>";
        
        buildStandardRow($a_lang['afunc_296'], $lock_value);
        buildInputRow($a_lang['afunc_297'], "setting[time_to_lock]", $config['time_to_lock']);
        buildInputRow($a_lang['afunc_298'], "setting[user_rate_factor]", $config['user_rate_factor']);
		include_once($_ENGINE['eng_dir']."admin/enginelib/function.img.php");
		if(chkgd2() >= 2) {
			buildTableSeparator($a_lang['settings_for_thumbnails']);		
			buildInputYesNo($a_lang['auto_image_resize'], "setting[active_image_resizer]", $config['active_image_resizer']);
			buildInputRow($a_lang['thumbnail_height'], "setting[image_height]", $config['image_height']);
			buildInputRow($a_lang['thumbnail_width'], "setting[image_width]", $config['image_width']);
		}
        buildTableSeparator($a_lang['afunc_78']);
		buildInputYesNo($a_lang['set_direct_download'], "setting[front_download]", $config['front_download']);
        //buildInputYesNo($a_lang['afunc_79'], "setting[show_path]", $config['show_path']);
        buildInputYesNo($a_lang['afunc_319'], "setting[enable_quickjump]", $config['enable_quickjump']);
        buildFormFooter($a_lang['afunc_57'], "");
    }
	
    if($step == 'info') {
        buildHeaderRow($a_lang['path_and_url_external_use'],"setting.gif");
        buildTableHeader($a_lang['path']);
        buildStandardRow($a_lang['path_to_the_engine'], $_ENGINE['eng_dir']);
        buildTableSeparator("Url's");
        $result = $db_sql->sql_query("SELECT * FROM $cat_table");
        while($cat = $db_sql->fetch_array($result)) {
            $cat = stripslashes_array($cat);
            buildStandardRow(sprintf($a_lang['url_to_display_category'],$cat['titel'],$cat['titel']), $config['dlscripturl']."/index.php?subcat=".$cat['catid']);
        }
        buildTableFooter();
    }	
  
    if($step == 'onoff') {
        buildHeaderRow($a_lang['afunc_282'],"setting.gif",1);
        buildInfo($a_lang['info12'][0],$a_lang['info12'][1]);	
        buildFormHeader("settings.php", "post", "onoff");
        buildHiddenField("styleid","1");
        buildTableHeader($a_lang['afunc_283']);
        
        echo "
        <tr class=\"".switchBgColor()."\">
        <td>
        $a_lang[afunc_284]:
        </td>
        <td>
        <select class=\"input\" name=\"isoffline\">
        <option value=\"0\""; if($config['isoffline'] == 0) echo "selected";
        echo "
        >$a_lang[afunc_285]</option>
        <option style=\"color: red;\" value=\"1\""; if($config['isoffline'] == 1) echo "selected";
        echo "
        >$a_lang[afunc_286]</option>
        </select>
        </td>
        </tr>";
        
        buildTextareaRow($a_lang['afunc_287'], "offline_why", $config['offline_why'], "80","10","","wrap=\"off\"");
        buildFormFooter($a_lang['afunc_57'], "");
    } 
    
    if($step == 'update_counter') {
        buildHeaderRow($a_lang['settings_update_counter'],'update_counter.gif');
        buildFormHeader("settings.php", "post", "comment_count");
        buildTableHeader($a_lang['settings_update_comment_counter']);
        buildLightColumn($a_lang['settings_updates_counter_for_comments'],1,1,2);
        buildInputRow($a_lang['settings_number_per_cycle_comments'], 'interval', "5");
        buildFormFooter($a_lang['settings_update_comments'], $a_lang['settings_reset']);
        
        buildFormHeader("settings.php", "post", "file_count");
        buildTableHeader($a_lang['settings_update_file_counter']);
        buildDarkColumn($a_lang['settings_updates_counter_for_files'],1,1,2);
        buildInputRow($a_lang['settings_number_per_cycle_files'], 'interval', "5");
        buildFormFooter($a_lang['settings_update_files'], $a_lang['settings_reset']);        
        
    }
}

buildAdminFooter();
?>