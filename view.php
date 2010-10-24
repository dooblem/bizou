<?php

define('IMAGES_DIR', 'images');

$shortPath = $_SERVER["PATH_INFO"];
if ($shortPath == '/') $shortPath = '';
// extra security check to avoid /photos/index/../.. like urls, maybe useless but..
if (strpos($shortPath, '..') !== false) die(".. found in url");

$scriptPath = $_SERVER["SCRIPT_NAME"];

// get all images in an array
$images = array();

$files = scandir(IMAGES_DIR.dirname($shortPath));
foreach ($files as $file) {
	$ext = strtolower(substr($file, -4));
	if ($ext == ".jpg" or $ext == ".png")
		$images[] = $file;
}

// find the image position
$pos = array_search(basename($shortPath), $images);
if ($pos === false) die("Image not found");

// get prev and next images
$prevImage = '';
$nextImage = '';
if ($pos > 0)
	$prevImage = $images[$pos-1];
if ($pos < sizeof($images)-1)
	$nextImage = $images[$pos+1];

// template variables
$imageUrl = dirname($scriptPath)."/".IMAGES_DIR.$shortPath;

if ($nextImage === '') {
	$nextImageUrl = '';
	$nextPageUrl = '';
} else {
	$nextImageUrl = dirname($scriptPath)."/".IMAGES_DIR.dirname($shortPath)."/$nextImage";
	$nextPageUrl = dirname($_SERVER["REQUEST_URI"])."/$nextImage";
}
if ($prevImage === '') $prevPageUrl = '';
else $prevPageUrl = dirname($_SERVER["REQUEST_URI"])."/$prevImage";

$directoryUrl = dirname($_SERVER["SCRIPT_NAME"])."/index.php".dirname($shortPath);

header('Content-Type: text/html; charset=utf-8');
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
?>
<html>
<head>
<style type="text/css">
html, body {
height: 100%;
}
body {
margin: 0;
text-align: center;
background: black;
color: white;
}
#theimage {
max-width: 100%;
max-height: 100%;
}
a {
	color: white;
	text-decoration: none;
}
#next, #previous, #up {
	position: fixed;
	font-size: 4em;
	font-weight: bold;
}

#up {
	top: 0;
	left: 0;
	
}
#next {
	top: 50%;
	right: -0;
	
}
#previous {
	top: 50%;
	left: 0;
}
img {
	border: 0;
}
</style>

<?php if ($nextImageUrl !== '') { ?>
<link rel="prefetch" href="<?php echo $nextImageUrl ?>" />
<link rel="prefetch" href="<?php echo $nextPageUrl ?>" />
<?php } ?>

</head>
<body>

<a href="<?php echo $imageUrl ?>"><img src="<?php echo $imageUrl ?>" id="theimage" /></a>

<div id="up">
<a href="<?php echo $directoryUrl ?>" title="Back to directory">^</a>
</div>

<?php if ($nextPageUrl !== '') { ?>
<div id="next">
<a href="<?php echo $nextPageUrl ?>" title="Next image">&gt;</a>
</div>
<?php } ?>

<?php if ($prevPageUrl !== '') { ?>
<div id="previous">
<a href="<?php echo $prevPageUrl ?>" title="Previous image">&lt;</a>
</div>
<?php } ?>

</body>
</html>
