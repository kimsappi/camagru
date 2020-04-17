<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$_POST || !$_FILES || !isset($_FILES["imageBlob"]) || !isset($_FILES["imageBlob"]["type"]) || $_FILES["imageBlob"]["type"] !== "image/png" || $_FILES["imageBlob"]["type"] > 5000000)
{
	header("Location: /");
	exit();
}
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($functions_path . "imageFunctions.php");

$uploadedImage = imagecreatefrompng($_FILES["imageBlob"]["tmp_name"]);
$resizedImage = cropAndResizeImage($uploadedImage);
$src = imagecreatefrompng($filters_path . "rainbow-gradient.png");
//imagecopymerge($resizedImage['image'], $src, 0, 0, 0, 0, 500, 500, 30);
imagecopymerge($resizedImage['image'], $src, 0, 0, 0, 0, $resizedImage['size'], $resizedImage['size'], 30);
imagepng($resizedImage['image'], $uploads_path . "test_filtered.png");
error_log($resizedImage['size']);
