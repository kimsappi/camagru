<?php
$head_title = "Profile | Camagru";

require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");
require_once($functions_path . "utils.php");

if (!isset($_SESSION["username"]))
{
	header("Location: /login.php?destination=take_pic.php");
	exit();
}


?>

<!-- Page body -->
<div class='container'>
	<div class='row'>
		<h1>Edit profile<h2>
		<form method='post'>
			<label for='username'>Username</label>
			<input type='text' name='username' value='<?= sanitiseOutput($_SESSION['username']) ?>'>
			<label for='email'>Email</label>
			<input type='email' name='email' value='<?= sanitiseOutput($_SESSION['email']) ?>'>
			<label for='oldPassword'>Old password</label>
			<input type='password' name='oldPassword'>
			<label for='newPassword'>New password</label>
			<input type='password' name='newPassword'>
			<label for='confirmPassword'>Confirm new password</label>
			<input type='password' name='confirmPassword'>
			<input type='submit' name='submit' value='Submit'>
	</div>
</div>

<?php
require_once($templates_path . "footer.php");
?>
