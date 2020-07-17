// Initial value for webcam size
let size = 720;
let imageBlob = null;
let videoIsPlaying = false;

function initialiseWebcamStreamOnload()
{
	/* Open webcam stream and direct it to <video>, alert if error */
	navigator.mediaDevices.getUserMedia({video: {facingMode: "user"}, audio: false})
		.then((mediaStream) =>
		{
			/* Set webcam to stream to #webcam element */
			const webcamElement = document.getElementById("webcam");
			webcamElement.srcObject = mediaStream;

			/* Set maximum available resolution for webcam, up to 720 */
			let facingMode = 'user';
			if (mediaStream.getVideoTracks()[0].getCapabilities === 'function') {
				const capabilities = mediaStream.getVideoTracks()[0].getCapabilities();
				size = Math.min(capabilities.height.max, capabilities.width.max, 720);
				facingMode = capabilities.facingMode;
			}
			const constraints = {height: {min: 200, ideal: 720}, width: {min: 200, ideal: 720}, facingMode: facingMode};
			mediaStream.getVideoTracks()[0].applyConstraints(constraints)
				.then(() => {webcamElement.srcObject = mediaStream;});
		})
		.catch((e) =>
		{
			//console.log(e);
			alert("Please make sure you have a webcam and allow your browser access to it.");
		})
	;
	
	/* Add eventListener for button to take/cancel photo and upload photo */
	document.getElementById("take_pic_from_webcam").addEventListener("click", takePicFromWebcamStream);
	document.getElementById("cancel_pic_from_webcam").addEventListener("click", cancelPicFromWebcam);
	document.getElementById("webcam").addEventListener("canplay", webcamStreamStartedPlaying());
	document.getElementById("filter").addEventListener("change", loadNewFilterPreview);

	whyIsFirefoxNotStandardsCompliant();
}

const firefoxDefaultFilter = () => {
	const filterElement = document.getElementById('filter_preview');
	if (navigator.userAgent.search('Firefox') > -1) {
		filterElement.src = 'images/firefoxDefaultFilter.png';
	}
	else
	filterElement.src = '';
}

const whyIsFirefoxNotStandardsCompliant = () => {
	if (navigator.userAgent.search('Firefox') > -1) {
		const container = document.getElementById('firefoxErrorContainer');
		const div = document.createElement('div');
		div.classList.add('formExplanation');
		div.innerHTML = 'You appear to be using Firefox. As Firefox is not standards compliant, the final image will only contain the area covered by the filter.';
		container.appendChild(div);
		
		firefoxDefaultFilter();
	}
}

const webcamStreamStartedPlaying = () => {
	if (!videoIsPlaying) {
		videoIsPlaying = true;
		// This hack makes uploaded images square by initialising the canvas or something.
		// I have no idea why. It doesn't work if you just cancel...
		takePicFromWebcamStream();
		cancelPicFromWebcam();
	}
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
		button.style.backgroundColor = 'lightgreen';
	}
	else
	{
		button.removeEventListener("click", uploadPic);
		button.addEventListener("click", takePicFromWebcamStream);
		button.innerHTML = "Snap!";
		button.style.backgroundColor = 'lightblue';
	}
}

/*
** Capture a momentary snapshot and display it instead of the webcam
** stream. Change button functionality to allow cancellation/uploading.
*/
function takePicFromWebcamStream()
{
	const canvas = document.getElementById('canvas');
	const context = canvas.getContext('2d');
	const video = document.getElementById('webcam');
	//const dimensions = getComputedStyle(webcamContainer).height;
	const previewElement = document.getElementById("img_preview");
	// const imageElements = document.querySelectorAll('.resizeSelectorClass');
	const imageWidth = getComputedStyle(video).width;
	//console.log(imageWidth);

	// context.width = dimensions;
	// context.height = dimensions;
	// context.canvas.width = dimensions;
	// context.canvas.height = dimensions;
	
	canvas.width = parseInt(imageWidth);
	canvas.height = canvas.width;
	//console.log('c' + canvas.height);
	context.drawImage(video, 0, 0, 720, 720, 0, 0, canvas.height, canvas.height);

	const imageData = canvas.toDataURL();
	previewElement.setAttribute('src', imageData);
	canvas.toBlob(blob => imageBlob = blob);
	// if (imageCapture)
	// {
	// 	imageCapture.takePhoto()
	// 		.then((blob) =>
	// 		{
	// 			const previewElement = document.getElementById("img_preview");
				const webcamElement = document.getElementById("webcam");
	// 			const size = webcamElement.offsetHeight;
	// 			previewElement.src = URL.createObjectURL(blob);
	 			previewElement.style.display = "block";
	 			previewElement.style.width = size + 'px';
	 			previewElement.style.height = size + 'px';
	 			webcamElement.style.display = "none";
	 			changeElementDisplay("cancel_pic_from_webcam", "inline-block");
				changeTakePicButtonFunctionality(true);
	// 			imageBlob = blob;
	// 		})
	// 		.catch(() =>
	// 		{
	// 			alert("Please enable your webcam.");
	// 		})
	// 	;
	// }

	previewElement.style.height = getComputedStyle(previewElement).width;
}

/*
** Redisplay webcam stream, change buttons back to initial state.
*/
function cancelPicFromWebcam()
{
	let previewElement = document.getElementById("img_preview");
	let webcamElement = document.getElementById("webcam");
	const canvas = document.getElementById('canvas');

	const imageElements = document.querySelectorAll('.resizeSelectorClass');
	const imageWidth = imageElements[0].offsetWidth || imageElements[1].offsetWidth;
	canvas.height = imageWidth;
	canvas.width = imageWidth;

	const context = canvas.getContext('2d');
	context.fillStyle = "#AAA";
    context.fillRect(0, 0, canvas.width, canvas.height);
	const data = canvas.toDataURL('image/png');
    previewElement.setAttribute('src', data);

	imageBlob = null;
	// if (previewElement.src)
	// {
		previewElement.style.display = "none";
		webcamElement.style.display = "block";
		URL.revokeObjectURL(previewElement.src);
		previewElement.src = "#";
	// }
	changeElementDisplay("cancel_pic_from_webcam", "none");
	changeTakePicButtonFunctionality(false);
}

function uploadPic(uploadedFile = null)
{
	const filterElement = document.getElementById('filter');
	if (!filterElement.value)
		alert('You must select a filter before uploading!');

	else {
		let data = new FormData();
		if (uploadedFile instanceof Event)
			data.append("imageBlob", imageBlob, "image");
		else {
			data.append("imageBlob", uploadedFile, "image");
		}
		data.append("filter", document.getElementById('filter').value);
		fetch("/upload.php", {
			method: 'post',
			body: data
		})
			.then(response => response.json())
			.then(newId => {
				let redir = '';
				if (newId)
					redir = 'post.php?id=' + newId;
				else
					redir = 'index.php';
				window.location.href = redir;
			});
	}
}

function changeElementDisplay(id, display)
{
	let element = document.getElementById(id);
	element.style.display = display;
}

/*
** User has uploaded an image, display it and remove webcam functionality
*/
const userSelectsUploadFile = () => {
	// Remove cancel button
	const cancelButton = document.getElementById('cancel_pic_from_webcam');
	cancelButton.style.display = 'none';

	const inputElement = document.getElementById('fileUpload');
	const fileData = inputElement.files[0];
	if (!fileData)
		return;
	if (!fileData.type.startsWith('image/')) {
		alert('You can only upload image files.');
		inputElement.value = null;
		return;
	}
	const imgElement = document.getElementById('img_preview');
	imgElement.src = '';
	imgElement.file = fileData;
	document.getElementById('webcam').remove();
	changeElementDisplay('img_preview', 'block');
	changeElementDisplay('take_pic_from_webcam', 'none');
	changeElementDisplay('upload_old_pic', 'block');
	document.getElementById("upload_old_pic").addEventListener("click", uploadOldPic);

	const reader = new FileReader();
	reader.onload = (img => e => img.src = e.target.result)(imgElement);
	reader.readAsDataURL(fileData);

	document.getElementById('fileUploadLabel').style.display = 'none';
	document.querySelector('.uploadInfo').style.display = 'none';
}

/*
** User clicks upload button, upload image to server
*/
const uploadOldPic = () => {
	const inputElement = document.getElementById('fileUpload');
	const fileData = inputElement.files[0];
	if (!fileData)
		return;
	if (!fileData.type.startsWith('image/')) {
		alert('You can only upload image files.');
		inputElement.value = null;
		return;
	}
	uploadPic(fileData);
}

/*
** Changes overlay filter preview
*/
const loadNewFilterPreview = event => {
	if (event.target.value) {
		const filterPreview = document.getElementById('filter_preview');
		filterPreview.src = 'images/filters/' + event.target.value;
	}
	else
		firefoxDefaultFilter();
}
