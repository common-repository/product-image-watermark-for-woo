<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb, $wpe_img_wtm_model;
?>

<div id="message" class="updated fade"></div>

<div class="wrap wpeimgwtm_regenthumbs">
	<h2><?php esc_html_e('Regenerate Thumbnails', 'wpe_img_wtm'); ?></h2>

	<?php
		// If the button was clicked
		if ( ! empty( $_POST['wpeimgwtm_regen_thumb'] ) || ! empty( $_REQUEST['ids'] ) ) {

			// Form nonce check
			check_admin_referer( 'wpeimgwtm_regen_thumb' );

			// Create the list of image IDs
			if ( ! empty( $_REQUEST['ids'] ) ) {
				$images = array_map( 'intval', explode( ',', trim( $wpe_img_wtm_model->wte_img_wtm_escape_slashes_deep($_REQUEST['ids']), ',' ) ) );
				$ids 	= implode( ',', $images );
			} else {
				// Directly querying the database is normally frowned upon, but all
				// of the API functions will return the full post objects which will
				// suck up lots of memory. This is best, just not as future proof.
				if ( ! $images = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' ORDER BY ID DESC" ) ) {
					echo '	<p>' . sprintf( esc_html__( "Unable to find any images. Are you sure %ssome exist%s?", 'wpe_img_wtm' ), '<a href="'.admin_url( 'upload.php?post_mime_type=image' ).'">', '</a>' ) . "</p></div>";
					return;
				}

				// Generate the list of IDs
				$ids = array();
				foreach ( $images as $image ){
					$ids[] = $image->ID;
				}

			}

			echo '	<p>' . esc_html__( "Please be patient while the thumbnails are regenerated. This can take a while if your server is slow (inexpensive hosting) or if you have many images. Do not navigate away from this page until this script is done or the thumbnails will not be resized. You will be notified via this page when the regenerating is completed.", 'wpe_img_wtm' ) . '</p>';

			$count = count( $images );

			$text_goback 	 = 
			$text_failures   = sprintf( esc_html__( 'All done! %1$s image(s) were successfully resized in %2$s seconds and there were %3$s failure(s).', 'wpe_img_wtm' ), "' + wpeimgwtm_rt_successes + '", "' + wpeimgwtm_rt_totaltime + '", "' + wpeimgwtm_rt_errors + '" );
			$text_nofailures = sprintf( esc_html__( 'All done! %1$s image(s) were successfully resized in %2$s seconds and there were 0 failures.', 'wpe_img_wtm' ), "' + wpeimgwtm_rt_successes + '", "' + wpeimgwtm_rt_totaltime + '" );
			?>

			<div id="wpeimgwtm_regen_thumb_bar">
				<div id="wpeimgwtm_regen_thumb_bar_percent"></div>
			</div>

			<p><input type="button" class="button hide-if-no-js" name="wpeimgwtm_regen_thumb_stop" id="wpeimgwtm_regen_thumb_stop" value="<?php esc_html_e( 'Abort Resizing Images', 'wpe_img_wtm' ) ?>" /></p>

			<h3 class="title"><?php esc_html_e( 'Debugging Information', 'wpe_img_wtm' ) ?></h3>

			<p>
				<?php printf( esc_html__( 'Total Images: %s', 'wpe_img_wtm' ), $count ); ?><br />
				<?php printf( esc_html__( 'Images Resized: %s', 'wpe_img_wtm' ), '<span id="wpeimgwtm_regen_thumb_success">0</span>' ); ?><br />
				<?php printf( esc_html__( 'Resize Failures: %s', 'wpe_img_wtm' ), '<span id="wpeimgwtm_regen_thumb_failure">0</span>' ); ?>
			</p>

			<ol id="wpeimgwtm_regen_thumb_debuglist">
				<li></li>
			</ol>
			<?php 
			wp_localize_script( 'wpe-img-wtm-admin-reg-imgs-scripts', 'WpeIMGRGEN', 
				array( 
					'ajaxurl'		=>	admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
					'text_failures'	=>	$text_failures,
					'text_nofailures'	=>	$text_nofailures,
					'ids'	=> $ids,
					'stopping' => esc_html__( 'Stopping...', 'wpe_img_wtm' )
				)
			);

			//Enque script for regerate images
            
            wp_enqueue_script( 'wpe-img-wtm-admin-reg-imgs-scripts' );
			
		} 
		else {
		?>
			<form method="post" action="">
				<?php wp_nonce_field('wpeimgwtm_regen_thumb') ?>

			<p><?php esc_html_e( "Thumbnail regeneration is not reversible, but you can just change your thumbnail dimensions back to the old values and click the button again if you don't like the results.", 'wpe_img_wtm' ); ?></p>

			<p><?php esc_html_e( 'To begin, just press the button below.', 'wpe_img_wtm'); ?></p>

			<p><input type="submit" class="button hide-if-no-js" name="wpeimgwtm_regen_thumb" id="regenerate-thumbnails" value="<?php esc_html_e( 'Regenerate All Thumbnails', 'wpe_img_wtm' ) ?>" /></p>

			</form>
		<?php
		} ?>
		</div>