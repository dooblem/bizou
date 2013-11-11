<?php

if ($GLOBALS['simplePath'] === "") {
        header("HTTP/1.1 403 Forbidden");
        die("Forbdidden directory");
}

if (dirname($GLOBALS['simplePath']) === "/") {
	$GLOBALS['parentLink'] = "";
}
