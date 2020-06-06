<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($functions_path . "dbConnect.php");

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['hash']) || !strlen($_GET['hash'])) {
	header('Location: /');
	exit();
}

if (!$connection = dbConnect())
{
	header("Location: /");
	exit();
}

$query = <<<EOD
SELECT `id`, `password` FROM `users`
	WHERE BINARY `email_verification_string` = ?;
EOD;

$query = $connection->prepare($query);
$query->execute([$_GET['hash']]);
$result = $query->fetch();

if (!$result || !strlen($result['password']))
{
	header("Location: /");
	exit();
}

$query = <<<EOD
UPDATE `users`
	SET `email_verification_string` = ''
	WHERE `id` = {$result['id']}
EOD;

$connection->query($query);

header("refresh:5;url=/login.php");
?>

<p>Email confirmation successful. You will be redirected to the <a href='/login.php'>login page</a> in 5 seconds.</p>
