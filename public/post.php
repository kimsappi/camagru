<?php
$head_title = "Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($config_path . "config.php");
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");
require_once($functions_path . "dbConnect.php");
require_once($functions_path . "utils.php");

if (!$connection = dbConnect()) {
	echo "The site appears to be broken! Check back later.";
	exit();
}

// POST for commenting
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	print_r($_POST);
	print_r($_GET);
	if (!isset($_POST['commentInput']) || strlen($_POST['commentInput']) < 1 || !isset($_SESSION['username']) || !isset($_GET['id']))
		exit();
	
	$commentPostId = $_GET['id'];
	$commentUserId = $_SESSION['user_id'];
	$queryStr = <<<EOD
	INSERT INTO `comments` (`post_id`, `user_id`, `content`)
		VALUES (?, ?, ?);
	EOD;
	$query = $connection->prepare($queryStr);
	$query->execute([$commentPostId, $commentUserId, $_POST['commentInput']]);
}

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (!isset($_GET['id']) || !intval($_GET['id'])))
{
	header('Location: /');
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

$commentForm = '';
if (isset($_SESSION['username']))
	$commentForm = <<<EOD
	<form method='post'>
		<input type='textarea' name='commentInput' id='commentInput'>
		<input type='submit' name='submit' value='OK'>
	</form>
	EOD;


$commentsHTML = '';
$query = <<<EOD
SELECT * FROM `comments`
	WHERE `post_id` = $postId
	ORDER BY `id` ASC;
EOD;
foreach ($connection->query($query) as $comment)
{
	$commentContent = sanitiseOutput($comment['content']);
	$commentsHTML .= "<div>$commentContent</div>";
}
?>



<!-- Page body -->
	<div class='row'>
		<div class='col-sm-12 col-lg-10'>
			<img src='<?= $uploads_path_url . $fileName; ?>' alt='Post image'>
		</div>

		<div class='col-sm-12 col-lg-2'>
			<?php
			require_once($templates_path . "sideGallery.php");
			?>
		</div>
	</div>

<?php
echo $commentForm;
echo $commentsHTML;
?>

<?php
require_once($templates_path . "footer.php");
?>

</div>
