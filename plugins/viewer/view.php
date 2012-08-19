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
require "$bizouRootFromHere/functions.php";

// extract /path/to/image.jpg from /view.php/path/to/image.jpg
$simpleImagePath = getPathInfo();

if (! is_file("$bizouRootFromHere/".IMAGES_DIR.$simpleImagePath)) {
	header("HTTP/1.1 404 Not Found");
	die("File Not Found");
}

// get all images in an array
$images = array();

$files = scandir("$bizouRootFromHere/".IMAGES_DIR.dirname($simpleImagePath));
foreach ($files as $file) if ($file[0] != '.')
{
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
if (substr($bizouRootUrl, -1) !== '/') $bizouRootUrl.='/';  // add a trailing / to rootUrl
// scriptUrl = /path/to/bizou/plugins/viewer/view.php
// bizouRootUrl = /path/to/bizou/

// template variables
$imageUrl = $bizouRootUrl.IMAGES_DIR.$simpleImagePath;

if ($nextImage === '') {
	$nextImageUrl = '';
	$nextPageUrl = '';
} else {
	$nextImageUrl = dirname($bizouRootUrl.IMAGES_DIR.$simpleImagePath)."/$nextImage";
	$nextPageUrl = dirname($_SERVER["REQUEST_URI"])."/$nextImage";
}
if ($prevImage === '') $prevPageUrl = '';
else $prevPageUrl = dirname($_SERVER["REQUEST_URI"])."/$prevImage";

$directoryUrl = $bizouRootUrl."index.php".dirname($simpleImagePath);

$firefox = strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false;

///// template starts here /////
header('Content-Type: text/html; charset=utf-8');
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));

require 'template.php';

?>
