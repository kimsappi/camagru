<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");

if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf'])
{
	echo json_encode('csrf');
	exit();
}

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !$_POST || !$_FILES ||
	!isset($_POST['filter']) || !strlen($_POST['filter']) || !isset($_FILES["imageBlob"]) ||
	!isset($_FILES["imageBlob"]["type"]) || substr($_FILES["imageBlob"]["type"], 0, 6) !== "image/" ||
	$_FILES["imageBlob"]["size"] > 5000000 || !isset($_POST['opacity']) || !is_numeric($_POST['opacity']) ||
	$_POST['opacity'] < 19 || $_POST['opacity'] > 71)
{
	header("Location: /");
	exit();
}

require_once($functions_path . "imageFunctions.php");

if (!file_exists($filters_path . $_POST['filter']) || ($_FILES["imageBlob"]["type"] !== 'image/png' && $_FILES["imageBlob"]["type"] !== 'image/jpeg'))
{
	header("Location: /");
	exit();
}
$filterSrc = imagecreatefrompng($filters_path . $_POST['filter']);
imagesavealpha($filterSrc, TRUE);

if ($_FILES["imageBlob"]["type"] === 'image/png')
	$uploadedImage = imagecreatefrompng($_FILES["imageBlob"]["tmp_name"]);
elseif ($_FILES["imageBlob"]["type"] === 'image/jpeg')
	$uploadedImage = imagecreatefromjpeg($_FILES["imageBlob"]["tmp_name"]);
else {
	header("Location: /");
	exit();
}

$resizedImage = cropAndResizeImage($uploadedImage);

//imagepng($resizedImage['image'], $uploads_path . 'imagepngresult.png');
// imagecreatetruecolor MANDATORY for good quality
$filterSquare = imagecreatetruecolor($resizedImage['size'], $resizedImage['size']);
imagesavealpha($filterSquare, TRUE);
//imagealphablending($filterSquare, FALSE);
$transparency = imagecolorallocatealpha($filterSquare, 0, 0, 0, 127);
imagefill($filterSquare, 0, 0, $transparency);
imagecopyresampled($filterSquare, $filterSrc, 0, 0, 0, 0, $resizedImage['size'], $resizedImage['size'], imagesx($filterSrc), imagesy($filterSrc));

// Hack for imagecopymerge alpha channel darkness problem
$tempTransparencyLayer = imagecreatetruecolor($resizedImage['size'], $resizedImage['size']);
imagecopy($tempTransparencyLayer, $resizedImage['image'], 0, 0, 0, 0, $resizedImage['size'], $resizedImage['size']);
imagecopy($tempTransparencyLayer, $filterSquare, 0, 0, 0, 0, $resizedImage['size'], $resizedImage['size']);
imagecopymerge($resizedImage['image'], $tempTransparencyLayer, 0, 0, 0, 0, $resizedImage['size'], $resizedImage['size'], $_POST['opacity']);

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
// error_log('New post added, id: ' . $newPostId);

if (!file_exists($uploads_path))
	mkdir($uploads_path);
imagepng($resizedImage['image'], $uploads_path . $newPostId . "." . $extension);
//rename($_FILES["imageBlob"]["tmp_name"], $uploads_path . 'snap.png');
// error_log('Square size of new image: ' . $resizedImage['size']);
//error_log($newPostId);
echo json_encode($newPostId);
