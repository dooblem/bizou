<?php

// load plugins
$plugins = array();
if (is_dir("plugins")) {
	$plugins = scandir("plugins");
	array_shift($plugins); array_shift($plugins); // remove . and ..
	foreach ($plugins as $p) if (is_file("plugins/$p/functions.php"))
		require "plugins/$p/functions.php";
}

function plugins_include($phpFile)
{
	foreach ($GLOBALS['plugins'] as $p) if (is_file("plugins/$p/$phpFile"))
		require "plugins/$p/$phpFile";
}

function getPathInfo()
{
	$simplePath = $_SERVER["PATH_INFO"];
	if ($simplePath == '/') $simplePath = '';
	// extra security check to avoid /photos/index/../.. like urls, maybe useless but..
	if (strpos($simplePath, '..') !== false) die(".. found in url");
	return $simplePath;
}


if (! function_exists('getImageLink')) {
function getImageLink($imageSimplePath)
{
	return $GLOBALS['rootUrl'].IMAGES_DIR.$imageSimplePath;
}
}

function getPreview($imgFile, $maxSize = THUMB_SIZE)
{
	# example: data/myalbum/100.mypic.jpg
	$newImgFile = DATA_DIR."/".dirname($imgFile)."/".$maxSize.".".basename($imgFile);
	
	# if the preview is a symlink, image is already good sized
	if (is_link($newImgFile)) return $imgFile;
	
	if (! is_file($newImgFile))
	{
		# this tels the template to flush output after displaying previews
		$GLOBALS["generating"] = true;

		# reset script time limit to 20s (wont work in safe mode)
		set_time_limit(20);

		$ext = strtolower(substr($imgFile, -4));
		if ($ext == ".jpg")
			$img = imagecreatefromjpeg($imgFile);
		else
			$img = imagecreatefrompng($imgFile);

		$w = imagesx($img);
		$h = imagesy($img);
		# if the image is already small, make a symlink, and return it
		if ($w <= $maxSize and $h <= $maxSize) {
			imagedestroy($img);
			symlink($imgFile, $newImgFile);
			return $imgFile;
		}

		# config to allow group writable files
		umask(DATA_UMASK);
		# create the thumbs directory recursively
		if (! is_dir(dirname($newImgFile))) mkdir(dirname($newImgFile), 0777, true);

		if ($w > $h) {
			$newW = $maxSize;
			$newH = $h/($w/$maxSize);
		} else {
			$newW = $w/($h/$maxSize);
			$newH = $maxSize;
		}

		$newImg = imagecreatetruecolor($newW, $newH);

		imagecopyresampled($newImg, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);

		if ($ext == ".jpg")
			imagejpeg($newImg, $newImgFile);
		else
			imagepng($newImg, $newImgFile);
		
		imagedestroy($img);
		imagedestroy($newImg);
	}

	return $newImgFile;
}

function getAlbumPreview($dir)
{
	$previewFile = DATA_DIR."/$dir/albumpreview";

	if (is_file("$previewFile.jpg")) {
		return "$previewFile.jpg";
	} else if (is_file("$previewFile.empty")) {
		return "";
	} else if (is_file("$previewFile.png")) {
		return "$previewFile.png";
	} else {
		# config to allow group writable files
		umask(DATA_UMASK);
		# create the thumbs directory recursively
		if (! is_dir(dirname($previewFile))) mkdir(dirname($previewFile), 0777, true);

		// no preview: look for a preview in current dir, write it, return it
		foreach (scandir($dir) as $file) if ($file[0] != '.') {
			$ext = strtolower(substr($file, -4));
			if ($ext == ".jpg" or $ext == ".png") {
				$thumb = getPreview("$dir/$file");
				copy($thumb, $previewFile.$ext);
				return $previewFile.$ext;
			} else if (is_dir("$dir/$file")) {
				$subPreview = getAlbumPreview("$dir/$file");
				if ($subPreview) {
					$myPreview = dirname($previewFile)."/".basename($subPreview);
					copy($subPreview, $myPreview);
					return $myPreview;
				}
			}
		}

		// nothing found. create empty file
		touch("$previewFile.empty");
		return "";
	}
}

?>
