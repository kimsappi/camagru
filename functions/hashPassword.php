<?php
function hashPassword(string $password, string $username, string $salt = NULL): string
{
	if (is_null($salt))
	{
		require($_SERVER["DOCUMENT_ROOT"] . "/require.php");
		require_once($config_path . "config.php");
	}
	$salt = 'camagru.com';
	$salt .= $username;
	$salt = hash("md5", $salt);
	return hash("sha256", $password . $salt);
}
