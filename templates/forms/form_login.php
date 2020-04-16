<?php
if (isset($_GET["destination"]))
{
	$destination = $_GET["destination"];
	echo "<form action=\"/login_submit.php?destination=$destination\" method=\"post\">";
}
else
	echo '<form action="/login.php" method="post">';
?>
	<label for="username">Username</label>
	<input type="text" name="username">
	<label for="password">Password</label>
	<input type="password" name="password">
	<input type="submit" value="Log in">
</form>
