if ( 0 == jQuery( '#picture_id' ).val() ) {
		jQuery( '.remove_image_button' ).hide();
	}

	var file_frame;

	jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

		event.preventDefault();


		if ( file_frame ) {
			file_frame.open();
			return;
		}


		file_frame = wp.media.frames.downloadable_file = wp.media({
			title: 'Choose an image',
			button: {
				text: 'Use image'
			},
			multiple: false
		});

		
		file_frame.on( 'select', function() {
			var attachment = file_frame.state().get( 'selection' ).first().toJSON();
			var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

			jQuery( '#picture_id' ).val( attachment.id );
			jQuery( '#product_thumbnail' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
			jQuery( '.remove_image_button' ).show();
		});

		file_frame.open();
	});

	jQuery( document ).on( 'click', '.remove_image_button', function() {
		jQuery( '#product_thumbnail' ).find( 'img' ).attr( 'src', '' );
		jQuery( '#picture_id' ).val( '' );
		jQuery( '.remove_image_button' ).hide();
		return false;
	});

	jQuery( document ).ajaxComplete( function( event, request, options ) {
		if ( request && 4 === request.readyState && 200 === request.status
			&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {

			var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
			if ( ! res || res.errors ) {
				return;
			}
			
			jQuery( '#product_thumbnail' ).find( 'img' ).attr( 'src', '' );
			jQuery( '#picture_id' ).val( '' );
			jQuery( '.remove_image_button' ).hide();
			return;
		}
	} );


// Очищаем поля
jQuery(document).on('click', '#custom-button-reset', function(e) {
	e.preventDefault();

	var $dateField = jQuery('#_datafield');
	var $typeField = jQuery('#_producttype');
	var $imageField = jQuery('#picture_id');
	var $image = jQuery('#picture_img');

	$dateField.val('');
	$typeField.val('');
	$imageField.val('');
	$image.attr('src', '');
})