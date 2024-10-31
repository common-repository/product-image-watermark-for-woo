<?php 

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Public Class
 *
 * Handles generic Public functionality and AJAX requests.
 *
 * @package Product Image Watermark for Woocommerce
 * @since 1.0.0
 */

 class Wte_Img_Wtm_Public{

	var $model;
    public function __construct() {
	
		global $wpe_img_wtm_model;
		$this->model = $wpe_img_wtm_model;	

	}
	
	/**
	 * Handles changing orginal files - on Thank you page downloads - Downloads page - My Account
	 *
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */

	 public function wpe_wtm_img_change_download_links_after_purchase($file_path, $instance, $download_id){
		 
		$upload_dir = wp_upload_dir();
		$default_file_path = $file_path;
		$file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $file_path);
		$download_file = '';
		
		if( file_exists( $file_path ) ) { 
			
			$backup_file = $this->model->wpe_img_wtm_backup_image_file_name($file_path);
		
			if( file_exists( $backup_file ) && is_file( $backup_file ) ) { // Check backup file exist

				$download_file = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $backup_file ); // replace absulate path to url
				$file_path = $download_file;	
		
			} else {

				$file_path = $default_file_path;
				
			}
		
			
		} 
		
		return $file_path;

	 }	

	 /**
	 * Handles changing orginal files - on Thank you page downloads - Downloads page - My Account
	 *
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */

	public function wpe_wtm_img_change_download_names_after_purchase($filename, $product_id){

		$filename = str_replace(WPE_IMG_WTM_BACKUP_PREFIX, '',$filename); 
		return $filename;

	}	

    
    /**
	 * Adding Hooks
	 *
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
	
	 public function add_hooks() {
		
		add_filter( 'woocommerce_product_file_download_path', array($this,'wpe_wtm_img_change_download_links_after_purchase'), 10,3);
		add_filter( 'woocommerce_file_download_filename', array($this,'wpe_wtm_img_change_download_names_after_purchase'), 10,2);
	}


 }