(function($) {

// Object for creating WordPress 3.5 media upload menu 
// for selecting skin images.
wp.media.shibaGalleryMediaManager = {
	
	init: function() {
		// Create the media frame.
		this.frame = wp.media.frames.shibaGalleryMediaManager = wp.media({
			title: 'Choose Default Image',
			library: {
				type: 'image'
			},
			button: {
				text: 'Set as default image',
			}
		});
		
		// When an image is selected, run a callback.
		this.frame.on( 'select', function() {
			// Grab the selected attachment.
			var attachment = wp.media.shibaGalleryMediaManager.frame.state().get('selection').first();
//			console.log(attachment);
			$("input[type=hidden][name='shiba_gallery_options[default_image]']").val(attachment.attributes.id);
			$('#shiba-gallery-default-image img').attr("src", attachment.attributes.url).show();
			// Set width and height - limit it to 200x200
			var w = attachment.attributes.width,
				h = attachment.attributes.height;
		});
			
		// Create the frame
		$('#choose-default-image').click( function( event ) {
//			console.log("choose default image");									   
			wp.media.shibaGalleryMediaManager.$el = $(this);
			event.preventDefault();

			wp.media.shibaGalleryMediaManager.frame.open();
		});
		
		$('#unset-default-image').click( function( event ) {
//			console.log("unset default image");									   
			$("input[type=hidden][name='shiba_gallery_options[default_image]']").val(0);
			$('#shiba-gallery-default-image img').hide();
//			$('#shiba-gallery_options').submit();
			event.preventDefault();			
		});
		
	} // end init
}; // end shibaGalleryMediaManager

wp.media.shibaGalleryMediaManager.init();

}(jQuery));