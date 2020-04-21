</head>
<?php
if (isset($body_onload))
	echo "<body onload='$body_onload'";
else
	echo "<body>";
?>
<div class='container'>
	<header>
		<nav>
			<a href="/index.php">Home</a>
		</nav>
		<?php
			if (isset($_SESSION["username"]))
			{
				$userName = htmlentities($_SESSION['username']);
				echo "<div id='username'>".$userName."</div><div id='logout'><a href='/logout.php'>Log out</a></div>";
				//if ($_SESSION["is_admin"])
				//	echo "<div id='adminpanel'><a href='/admin.php'>Admin panel</a></div>";
			}
			else
			{
				echo "<a href='/register.php'>Register</a><a href='/login.php'>Log in</a>";
			}
		?>
	</header>
