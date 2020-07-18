<?php
$head_title = "Profile | Camagru";

require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
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
	if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
		header('Location: /?csrf=1');
		exit();
	}
	$changesMadeCorrectly = TRUE;
	$userData = NULL;

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
				"SELECT `id`, `username`, `password`, `email`, `email_on_comment` FROM users
					WHERE `username` = ?;"
			);
			if ($query->execute([$_SESSION["username"]]))
			{
				$userData = $query->fetch();
				if (!$userData || // Username not found in database
					$userData["password"] !== hashPassword($_POST["oldPassword"], $_SESSION["username"])
				)
				{
					// Old password is wrong
					$errorWithOldPassword = 'Old password is wrong, changes not made.';
					$changesMadeCorrectly = FALSE;
				}

				// User used the correct password, check and possibly make other changes
				if ($changesMadeCorrectly)
				{
					// Change username (requires changing password as well because that's how it's hashed)
					if (!isset($_POST['username']) || $_POST['username'] != $_SESSION['username'])
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
									$_SESSION['username'] = $_POST['username'];
								}
								else
									$errorWithName = 'Username is already in use.';
							}
							else
								$errorWithName = 'There was a problem with changing your username. Try again later.';
						}
						else
							$errorWithName = 'Your new username does not meet the criteria for usernames.';
					}
					if (strlen($errorWithName) > 0)
						$changesMadeCorrectly = FALSE;

					// Change email
					if (!isset($_POST['email']) || $_POST['email'] != $_SESSION['email'])
					{
						if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
						{
							$query = $connection->prepare(
								"SELECT `id` FROM users
									WHERE `email` = ?;"
							);
							if ($query->execute([$_POST["email"]]))
							{
								if (!$query->fetch())
								{
									$query = "UPDATE `users` SET `email` = ? WHERE `id` = ?";
									$query = $connection->prepare($query);
									$query->execute([$_POST['email'], $_SESSION['user_id']]);
									$_SESSION['email'] = $_POST['email'];
								}
								else
									$errorWithEmail = 'Email is already in use.';
							}
							else
								$errorWithEmail = 'There was a problem with changing your email. Try again later.';
						}
						else
							$errorWithEmail = 'Not a valid email address.';
					}
					if (strlen($errorWithEmail) > 0)
						$changesMadeCorrectly = FALSE;

					// Change password
					if (!isset($_POST['newPassword']) || (strlen($_POST['newPassword']) > 0 && $_POST['newPassword'] != $_POST['oldPassword']))
					{
						if (!isset($_POST['confirmPassword']) || (validatePasswordStrength($_POST['newPassword']) && $_POST['newPassword'] == $_POST['confirmPassword']))
						{
							$newHashedPassword = hashPassword($_POST['newPassword'], $_SESSION['username']);
							$query = "UPDATE `users` SET `password` = ? WHERE `id` = ?";
							$query = $connection->prepare($query);
							$query->execute([$newHashedPassword, $_SESSION['user_id']]);
						}
						else
							$errorWithNewPassword = 'New password does not meet criteria or passwords don\'t match.';
					}
					if (strlen($errorWithNewPassword) > 0)
						$changesMadeCorrectly = FALSE;

					// Change email on comment preference
					$shouldEmailOnComment = intval(isset($_POST['emailOnComment']));
					$query = "UPDATE `users` SET `email_on_comment` = ? WHERE `id` = ?;";
					$query = $connection->prepare($query);
					$query->execute([$shouldEmailOnComment, $_SESSION['user_id']]);
					$_SESSION['email_on_comment'] = $shouldEmailOnComment;
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

$csrfHash = generateFormValidationHash($_SESSION['user_id']);
$_SESSION['csrf'] = $csrfHash;

// <!-- Page body -->
// These are here instead of the top, so we get the new username immediately on submission
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");
?>
<div class='container'>
	<div class='row'>
		<?php
		if (!$changesMadeCorrectly)
		{
			echo "<div class='statusText'>$errorWithName</div>";
			echo "<div class='statusText'>$errorWithEmail</div>";
			echo "<div class='statusText'>$errorWithNewPassword</div>";
			echo "<div class='statusText'>$errorWithOldPassword</div>";
		}
		else
			echo "<div class='statusText'>All changes saved!</div>";
		?>
	</div>
	<div class='row'>
		<div class='col-12'>
			<h2>Edit profile</h2>
		</div>	
		<form method='post' id='editProfileForm'>
			<div>Change username</div>
			<label for='username'>Username</label>
			<input type='text' name='username' value='<?= sanitiseOutput($_SESSION['username']) ?>' pattern="[a-zA-Z_]{4,14}$">
			<div class='formExplanation'>(4-14 characters, [a-zA-Z_])</div>
			<div>Change email</div>
			<label for='email'>Email</label>
			<input type='email' name='email' value='<?= sanitiseOutput($_SESSION['email']) ?>'>
			<div>Change password</div>
			<label for='newPassword'>New password</label>
			<input type='password' name='newPassword' pattern=".{8,}">
			<br>
			<label for='confirmPassword'>Confirm new password</label>
			<input type='password' name='confirmPassword' pattern=".{8,}">
			<div class='formExplanation'>(>7 characters, must include 3 of: lowercase, capital, digit, special)</div>
			<div>Communication settings</div>
			<input type='checkbox' name='emailOnComment' <?= $_SESSION['email_on_comment'] ? 'checked' : '' ?>>
			<label for='emailOnComment'>Receive emails upon comment to your posts</label>
			<div class='maroon'>Confirm all changes with current password</div>
			<label for='oldPassword'>Password</label>
			<input type='password' name='oldPassword' pattern=".{8,}" required>
			<input type='text' name='csrf' value='<?= $csrfHash ?>' class='displayNone'></input>
			<input type='submit' name='submit' value='Submit'>
		</form>
	</div>
</div>

<?php
require_once($templates_path . "footer.php");
?>
