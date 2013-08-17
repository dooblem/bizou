<?php
$filename = $GLOBALS["file"]["name"];
# comment this to have the file extensions
$filename = substr($filename, 0, strrpos($filename, '.')); 
?>
<div class="imagefilename"><a href="<?= $GLOBALS["file"]["link"] ?>"><?= $filename ?></a></div>
