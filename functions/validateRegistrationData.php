<?php
function validatePassWordStrength(string $password, $confirmPassword = FALSE): bool
{
	/*
	** Password minimum characters, at least 3 of: [lowercase, uppercase,
	** numeric, special character], passwords must match
	*/

	$password_strength = 0;
	$password_strength += preg_match("/[a-z]/", $password);
	$password_strength += preg_match("/[A-Z]/", $password);
	$password_strength += preg_match("/[0-9]/", $password);
	$password_strength += preg_match("/[\W]/", $password);
	if (
		($password !== $confirmPassword && $confirmPassword !== FALSE) ||
		strlen($password) < 8 ||
		$password_strength < 3
	)
		return FALSE;
	return TRUE;
}

function validateUsername(string $username): bool
{
	/* Username 4-14 characters, only alphanumerics and underscores */
	
	if (
		strlen($username) < 4 ||
		strlen($username) > 14 ||
		!preg_match("/^[\w]+$/", $username)
	)
		return FALSE;
	return TRUE;
}

function validateRegistrationData(Array $arr): bool
{
	if (!validateUsername($arr['username']))
		return FALSE;

	if (!validatePassWordStrength($arr["password"], $arr["password2"]))
		return FALSE;
	/*
	** Email is complicated. Must be of form 'local@host'. Host must include
	** valid TLD. Comments not allowed. IP as host not allowed.
	*/
	// PHP does have an email verification feature built in...
	// $email = $arr["email"];

	// if (
	// 	// The next line enforces good format and character set
	// 	!preg_match("/^[\w!#$%&'*+\-\/=?^_`{|}~.]+@[a-zA-Z0-9\-.]+\..*[a-zA-Z]+.*/", $email) ||
	// 	preg_match("/\.\./", $email) // Double dot not allowed in emails
	// )
	// 	return FALSE;

	// $email_arr = explode("@", $email);

	// /* Local not allowed to begin or end with '.' */
	// if (
	// 	$email_arr[0][0] === "." ||
	// 	$email_arr[0][-1] === "."
	// )
	// 	return FALSE;
	
	// /* Domain not allowed to begin or end with '-' */
	// foreach (explode(".", $email_arr[1]) as $domain)
	// {
	// 	if (
	// 		$domain[0] === "-" ||
	// 		$domain[-1] === "-"
	// 	)
	// 		return FALSE;
	// }
	
	/* All data is valid, return TRUE */
	return filter_var($arr['email'], FILTER_VALIDATE_EMAIL);
	//return TRUE;
}
?>
