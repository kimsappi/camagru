<?php
function validateRegistrationData(Array $arr): bool
{
	/* Username 4-24 characters, only alphanumerics and underscores */
	if (
		strlen($arr["username"]) < 4 ||
		strlen($arr["username"]) > 24 ||
		!preg_match("/^[\w]+$/", $arr["username"])
	)
		return FALSE;

	/*
	** Password minimum characters, at least 3 of: [lowercase, uppercase,
	** numeric, special character], passwords must match
	*/
	$password_strength = 0;
	$password_strength += preg_match("/[a-z]/", $arr["password"]);
	$password_strength += preg_match("/[A-Z]/", $arr["password"]);
	$password_strength += preg_match("/[0-9]/", $arr["password"]);
	$password_strength += preg_match("/[\W]/", $arr["password"]);
	if (
		$arr["password"] !== $arr["password2"] ||
		strlen($arr["password"]) < 8 ||
		$password_strength < 3
	)
		return FALSE;

	/*
	** Email is complicated. Must be of form 'local@host'. Host must include
	** valid TLD. Comments not allowed. IP as host not allowed.
	*/
	$email = $arr["email"];

	if (
		// The next line enforces good format and character set
		!preg_match("/^[\w!#$%&'*+\-\/=?^_`{|}~.]+@[a-zA-Z0-9\-.]+\..*[a-zA-Z]+.*/", $email) ||
		preg_match("/\.\./", $email) // Double dot not allowed in emails
	)
		return FALSE;

	$email_arr = explode("@", $email);

	/* Local not allowed to begin or end with '.' */
	if (
		$email_arr[0][0] === "." ||
		$email_arr[0][-1] === "."
	)
		return FALSE;
	
	/* Domain not allowed to begin or end with '-' */
	foreach (explode(".", $email_arr[1]) as $domain)
	{
		if (
			$domain[0] === "-" ||
			$domain[-1] === "-"
		)
			return FALSE;
	}
	
	/* All data is valid, return TRUE */
	return TRUE;
}
?>