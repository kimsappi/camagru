<?php
$head_title = "Register | Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");

/* Registration was submitted */
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
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
	require_once($functions_path . "validateRegistrationData.php");
	if (!validateRegistrationData($_POST))
	{
		header("Location: /register.php");
		exit();
	}

	require_once($functions_path . "dbConnect.php");
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
		require_once($functions_path . "hashPassword.php");
		$password = hashPassword($_POST["password"], $_POST["username"]);
		$query = $connection->prepare(
			"INSERT IGNORE INTO users (`username`, `password`, `email`)
				VALUES (?, ?, ?);"
		);
		$query->execute([$_POST["username"], $password, $_POST["email"]]);
		header("Location: index.php");
		exit();
	}
}

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
