<?php
$head_title = "Snap! | Camagru";
$body_onload = "initialiseWebcamStreamOnload();";

require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");

if (!isset($_SESSION["username"]))
{
	header("Location: /login.php?destination=take_pic.php");
	exit();
}

$filtersSelectHTML = "<option value=''>Select filter</option>";
$filtersDir = new DirectoryIterator($filters_path);
foreach ($filtersDir as $fileData)
{
	if (!$fileData->isDot())
	{
		$filterFileName = $fileData->getFilename();
		$filterDisplayName = explode('.', $filterFileName)[0];
		$filterDisplayName = ucwords(preg_replace('/[-_]+/', ' ', $filterDisplayName));

		$filtersSelectHTML .= <<<EOD
		<option value='{$filterFileName}'>{$filterDisplayName}</option>	
EOD;
	}
}

// Page body
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");
?>
<div class='container noMargin'>
	<div class='row uploadInfo'>
		You can also upload an image file (png, jpeg, max 4 MB)
	</div>
	<div class='row'>
		<label for='fileUpload' id='fileUploadLabel'>Upload!</label>
		<input type='file' id='fileUpload' oninput='userSelectsUploadFile();'>
	</div>
	<div class='row' id='firefoxErrorContainer'>
		<h4>Select a filter:</h4>
		<select id='filter' name='filter'>
			<?= $filtersSelectHTML ?>
		</select>
	</div>
	<div class='row'>
		<div class="col-12 col-md-10">
			<div id="webcam_container">
				<video autoplay id="webcam" class='square resizeSelectorClass'></video>
				<canvas id='canvas'></canvas>
				<img id="img_preview" class='resizeSelectorClass'>
				<img id='filter_preview' class='resizeSelectorClass'>
			</div>
		</div>
		<div class='col-12 col-md-2 sideGallery' id='postSideGallery'>
			<?php
			require_once($templates_path . "sideGallery.php");
			?>
		</div>
	</div>
	<div class='row col-12 flexRow flexSpaceAround marginTop1Em'>
		<button id="take_pic_from_webcam" class='takePicControlButton'>Snap!</button>
		<button id="cancel_pic_from_webcam" class='takePicControlButton'>Cancel</button>
		<button id="upload_old_pic" class='takePicControlButton'>Upload</button>
	</div>
</div>

<!-- Load scripts required for webcam functionality and formatting -->
<script src="/static/webcam.js"></script>
<script src="/static/sideGallery.js"></script>

<?php
require_once($templates_path . "footer.php");
?>
