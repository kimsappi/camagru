<?php
$head_title = "Profile | Camagru";

require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");
require_once($functions_path . "utils.php");
require_once($functions_path . "dbConnect.php");
require_once($functions_path . "validateRegistrationData.php");
require_once($functions_path . "hashPassword.php");

if (!isset($_SESSION["username"]))
{
	header("Location: /login.php?destination=take_pic.php");
	exit();
}

// User submitted form, check what should be changed and change it
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	$errorWithName = '';
	$errorWithEmail = '';
	$errorWithNewPassword = '';
	$errorWithOldPassword = '';
	$userData = NULL;
	print_r($_POST);

	if (isset($_POST['oldPassword']) && $_POST['oldPassword'])
	{
		if (!$connection = dbConnect())
		{
			header("Location: /login.php");
			exit();
		}
		echo 'xd';
		// First check if the password is a valid password, then query for user data
		if (validatePassWordStrength($_POST['oldPassword']))
		{
			echo 'wow';
			$query = $connection->prepare(
				"SELECT `id`, `username`, `password`, `email` FROM users
					WHERE `username` = ?;"
			);
			if ($query->execute([$_SESSION["username"]]))
			{
				$userData = $query->fetch();
				// Old password is wrong, abort
				if (!$userData || // Username not found in database
					$userData["password"] !== hashPassword($_POST["oldPassword"], $_SESSION["username"])
				)
					$errorWithOldPassword = 'Old password is wrong, changes not made.';
			}
		}
	}	
}
?>

<!-- Page body -->
<div class='container'>
	<div class='row'>
		<h1>Edit profile<h2>
		<form method='post'>
			<div>Change username</div>
			<label for='username'>Username</label>
			<input type='text' name='username' value='<?= sanitiseOutput($_SESSION['username']) ?>'>
			<div>Change email</div>
			<label for='email'>Email</label>
			<input type='email' name='email' value='<?= sanitiseOutput($_SESSION['email']) ?>'>
			<div>Change password</div>
			<label for='newPassword'>New password</label>
			<input type='password' name='newPassword'>
			<label for='confirmPassword'>Confirm new password</label>
			<input type='password' name='confirmPassword'>
			<div>Confirm all changes with current password</div>
			<label for='oldPassword'>Password</label>
			<input type='password' name='oldPassword'>
			<input type='submit' name='submit' value='Submit'>
		</form>
	</div>
</div>

<?php
require_once($templates_path . "footer.php");
?>
