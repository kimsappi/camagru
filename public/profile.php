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

$errorWithName = '';
$errorWithEmail = '';
$errorWithNewPassword = '';
$errorWithOldPassword = '';
$changesMadeCorrectly = FALSE;

// User submitted form, check what should be changed and change it
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	$changesMadeCorrectly = TRUE;
	$userData = NULL;
	print_r($_POST);
	print('<br>');
	print(hashPassword($_POST["oldPassword"], $_SESSION["username"]));

	if (isset($_POST['oldPassword']) && $_POST['oldPassword'])
	{
		if (!$connection = dbConnect())
		{
			header("Location: /login.php");
			exit();
		}

		// First check if the password is a valid password, then query for user's data
		if (validatePassWordStrength($_POST['oldPassword']))
		{
			$query = $connection->prepare(
				"SELECT `id`, `username`, `password`, `email` FROM users
					WHERE `username` = ?;"
			);
			if ($query->execute([$_SESSION["username"]]))
			{
				$userData = $query->fetch();
				if (!$userData || // Username not found in database
					$userData["password"] !== hashPassword($_POST["oldPassword"], $_SESSION["username"])
				)
				{
					echo '<br>';
					print_r($userData['password']);
					if ($userData["password"] !== hashPassword($_POST["oldPassword"], $_SESSION["username"]))
						echo strlen($userData["password"]);
					// Old password is wrong
					$errorWithOldPassword = 'Old password is wrong, changes not made.';
					$changesMadeCorrectly = FALSE;
				}

				// User used the correct password, check and possibly make other changes
				if ($changesMadeCorrectly)
				{
					if ($_POST['username'] != $_SESSION['username'])
					{
						if (validateUsername($_POST['username']))
						{
							$query = $connection->prepare(
								"SELECT `id` FROM users
									WHERE `username` = ?;"
							);
							if ($query->execute([$_POST["username"]]))
							{
								if (!$query->fetch())
								{
									$newHashedPassword = hashPassword($_POST['oldPassword'], $_POST['username']);
									$query = "UPDATE `users` SET `username` = ?, `password` = ? WHERE `id` = ?";
									$query = $connection->prepare($query);
									$query->execute([$_POST['username'], $newHashedPassword, $_SESSION['user_id']]);
								}
							}
						}
					}
				}
			}
		}
		else
		{
			$errorWithOldPassword = 'Old password does not match password criteria, changes not made.';
			$changesMadeCorrectly = FALSE;
		}
	}
	else
	{
		$errorWithOldPassword = 'Enter your current password to confirm changes.';
		$changesMadeCorrectly = FALSE;
	}
}
?>

<!-- Page body -->
<div class='container'>
	<div class='row'>
		<?php
		if (!$changesMadeCorrectly)
		{
			echo "<div class='formError'>$errorWithName</div>";
			echo "<div class='formError'>$errorWithEmail</div>";
			echo "<div class='formError'>$errorWithNewPassword</div>";
			echo "<div class='formError'>$errorWithOldPassword</div>";
		}
		else
			echo "<div class='formError'>All changes saved!</div>";
		?>
	</div>
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
