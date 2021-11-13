<html>
	<head>
		<title><?=$_SERVER ["HTTP_HOST"];?>: One click file hostion</title>
		<link rel="stylesheet" type="text/css" media="all" href="style.css">
	</head>
	<body>
		<table width="100%" height="100%"><tr valign="center"><td align="center" nowrap><div id="upload">
		<?php
			if (@$_POST ['upload'] == "")
			{
				echo 'Select file to upload | Maximum file size 2000 MB | Split archives allowed<br><br>';
				echo '<form enctype="multipart/form-data" action="" method="post">';
				echo '<input type="hidden" name="MAX_FILE_SIZE" value="30000">';
				echo '<input type="file" name="file" size="45">';
				echo '<input type="submit" value=" Upload " name="upload">';
				echo '</form>';
			} else {
				$upload_dir = "/uploads/";
				$upload_path = dirname (__FILE__).$upload_dir;
				$upload_filename = md5 (microtime());
				$upload_link = "http://".$_SERVER ["HTTP_HOST"].dirname ($_SERVER ["PHP_SELF"]).$upload_dir.$upload_filename;
				if (@move_uploaded_file ($_FILES['file']['tmp_name'], $upload_path.$upload_filename))
				{
					echo "<b>File is valid, and was successfully uploaded.</b><br><br>";
					echo "Download Link:<br>";
					echo "<input type='text' size=80 onclick='this.select()' value='".$upload_link."'><br><br>";
					echo "Download Link in HTML (for use in web sites, myspace, blogs, etc):<br>";
					echo "<input type='text' size=80 onclick='this.select()' value=\"<a href='".$upload_link."'>".$upload_link."</a>\"><br><br>";
					echo "Download Link in Forum code (for use in phpBB, vBulletin, etc):<br>";
					echo "<input type='text' size=80 onclick='this.select()' value='[url]".$upload_link."[/url]'><br><br>";
					echo "<a href='?".md5(microtime())."'>Upload another file</a>";
				} else {
					echo "<b>There some errors!</b>";
				}
			}
		?>
	</div></td></tr></table></body>
</html>