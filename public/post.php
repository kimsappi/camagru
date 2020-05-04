<?php
$head_title = "Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($config_path . "config.php");
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");
require_once($templates_path . "Comment.php");
require_once($functions_path . "dbConnect.php");

if (!$connection = dbConnect()) {
	echo "The site appears to be broken! Check back later.";
	exit();
}

// POST for commenting
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
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

// Deletion is via GET too because this was easiest
if (isset($_GET['delete']))
{
	if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $result['user_id'])
	{
		unlink($uploads_path . $fileName);
		$query = $connection->prepare("DELETE FROM posts WHERE `id` = ?;");
		$query->execute([$postId]);
	}
	header('Location: /');
	exit();
}

// Liking is handled via GET too because why not
if (isset($_SESSION['user_id']) && isset($_GET['like']))
{
	$likeQuery = <<<EOD
	INSERT INTO `likes` (`post_id`, `user_id`)
		VALUES ($postId, {$_SESSION['user_id']});
EOD;
	try
	{
		$connection->query($likeQuery);
	}
	catch (PDOException $e)
	{
		// User has already liked this post, do nothing
	}
}

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
	$commentHTML = new Comment($comment);
	$commentsHTML .= $commentHTML;
}

// Get the number of likes for the current post
$likesQuery = "SELECT COUNT(*) FROM `likes` WHERE `post_id` = $postId";
$likesQuery = $connection->query($likesQuery);
$likesCount = $likesQuery->fetchColumn();

// If the current user is the author of the post, allow deletion
$deletionHTML = '';
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $result['user_id'])
{
	$deletionHTML = <<<EOD
	<a href='post.php?id=$postId&delete=1'>Delete this post</a>
EOD;
}

?>



<!-- Page body -->
<?= $deletionHTML ?>
<div class='row'>
	<div class='col-12 col-md-10' id='postMainImage'>
		<img src='<?= $uploads_path_url . $fileName; ?>' alt='Post image' id='postMainImageImg' class='resizeSelectorClass'>
	</div>

	<div class='col-12 col-md-2 sideGallery' id='postSideGallery'>
		<?php
		require_once($templates_path . "sideGallery.php");
		?>
	</div>
</div>

<div class='row'>
<a href='post.php?id=<?= $postId ?>&like=1'>&#x2764;&#xFE0F; <?= $likesCount ?></a>
</div>

<?= $commentForm; ?>

<div id='commentsContainer'>
<?= $commentsHTML; ?>
</div>

<script src='/static/sideGallery.js'></script>

<?php
require_once($templates_path . "footer.php");
?>

