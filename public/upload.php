<?php
if (!$_POST)
{
	header("Location: index.php");
	exit();
}
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
move_uploaded_file($_FILES["imageBlob"]["tmp_name"], $uploads_path . "test.png");