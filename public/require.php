<?php
if (!isset($_SESSION))
	session_start();

$root_path = $_SERVER["DOCUMENT_ROOT"];
$uploads_path = $root_path . "/images/uploads/";
$filters_path = $root_path . "/images/filters/";
$templates_path = $root_path . "/../templates/";
$functions_path = $root_path . "/../functions/";
$static_path = $root_path . "/static/";
$config_path = $root_path . "/../config/";
?>