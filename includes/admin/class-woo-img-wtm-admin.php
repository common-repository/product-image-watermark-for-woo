<?php 

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Model Class
 *
 * Handles generic plugin functionality.
 *
 * @package Product Image Watermark for Woocommerce
 * @since 1.0.0
 */

 class Wte_Img_Wtm_Admin{

    var $model, $scripts;
    public function __construct() {
    
        global $wpe_img_wtm_model, $wpe_img_wtm_scripts;
        $this->model = $wpe_img_wtm_model;
        $this->scripts = $wpe_img_wtm_scripts;
        
	}
	
	/**
	 * Delete Backup Image
	 *
	 * Handles to delete backup image from
	 * media folder when media is deleted
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */

	public function wpe_img_wtm_delete_backup_image($postid) {


		//get attachment data
		$attachment_data = get_post( $postid );
		if( isset( $attachment_data->guid ) && !empty( $attachment_data->guid ) ) { // Check file link is not empty
	
			//get upload file path
			$upload_dir = wp_upload_dir();
			$file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $attachment_data->guid );
			$file = basename($file_path);
			
			$backup_file_path = '_wpe_img_wtm_'.$file; 
			$backup_full_path = str_replace($file,$backup_file_path,$file_path);
			
			 //Check file is exist
			 if( !empty($backup_full_path ) && file_exists( $backup_full_path ) ) {
				unlink( $backup_full_path ); //delete file from server
			}
		}	
	}

	/**
	 * Code to apply watermark on images
	 *
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
	
	 public function wpe_img_wtm_apply_watermark($filepath, $type, $source_image_to_use){
		
		
		$save_as_file = $filepath;
		if ( is_file( $filepath ) ) { //Check valid path or not

			// Get overlay image size
			$original_image_details = getimagesize( $filepath );
			
			$func_type = preg_replace( '#image/#i', '', $original_image_details['mime'] );
		
			// List of allow image formats
			$acceptable_formats = array( 'jpeg', 'gif', 'png' );
			if ( ! in_array( $func_type, $acceptable_formats ) ) {
				return false;
			}

			$funcName = 'imagecreatefrom' . $func_type;

			ob_start();

			$original_image = $funcName( $filepath );	
			
			$error = ob_get_clean();

			if ( !$original_image ) {
				return false;
			} 

		} else {

			return false;

		}

		$watermark_position = !empty(get_option('wpe_img_wtm_'.$type.'_align')) ? get_option('wpe_img_wtm_'.$type.'_align') : '';
		
		// Get is watermark repeted on whole image

		$is_watermark_repeated = !empty(get_option('wpe_img_wtm_'.$type.'_repeated_on_image')) ? get_option('wpe_img_wtm_'.$type.'_repeated_on_image') : '';
		if ( !empty($watermark_position) || !empty($is_watermark_repeated)) { // Chck the watermark image position is set or not
			
			$watermark_image = !empty(get_option('wpe_img_wtm_'.$type.'_img')) ? get_option('wpe_img_wtm_'.$type.'_img') : '';
			
			if ( $watermark_image ) {
				
				$upload_dir  = wp_upload_dir();
				$watermark_image_path = $upload_dir['basedir'] . $watermark_image;
			
				if ( is_file( $watermark_image_path ) ) { // Check overlay image is set and valid path or not
					
					
					$overlay = imagecreatefrompng( $watermark_image_path );

					if ( $original_image && $overlay ) {

						imagealphablending( $overlay, false );
						imagesavealpha( $overlay, true );

						$original_image_width 	= imagesx( $original_image );
						$original_image_height 	= imagesy( $original_image );
						$watermark_image_width 	= imagesx( $overlay );
						$watermark_image_height = imagesy( $overlay );
					
						if($watermark_position != 'no-watermark' || $is_watermark_repeated == "no" ) {

							
							switch ( $watermark_position ) { // Check that overlay position is set or not.

								//top left
								case 'tl':
									$watermark_start_x = 0;
									$watermark_start_y = 0;
									break;
									
								//top center
								case 'tc':
									$watermark_start_x = ( $original_image_width/2 ) - ( $watermark_image_width/2 );
									$watermark_start_y = 0;
									break;
									
								//top right
								case 'tr':
									$watermark_start_x = $original_image_width - $watermark_image_width;
									$watermark_start_y = 0;
									break;
									
								// middle left
								case 'ml':
									$watermark_start_x = 0;
									$watermark_start_y = ( $original_image_height/2 ) - ( $watermark_image_height/2 );
									break;
									
								//middle center
								case 'mc':
									$watermark_start_x = ( $original_image_width/2 ) - ( $watermark_image_width/2 );
									$watermark_start_y = ( $original_image_height/2 ) - ( $watermark_image_height/2 );
									break;
									
								// middle right
								case 'mr':
									$watermark_start_x = $original_image_width - $watermark_image_width;
									$watermark_start_y = ( $original_image_height/2 ) - ( $watermark_image_height/2 );
									break;
									
								// bottom left
								case 'bl':
									$watermark_start_x = 0;
									$watermark_start_y = $original_image_height - $watermark_image_height;
									break;
								
								//bottom center
								case 'bc':
									$watermark_start_x = ( $original_image_width/2 ) - ( $watermark_image_width/2 );
									$watermark_start_y = $original_image_height - $watermark_image_height;
									break;
									
								//bottom right
								case 'br':
								default:
									$watermark_start_x = $original_image_width - $watermark_image_width;
									$watermark_start_y = $original_image_height - $watermark_image_height;
									break;

							}	

							// Copy another image from main image and overlay it.
							imagecopy( $original_image, $overlay, $watermark_start_x, $watermark_start_y, 0, 0, $watermark_image_width, $watermark_image_height );

						} 
						
						
						if($is_watermark_repeated == "yes")  { 
							
							$img_paste_x = 0;

							while( $img_paste_x <  $original_image_width ){
								$img_paste_y = 0;

								while( $img_paste_y < $original_image_height ){
									imagecopy( $original_image, $overlay, $img_paste_x, $img_paste_y, 0, 0, $watermark_image_width, $watermark_image_height );
									$img_paste_y += $watermark_image_height;
								}
								$img_paste_x += $watermark_image_width;
							}

					
						}	
						
						$funcname_generate = 'image' . $func_type;
						if ( $func_type == 'jpeg' ) {
							
							$jpeg_quality = apply_filters( 'wpe_img_wtm_jpeg_quality', 100 );
							$jpeg_quality = ( isset($jpeg_quality) && trim($jpeg_quality) != '' ) ? intval( $jpeg_quality ) : 75;
							
							$funcname_generate( $original_image, $save_as_file, $jpeg_quality );
							
						} elseif ( $func_type == 'png' ) {
							
							//Creating the transparent background for png image
							imagesavealpha($original_image, true);
							$transparent = imagecolorallocatealpha($original_image, 0, 0, 0,127);
						    imagefill($original_image, 0, 0, $transparent);
						    
						    $png_quality = apply_filters( 'wpe_img_wtm_png_quality', 6 );
							$png_quality = ( isset($png_quality) && trim($png_quality) != '' ) ? intval( $png_quality ) : 6;
						    
							$funcname_generate( $original_image, $save_as_file, $png_quality );
							
						} else {
							$funcname_generate( $original_image, $save_as_file );
						}
						return true;
					}	

				}	

			}	

		}	
		return false;
	 }

    /**
	 * Apply image watermark to all sized images when uploading a image
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */

    public function wpe_img_wtm_generate_image_watermark($data) {

		global $post,$product;

		$is_download = false;
		
		if ( isset( $_REQUEST['post_id'] ) && ( int ) $_REQUEST['post_id'] > 0 ) {
			
			$post_id = ( int ) $this->model->wte_img_wtm_escape_slashes_deep($_REQUEST['post_id']);
			
		} elseif ( isset( $_REQUEST['id'] ) && ( int ) $_REQUEST['id'] > 0 ) {
			
			$post_id = ( int ) $this->model->wte_img_wtm_escape_slashes_deep($_REQUEST['id']);
			
		} 	elseif ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'upload-attachment' )  {

			$http_referrer 	= $_SERVER['HTTP_REFERER'];
		}

		if ( $post_id > 0 ) { // check post id

			$post = get_post( $post_id );
			$http_referrer 	= $_SERVER['HTTP_REFERER'];
			
			if ( $post && $post->post_type == 'attachment' && ( int ) $post->post_parent > 0 ) {
				// get the real post that this attachment is for.
				// this happens when we call "Regnerate Thumbs"" and some other times.
				$post = get_post( $post->post_parent );
				$post_id = $post->ID;
			}
	
			$product = wc_get_product( $post_id );
			
			if( !empty( $product ) ) {

	            $type = $product->get_type();

				if( $type == 'simple' || $type == 'variation' ){
					
					$is_product_enabled = get_post_meta($post_id,'_downloadable',true);

					if (($post && $post->post_type == WPE_IMG_WTM_MAIN_POSTTYPE && $product->is_downloadable('yes')) || $is_product_enabled) { // checked if post_type is download or not?
								
							$is_download = true;
							
					} 
					elseif ( $post && $post->post_type == 'product_variation' && $is_product_enabled ) { // checked if post_type is product variation or not?
						$is_download = true;
					}
					

				} elseif( $type == 'variable' ){
					
					$available_variations = $product->get_available_variations();
					
					if( !empty( $available_variations ) ) {	

						foreach($available_variations as $key => $value){

							$is_download_enabled = get_post_meta($value['variation_id'],'_downloadable',true);
							if($value['is_downloadable'] == 1 || $is_download_enabled == 1){

								$is_download = true;

							} 
						}
					}
				}
			}	
			

			// added a filter to allow watermark for other post types
	     	$is_download = apply_filters( 'wpe_img_wtm_is_download', $is_download );

			 if ( !$is_download ) { 
				 return $data;
			 } 
			 
			 ob_start();


		     // get settings for watermarking
			 $upload_dir  = wp_upload_dir();
			

	        // path to fully uploaded image is:
		     $filepath = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $data['file'];
			 
			 if ( !is_file( $filepath ) ) return $data; // should never happen, but just to be sure

			 $backup_file = $this->model->wpe_img_wtm_backup_image_file_name( $filepath );
			 
			 if ( is_file( $backup_file ) ) { 
				copy( $backup_file, $filepath );
				touch( $filepath );
			} else {
				copy( $filepath, $backup_file );
				touch( $backup_file );
			}

			$info = getimagesize($filepath);
			// $data dont contains full image so we need manuall merge it with custom code
			
			$img_size = array(
                'file' 		=> wp_basename( $filepath ),
                'width' 	=> $info[0],
                'height' 	=> $info[1],
                'mime-type' => $info['mime'],
            );	
			
			$full = array('full'=>$img_size);
		
			$data['sizes'] = array_merge($data['sizes'],$full);
			if ( count( $data['sizes'] ) ) {
				
				foreach ( $data['sizes'] as $sizename => $size_data ) {

					$thumb_filepath = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . dirname( $data['file'] ) . DIRECTORY_SEPARATOR . $size_data['file'];
					if ( function_exists( 'wp_get_image_editor' ) && isset( $size_data['width'] ) && isset( $size_data['height'] ) && $size_data['width'] && $size_data['height'] && is_file( $backup_file ) && $sizename != 'full' ) {

						$image = wp_get_image_editor( $filepath );

						if ( ! is_wp_error( $image ) ) {
							$image->resize( $size_data['width'], $size_data['height'], true );
							
						}

					}		
					$this->wpe_img_wtm_apply_watermark( $thumb_filepath, $sizename, $backup_file );

				}		
				
			}	
			
		}	
		$output = ob_get_clean();
		return $data;
	  
    }
	
	
	 /**
	 * Add Custom Fields For Image Watermark Per User
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */


	public function wpe_img_wtm_add_customer_meta_fields( $user ) {

		
		if ( !current_user_can( 'edit_user' ) ) {
			return false;
		} 
		
		 $wpe_img_wtm_user_enable = get_the_author_meta( 'wpe_img_wtm_user_enable', $user->ID );
		 
		?>
		
		<h3><?php echo esc_html__( 'Image Watermark Settings', 'wpe_img_wtm' ); ?></h3>  
		<table class="form-table">
			<tr>
				<th><label><?php echo esc_html__( 'Disable Image Watermark:', 'wpe_img_wtm' ); ?></label></th>
				<td>
     				<input class="wpe_img_wtm_on_off_btn" id="wpe_img_wtm_user_enable" value="1" type="checkbox" name="wpe_img_wtm_user_enable" <?php if( $wpe_img_wtm_user_enable =='1' ) { echo 'checked="checked"'; } ?> />
					<span class="description"><?php echo esc_html__( 'Check this box to disable image watermark for images uploaded by this user.', 'wpe_img_wtm' ); ?></span>
				</td>
			</tr>
		</table>
		<?php

	}
	
	 /**
	 * Add Custom Fields For Image Watermark Per User
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
	public function wpe_img_wtm_save_customer_meta_fields($user_id) {
	

		if ( !current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$user_image_watermark = isset( $_POST['wpe_img_wtm_user_enable'] ) ?  $this->model->wte_img_wtm_escape_slashes_deep($_POST['wpe_img_wtm_user_enable']) : '';
		
		$user_image_watermark = ( $user_image_watermark == 1 ) ? $user_image_watermark : '';

		update_user_meta( $user_id, 'wpe_img_wtm_user_enable', $user_image_watermark ); 


	}

	/**
	 * Add New menu
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
 
	public function wpe_img_wtm_add_admin_menu() {

		//Get speaker event page slug
		$wpe_pageslug	= 'admin.php?page=wc-admin';

		//Register submenu for regenerate thumbails
		add_submenu_page('woocommerce', esc_html__( 'Regenerate Thumb', 'wpe_img_wtm' ), esc_html__( 'Regenerate Thumb', 'wpe_img_wtm' ), "manage_options", 'wpe_img_wtm_regenerate_thumb', array( $this, 'wpe_img_wtm_regenerate_thumb_page' ) );
	}


	/**
	 * Includes regenerate thumb functionality
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	*/

	public function wpe_img_wtm_regenerate_thumb_page(){

		include_once( WPE_IMG_WTM_ADMIN.'/forms/wpe-img-wtm-regenerate-img.php');
	}


	/**
	 * Handles process of regenerating thumbnails
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	*/

	public function wpe_img_wtm_regenerate_thumb_process() {

		$result = array();

		header( 'Content-type: application/json' );
		
		if( isset( $_REQUEST['id'] ) ){

			$id = (int) $this->model->wte_img_wtm_escape_slashes_deep($_REQUEST['id']);
			$image = get_post( $id );

			if ( ! $image || 'attachment' != $image->post_type || 'image/' != substr( $image->post_mime_type, 0, 6 ) ) {
				$result['error'] = sprintf( esc_html__( 'Failed resize: %s is an invalid image ID.', 'eddimgwtm'), esc_html( $_REQUEST['id'] ) );
				echo json_encode( $result );
				exit;
			}

			$fullsizepath = get_attached_file( $image->ID );

			if ( false === $fullsizepath || ! file_exists( $fullsizepath ) ){

				$result['error'] = sprintf( esc_html__( 'The originally uploaded image file cannot be found at %s', 'eddimgwtm' ), '<code>' . esc_html( $fullsizepath ) . '</code>' );
				echo json_encode( $result );
				exit;
			}

			set_time_limit( 900 ); // 5 minutes per image should be PLENTY

			$metadata = wp_generate_attachment_metadata( $image->ID, $fullsizepath );

			if ( is_wp_error( $metadata ) ) {

				$result['error'] = $metadata->get_error_message();
				echo json_encode( $result );
				exit;
			}

			if ( empty( $metadata ) ) {

				$result['error'] =  esc_html__( 'Unknown failure reason.', 'eddimgwtm' );
				echo json_encode( $result );
				exit;
			}

			// If this fails, then it just means that nothing was changed (old value == new value)
			wp_update_attachment_metadata( $image->ID, $metadata );

			$result['success'] =  sprintf( esc_html__( '&quot;%1$s&quot; (ID %2$s) was successfully resized in %3$s seconds.', 'eddimgwtm' ), esc_html( get_the_title( $image->ID ) ), $image->ID, timer_stop() );
			echo json_encode( $result );
			exit;
		}

	}		


	/**
	 * Handles logic to update downloadable status of simple products
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
	
	 public function wpw_update_status_for_simple_downloadable(){	

		if(!empty($_POST['post_id']) && isset( $_POST['download_status'] ) ) {

			$postid 	 	= $this->model->wte_img_wtm_escape_slashes_deep($_POST['post_id']);
			$post_status 	= $this->model->wte_img_wtm_escape_slashes_deep($_POST['download_status']);
			$post_status    = ( $post_status == 1 ) ? $post_status : '0';
			update_post_meta( $postid,'is_download_enabled', $post_status);
		}
	 }


	 /**
	 * Handles logic to update downloadable status of simple products
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */

	 public function wpw_update_status_for_variable_downloadable(){	

		if(!empty($_POST['post_id']) && isset( $_POST['download_status'] ) ){

			$postid 	 = $this->model->wte_img_wtm_escape_slashes_deep($_POST['post_id']);
			$post_status = $this->model->wte_img_wtm_escape_slashes_deep($_POST['download_status']);
			$post_status = ( $post_status == 1 ) ? $post_status : '0';

			update_post_meta($postid,'is_download_enabled', $post_status);
		}


	 }

    
     /**
	 * Adding Hooks
	 *
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
	public function add_hooks() {
            
        //add filter to section setting 
		 add_filter( 'woocommerce_get_settings_pages', array($this->model, 'wpe_img_wtm_admin_settings_tab'));

		//delete backup image from media folder when media is deleted
		add_action( 'delete_attachment', array( $this, 'wpe_img_wtm_delete_backup_image' ) );
		 
	    // Add custom fields for image watermark per user
		add_action( 'show_user_profile', array($this, 'wpe_img_wtm_add_customer_meta_fields'), 20 );
		add_action( 'edit_user_profile', array($this, 'wpe_img_wtm_add_customer_meta_fields'), 20 );

		// Save customer image watermark meta fields
		add_action( 'personal_options_update', array($this, 'wpe_img_wtm_save_customer_meta_fields') );
		add_action( 'edit_user_profile_update', array($this, 'wpe_img_wtm_save_customer_meta_fields') );

		add_action( 'admin_menu', array( $this, 'wpe_img_wtm_add_admin_menu' ) );


		add_action( 'wp_ajax_wpe_img_wtm_regenerate_thumb_process', array( $this, 'wpe_img_wtm_regenerate_thumb_process' ) );
		add_action( 'wp_ajax_nopriv_wpe_img_wtm_regenerate_thumb_process', array( $this, 'wpe_img_wtm_regenerate_thumb_process' ) );

		/* Ajax method to update status of simple downloadable products */
		add_action( 'wp_ajax_wpw_update_status_for_simple_downloadable', array( $this, 'wpw_update_status_for_simple_downloadable' ) );
		add_action( 'wp_ajax_nopriv_wpw_update_status_for_simple_downloadable', array( $this, 'wpw_update_status_for_simple_downloadable' ) );

		add_action( 'wp_ajax_wpw_update_status_for_variable_downloadable', array( $this, 'wpw_update_status_for_variable_downloadable' ) );
		add_action( 'wp_ajax_nopriv_wpw_update_status_for_variable_downloadable', array( $this, 'wpw_update_status_for_variable_downloadable' ) );

		
	}

 }