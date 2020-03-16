<?php
function dbConnect()
{
	require($_SERVER["DOCUMENT_ROOT"] . "/require.php");
	require_once($config_path . "database.php");
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