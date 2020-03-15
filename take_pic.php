<?php
$head_title = "Snap! | Camagru";
$body_onload = "initialiseWebcamStreamOnload();";
require_once($_SERVER["DOCUMENT_ROOT"] . "/templates/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/templates/header.php");
?>
<div id="webcam_container">
	<video autoplay id="webcam"></video>
	<img id="img_preview">
</div>
<br />
<button id="take_pic_from_webcam">Snap!</button>
<button id="cancel_pic_from_webcam">Cancel</button>
<script src="/static/webcam.js"></script>
<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/templates/footer.php");
?>