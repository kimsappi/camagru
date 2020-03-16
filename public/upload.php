<?php
if (!$_POST)
{
	header("Location: index.php");
	exit();
}
session_start();
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
move_uploaded_file($_FILES["imageBlob"]["tmp_name"], $uploads_path."test.png");