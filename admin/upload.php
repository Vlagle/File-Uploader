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
|   > Upload-Funktionen Admin Center
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: upload.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

define("FILE_NAME","upload.php");

include_once('adminfunc.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
$auth->checkEnginePerm("canaccessadmincent");

$destroy_progress = false;

function GetBatchEditForm($catid="",$file_no) {
    global $cat_table,$config,$dl_table,$a_lang,$db_sql;
    $result = $db_sql->sql_query("SELECT * FROM $cat_table WHERE catid='$catid'");
    $catinf = $db_sql->fetch_array($result);
    $catinf = stripslashes_array($catinf);		 
    
	buildTableSeparator($a_lang['upload_file']." ".$file_no);    
    buildHiddenField("catid[$file_no]",$catid);
    buildInputRow($a_lang['afunc_134'], "title[$file_no]", $dl['title']);
	buildUploadRow($a_lang['afunc_138'], "file_$file_no");  
    
    $status_option = "<select class=\"input\" name=\"status[$file_no]\">";
    $status_option .= "<option value=\"2\" ";
    $status_option .= ">$a_lang[afunc_142]</option>";
    $status_option .= "<option value=\"1\" selected";
    $status_option .= ">$a_lang[afunc_143]</option>";
    $status_option .= "<option value=\"3\" ";
    $status_option .= ">$a_lang[afunc_144]</option>"; 
    $status_option .= "</select>";               
    buildStandardRow($a_lang['afunc_141'], $status_option);    
    
      
    $reg_option = "<select class=\"input\" name=\"onlyreg[$file_no]\">";
    $reg_option .= "<option value=\"0\" ";
    if ($dl['onlyreg'] == 0) $reg_option .= "selected"; 
    $reg_option .= ">$a_lang[afunc_146]</option>";
    $reg_option .= "<option value=\"1\" ";
    if ($dl['onlyreg'] == 1) $reg_option .= "selected"; 
    $reg_option .= ">$a_lang[afunc_147]</option>";
    $reg_option .= "</select>";
    buildStandardRow($a_lang['afunc_145'], $reg_option);
    buildTextareaRow($a_lang['afunc_136'], "dl_description[$file_no]", $dl['dl_description'], 65, 10,0, "wrap=\"soft\"");
    buildInputRow($a_lang['afunc_149'], "dlhits[$file_no]", $dl['dlhits']);
    buildInputRow($a_lang['afunc_150'], "dlvotes[$file_no]", $dl['dlvotes']);
    buildInputRow($a_lang['afunc_151'], "dlpoints[$file_no]", $dl['dlpoints']); 
    //echo "<tr class=\"".switchBgColor()."\">\n<td valign=\"top\"><p>".$a_lang['afunc_153']."</p></td>\n";
    //echo "<td><p><input type=\"text\" size=\"40\" name=\"dlauthor[$file_no]\" value=\"".$dl['dlauthor']."\">&nbsp;<a href=\"Javascript: LoadUser();\"><img src=\"images/user.gif\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">User ausw&auml;hlen</a></p></td>\n</tr>\n";
      
    buildInputRow($a_lang['afunc_153'], "dlauthor[$file_no]", $dl['dlauthor']);
    buildInputRow($a_lang['afunc_214'], "authormail[$file_no]", $dl['authormail']);
    buildInputRow($a_lang['afunc_154'], "hplink[$file_no]", $dl['hplink']);
}
  
$message = "";

$max_fsize = $config['maxsize'];

if($action == "file" || $action == "process_batch") {
    $folder = $config['fileurl'];
    $filesdir = strrchr($folder,303);
    $filesdir = substr($filesdir, 1); 
    $filesdir = "./../".$filesdir;	
    
	$fieldname = $a_lang['uploads_url'];
	$end_dir = $config['fileurl'];
} elseif($action == "thumb") {
    $folder = $config['thumburl'];
    $filesdir = strrchr($folder,303);
    $filesdir = substr($filesdir, 1); 
    $filesdir = "./../".$filesdir;	

	$fieldname = $a_lang['uploads_dat_th'];
	$end_dir = "";
}

$pic_extens = "gif,jpg,jpeg,png";
$file_extens = explode("\r\n",$config['upload_extension']);
$file_extens = implode(",",$file_extens); 
$extens = $file_extens.",".$pic_extens;

if($action == "process_batch") {
	$wall_date = time();
   	include_once($_ENGINE['eng_dir']."admin/enginelib/class.upload.php");
   	$my_upload = new upload();
	for ($i = 1; $i <= $count_files; $i++) {
	    if(!@is_writeable($filesdir)) {
	        $message .= $a_lang['uploads_nopermission']."<br>";
	    } else {
	    	if($rename == 0) $my_upload->setChangeFilename(0);
	    	$my_upload->setAllowedExtensions($extens);
			$my_upload->setMaxFileSize($config['maxsize']);
	    	$my_upload->setFilesDir($filesdir);
	    	if($my_upload->uploadFile("file_$i")) {
				$field_name = "file_".$i;
				$size = $_FILES[$field_name]['size'];
	    		$message .= "Batch-File ".$i." ".$a_lang['uploads_ok1']."<br>";
	    		$new_name = $my_upload->getDestName();
				$db_sql->sql_query("UPDATE $cat_table SET download_count=download_count+1 WHERE catid='".$catid[$i]."'");
			    $db_sql->sql_query("INSERT INTO $dl_table 
                        (catid,dltitle,dldesc,status,dlurl,dl_date,dlhits,dlvotes,hplink,dlsize,dlpoints,dlauthor,authormail,thumb,onlyreg)
			    		VALUES ('".$catid[$i]."','".addslashes(htmlspecialchars($title[$i]))."','".addslashes($dl_description[$i])."','".intval($status[$i])."','".$end_dir."/".$new_name."','".time()."','".addslashes($dlhits[$i])."','".addslashes($dlvotes[$i])."','".addslashes($hplink[$i])."','".intval($size)."','".addslashes($dlpoints[$i])."','".addslashes(htmlspecialchars($dlauthor[$i]))."','".addslashes(htmlspecialchars($authormail[$i]))."','','".intval($onlyreg[$i])."')");
	    	} else {
	    		$message .= "Batch-File ".$i." ".$my_upload->getErrorCode()."<br>";
	    		$action = "batch_start";
	    	}            
	    }			
	}
    $destroy_progress = true;	
}

if($action == "file") { 
    if(!@is_writeable($filesdir)) {
        $message .= $a_lang['uploads_nopermission']."<br>";
    } else {
    	include_once($_ENGINE['eng_dir']."admin/enginelib/class.upload.php");
    	$my_upload = new upload();
    	
    	if($rename == 0) $my_upload->setChangeFilename(0);
    	$my_upload->setAllowedExtensions($extens);
		$my_upload->setMaxFileSize($config['maxsize']);
    	$my_upload->setFilesDir($filesdir);
    	if($my_upload->uploadFile("file")) {
    		$message = $a_lang['uploads_ok1'];
    		$new_name = $my_upload->getDestName();
    	} else {
    		$message = $my_upload->getErrorCode();
    		$step = "file";
    	}	
    	$head_js = "
    	<script language=\"JavaScript\">
    	<!--	
    	function filedata2(data,size) { 
            opener.document.alp.dlurl.value = data;
            opener.document.alp.dlsize.value = size;
    	    self.close(); 
    	} 	
    	//-->
    	</script>	
    	";
    }
    $destroy_progress = true;
}

if($action == "thumb") {
    if(!@is_writeable($filesdir)) {
        $message .= $a_lang['uploads_nopermission']."<br>";
    } else {
    	include_once($_ENGINE['eng_dir']."admin/enginelib/class.upload.php");
    	$my_upload = new upload();
    	
    	if($rename == 0) $my_upload->setChangeFilename(0);
    	$my_upload->setAllowedExtensions($extens);
		$my_upload->setMaxFileSize($config['maxsize']);
    	$my_upload->setFilesDir($filesdir);
    	if($my_upload->uploadFile("file")) {
    		$message = $a_lang['uploads_ok1'];
    		$new_name = $my_upload->getDestName();
    	} else {
    		$message = $my_upload->getErrorCode();
    		$step = "thumb";
    	}	
    	$head_js = "
    	<script language=\"JavaScript\">
    	<!--	
        function filedata(data) { 
            opener.document.alp.thumb.value += data+\" \"; 
            self.close(); 
        }        
    	//-->
    	</script>	
    	";
        
    }
    $destroy_progress = true;
}

buildAdminHeader($head_js);

if($destroy_progress) echo "<script language=javascript>\n showProgress();\n hideProgress();\n</script>";

if ($message != "") buildMessageRow($message);
if ($head_js != '' && $new_name) {
    if($action == "file") {
        buildTransferRow($a_lang['uploads_ok6'],$a_lang['uploads_ok7'].$new_name.$a_lang['uploads_ok8'],"filedata2",$config['fileurl']."/".$new_name,$_FILES['file']['size']);
    } else {
        buildTransferRow($a_lang['uploads_ok6'],$a_lang['uploads_ok7'].$new_name.$a_lang['uploads_ok8'],"filedata",$new_name);
    }
}   
   
if($step == 'file') {
    buildHeaderRow($a_lang['uploads_fileupload'],"upload_file.gif");	
    $head = $a_lang['uploads_h1'];  
    buildFormHeader("upload.php", "post", $step, "alp", 1);
    buildTableHeader($a_lang['uploads_new']);
    buildUploadRow("<b>$a_lang[uploads_search] $head</b><br><span class=\"smalltext\">$a_lang[uploads_message]</span>", "file");
    buildRadioRow($a_lang['uploads_changename'], "rename");
    buildFormFooter($a_lang['uploads_button1'], $a_lang['uploads_reset'], 2, ""," onClick='showProgress()'");
    closeWindowRow();
}   

if($step == 'thumb') {
    buildHeaderRow($a_lang['uploads_thumbupload'],"upload_file.gif");	
    $head = $a_lang['uploads_h1'];  
    buildFormHeader("upload.php", "post", $step, "alp", 1);
    buildTableHeader($a_lang['uploads_new']);
    buildUploadRow("<b>$a_lang[uploads_search] $head</b><br><span class=\"smalltext\">$a_lang[uploads_message]</span>", "file");
    buildRadioRow($a_lang['uploads_changename'], "rename");
    buildFormFooter($a_lang['uploads_button1'], $a_lang['uploads_reset'], 2, ""," onClick='showProgress()'");
    closeWindowRow();
}   
   
if($action == "batch_start") {
	buildHeaderRow($a_lang['uploads_batch2'],"upload_file.gif");
	buildFormHeader("upload.php", "post", "batch_show");
	buildTableHeader($a_lang['uploads_batch3']);	
	$option_field1 = "<select name=\"count_files\">";
	for($i=2;$i<=15;$i++) {
		$option_field1 .= "<option value=\"".$i."\">".$i."</option>";
	}	
	$option_field1 .= "</select>";
	buildStandardRow($a_lang['uploads_batch1'], $option_field1);
	$option_field2 = "<select name=\"batch_category\">";
	$option_field2 .= makeACatLink();
	$option_field2 .= "</select>";
	buildStandardRow($a_lang['uploads_batch4'], $option_field2);
	buildFormFooter($a_lang['uploads_batch5'], "");
}   
   
   
if($action == "batch_show") {
	buildHeaderRow($a_lang['uploads_batch2'],"upload_file.gif");
	buildFormHeader("upload.php", "post", "process_batch", "alp", 1);
	buildHiddenField("count_files",$count_files);
	buildTableHeader($a_lang['uploads_batch6']);
	buildRadioRow($a_lang['uploads_batch7'], "rename");
	for($i=1;$i<=$count_files;$i++) {
		GetBatchEditForm($batch_category,$i);
	}		
	buildFormFooter($a_lang['uploads_batch8'], $a_lang['uploads_reset'] , 2, ""," onClick='showProgress()'");
}   

buildAdminFooter();
?>