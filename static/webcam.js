let imageCapture = null;

function initialiseWebcamStreamOnload()
{
	/* Open webcam stream and direct it to <video>, alert if error */
	navigator.mediaDevices.getUserMedia({video: {facingMode: "user"}, audio: false})
		.then((mediaStream) =>
		{
			let webcamElement = document.getElementById("webcam");
			webcamElement.srcObject = mediaStream;
			imageCapture = new ImageCapture(mediaStream.getVideoTracks()[0]);
		})
		.catch(() =>
		{
			alert("Please make sure you have a webcam and allow your browser access to it.");
		})
	;
	
	/* Add eventListener for button to take/cancel photo */
	document.getElementById("take_pic_from_webcam").addEventListener("click", takePicFromWebcamStream);
	document.getElementById("cancel_pic_from_webcam").addEventListener("click", cancelPicFromWebcam);
}

function takePicFromWebcamStream()
{
	if (imageCapture)
	{
		imageCapture.takePhoto()
			.then((blob) =>
			{
				let previewElement = document.getElementById("img_preview");
				previewElement.src = URL.createObjectURL(blob);
				previewElement.style.visibility = "visible";
				changeElementDisplay("cancel_pic_from_webcam", "inline-block");
			})
			.catch(() =>
			{
				alert("Please enable your webcam.");
			})
		;
	}
}

function cancelPicFromWebcam()
{
	let previewElement = document.getElementById("img_preview");
	if (previewElement.src)
	{
		previewElement.style.visibility = "hidden";
		URL.revokeObjectURL(previewElement.src);
		previewElement.src = "#";
	}
}

function changeElementDisplay(id, display)
{
	let element = document.getElementById(id);
	element.style.display = display;
}