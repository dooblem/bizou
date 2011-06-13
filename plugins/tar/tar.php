<?php

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

$out = exec("tar --no-recursion --totals -cf /dev/null $filesarg 2>&1");
preg_match('/^Total bytes written: ([0-9]+) /', $out, $matches);
$totalsize = $matches[1];

header("Content-Length: $totalsize");
header('Content-Type: application/x-tar');
header('Content-Disposition: attachment; filename="'.basename($realDir).'.tar"');

passthru("tar --no-recursion -c $filesarg");

?>
