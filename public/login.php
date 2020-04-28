<?php
$head_title = "Login | Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{	
	if (!isset($_POST["username"]) || !isset($_POST["password"]))
	{
		header("Location: /login.php");
		exit();
	}

	echo $root_path;
	//exit();
	require_once($functions_path . "dbConnect.php");
	if (!$connection = dbConnect())
	{
		header("Location: /login.php");
		exit();
	}

	$query = $connection->prepare(
		"SELECT `id`, `username`, `password`, `email` FROM users
			WHERE `username` = ?;"
	);
	if ($query->execute([$_POST["username"]]))
	{
		require_once($functions_path . "hashPassword.php");
		$result = $query->fetch();
		if (!$result || // Username not found in database
			$result["password"] !== hashPassword($_POST["password"], $_POST["username"])
		)
		{
			header("Location: /login.php");
			exit();
		}
		$_SESSION["username"] = $result["username"];
		$_SESSION["user_id"] = $result["id"];
		$_SESSION["email"] = $result["email"];
		//require_once($_SERVER["DOCUMENT_ROOT"] . "/functions/idAdmin.php");
		//$_SESSION["is_admin"] = isAdmin($result["id"]);
		if (isset($_GET["destination"]))
			header("Location: " . $_GET["destination"]);
		else
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
	require_once($templates_path . "forms/form_login.php");
}
?>
