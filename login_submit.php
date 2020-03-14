<?php
if (!isset($_POST["username"]) || !isset($_POST["password"]))
{
	header("Location: /login.php");
	exit();
}

?>