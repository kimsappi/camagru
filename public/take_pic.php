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

<!-- Page body -->
<div class='container'>
	<div class='row'>
		<div id="webcam_container" class="square col-12 col-md-10">
			<video autoplay id="webcam"></video>
			<img id="img_preview">
		</div>
		<div class='col-12 col-md-2 sideGallery' id='postSideGallery'>
			<?php
			require_once($templates_path . "sideGallery.php");
			?>
		</div>
	</div>
	<div class='row'>
		<button id="take_pic_from_webcam">Snap!</button>
		<button id="cancel_pic_from_webcam">Cancel</button>
	</div>
</div>

<!-- Load scripts required for webcam functionality -->
<script src="/static/webcam.js"></script>

<?php
require_once($templates_path . "footer.php");
?>
