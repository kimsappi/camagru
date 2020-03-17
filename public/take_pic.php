<?php
$head_title = "Snap! | Camagru";
$body_onload = "initialiseWebcamStreamOnload();";

require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");

if (!isset($_SESSION["username"]))
{
	header("Location: /login.php?destination=take_pic.php");
	exit();
}
?>
<div id="webcam_container" class="square">
	<video autoplay id="webcam"></video>
	<img id="img_preview">
</div>
<br />
<button id="take_pic_from_webcam">Snap!</button>
<button id="cancel_pic_from_webcam">Cancel</button>
<script src="/static/webcam.js"></script>
<?php
require_once($templates_path . "footer.php");
?>