'use strict';
jQuery(document).ready(function($) {	

   setTimeout(function(){
			$(".data .options .variable_is_downloadable").on('change', function(e) {
				var variation_check=$(this);
				var variable_download_status = '';
				var variation_id = $(variation_check).parent().parent().parent().find(".form-row-first a").attr("rel");
				if(variation_check.is(':checked')){
					variable_download_status = 1;
				} else {
					variable_download_status = 0;
				}	  	
				
				$.ajax({
					url : WooImgWtm.ajax_url,
					type : 'post',
					data : {
						action : 'wpw_update_status_for_variable_downloadable',               
						post_id : variation_id,
						download_status : variable_download_status
					},
					success : function( response ) {
						
					}
			});
	  });
	},5500); 	


	/* jQuery trigger on change of drop-down on downloadable selection */
	var orginal_upload_url = $("#set-post-thumbnail").attr("href");
	$('#_downloadable').change(function() {
	
		var post_id = $("#post_ID").val();
		var product_download_status = '';
		if(this.checked) {
			
			product_download_status = 1
	
		} else {

			product_download_status = 0	

		}
		$.ajax({
				url : WooImgWtm.ajax_url,
				type : 'post',
				data : {
					action : 'wpw_update_status_for_simple_downloadable',               
					post_id : post_id,
					download_status : product_download_status
				},
				success : function( response ) {
					
				}
		});
        
    });

	//Media Uploader
	$( document ).on( 'click', '.woo-wtm-upload-preview-button', function() {
	
		var imgfield,showfield;
		imgfield = jQuery(this).prev('input').attr('id');
		showfield = jQuery(this).parents('td').find('.woo-img-wtm-img-view');
		 
		if(typeof wp == "undefined" || WooImgWtm.new_media_ui != '1' ){// check for media uploader
				
			tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
	    	
			window.original_send_to_editor = window.send_to_editor;
			window.send_to_editor = function(html) {
				
				if(imgfield)  {
					
					var mediaurl = $('img',html).attr('src');
					mediaurl = mediaurl.replace(WooImgWtm.upload_base_url,'');
					$('#'+imgfield).val(mediaurl);
					showfield.html('<img src="'+mediaurl+'" />');
					tb_remove();
					imgfield = '';
					
				} else {
					
					window.original_send_to_editor(html);
					
				}
			};
	    	return false;
			
		      
		} else {
			
			
			var file_frame;
			
			//new media uploader
			var button = jQuery(this);
			
			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				file_frame.open();
			  return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				frame: 'post',
				state: 'insert',
				multiple: false  // Set to true to allow multiple files to be selected
			});
	
			file_frame.on( 'menu:render:default', function(view) {
		        // Store our views in an object.
		        var views = {};
	
		        // Unset default menu items
		        view.unset('library-separator');
		        view.unset('gallery');
		        view.unset('featured-image');
		        view.unset('embed');
	
		        // Initialize the views in our view object.
		        view.set(views);
		    });
			
			file_frame.on( 'insert', function() {
	
				// Get selected size from media uploader
				var selected_size = $('.attachment-display-settings .size').val();
				
				
				var selection = file_frame.state().get('selection');
				selection.each( function( attachment, index ) {
					attachment = attachment.toJSON();
					
					// Selected attachment url from media uploader
					var attachment_url = attachment.sizes[selected_size].url;
					var media = '';
					if(index == 0){
						// place first attachment in field
						media = attachment_url.replace(WooImgWtm.upload_base_url,'');
						$('#'+imgfield).val(media);
						showfield.html('<img src="'+attachment_url+'" />');
						
					} else{
						media = attachment_url.replace(WooImgWtm.upload_base_url,'');
						$('#'+imgfield).val(media);
						showfield.html('<img src="'+attachment_url+'" />');
					}
				});
			});

			file_frame.open();

		}
		
	});
	

	$(document).on( 'change', "input[name*='_repeated_on_image']", function(){

		if( $(this).is(":checked")){
			$(this).closest('tr').next('tr').hide();
		} else{
			$(this).closest('tr').next('tr').show();
		}
	});
 	
 	$("input[name*='_repeated_on_image']").each( function(){

 		if( $(this).is(":checked")){
			$(this).closest('tr').next('tr').hide();
		} else{
			$(this).closest('tr').next('tr').show();
		}
 	});

}); // end jQuery(document).ready

