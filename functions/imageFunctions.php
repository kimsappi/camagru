<?php
function cropAndResizeImage(&$image)
{
	require($_SERVER["DOCUMENT_ROOT"] . "/require.php");
	require($config_path . "config.php");

	$maxRes = $maxImageRes; // config.php
	$width = imagesx($image);
	$height = imagesy($image);
	$squareSize = min([$width, $height]);

	/* Create new square image canvas */
	$squareImage = imagecreate(min([$squareSize, $maxRes]), min([$squareSize, $maxRes]));
	
	/* Original image larger than maxRes, need to downscale image */
	if ($squareSize > $maxRes)
		imagecopyresampled($squareImage, $image,
			0, 0, // Destination offset coordinates (width, height)
			($width - $squareSize) / 2, ($height - $squareSize) / 2, // Source offset coordinates
			$maxRes, $maxRes, // Destination width, height
			$squareSize, $squareSize // Source width, height
		);
	/* Otherwise just copy a square part onto the square canvas */
	else
		imagecopy($squareImage, $image,
			0, 0, // Destination offset coordinates (width, height)
			($width - $squareSize) / 2, ($height - $squareSize) / 2, // Source offset coordinates
			$squareSize, $squareSize // Source width, height
		);
	
	return [
		'image' => $squareImage,
		'size' => $squareSize
	];
}
