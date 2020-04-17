let imageCapture = null;
let imageBlob = null;

function initialiseWebcamStreamOnload()
{
	/* Open webcam stream and direct it to <video>, alert if error */
	navigator.mediaDevices.getUserMedia({video: {facingMode: "user"}, audio: false})
		.then((mediaStream) =>
		{
			/* Set webcam to stream to #webcam element */
			const webcamElement = document.getElementById("webcam");
			webcamElement.srcObject = mediaStream;

			/* Set maximum available resolution for webcam, up to 1080 */
			const capabilities = mediaStream.getVideoTracks()[0].getCapabilities();
			const size = Math.min(capabilities.height.max, capabilities.width.max, 1080);
			const constraints = {height: size, width: size, facingMode: capabilities.facingMode};
			mediaStream.getVideoTracks()[0].applyConstraints(constraints);

			/* Set imageCapture object to video track for photo taking */
			imageCapture = new ImageCapture(mediaStream.getVideoTracks()[0]);
		})
		.catch((e) =>
		{
			alert("Please make sure you have a webcam and allow your browser access to it.");
		})
	;
	
	/* Add eventListener for button to take/cancel photo */
	document.getElementById("take_pic_from_webcam").addEventListener("click", takePicFromWebcamStream);
	document.getElementById("cancel_pic_from_webcam").addEventListener("click", cancelPicFromWebcam);
}

/*
** Change functionality of the take pic button to upload button (toUpload: true)
** or back to take pic (toUpload: false)
*/
function changeTakePicButtonFunctionality(toUpload)
{
	let button = document.getElementById("take_pic_from_webcam");
	if (toUpload)
	{
		button.removeEventListener("click", takePicFromWebcamStream);
		button.addEventListener("click", uploadPic);
		button.innerHTML = "Upload!";
	}
	else
	{
		button.removeEventListener("click", uploadPic);
		button.addEventListener("click", takePicFromWebcamStream);
		button.innerHTML = "Snap!";
	}
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
				changeTakePicButtonFunctionality(true);
				imageBlob = blob;
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
	imageBlob = null;
	if (previewElement.src)
	{
		previewElement.style.visibility = "hidden";
		URL.revokeObjectURL(previewElement.src);
		previewElement.src = "#";
	}
	changeElementDisplay("cancel_pic_from_webcam", "none");
	changeTakePicButtonFunctionality(false);
}

function uploadPic()
{
	let data = new FormData();
	data.append("imageBlob", imageBlob, "image");
	data.append("filter", "1");
	fetch("/upload.php", {
		method: 'post',
		body: data
	})
		.then(window.location.href='/');
	//let request = new XMLHttpRequest();
	//request.open("POST", "/upload.php");
	//request.send(data);
}

function changeElementDisplay(id, display)
{
	let element = document.getElementById(id);
	element.style.display = display;
}
