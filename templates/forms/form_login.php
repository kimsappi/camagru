<?php
if (isset($_GET["destination"]))
{
	$destination = $_GET["destination"];
	echo "<form action=\"/login.php?destination=$destination\" method=\"post\" id='editProfileForm'>";
}
else
	echo '<form action="/login.php" method="post" id="editProfileForm">';
?>
	<div>
		<label for="username">Username</label>
		<input type="text" name="username">
	</div>
	<div>
		<label for="password">Password</label>
		<input type="password" name="password">
	</div>
	<input type="submit" value="Log in">
</form>
