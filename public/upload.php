<?php
if (!$_POST || !$_FILES || !isset($_FILES["imageBlob"]) || !isset($_FILES["imageBlob"]["type"]) || $_FILES["imageBlob"]["type"] !== "image/png" || $_FILES["imageBlob"]["type"] > 5000000)
{
	header("Location: index.php");
	exit();
}
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($functions_path . "imageFunctions.php");

$uploadedImage = imagecreatefrompng($_FILES["imageBlob"]["tmp_name"]);
cropAndResizeImage($uploadedImage);
$src = imagecreatefrompng($filters_path . "rainbow-gradient.png");
imagecopymerge($uploadedImage, $src, 0, 0, 0, 0, 500, 500, 30);
imagepng($uploadedImage, $uploads_path . "test_filtered.png");