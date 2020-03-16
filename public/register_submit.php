<?php
/* Fail registration if all fields not filled */
foreach (["username", "password", "password2", "email"] as $field)
{
	if (!isset($_POST[$field]) || !$_POST[$field])
	{
		header("Location: /register.php");
		exit();
	}
}

/* Fail registration if data not correct, e.g. username length, pw complexity */
require_once($_SERVER["DOCUMENT_ROOT"] . "/functions/validateRegistrationData.php");
if (!validateRegistrationData($_POST))
{
	header("Location: /register.php");
	exit();
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/functions/dbConnect.php");
if (!$connection = dbConnect())
{
	header("Location: /register.php");
	exit();
}

$query = $connection->prepare(
	"SELECT id FROM users
		WHERE username = ?
			OR email = ?;"
);

if ($query->execute([$_POST["username"], $_POST["email"]]))
{
	if ($query->fetch()) // Existing account found for username and/or password
	{
		echo "Username or email already associated with an account.";
		exit();
	}
	require_once($_SERVER["DOCUMENT_ROOT"] . "/functions/hashPassword.php");
	$password = hashPassword($_POST["password"], $_POST["username"]);
	$query = $connection->prepare(
		"INSERT IGNORE INTO users (`username`, `password`, `email`)
			VALUES (?, ?, ?);"
	);
	$query->execute([$_POST["username"], $password, $_POST["email"]]);
}
?>