<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Misc Functions
 * 
 * All misc functions handles to 
 * different functions 
 * 
 * @package Product Image Watermark for Woocommerce
 * @since 1.0.0
 *
 */
	
/**
* Get Image Types
* 
* Handels to get image types
* 
* @package Product Image Watermark for Woocommerce
* @since 1.0.0
*/
function wpe_img_wtm_get_types() {

    $full_img_types = array( 'full' );
    $img_types = get_intermediate_image_sizes();
    
    $img_types = array_merge( $full_img_types, $img_types );
    
    return $img_types;
}

