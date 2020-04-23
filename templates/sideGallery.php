<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");

if (!isset($connection))
{
	require_once($functions_path . "dbConnect.php");
	$connection = dbConnect();
}

if (!$connection)
{
	echo "The site appears to be broken";
	exit();
}

$sideGalleryHTML = '';
$query = "SELECT * FROM `posts` ORDER BY `id` DESC;";
foreach ($connection->query($query) as $galleryPost)
{
	$galleryPostId = $galleryPost['id'];
	$galleryPostFileName = $galleryPostId . '.' . $galleryPost['extension'];

	// Don't show current post in side gallery in post view
	if (isset($postId) && $galleryPostId != $postId)
	{
		$sideGalleryHTML .= <<<EOD
		<a href='/post.php?id=$galleryPostId'>
			<img src="$uploads_path_url$galleryPostFileName" class='sideGalleryImage'>
		</a>
EOD;
	}
}

echo $sideGalleryHTML;
?>
