<?php
/*
    Bizou - a (french) KISS php image gallery
    Copyright (C) 2010  Marc MAURICE

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

$bizouRootFromHere = '../..';
require "$bizouRootFromHere/config.php";

$simpleImagePath = $_SERVER["PATH_INFO"];
if ($simpleImagePath == '/') $simpleImagePath = '';
// extra security check to avoid /photos/index/../.. like urls, maybe useless but..
if (strpos($simpleImagePath, '..') !== false) die(".. found in url");


if (! is_file("$bizouRootFromHere/".IMAGES_DIR.$simpleImagePath)) {
	header("HTTP/1.1 404 Not Found");
	die("File Not Found");
}

// get all images in an array
$images = array();

$files = scandir("$bizouRootFromHere/".IMAGES_DIR.dirname($simpleImagePath));
foreach ($files as $file) {
	$ext = strtolower(substr($file, -4));
	if ($ext == ".jpg" or $ext == ".png")
		$images[] = $file;
}

// find the image position
$pos = array_search(basename($simpleImagePath), $images);
if ($pos === false) die("Image not found");

// get prev and next images
$prevImage = '';
$nextImage = '';
if ($pos > 0)
	$prevImage = $images[$pos-1];
if ($pos < sizeof($images)-1)
	$nextImage = $images[$pos+1];

$scriptUrl = $_SERVER["SCRIPT_NAME"];
$bizouRootUrl = dirname(dirname(dirname($scriptUrl)));
// scriptUrl = /path/to/bizou/plugins/viewer/view.php
// bizouRootUrl = /path/to/bizou

// template variables
$imageUrl = "$bizouRootUrl/".IMAGES_DIR.$simpleImagePath;

if ($nextImage === '') {
	$nextImageUrl = '';
	$nextPageUrl = '';
} else {
	$nextImageUrl = "$bizouRootUrl/".IMAGES_DIR.dirname($simpleImagePath)."/$nextImage";
	$nextPageUrl = dirname($_SERVER["REQUEST_URI"])."/$nextImage";
}
if ($prevImage === '') $prevPageUrl = '';
else $prevPageUrl = dirname($_SERVER["REQUEST_URI"])."/$prevImage";

$directoryUrl = "$bizouRootUrl/index.php".dirname($simpleImagePath);

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
 <?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false) { ?>
<link rel="prefetch" href="<?php echo $nextImageUrl ?>" />
<link rel="prefetch" href="<?php echo $nextPageUrl ?>" />

 <?php } else { ?>
<script type="text/javascript">
window.onload = function() {
	var im = new Image();
	im.src = '<?php echo $nextImageUrl ?>';
	var req = new XMLHttpRequest();
	req.open('GET', '<?php echo $nextPageUrl ?>', false);
	req.send(null);
};
</script>
 <?php } ?>
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

<script language="javascript">
// keyboard navigation
function keyup(e)
{
	switch (e.keyCode) {
		case 37: // left
			window.location = "<?php echo $prevPageUrl ?>";
		break;
		case 39: // right
		case 32: // space
			window.location = "<?php echo $nextPageUrl ?>";
		break;
		case 38: // up  (down is 40)
			window.location = "<?php echo $directoryUrl ?>";
		break;
		case 13: // enter
			window.location = "<?php echo $imageUrl ?>";
		break;
	}
}

if (document.addEventListener) {
        document.addEventListener("keyup", keyup, false);
}
</script>

</body>
</html>
