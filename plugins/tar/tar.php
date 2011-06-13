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

# do not enable recursive tars by default
$TAR_FLAGS = "--no-recursion";

# send content length for browsers to display the progress bar
# note : won't work if the http server uses Chunked transfer encoding (http://en.wikipedia.org/wiki/Chunked_transfer_encoding)
# probably the case with gzip content-encoding.
# For this we need to tar files to /dev/null before the real tar
$SEND_CONTENT_LENGTH = true;

########################
$bizouRootFromHere = '../..';
require "$bizouRootFromHere/config.php";

if (isset($_SERVER["PATH_INFO"])) {
	$simplePath = $_SERVER["PATH_INFO"];
} else {
	$simplePath = '/';
}

if (strpos($simplePath, '..') !== false) die(".. found in url");

$realDir = "$bizouRootFromHere/".IMAGES_DIR.$simplePath;

if ( ! is_dir($realDir) ) {
	header("HTTP/1.1 404 Not Found");
	die("Directory Not Found");
}

# change to the parent directory
chdir(dirname($realDir));

$filesarg = escapeshellarg(basename($realDir))."/*";

# compute and send content-length header
if ($SEND_CONTENT_LENGTH) {
	$out = exec("tar $TAR_FLAGS --totals -cf /dev/null $filesarg 2>&1");
	preg_match('/^Total bytes written: ([0-9]+) /', $out, $matches);
	$totalsize = $matches[1];

	header("Content-Length: $totalsize");
}

# final step : stream the directory content via tar
header('Content-Type: application/x-tar');
header('Content-Disposition: attachment; filename="'.basename($realDir).'.tar"');

passthru("tar $TAR_FLAGS -c $filesarg");

?>
