<?php
$head_title = "Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
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
$query = "SELECT `id`, `extension` FROM posts ORDER BY `id` DESC";
foreach ($connection->query($query) as $imageData)
{
	$fileName = $imageData['id'] . '.' . $imageData['extension'];
	echo <<<EOD
		<img src="$uploads_path_url$fileName" class='thumbnail'>
	EOD;
}
?>

</div>

<?php
require_once($templates_path . "footer.php");
?>
