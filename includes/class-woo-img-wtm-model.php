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

 class Wte_Img_Wtm_Model{


    public function __construct() {
	
	}

	/**
	 * Escape Tags & Slashes
	 *
	 * Handles escapping the slashes and tags
	 *
	 * @package  Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
	public function wte_img_wtm_escape_attr($data){

 
		 return esc_attr(stripslashes($data));
	  			

	}

	/**
	 * Strip Slashes From Array
	 * 
	 * @package  Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
	public function wte_img_wtm_escape_slashes_deep($data = array(), $flag = false){


		if( $flag != true ) {
			
			$data = $this->wte_img_wtm_nohtml_kses($data);
			
		}
		$data = stripslashes_deep($data);
		return $data;


	}


	/**
	 * Strip Html Tags 
	 * 
	 * It will sanitize text input (strip html tags, and escape characters)
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 * 
	 */

	 public function wte_img_wtm_nohtml_kses($data = array()){


		if ( is_array($data) ) {
			
			$data = array_map( array( $this,'wte_img_wtm_nohtml_kses' ), $data );
			
		} elseif ( is_string( $data ) ) {
			
			$data = wp_filter_nohtml_kses($data);
		}
		
		return $data;

	 }
	
	/**
	 * Handles to add plugin settings of WaterMark Woocommerce -> Settings
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */ 

	function wpe_img_wtm_admin_settings_tab($settings) {


		    $settings[] = include( WPE_IMG_WTM_ADMIN . '/class-wtm-admin-settings-tabs.php' );
				
		    return $settings; 
	}

	/**
	 * Set backup file name
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */

	public function  wpe_img_wtm_backup_image_file_name( $filepath ) {
		
		$filepath = str_replace( 'jpeg', 'jpg', $filepath );
		$filepath = strtolower( $filepath );
		
		return dirname( $filepath ).DIRECTORY_SEPARATOR . WPE_IMG_WTM_BACKUP_PREFIX . basename( $filepath );
	}


 }