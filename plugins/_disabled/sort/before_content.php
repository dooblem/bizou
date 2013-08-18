<?php

# reverts the order of folders
# usefull if you have date named albums and you want the most recent first
$GLOBALS["folders"] = array_reverse($GLOBALS["folders"]);

# sort imageFiles by modification time instead of filename
# usefull if you have renammed camera photos
# http://stackoverflow.com/questions/2667065/sort-files-by-date-in-php
# http://fr.php.net/create_function
#usort( $GLOBALS["imageFiles"], create_function('$a,$b', 'return filemtime($a["file"]) > filemtime($b["file"]);') );
