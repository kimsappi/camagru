<?php
$head_title = "Register | Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/src/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/src/header.php");
if (isset($_SESSION["username"]))
{
	header("Location: /index.php");
	exit();
}
else
{
	require_once($_SERVER["DOCUMENT_ROOT"] . "/src/forms/form_register.html");
}
?>