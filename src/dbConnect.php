<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/config/database.php");
function dbConnect()
{
	try
	{
		$connection = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e)
	{
		$connection = NULL;
	}
	return $connection;
}
?>