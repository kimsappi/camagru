<?php
$head_title = "Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($config_path . "config.php");
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");
require_once($functions_path . "dbConnect.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && (!isset($_GET['id']) || !intval($_GET['id'])))
{
	header('Location: /');
	exit();
}

if (!$connection = dbConnect()) {
	echo "The site appears to be broken! Check back later.";
	exit();
}

$postId = intval($_GET['id']);

$query = $connection->prepare("SELECT * FROM posts WHERE `id` = ?;");
$query->execute([$postId]);
$result = $query->fetch(PDO::FETCH_ASSOC);

if (!$result)
{
	header('Location: /');
	exit();
}

$fileName = $result['id'] . '.' . $result['extension'];
?>

<img src='<?= $uploads_path_url . $fileName; ?>' alt='Post image'>



<?php
require_once($templates_path . "footer.php");
?>
