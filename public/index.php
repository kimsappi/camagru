<?php
$head_title = "Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($config_path . "config.php");
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");
?>
<button onclick="window.location.href = '/take_pic.php';">Snap!</button>

<div id='indexGallery'>
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
		<div><a href="/?page=$prevPage">Previous page</a></div>
EOD;
}

$imagesFound = 0;
foreach ($connection->query($query) as $imageData)
{
	$imageId = $imageData['id'];
	$fileName = $imageId . '.' . $imageData['extension'];
	echo <<<EOD
	<a href="post.php?id=$imageId">
		<img src="$uploads_path_url$fileName" class='thumbnail' alt='Thumbnail'>
	</a>
EOD;
	++$imagesFound;

	
	if ($imagesFound >= $posts_per_page)
	{
		$nextPage = $currentPage + 1;
		$nextPageLink = <<<EOD
			<div><a href="/?page=$nextPage">Next page</a></div>
EOD;
		break;
	}
}

if (!$imagesFound)
{
	$query = "SELECT `id` FROM posts LIMIT 1;";
	if ($connection->query($query))
	{
		echo "Gallery is currently empty. Why not make the FIRST post?";
	}
}
?>

</div>

<div id='galleryNextPrevNavigation'>
	<?php
	echo $prevPageLink;
	echo $nextPageLink;
	?>
</div>


<?php
require_once($templates_path . "footer.php");
?>
