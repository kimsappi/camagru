<?php
foreach (["username", "password", "password2", "email"] as $field)
{
	if (!isset($_POST[$field]))
	{
		header("Location: /register.php");
		exit();
	}
}

?>