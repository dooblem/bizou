<?php
header('Content-Type: text/html; charset=utf-8');
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
?>
<html>
<head>
<style type="text/css">
img {
	border: 0;
	vertical-align: middle;
}

.square {
	display: inline-block;
}

.image {
	width: 100px;
	height: 100px;
	display: table-cell;
	text-align: center;
	vertical-align: middle;
}
</style>
</head>
<body>

<?php

function getPreview($imgFile, $maxSize)
{
	# example: data/myalbum/100.mypic.jpg
	$newImgFile = "data/".dirname($imgFile)."/".$maxSize.".".basename($imgFile);
	
	if (! is_file($newImgFile))
	{
		$img = imagecreatefromjpeg($imgFile);

		$w = imagesx($img);
		$h = imagesy($img);
		# don't do anything if the image is already small
		if ($w <= $maxSize and $h <= $maxSize) {
			imagedestroy($img);
			return $imgFile;
		}

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

		imagejpeg($newImg, $newImgFile); 
		
		imagedestroy($img);
		imagedestroy($newImg);
	}

	return dirname($_SERVER["SCRIPT_NAME"])."/".$newImgFile;
}

function getAlbumPreview($dir)
{
	foreach (scandir($dir) as $file) if ($file != '.' and $file != '..') {
		if (mime_content_type("$dir/$file") == "image/jpeg")
			return getPreview("$dir/$file", 100);
	}

	return '';
}

$shortPath = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "";
if ($shortPath == '/') $shortPath = '';
$scriptUrlPath = substr($_SERVER["SCRIPT_NAME"], 0, -4); // trim .php

$imageFiles = array();
$otherFiles = array();

$realDir = "images$shortPath";

foreach (scandir($realDir) as $file) if ($file != '.')
{
	if ($file == '..')
	{
		echo "<div><a href=\"$scriptUrlPath".dirname($shortPath)."/\">..</a></div>\n";
	}
	elseif (is_dir("$realDir/$file"))
	{
		echo "<div>";
		$preview = getAlbumPreview("$realDir/$file");
		if ($preview !== '') {
			echo "<img src=\"$preview\" /> ";
		}

		echo "<a href=\"$scriptUrlPath$shortPath/$file\">$file</a>";
		echo "</div>\n";
	}
	else
	{
		$mime = mime_content_type("$realDir/$file");

		if ($mime == "image/jpeg")
			$imageFiles[] = $file;
		else
			$otherFiles[] = $file;
	}
}

foreach ($imageFiles as $file) {
	echo "<div class=\"square\"><div class=\"image\"><a href=\"".dirname($scriptUrlPath)."/view/$shortPath/$file\"><img src=\"".getPreview("$realDir/$file", 100)."\" /></a></div></div>\n";
}

foreach ($otherFiles as $file) {
	echo "<div><a href=\"".dirname($scriptUrlPath)."/$realDir/$file\">$file</a></div>\n";
}

?>

</body>
</html>
