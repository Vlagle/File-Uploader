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
// $Id: function.img.php 6 2005-10-08 10:12:03Z alex $

define("FILE_NAME","img_global.php");

function calcThumbnail($dl,$add="./") {
	global $config, $resize;
		$pic = array();
		if ($config['active_image_resizer']) {
			$orig_data = @getimagesize($add."thumbnail/".$dl['thumb']);

			if($orig_data[0] > $orig_data[1]) {
				$pic['width'] = $config['image_width'];
				$pic['height'] = @floor(($orig_data[1]*$config['image_width'])/$orig_data[0]);
			} else {
				$pic['height'] = $config['image_height'];
				$pic['width'] = @floor(($orig_data[0]*$config['image_height'])/$orig_data[1]);
			}
			$resize == true;
		} else {
			$size = @getimagesize($add."thumbnail/".$dl['thumb']);
			if($size[0] != "") $pic['width'] = $size[0];
			if($size[1] != "") $pic['height'] = $size[1];
			$resize == false;
		}	
	return $pic;	
}	

function createThumbnail($source,$size_array,$dest,$gd_version) {
	global $config,$settings;
	$pic_exist = FALSE;
	$type = getImgType($source);
	$size = @getimagesize($source);
	if (function_exists("imagecreatetruecolor") && $gd_version >= 2) {
	  $thumb = imagecreatetruecolor($size_array['width'], $size_array['height']);
	} else {
	  $thumb = imagecreate($size_array['width'], $size_array['height']);
	}
	
	if ($image = @call_user_func("imagecreatefrom".$type, $source)) {
	  if (function_exists("imagecopyresampled") && $gd_version >= 2) {
	    imagecopyresampled($thumb, $image, 0, 0, 0, 0, $size_array['width'], $size_array['height'], $size[0], $size[1]);
	  } else {
	    imagecopyresized($thumb, $image, 0, 0, 0, 0, $size_array['width'], $size_array['height'], $size[0], $size[1]);
	  }
	  @call_user_func("image".$type, $thumb, $dest, 100);
	  imagedestroy($thumb);
	}		
	if(file_exists($dest)) $pic_exist = TRUE;
	
	return $pic_exist;
}

function getImgType($source) {
	$size = @getimagesize($source);

	switch($size[2]) {
		case 1:
			return 'gif';
			break;
		case 2:
			return 'jpeg';
			break;
		case 3:
			return 'png';
			break;
		}
}
		
/*
Siehe www.php.net/createimagetruecolor 
comment from Andreas 28-Feb-2003 01:15
*/
function chkgd2() {
	ob_start();
	phpinfo(8);
	$phpinfo = ob_get_contents();
	ob_end_clean();
	$phpinfo = strip_tags($phpinfo);
	$phpinfo = stristr($phpinfo,"gd version");
	$phpinfo = stristr($phpinfo,"version");
	$end = strpos($phpinfo,".");
	$phpinfo = substr($phpinfo,0,$end);
	$length = strlen($phpinfo)-1;
	$phpinfo = substr($phpinfo,$length);
	return $phpinfo;
}


?>