<?php
function hashPassword(string $password, string $username, string $salt = NULL): string
{
	if (is_null($salt))
		require_once($_SERVER["DOCUMENT_ROOT"] . "/config/config.php");
	$salt .= $username;
	$salt = hash("md5", $salt);
	return hash("sha256", $password . $salt);
}