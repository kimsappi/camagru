// Sets max height of side gallery to height of post image or webcam view
const resizeSideGalleryOnResize = () => {
	const mainElement = document.getElementById('postMainImage') || document.getElementById('webcam_container');
	const height = getComputedStyle(mainElement).height;
	const postSideGallery = document.getElementById('postSideGallery');
	postSideGallery.style.maxHeight = height;
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

const resizeMainImage = () => {
	if (!document.getElementById('img_preview'))
		return;
	const imgPreview = document.getElementById('img_preview');
	imgPreview.style.height = getComputedStyle(imgPreview).width;

	const filterPreview = document.getElementById('filter_preview');
	const filterSizeReferenceElement = 
		document.getElementById('webcam') && document.getElementById('webcam').style.display !== 'none' ?
		document.getElementById('webcam') :
		document.getElementById('img_preview');
	filterPreview.style.height = getComputedStyle(filterSizeReferenceElement).height;
	// console.log(getComputedStyle(filterSizeReferenceElement).height);
	// console.log(filterSizeReferenceElement.outerHTML);
	filterPreview.style.width = filterPreview.style.height;

	// if (!document.getElementById('webcam'))
	// 	return;
	// const webcam = document.getElementById('webcam');
	// webcam.style.width = getComputedStyle(webcam).height;
}

window.addEventListener('resize', resizeSideGalleryOnResize);
window.addEventListener('load', resizeSideGalleryOnResize);
window.addEventListener('resize', centerPostMainImage);
window.addEventListener('load', centerPostMainImage);
window.addEventListener('resize', resizeMainImage);

if (document.getElementById('webcam_container')) {
	const webcamContainer = document.getElementById('webcam');
	webcamContainer.addEventListener('loadeddata', resizeSideGalleryOnResize);
	webcamContainer.addEventListener('loadeddata', centerPostMainImage);
}

// Trying to make uploaded img preview square
// const resizeUploadedImage = () => {
// 	const imageElement = document.getElementById('img_preview');
// 	const imageContainer = imageElement.parentElement;
// 	//imageContainer.width = '300px';

// 	//imageElement.style.height = imageElement.parentElement.height;
// }

/*
** Resizing doesn't work properly upon loading the webcam view even with the above.
** Could probably be fixed by adding a load eventListener to the video, but...
*/
setInterval(() => {
	resizeSideGalleryOnResize();
	centerPostMainImage();
	resizeMainImage();
	// resizeUploadedImage();
}, 1000);
