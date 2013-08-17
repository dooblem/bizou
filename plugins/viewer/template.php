<html>
<head>
<title> <?= IMAGES_DIR.$simpleImagePath ?> </title>
<style type="text/css">
html, body {
height: 100%;
}
body {
margin: 0;
text-align: center;
background: black;
color: white;
}
#theimage {
max-width: 100%;
max-height: 100%;
}
a {
	color: white;
	text-decoration: none;
}
#next, #previous, #up {
	position: fixed;
	font-size: 4em;
	font-weight: bold;
}

#up {
	top: 0;
	left: 0;
	
}
#next {
	top: 50%;
	right: -0;
	
}
#previous {
	top: 50%;
	left: 0;
}
img {
	border: 0;
}
</style>

<?php if ($nextImageUrl !== '' and $firefox) { ?>
<link rel="prefetch" href="<?= $nextImageUrl ?>" />
<link rel="prefetch" href="<?= $nextPageUrl ?>" />
<?php } ?>

</head>
<body>

<a href="<?= $imageUrl ?>"><img src="<?= $imageUrl ?>" id="theimage" /></a>

<div id="up">
<a href="<?= $directoryUrl ?>" title="Back to directory">^</a>
</div>

<?php if ($nextPageUrl !== '') { ?>
<div id="next">
<a href="<?= $nextPageUrl ?>" title="Next image">&gt;</a>
</div>
<?php } ?>

<?php if ($prevPageUrl !== '') { ?>
<div id="previous">
<a href="<?= $prevPageUrl ?>" title="Previous image">&lt;</a>
</div>
<?php } ?>

<script type="text/javascript">

<?php if ($nextImageUrl !== '' and ! $firefox) { ?>
window.onload = function() { // for browsers not supporting link rel=prefetch
	var im = new Image();
	im.src = '<?= $nextImageUrl ?>';
	var req = new XMLHttpRequest();
	req.open('GET', '<?= $nextPageUrl ?>', false);
	req.send(null);
};
<?php } ?>

// keyboard navigation
function keyup(e)
{
	switch (e.keyCode) {
		case 37: // left
			window.location = "<?= $prevPageUrl ?>";
		break;
		case 39: // right
		case 32: // space
			window.location = "<?= $nextPageUrl ?>";
		break;
		case 38: // up  (down is 40)
			window.location = "<?= $directoryUrl ?>";
		break;
		case 13: // enter
			window.location = "<?= $imageUrl ?>";
		break;
	}
}

if (document.addEventListener) {
        document.addEventListener("keyup", keyup, false);
}
</script>

</body>
</html>
