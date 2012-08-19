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

require 'config.php';

// global variables, globals should remain contant
$scriptUrl = $_SERVER["SCRIPT_NAME"];
$rootUrl = dirname($scriptUrl);
if (substr($rootUrl, -1) !== '/') $rootUrl.='/';  // add a trailing / to rootUrl
// $scriptUrl =  "/path/to/bizou/index.php"
// $rootUrl =  "/path/to/bizou/"

require 'functions.php';


// if url == http://localhost/photos/index.php/toto/titi, path_info == /toto/titi
// if url == http://localhost/photos/index.php, path_info is not set
// if url == http://localhost/photos/, path_info is not set
// if path_info is not set, we are at top level, so we redirect to /photos/index.php/
if (! isset($_SERVER["PATH_INFO"])) {
	header("Location: $scriptUrl/");
	exit();
}

// simplePath is the simple path to the directory
// extract /path/to/dir/ from /index.php/path/to/dir/
$simplePath = getPathInfo();

# realDir is the directory in filesystem
# seen from current script directory
$realDir = IMAGES_DIR.$simplePath;

if (! is_dir($realDir)) {
	header("HTTP/1.1 404 Not Found");
	die("Directory Not Found");
}

$folders = array();
$imageFiles = array();
$otherFiles = array();

foreach (scandir($realDir) as $file) if ($file[0] != '.')
{
	if (is_dir("$realDir/$file"))
	{
		$folders[] = array( "name" => $file, "file" => "$realDir/$file", "link" => "$scriptUrl$simplePath/$file" );
	}
	else
	{
		$ext = strtolower(substr($file, -4));
		if ($ext == ".jpg" or $ext == ".png") {
			$imageFiles[] = array( "name" => $file, "file" => "$realDir/$file", "link" => getImageLink("$simplePath/$file") );
		} else {
			$otherFiles[] = array( "name" => $file, "link" => "$rootUrl$realDir/$file" );
		}
	}
}

if (dirname($simplePath) !== '')
	$parentLink = $scriptUrl.dirname($simplePath);
else
	$parentLink = "";

///// template starts here /////
header('Content-Type: text/html; charset=utf-8');
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));

require 'template.php';

?>
