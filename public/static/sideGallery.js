// Sets max height of side gallery to height of post image or webcam view
const resizeSideGalleryOnResize = () => {
	const mainElement = document.getElementById('postMainImage') || document.getElementById('webcam_container');
	const height = mainElement.offsetHeight;
	const postSideGallery = document.getElementById('postSideGallery');
	postSideGallery.style.maxHeight = height + 'px';
}

// Centers post image or webcam view within its container and
// aligns left side of gallery with webcam or image of necessary
const centerPostMainImage = () => {
	const imageElements = document.querySelectorAll('.resizeSelectorClass');
	const containerElement = document.getElementById('postMainImage') || document.getElementById('webcam_container');
	const containerWidth = containerElement.offsetWidth;
	const imageWidth = imageElements[0].offsetWidth || imageElements[1].offsetWidth;
	const leftMargin = (containerWidth - imageWidth) / 2;
	const sideGallery = document.getElementById('postSideGallery');

	if (leftMargin < 20) {
		sideGallery.style.paddingLeft = leftMargin + 'px';
	}
	
	if (leftMargin > 0) {
		imageElements.forEach(element => element.style.marginLeft = leftMargin + 'px');
	}
	else {
		imageElements.forEach(element => {
			element.style.marginLeft = '0px';
			element.style.marginRight = '0px';
		});

		sideGallery.style.paddingLeft = '0px';
	}
}

window.addEventListener('resize', resizeSideGalleryOnResize);
window.addEventListener('load', resizeSideGalleryOnResize);
window.addEventListener('resize', centerPostMainImage);
window.addEventListener('load', centerPostMainImage);

if (document.getElementById('webcam_container')) {
	const webcamContainer = document.getElementById('webcam');
	webcamContainer.addEventListener('loadeddata', resizeSideGalleryOnResize);
	webcamContainer.addEventListener('loadeddata', centerPostMainImage);
}

/*
** Resizing doesn't work properly upon loading the webcam view even with the above.
** Could probably be fixed by adding a load eventListener to the video, but...
*/
setInterval(() => {
	resizeSideGalleryOnResize();
	centerPostMainImage();
}, 5000);
