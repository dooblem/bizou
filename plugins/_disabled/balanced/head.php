<script language="javascript" src="../plugins/balanced/scripts.js"></script> 
<script language="javascript">
        var thumbsize = <?php echo THUMB_SIZE ?>;
	window.onload = function(event) {
                applyOptimalImagesSize(thumbsize);
        }
        window.onresize = function(event) {
                applyOptimalImagesSize(thumbsize);
        }
</script>
