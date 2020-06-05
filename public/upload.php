<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !$_POST || !$_FILES || !isset($_POST['filter']) || !strlen($_POST['filter']) || !isset($_FILES["imageBlob"]) || !isset($_FILES["imageBlob"]["type"]) || substr($_FILES["imageBlob"]["type"], 0, 6) !== "image/" || $_FILES["imageBlob"]["size"] > 5000000)
{
	header("Location: /");
	exit();
}
error_log(print_r($_FILES, true));
error_log(print_r($_POST, true));

require_once($functions_path . "imageFunctions.php");

if (!file_exists($filters_path . $_POST['filter']) || ($_FILES["imageBlob"]["type"] !== 'image/png' && $_FILES["imageBlob"]["type"] !== 'image/jpeg'))
{
	header("Location: /");
	exit();
}
$filterSrc = imagecreatefrompng($filters_path . $_POST['filter']);

if ($_FILES["imageBlob"]["type"] === 'image/png')
	$uploadedImage = imagecreatefrompng($_FILES["imageBlob"]["tmp_name"]);
elseif ($_FILES["imageBlob"]["type"] === 'image/jpeg')
	$uploadedImage = imagecreatefromjpeg($_FILES["imageBlob"]["tmp_name"]);

$resizedImage = cropAndResizeImage($uploadedImage);

imagepng($resizedImage['image'], $uploads_path . 'imagepngresult.png');
// imagecreatetruecolor MANDATORY for good quality
$filterSquare = imagecreatetruecolor($resizedImage['size'], $resizedImage['size']);
imagecopyresampled($filterSquare, $filterSrc, 0, 0, 0, 0, $resizedImage['size'], $resizedImage['size'], imagesx($filterSrc), imagesy($filterSrc));
imagecopymerge($resizedImage['image'], $filterSquare, 0, 0, 0, 0, $resizedImage['size'], $resizedImage['size'], 30);

require_once($functions_path . "dbConnect.php");
if (!$connection = dbConnect())
{
	header("Location: /");
	exit();
}

$extension = 'png';
$query = $connection->prepare(
	"INSERT INTO posts (`user_id`, `extension`)
		VALUES (?, ?);"
);
$query->execute([$_SESSION['user_id'], $extension]);
$newPostId = $connection->lastInsertId();
error_log('New post added, id: ' . $newPostId);

imagepng($resizedImage['image'], $uploads_path . $newPostId . "." . $extension);
rename($_FILES["imageBlob"]["tmp_name"], $uploads_path . 'snap.png');
error_log('Square size of new image: ' . $resizedImage['size']);
