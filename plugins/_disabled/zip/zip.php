<?php
/*
    Bizou zip plugin
    Copyright (C) 2013  Felix Friedrich

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
/*
	This script creates a zip file from a given folder (and subfolders).
	Call it like http(s)://URL_OF_BIZOU/zip.php/FOLDER
	where FOLDER is a folder in BIZOU_DIR/images.
*/

$bizouRootFromHere = '../..';
require "$bizouRootFromHere/config.php";

/*
	This function add a given folder (and it's subfolders) to an
	existing zip archive.
*/
function zipFolder($zip, $folder) {
	global $bizouRootFromHere;

	$zip->addEmptyDir($folder);
	$path = "$bizouRootFromHere/".IMAGES_DIR."/".$folder."/";

        foreach (scandir($path) as $entry) {

		if (($entry != '.') and ($entry != '..')) {

			if (is_dir($path.$entry)) {
				zipFolder($zip, $folder."/".$entry);
			} else {
		                $zip->addFile($path.$entry, $folder."/".$entry);
			}
		}
        }
}

/*
	Setting some needed variables.
*/
$folder = $_SERVER["PATH_INFO"];
$folder = trim($folder, "/");

if (($folder == "/") or ($folder == "")) {
	$filename = "all.zip";
}
else {
	$filename = $folder.".zip";
}

if (is_dir("$bizouRootFromHere/".IMAGES_DIR."/".$folder)) {

        $tmp = tempnam("/tmp", "bizou_"); // Getting a temporary file.
        unlink($tmp); // Deleting the temporary file in order to recreate it as a zip archive.

	// Creating zip archive.
	$zip = new ZipArchive();
        if ($zip->open($tmp, ZIPARCHIVE::CREATE) !== TRUE) {
                die("Cannot open temorary file :-(");
        }

	// Adding the given folder to the zip archive.
        zipFolder($zip, $folder);

        $zip->close();

	// Returning zip file for downloading.
        header('Content-type: application/zip');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile($tmp);
        unlink($tmp);

} else {
	
	/*
		The given folder does not seem to be a folder in BIZOU_DIR/images,
		this we die.
	*/
	header('HTTP/1.1 404 Not Found');
        die("Gallery does not exist. Go away!");
}
?>
