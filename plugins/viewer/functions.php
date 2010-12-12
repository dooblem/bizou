<?php
function getThumbTarget($imageSimplePath)
{
	return dirname($_SERVER["SCRIPT_NAME"])."/plugins/viewer/view.php$imageSimplePath";
}
?>
