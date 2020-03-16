<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");

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
	"SELECT `id`, `username`, `password` FROM users
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
	//require_once($_SERVER["DOCUMENT_ROOT"] . "/functions/idAdmin.php");
	//$_SESSION["is_admin"] = isAdmin($result["id"]);
	if (isset($_GET["destination"]))
		header("Location: " . $_GET["destination"]);
	else
		header("Location: index.php");
	exit();
}
?>