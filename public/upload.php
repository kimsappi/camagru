<?php
if (!$_POST)
{
	header("Location: index.php");
	exit();
}
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
move_uploaded_file($_FILES["imageBlob"]["tmp_name"], $uploads_path . "test.png");
$dest = imagecreatefrompng($uploads_path . "test.png");
$src = imagecreatefrompng($filters_path . "rainbow-gradient.png");
imagecopymerge($dest, $src, 0, 0, 0, 0, 500, 500, 30);
imagepng($dest, $uploads_path . "test_filtered.png");