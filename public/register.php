<?php
$head_title = "Register | Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");
if (isset($_SESSION["username"]))
{
	header("Location: /index.php");
	exit();
}
else
{
	require_once($templates_path . "forms/form_register.html");
}
?>