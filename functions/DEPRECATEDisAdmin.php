<?php
function isAdmin(int $id): bool
{
	require_once($_SERVER["DOCUMENT_ROOT"] . "/functions/dbConnect.php");
	if (!$connection = dbConnect())
		return FALSE;

	$query = $connection->prepare("SELECT * FROM admins WHERE `id` = ?");
	if (!$query->execute([$id]))
		return FALSE;
	else
		return TRUE;
}