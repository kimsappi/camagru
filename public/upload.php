<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !$_POST || !$_FILES)
{
	header("Location: /");
	exit();
}
error_log(print_r($_FILES, true));
// !isset($_FILES["imageBlob"]) || !isset($_FILES["imageBlob"]["type"]) || $_FILES["imageBlob"]["type"] !== "image/png" || $_FILES["imageBlob"]["size"] > 5000000
require_once($functions_path . "imageFunctions.php");

$uploadedImage = imagecreatefrompng($_FILES["imageBlob"]["tmp_name"]);
$resizedImage = cropAndResizeImage($uploadedImage);
$src = imagecreatefrompng($filters_path . "testi.png");
imagecopymerge($resizedImage['image'], $src, 0, 0, 0, 0, $resizedImage['size'], $resizedImage['size'], 30);

require_once($functions_path . "dbConnect.php");
if (!$connection = dbConnect())
{
	header("Location: /register.php");
	exit();
}

$extension = 'png';
$query = $connection->prepare(
	"INSERT INTO posts (`user_id`, `extension`)
		VALUES (?, ?)"
);
$query->execute([$_SESSION['user_id'], $extension]);
$newPostId = $connection->lastInsertId();
error_log('New post added, id: ' . $newPostId);

imagepng($resizedImage['image'], $uploads_path . $newPostId . "." . $extension);
error_log('Square size of new image: ' . $resizedImage['size']);
