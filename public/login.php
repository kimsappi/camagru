<?php
$head_title = "Login | Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/templates/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/templates/header.php");
if (isset($_SESSION["username"]))
{
	header("Location: /index.php");
	exit();
}
else
{
	require_once($_SERVER["DOCUMENT_ROOT"] . "/templates/forms/form_login.php");
}
?>