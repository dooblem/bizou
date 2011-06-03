<html>
<head>
<title> <?php echo $realDir ?> </title>
<style type="text/css">
body {
	margin-top: 0;
	font-family: sans-serif;
}
img {
	border: 0;
}
a {
	text-decoration: none;
}
.square {
	display: inline-block;
}
.image, .foldername, .image_nopreview, .foldername_nopreview {
	display: table-cell;
	vertical-align: middle;
}
.image, .image_nopreview {
	width: <?php echo THUMB_SIZE ?>px;
	text-align: center;
}
.image, .foldername {
	height: <?php echo THUMB_SIZE ?>px;
}
.foldername, .foldername_nopreview {
	padding-left: 1ex;
}
#parentfolder {
	font-size: 4em;
	font-weight: bold;
	height: 0.6em;
}
#credit {
	text-align: right;
	font-size: 0.25cm;
	color: gray;
}
</style>
<?php foreach ($plugins as $p) if (is_file("plugins/$p/style.css")) { ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $rootUrl."plugins/$p/style.css" ?>" />
<?php } ?>
</head>
<body>

<div id="parentfolder"><a href="<?php echo $parentLink ?>">
<?php if ($parentLink !== '') { ?>
^
<?php } ?>
&nbsp;</a></div>

<?php plugins_include("before_content.php") ?>

<?php foreach($folders as $folder) { ?>
	<div class="folder">
	<?php if ($folder["preview"] === "") { ?>
		<div class="square"><div class="image_nopreview"> - </div></div>
		<div class="square"><div class="foldername_nopreview"> <a href="<?php echo $folder["link"] ?>"><?php echo $folder["name"] ?></a> </div></div>
	<?php } else { ?>
		<div class="square"><div class="image"> <a href="<?php echo $folder["link"] ?>"><img src="<?php echo $folder["preview"] ?>" /></a> </div></div>
		<div class="square"><div class="foldername"> <a href="<?php echo $folder["link"] ?>"><?php echo $folder["name"] ?></a> </div></div>
	<?php } ?>
	</div>
<?php } ?>

<?php foreach ($imageFiles as $file) { ?>
	<div class="square"><div class="image"><a href="<?php echo $file["link"] ?>"><img src="<?php echo $file["url"] ?>" alt="<?php echo $file["name"] ?>" /></a></div></div>
<?php } ?>

<?php foreach ($otherFiles as $file) { ?>
	<div class="miscfile"><a href="<?php echo $file["link"] ?>"><?php echo $file["name"] ?></a></div>
<?php } ?>

<p id="credit">
Generated by <a href="http://www.positon.org/bizou/">Bizou</a>
</p>

</body>
</html>
