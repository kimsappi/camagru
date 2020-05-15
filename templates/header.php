</head>
<?php
if (isset($body_onload))
	echo "<body onload='$body_onload'>";
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
				require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
				require_once($functions_path . "/utils.php");
				$userName = sanitiseOutput($_SESSION['username']);
				echo <<<EOD
				<div id='username'>
					<a href='/profile.php'>
						$userName
					</a>
				</div>
				<div id='logout'><a href='/logout.php'>Log out</a></div>
EOD;
				//if ($_SESSION["is_admin"])
				//	echo "<div id='adminpanel'><a href='/admin.php'>Admin panel</a></div>";
			}
			else
			{
				echo "<a href='/register.php'>Register</a><a href='/login.php'>Log in</a>";
			}
		?>
	</header>
