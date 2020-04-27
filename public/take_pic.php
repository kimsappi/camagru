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
<div class='container noMargin'>
	<div class='row'>
		<input type='file' id='fileUpload' oninput='userSelectsUploadFile();'>
	</div>
	<div class='row'>
		<div id="webcam_container" class="col-12 col-md-10">
			<video autoplay id="webcam" class='square resizeSelectorClass'></video>
			<img id="img_preview" class='square resizeSelectorClass'>
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
		<button id="upload_old_pic">Upload</button>
	</div>
</div>

<!-- Load scripts required for webcam functionality and formatting -->
<script src="/static/webcam.js"></script>
<script src="/static/sideGallery.js"></script>

<?php
require_once($templates_path . "footer.php");
?>
