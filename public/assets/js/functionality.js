Dropzone.options.newPostForm = {
	paramName: "file-upload",
	dictDefaultMessage: "",
	previewsContainer: ".dropzone-previews",
	clickable: "#file-upload-label",
	maxThumbnailFilesize: 20,
	maxFilesize: 30,
	parallelUploads: 2,
	uploadMultiple: true
}

$(document).ready(function() {

	//scrolling for the header bar

	 $(window).scroll( function() {
        if ($(window).scrollTop() > $('#logged-in-tbar').offset().top)
            $('.second-scroll-wrapper').addClass('floating');
        else
            $('.second-scroll-wrapper').removeClass('floating');
    });


	//Do the images

	$(".the-post").each(function(){
		theContainer = $(this).children(".post-images").children(".image-container");

		if (theContainer.length == 1) {
			theContainer.width(490);
		}

		else if (theContainer.length == 2) {
			theContainer.width(243);
		}

	});

	arrangeImages();

});

function arrangeImages() {

	postMasonry = $(".post-images");

	postMasonry.imagesLoaded().done(function(){
		postMasonry.isotope({
			itemSelector: ".image-container",
			masonry: {
				columnWidth: 245,
				gutterWidth: 5
			}
		});
	})
}