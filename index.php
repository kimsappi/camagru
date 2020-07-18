<?php
$head_title = "Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($config_path . "config.php");
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");
?>

<?php
require_once($functions_path . 'dbConnect.php');
if (!$connection = dbConnect()) {
	echo "The site appears to be broken! Check back later.";
	exit();
}

// Set current page for pagination purposes
if (isset($_GET['page']) && intval($_GET['page']) && intval($_GET['page'] > 0))
	$currentPage = $_GET['page'];
else
	$currentPage = 1;

// Pagination is defined in config/config.php
$queryOffset = ($currentPage - 1) * $posts_per_page;
$queryLimit = $posts_per_page + 1;

$query = <<<EOD
SELECT `id`, `extension` FROM posts
	ORDER BY `id` DESC
	LIMIT $queryOffset, $queryLimit;
EOD;

$prevPageLink = '';
$nextPageLink = '';
if ($currentPage > 1) {
	$prevPage = $currentPage - 1;
	$prevPageLink = <<<EOD
		<a href="/?page=$prevPage">Previous page</a>
EOD;
}

$indexGalleryHTML = '';
$imagesFound = 0;
foreach ($connection->query($query) as $imageData)
{
	$imageId = $imageData['id'];
	$fileName = $imageId . '.' . $imageData['extension'];
	if ($imagesFound < $posts_per_page && file_exists($uploads_path . $fileName))
	{
		$indexGalleryHTML .= <<<EOD
		<a href="post.php?id=$imageId">
			<img src="$uploads_path_url$fileName" class='thumbnailCustom' alt='Thumbnail'>
		</a>
EOD;
	}
	++$imagesFound;
	
	if ($imagesFound > $posts_per_page)
	{
		$nextPage = $currentPage + 1;
		$nextPageLink = <<<EOD
			<a href="/?page=$nextPage">Next page</a>
EOD;
		break;
	}
}

if (!$imagesFound)
{
	$query = "SELECT `id` FROM posts LIMIT 1;";
	if ($connection->query($query))
	{
		$indexGalleryHTML .= "Gallery is currently empty. Why not make the FIRST post?";
	}
}

$statusText = '';
if (isset($_GET['deleted'])) {
	$statusText = 'Post deleted successfully.';
}
if (isset($_GET['csrf'])) {
	$statusText = 'For security reasons, you should only use one tab at a time.';
}
?>



<!-- Page body -->
<div class='statusText'><?= $statusText ?></div>
<p>You can <a href='/take_pic.php' class='buttonStyleLink'>SNAP</a> a new picture with a webcam or upload a file:</p>
<!--
<form action='/file_upload.php' method='post'>
	<input type='file' name='picUpload'>
	<input type='submit' name='submit'>
</form>
-->
<div id='indexGallery' class='container'>
	<?= $indexGalleryHTML ?>
</div>

<div id='galleryNextPrevNavigation'>
	<div class='floatLeft'>
		<?= $prevPageLink ?>
	</div>
	<div class='floatRight'>
		<?= $nextPageLink ?>
	</div>
</div>


<?php
require_once($templates_path . "footer.php");
?>
