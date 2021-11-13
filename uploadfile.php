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
|   > Upload Funktionen für User
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information.
|
|	> $Id: uploadfile.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/
//print_r($_POST);
//exit;

include_once('lib.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");

$tpl->register('title', $lang['title_uploadfile']);
$tpl->loadFile('main', 'uploadfile.html');
$tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), $lang['uploadfile_register_new_file'] => '')));

if ($auth->user['canuploadfiles'] == 0) {
    if ($auth->user['canuploadfile'] != 1) {
        header("Location: ".$sess->url("index.php"));
        exit;
    }
}

$filesdir = substr(strrchr($config['fileurl'],303),1);
$thumbsdir = substr(strrchr($config['thumburl'],303),1);

$max_fsize = $config['maxsize'];
$public_size = @round($config['maxsize'] / 1024 /1024,1);
$public_extens = explode("\r\n",$config['upload_extension']);
$public_extens = implode(",",$public_extens);

if(isset($dl_cat)) {
    $cat_link = makeCatLink($dl_cat,0);
} else {
    $cat_link = makeCatLink("",0);
}

if ($auth->user['groupid'] == '1' || $auth->user['groupid'] == '5') {
    $upl_u_name = "<input class=\"input\" type=\"text\" size=\"100\" name=\"upload_user\" value=\"".$auth->user['username']."\" />";
    $email_input = "<input class=\"input\" type=\"text\" size=\"100\" name=\"upload_usermail\" value=\"".$auth->user['useremail']."\" />";
    $hp_input = "<input class=\"input\" type=\"text\" size=\"100\" name=\"hplink\" value=\"".$auth->user['userhp']."\" />";
} else {
    $upl_u_name =  $auth->user['username']."<input type=\"hidden\" name=\"upload_user\" value=\"".$auth->user['username']."\" />";
    $email_input = $auth->user['useremail']."<input type=\"hidden\" name=\"upload_usermail\" value=\"".$auth->user['useremail']."\" />";
    $hp_input = $auth->user['userhp']."<input type=\"hidden\" name=\"hplink\" value=\"".$auth->user['userhp']."\" />";
}

$tpl->register(array('input_user_name' => $upl_u_name,
                    'input_user_email' => $email_input,
                    'input_user_homepage' => $hp_input,
					'input_cat_link' => $cat_link,
					'max_file_size' => $config['maxsize'],
					'uploadfile_register_new_file' => $lang['uploadfile_register_new_file'],
					'uploadfile_authors_detail' => $lang['uploadfile_authors_detail'],
					'uploadfile_authors_name' => $lang['uploadfile_authors_name'],
					'uploadfile_email' => $lang['uploadfile_email'],
					'uploadfile_homepage' => $lang['uploadfile_homepage'],
					'uploadfile_details_to_your_file' => $lang['uploadfile_details_to_your_file'],
					'uploadfile_name_of_your_file' => $lang['uploadfile_name_of_your_file'],
					'uploadfile_category' => $lang['uploadfile_category'],
					'uploadfile_description' => $lang['uploadfile_description'],
					'uploadfile_small' => $lang['uploadfile_small'],
					'uploadfile_middle' => $lang['uploadfile_middle'],
					'uploadfile_big' => $lang['uploadfile_big'],
					'uploadfile_bigger' => $lang['uploadfile_bigger'],
					'uploadfile_file_and_previewimage' => sprintf($lang['uploadfile_file_and_previewimage'],$public_size),
					'uploadfile_file' => $lang['uploadfile_file'],
					'uploadfile_file_upload' => $lang['uploadfile_file_upload'],
					'uploadfile_max_filesize' => sprintf($lang['uploadfile_max_filesize'],$public_size),
					'uploadfile_choose_file' => $lang['uploadfile_choose_file'],
					'uploadfile_alternative' => $lang['uploadfile_alternative'],
					'uploadfile_link_file' => $lang['uploadfile_link_file'],
					'uploadfile_please_note_to_use_compl_url' => $lang['uploadfile_please_note_to_use_compl_url'],
					'uploadfile_link' => $lang['uploadfile_link'],
					'uploadfile_file_size' => $lang['uploadfile_file_size'],
					'uploadfile_bytes' => $lang['uploadfile_bytes'],
					'uploadfile_previewimage' => $lang['uploadfile_previewimage'],
					'uploadfile_choose_image' => $lang['uploadfile_choose_image'],
					'uploadfile_btn_register_file' => $lang['uploadfile_btn_register_file'],
					'uploadfile_btn_reset' => $lang['uploadfile_btn_reset'],
					'uploadfile_choose_file_or_link' => $lang['uploadfile_choose_file_or_link'],
					'uploadfile_not_possible_link_file' => $lang['uploadfile_not_possible_link_file'],
                    'uploadfile_js1' => $lang['uploadfile_js1'],
                    'uploadfile_js2' => $lang['uploadfile_js2'],
                    'uploadfile_js3' => $lang['uploadfile_js3'],
                    'uploadfile_js4' => $lang['uploadfile_js4'],
                    'comment_bold' => $lang['comment_bold'],
                    'comment_italic' => $lang['comment_italic'],
                    'comment_underline' => $lang['comment_underline'],
                    'comment_url' => $lang['comment_url'],
                    'comment_email' => $lang['comment_email'],
                    'comment_code' => $lang['comment_code'],
                    'comment_quote' => $lang['comment_quote'],
                    'comment_center' => $lang['comment_center'],
                    'comment_line' => $lang['comment_line']));

if($_POST['step'] == "upload") {
	$message = "";
	if($_POST['upl_file2'] == "") { // keine Url angegeben, dann muß es eine Datei sein
		if($_FILES['upl_thumbnail'] != "") $thumb_upload = uploadThumbnail($thumbsdir);

		if(empty($_POST['upload_user']) || empty($_POST['dl_cat']) || empty($_POST['dltitle'])) { // Pflicht-Felder nicht ausgef&uuml;llt.
	        rideSite($sess->url('uploadfile.php'), $lang['rec_error27']);
	        exit();
		} else {
			$extens = explode("\r\n",$config['upload_extension']);
			$extens = implode(",",$extens);

			include_once($_ENGINE['eng_dir']."admin/enginelib/class.upload.php");
			$upload = new upload();
			$upload->setChangeFilename(0);
			$upload->setAllowedExtensions($extens);
			$upload->setMaxFileSize($config['maxsize']);
			$upload->setFilesDir($filesdir);

			if($upload->uploadFile("upl_file")) {
                $new_name = $upload->getDestName();
				$dl_status = 3;
				list ($direct_upload) = $db_sql->sql_fetch_row("SELECT direct_upload FROM $cat_table WHERE catid='".$_POST['dl_cat']."'");
				if($direct_upload == 1) {
					$dl_status = 1;
				} else {
					if($auth->user['groupid'] == 1) {
						$dl_status = 1;
					} else {
						$dl_status = 3;
					}
				}

				$db_sql->sql_query("INSERT INTO $dl_table (catid,dltitle,dldesc,status,dlurl,dl_date,hplink,dlsize,dlauthor,authormail,thumb)
									VALUES('".$_POST['dl_cat']."','".trim(addslashes(htmlspecialchars($_POST['dltitle'])))."','".trim(addslashes($_POST['comment_message']))."','$dl_status','".$config['fileurl']."/".$new_name."','".time()."','".trim(addslashes(htmlspecialchars($_POST['hplink'])))."','".$_FILES['upl_file']['size']."','".trim(addslashes(htmlspecialchars($_POST['upload_user'])))."','".trim(addslashes(htmlspecialchars($_POST['upload_usermail'])))."','$thumb_upload')");
				$new_file_id = $db_sql->insert_id();

                if($dl_status == 1) $db_sql->sql_query("UPDATE $cat_table SET download_count=download_count+1 WHERE catid='".$_POST['dl_cat']."'");

				if($config['filemail'] == 1) {
				    $dltitle = trim(htmlspecialchars($_POST['dltitle']));
					$upload_user = $_POST['upload_user'];

				    include_once($_ENGINE['eng_dir']."admin/enginelib/class.phpmailer.php");
				    $mail = new PHPMailer();
				    $mail->SetLanguage($lang['php_mailer_lang'], $_ENGINE['eng_dir']."lang/".$config['language']."/");
				    if($config['use_smtp']) {
				        $mail->IsSMTP();
				        $mail->Host = $config['smtp_server'];
				        $mail->SMTPAuth = true;
				        $mail->Username = $config['smtp_username'];
				        $mail->Password = $config['smtp_password'];
				    } else {
				        $mail->IsMail();
				    }

				    $mail->From = $_POST['upload_usermail'];
				    $mail->FromName = $_POST['upload_user'];
				    $mail->AddAddress($config['admin_mail']);
				    $mail->Subject = $lang['mail_file_betreff'];
				    $mail->Body = sprintf($lang['mail_file_inhalt'],$_POST['upload_user'],$_POST['dltitle'],$new_file_id).sprintf($lang['mail_footer'],$config['scriptname']);
				    $mail->WordWrap = 50;
				    $mail->Send();
				}

				if ($dl_status == 3) {
			        rideSite($sess->url('index.php'), $lang['rec_error35']);
			        exit();
				} else {
			        rideSite($sess->url('index.php'), $lang['rec_error35b']);
			        exit();
				}
			} else {
		        rideSite($sess->url('uploadfile.php'), $lang['rec_error26'].$upload->getErrorCode());
		        exit();
			}
			unset($dltitle);
			unset($dldesc);
		}
	} else { // url angegeben!
		if($_POST['filesize'] == "") { // keine Grösse angegeben
	        rideSite($sess->url('uploadfile.php'), $lang['rec_error42']);
	        exit();
		} else { // Grösse angegeben
			$dl_status = 3;
			list ($direct_upload) = $db_sql->sql_fetch_row("SELECT direct_upload FROM $cat_table WHERE catid='".$_POST['dl_cat']."'");
			if($direct_upload == 1) {
				$dl_status = 1;
			} else {
				if($auth->user['groupid'] == 1) {
					$dl_status = 1;
				} else {
					$dl_status = 3;
				}
			}

			$db_sql->sql_query("INSERT INTO $dl_table (catid,dltitle,dldesc,status,dlurl,dl_date,hplink,dlsize,dlauthor,authormail,thumb)
								VALUES('".$_POST['dl_cat']."','".trim(addslashes(htmlspecialchars($_POST['dltitle'])))."','".trim(addslashes($_POST['comment_message']))."','$dl_status','".trim(addslashes(htmlspecialchars($_POST['upl_file2'])))."','".time()."','".trim(addslashes(htmlspecialchars($_POST['hplink'])))."','".trim(intval($_POST['filesize']))."','".trim(addslashes(htmlspecialchars($_POST['upload_user'])))."','".trim(addslashes(htmlspecialchars($_POST['upload_usermail'])))."','$thumb_upload')");
			$new_file_id = $db_sql->insert_id();

            if($dl_status == 1) $db_sql->sql_query("UPDATE $cat_table SET download_count=download_count+1 WHERE catid='".$_POST['dl_cat']."'");

			if($config['filemail'] == 1) {
			    $dltitle = trim(htmlspecialchars($_POST['dltitle']));
				$upload_user = $_POST['upload_user'];

			    include_once($_ENGINE['eng_dir']."admin/enginelib/class.phpmailer.php");
			    $mail = new PHPMailer();
			    $mail->SetLanguage($lang['php_mailer_lang'], $_ENGINE['eng_dir']."lang/");
			    if($config['use_smtp']) {
			        $mail->IsSMTP();
			        $mail->Host = $config['smtp_server'];
			        $mail->SMTPAuth = true;
			        $mail->Username = $config['smtp_username'];
			        $mail->Password = $config['smtp_password'];
			    } else {
			        $mail->IsMail();
			    }

			    $mail->From = $_POST['upload_usermail'];
			    $mail->FromName = $_POST['upload_user'];
			    $mail->AddAddress($config['admin_mail']);
			    $mail->Subject = $lang['mail_file_betreff'];
			    $mail->Body = sprintf($lang['mail_file_inhalt'],$_POST['upload_user'],$_POST['dltitle'],$new_file_id).sprintf($lang['mail_footer'],$config['scriptname']);
			    $mail->WordWrap = 50;
			    $mail->Send();
			}

			if ($dl_status == 3) {
		        rideSite($sess->url('index.php'), $lang['rec_error35']);
		        exit();
			} else {
		        rideSite($sess->url('index.php'), $lang['rec_error35b']);
		        exit();
			}
			unset($dltitle);
			unset($dldesc);
		}
	}
}

$tpl->register('query', showQueries($develope));
$tpl->register('header', $tpl->pget('header'));

$tpl->register('footer', $tpl->pget('footer'));
$tpl->pprint('main');
?>