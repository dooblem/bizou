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
		if (strtolower(substr($file, -4)) == ".jpg")
			return getPreview("$dir/$file", 100);
	}

	return '';
}

$shortPath = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "";
if ($shortPath == '/') $shortPath = '';
$scriptUrlPath = substr($_SERVER["SCRIPT_NAME"], 0, -4); // trim .php

$folders = array();
$imageFiles = array();
$otherFiles = array();

$realDir = "images$shortPath";

foreach (scandir($realDir) as $file) if ($file != '.' and $file != '..')
{
	if (is_dir("$realDir/$file"))
	{
		$folders[] = array( "name" => $file, "link" => "$scriptUrlPath$shortPath/$file", "preview" => getAlbumPreview("$realDir/$file") );
	}
	else
	{
		$ext = strtolower(substr($file, -4));
		if ($ext == ".jpg")
			$imageFiles[] = array( "name" => $file, "url" => getPreview("$realDir/$file", 100), "link" => dirname($scriptUrlPath)."/view/$shortPath/$file" );
		else
			$otherFiles[] = array( "name" => $file, "link" => dirname($scriptUrlPath)."/$realDir/$file" );
	}
}

if (dirname($shortPath) !== '')
	$parentLink = $scriptUrlPath.dirname($shortPath);
else
	$parentLink = "";

?>
<?php
///// template starts here /////
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

<?php if ($parentLink !== '') { ?>
	<div id="parentfolder"><a href="<?php echo $parentLink ?>">^</a></div>
<?php } ?>

<?php foreach($folders as $folder) { ?>
	<div class="folder">
	<a href="<?php echo $folder["link"] ?>">
	<?php if ($folder["preview"] !== "") { ?>
		<img src="<?php echo $folder["preview"] ?>" />
	<?php } ?>
	<?php echo $folder["name"] ?>
	</a>
	</div>
<?php } ?>

<?php foreach ($imageFiles as $file) { ?>
	<div class="square"><div class="image"><a href="<?php echo $file["link"] ?>"><img src="<?php echo $file["url"] ?>" alt="<?php echo $file["name"] ?>" /></a></div></div>
<?php } ?>

<?php foreach ($otherFiles as $file) { ?>
	<div class="miscfile"><a href="<?php echo $file["link"] ?>"><?php echo $file["name"] ?></a></div>
<?php } ?>

</body>
</html>
