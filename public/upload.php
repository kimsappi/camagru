<?php
if (!$_POST || !$_FILES || !isset($_FILES["imageBlob"]) || !isset($_FILES["imageBlob"]["type"]) || $_FILES["imageBlob"]["type"] !== "image/png" || $_FILES["imageBlob"]["type"] > 5000000)
{
	header("Location: index.php");
	exit();
}
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");

$uploaded_file = $_FILES["imageBlob"];
move_uploaded_file($_FILES["imageBlob"]["tmp_name"], $uploads_path . "test.png");
$dest = imagecreatefrompng($uploads_path . "test.png");
$src = imagecreatefrompng($filters_path . "rainbow-gradient.png");
imagecopymerge($dest, $src, 0, 0, 0, 0, 500, 500, 30);
imagepng($dest, $uploads_path . "test_filtered.png");