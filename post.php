<?php
$head_title = "Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($config_path . "config.php");
require_once($templates_path . "Comment.php");
require_once($functions_path . "dbConnect.php");

if (!$connection = dbConnect()) {
	echo "The site appears to be broken! Check back later.";
	exit();
}

$postId = intval($_GET['id']);
$query = $connection->prepare("SELECT * FROM posts WHERE `id` = ?;");
$query->execute([$postId]);
$result = $query->fetch(PDO::FETCH_ASSOC);
$postRow = $result;

$fileName = $result['id'] . '.' . $result['extension'];

if (!$result)
{
	header('Location: /');
	exit();
}

// POST for commenting, deleting, liking and unliking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf']) && ($_POST['csrf'] === $_SESSION['csrf']))
{

	// Commenting
	if (isset($_POST['commentInput']) && strlen($_POST['commentInput']) > 0 && isset($_SESSION['username']) && isset($_GET['id']))
	{	
		$commentPostId = $_GET['id'];
		$commentUserId = $_SESSION['user_id'];

		// Check if original poster needs to be emailed, also check if post exists
		$queryStr = <<<EOD
		SELECT `email`, `email_on_comment` FROM `users`
			INNER JOIN `posts`
				ON users.id = posts.user_id
			WHERE posts.id = ?;
EOD;

		$query = $connection->prepare($queryStr);
		$query->execute([$commentPostId]);
		$result = $query->fetch();
		if (!$result)
		{
			header('Location: /');
			exit();
		}

		// Send email to original poster if they have the setting selected
		if ($result['email_on_comment'])
		{
			$postURL = $rootURL = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/post.php?id=' . $commentPostId;
			$mailToPoster = <<<EOD
		<p>You have a new comment on your post: <a href='$postURL'>$postURL</a></p>
EOD;

			$emailHeaders = "MIME-Version: 1.0\r\n";
			$emailHeaders .= "Content-type: text/html; charset=iso-8859-1\r\n";
			mail($result['email'], 'Camagru | New comment on your post', $mailToPoster, $emailHeaders);
		}

		$queryStr = <<<EOD
		INSERT INTO `comments` (`post_id`, `user_id`, `content`)
			VALUES (?, ?, ?);
EOD;
		$query = $connection->prepare($queryStr);
		$query->execute([$commentPostId, $commentUserId, $_POST['commentInput']]);
	}

	// Deleting
	if (isset($_POST['delete']))
	{
		if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $postRow['user_id'])
		{
			unlink($uploads_path . $fileName);
			$query = $connection->prepare("DELETE FROM posts WHERE `id` = ?;");
			$query->execute([$postId]);
		}
		header('Location: /?deleted=1');
		exit();
	}

	// Liking/unliking
	if (isset($_SESSION['user_id']) && isset($_POST['like'])){
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
			$unlikeQuery = <<<EOD
			DELETE FROM `likes` WHERE post_id = $postId AND user_id = {$_SESSION['user_id']};
EOD;
			$connection->query($unlikeQuery);
		}
	}
}

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (!isset($_GET['id']) || !intval($_GET['id'])))
{
	header('Location: /');
	exit();
}

// Get original poster's name. Yes, this is done stupidly with 2 queries, but
// I had already completed the rest of the page and it would take more work to fix everything.
// error_log(print_r($postRow, TRUE));
// error_log("SELECT `username` FROM `users` WHERE `id` = {$postRow['user_id']};");
$posterName = $connection->query("SELECT `username` FROM `users` WHERE `id` = {$postRow['user_id']};");
$posterName = $posterName->fetch()['username'];

$commentForm = '';
if (isset($_SESSION['username']))
	$csrfHash = generateFormValidationHash($_SESSION['user_id']);
	$_SESSION['csrf'] = $csrfHash;
	$commentForm = <<<EOD
	<form method='post' id='commentForm'>
		<textarea name='commentInput' id='commentInput' rows='4' cols='50' wrap='soft' maxlength='500' required></textarea>
		<input type='text' name='csrf' value='$csrfHash' class='displayNone'></input>
		<br>
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
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $postRow['user_id'])
{
	$deletionHTML = <<<EOD
	<form method='post'>
		<input type='text' name='csrf' value='$csrfHash' class='displayNone'></input>
		<input type='text' name='delete' value='1' class='displayNone'></input>
		<a href='#' onclick='this.parentNode.submit();';>&#x274C; Delete this post</a>
	</form>
EOD;
}

// Page body
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");
?>

<div class='row'>
	<div class='col-12 col-md-10 flexRow flexSpaceEvenly'>
		<div>
			By: <span class='bold text2Em'><?= sanitiseOutput($posterName) ?></span>
		</div>
		<div id='likesCounter' class='text2Em'>
			<form method='post'>
				<input type='text' name='csrf' value='<?= $csrfHash ?>' class='displayNone'></input>
				<input type='text' name='like' value='1' class='displayNone'></input>
				<a href='#' onclick='this.parentNode.submit();';>&#x2764;&#xFE0F; <?= $likesCount ?></a>
			</form>
		</div>
		<div>
			<?= $deletionHTML ?>
		</div>
	</div>
</div>

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
	<div class='col-0 col-md-1'>

	</div>
	<div class='col-12 col-md-11'>
		<?= $commentForm; ?>
	</div>
</div>

<div class='row'>
	<div class='col-0 col-md-1'>

	</div>
	<div id='commentsContainer' class='col-12 col-md-11'>
		<?= $commentsHTML; ?>
	</div>
</div>

<script src='/static/sideGallery.js'></script>

<?php
require_once($templates_path . "footer.php");
?>

