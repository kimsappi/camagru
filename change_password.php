<?php
$head_title = 'Change password | Camagru';
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($functions_path . "dbConnect.php");

$errorContent = '';

if (isset($_SESSION['username']) || !$connection = dbConnect())
{
	header("Location: /");
	exit();
}

if (!isset($_GET['hash']) || !strlen($_GET['hash']))
{
	header('Location: /');
	exit();
}
$query = <<<EOD
SELECT `id`, `password`, `username` FROM `users`
	WHERE BINARY `email_verification_string` = ?;
EOD;
$query = $connection->prepare($query);
$query->execute([$_GET['hash']]);
$result = $query->fetch();
if (!$result || strlen($result['password']))
{
	header('Location: /');
	exit();
}

// Just display the form if GET
$contentHTML = <<<EOD
<h2>Enter new password</h2>
<form method='post'>
	<label for='password'>New password</label>
	<input type='password' name='password'>
	<label for='confirmPassword'>Confirm password</label>
	<input type='password' name='confirmPassword'>
	<input type='submit' name='submit'>
</form>
EOD;

// New password submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	require_once($functions_path . "hashPassword.php");
	require_once($functions_path . "validateRegistrationData.php");

	if (!isset($_POST['password']) || !isset($_POST['confirmPassword']) || !validatePasswordStrength($_POST['password'], $_POST['confirmPassword']))
	{
		$errorContent = '<p>Please enter a valid password</p>';
	}
	else
	{
		$hashedPassword = hashPassword($_POST['password'], $result['username']);
		$query = <<<EOD
		UPDATE `users` SET `password` = ?, `email_verification_string` = ''
			WHERE `id` = {$result['id']};
EOD;
		$query = $connection->prepare($query);
		$query->execute([$hashedPassword]);

		$contentHTML = '<p>Password changed successfully! You can now log in.</p>';
	}
}
?>

<!-- Page body -->
<?php
require_once($templates_path . 'head.php');
require_once($templates_path . 'header.php');

echo $errorContent;
echo $contentHTML;
?>

