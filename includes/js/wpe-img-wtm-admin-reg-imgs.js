'use strict';
jQuery(document).ready(function($){

    var i;
	var wpeimgwtm_rt_images = WpeIMGRGEN.ids;
    var wpeimgwtm_rt_total  = wpeimgwtm_rt_images.length;
    
    var wpeimgwtm_rt_count = 1;
	var wpeimgwtm_rt_percent = 0;
	var wpeimgwtm_rt_successes = 0;
	var wpeimgwtm_rt_errors = 0;
	var wpeimgwtm_rt_failedlist = '';
	var wpeimgwtm_rt_resulttext = '';
	var wpeimgwtm_rt_timestart = new Date().getTime();
	var wpeimgwtm_rt_timeend = 0;
	var wpeimgwtm_rt_totaltime = 0;
    var wpeimgwtm_rt_continue = true;
    
    // Create the progress bar
	$("#wpeimgwtm_regen_thumb_bar").progressbar();
    $("#wpeimgwtm_regen_thumb_bar_percent").html( "0%" );
    
    // Stop button
	$(document).on('click', "#wpeimgwtm_regen_thumb_stop",function() {
		wpeimgwtm_rt_continue = false;
		$('#wpeimgwtm_regen_thumb_stop').val(WpeIMGRGEN.stopping);
    });
    
    // Clear out the empty list element that's there for HTML validation purposes
    $("#wpeimgwtm_regen_thumb_debuglist li").remove();
    
    // Called after each resize. Updates debug information and the progress bar.
    function RegenThumbsUpdateStatus( id, success, response ) {

        $("#wpeimgwtm_regen_thumb_bar").progressbar( "value", ( wpeimgwtm_rt_count / wpeimgwtm_rt_total ) * 100 );
		$("#wpeimgwtm_regen_thumb_bar_percent").html( Math.round( ( wpeimgwtm_rt_count / wpeimgwtm_rt_total ) * 1000 ) / 10 + "%" );
        wpeimgwtm_rt_count = wpeimgwtm_rt_count + 1;
        
        if ( success ) {
			wpeimgwtm_rt_successes = wpeimgwtm_rt_successes + 1;
			$("#wpeimgwtm_regen_thumb_success").html(wpeimgwtm_rt_successes);
			$("#wpeimgwtm_regen_thumb_debuglist").append("<li>" + response.success + "</li>");
		} else {
			wpeimgwtm_rt_errors = wpeimgwtm_rt_errors + 1;
			wpeimgwtm_rt_failedlist = wpeimgwtm_rt_failedlist + ',' + id;
			$("#wpeimgwtm_regen_thumb_failure").html(wpeimgwtm_rt_errors);
			$("#wpeimgwtm_regen_thumb_debuglist").append("<li>" + response.error + "</li>");
		}

    }    


    // Called when all images have been processed. Shows the results and cleans up
    function RegenThumbsFinishUp() {
		wpeimgwtm_rt_timeend = new Date().getTime();
		wpeimgwtm_rt_totaltime = Math.round( ( wpeimgwtm_rt_timeend - wpeimgwtm_rt_timestart ) / 1000 );

		$('#wpeimgwtm_regen_thumb_stop').hide();

		if ( wpeimgwtm_rt_errors > 0 ) {
			wpeimgwtm_rt_resulttext = WpeIMGRGEN.text_failures;
		} else {
			wpeimgwtm_rt_resulttext = WpeIMGRGEN.text_nofailures;
		}

		$("#message").html("<p><strong>" + wpeimgwtm_rt_resulttext + "</strong></p>");
		$("#message").show();
    }
    
    // Regenerate a specified image via AJAX
    function RegenThumbs( id ) {

        $.ajax({

            type: 'POST',
			url: WpeIMGRGEN.ajaxurl,
			data: { action: "wpe_img_wtm_regenerate_thumb_process", id: id },
            success: function( response ) {
                if ( response !== Object( response ) || ( typeof response.success === "undefined" && typeof response.error === "undefined" ) ) {

                    response = new Object;
					response.success = false;

                }

                if ( response.success ) {
					RegenThumbsUpdateStatus( id, true, response );
				}
				else {
					RegenThumbsUpdateStatus( id, false, response );
				}

				if (  wpeimgwtm_rt_images.length && wpeimgwtm_rt_continue ) {
					RegenThumbs( wpeimgwtm_rt_images.shift() );
				}
				else {
					RegenThumbsFinishUp();
				}

            },
            error: function( response ) {
                
                RegenThumbsUpdateStatus( id, false, response );
                if ( wpeimgwtm_rt_images.length && wpeimgwtm_rt_continue ) {
					RegenThumbs(wpeimgwtm_rt_images.shift());
				}
				else {
					RegenThumbsFinishUp();
				}

            }    

        });    
    }
    RegenThumbs(wpeimgwtm_rt_images.shift());
}); 