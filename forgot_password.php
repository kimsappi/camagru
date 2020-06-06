<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($functions_path . "dbConnect.php");
require_once($templates_path . 'head.php');
require_once($templates_path . 'header.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
	if (isset($_SESSION['username']))
	{
		header('Location: /');
		exit();
	}

	echo <<<EOD
	<form method='post'>
		<label for='email'>Enter your email</label>
		<input type='email' name='email'>
		<input type='submit' name='submit'>
	</form>
EOD;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	if (!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || !$connection = dbConnect())
	{
		header('Location: /');
		exit();
	}

	$query = <<<EOD
	SELECT `id` FROM `users`
		WHERE `email` = ?;
EOD;
	$query = $connection->prepare($query);
	$query->execute([$_POST['email']]);
	$result = $query->fetch();
	if ($result)
	{
		$changePasswordHash = hash('md5', $_POST['email'] . date('l jS \of F Y h:i:s A'));
		$query = <<<EOD
		UPDATE `users`
			SET `email_verification_string` = '$changePasswordHash', `password` = ''
			WHERE `id` = {$result['id']};
EOD;
		$connection->query($query);

		$rootURL = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
		$changePassworldURL = $rootURL . 'change_password.php?hash=' . $changePasswordHash;

		$changePasswordMail = <<<EOD
<p>To change your password, click the following link></p>
<a href='$changePassworldURL'>$changePassworldURL</a>
EOD;

		$emailHeaders = "MIME-Version: 1.0\r\n";
		$emailHeaders .= "Content-type: text/html; charset=iso-8859-1\r\n";

		mail($_POST['email'], 'Camagru | Change password', $changePasswordMail, $emailHeaders);
	}
		
	echo <<<EOD
	Instructions to change your password have been sent to the provided email if a matching account was found.
EOD;
}
