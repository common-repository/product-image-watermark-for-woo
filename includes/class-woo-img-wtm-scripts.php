<?php 

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Scripts Class
 *
 * Handles adding scripts functionality to the admin pages
 * as well as the front pages.
 *
 * @package Product Image Watermark for Woocommerce
 * @since 1.0.0
 */

 class Wte_Img_Wtm_Scripts{


    public function __construct() {
	
	}

	/**
	 * Adding scripts on settings of admin - Download Image Watermark
	 *
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */

	 public function wpe_img_wtm_admin_scripts($hook_suffix) {

	     	global $wp_version;
			if((isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'wpe-img-wtm-settings') ||  $hook_suffix == "woocommerce_page_wpe_img_wtm_regenerate_thumb" || $hook_suffix == "post.php" || $hook_suffix == "post-new.php") {

				
				$upload_dir = wp_upload_dir();
				wp_register_script('wpe_img_wtm-admin-scripts', WPE_IMG_WTM_URL . 'includes/js/woo-img-wtm-admin.js', array(
					'jquery',
					'thickbox'
				) , WPE_IMG_WTM_VERSION, true);
				wp_enqueue_script('wpe_img_wtm-admin-scripts');

				//localize script
				$newui = $wp_version >= '3.5' ? '1' : '0'; //check wp version for showing media uploader
				wp_localize_script('wpe_img_wtm-admin-scripts', 'WooImgWtm', array(
					'new_media_ui' => $newui,
					'upload_base_url' => $upload_dir['baseurl'],
					'ajax_url' => admin_url('admin-ajax.php')
				));	

				wp_enqueue_media();
				wp_enqueue_script( 'jquery-ui-progressbar' );
				
			}


				//Enque regenerate thumb
				
				wp_register_script( 'wpe-img-wtm-admin-reg-imgs-scripts', WPE_IMG_WTM_URL.'includes/js/wpe-img-wtm-admin-reg-imgs.js' , array( 'jquery' ), WPE_IMG_WTM_VERSION, true );
			    
	 }


	 /**
	 * Enqueue Admin style
	 * 
	 * Handles to enqueue style for admin
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */

	public function wpe_img_wtm_admin_styles( $hook_suffix ) {

		if((isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'wpe-img-wtm-settings') || $hook_suffix == "woocommerce_page_wpe_img_wtm_regenerate_thumb") {

			wp_register_style( 'wpe-img-wtm-admin-style', WPE_IMG_WTM_URL.'includes/css/wpe-img-wtm-admin.css', array('jquery-ui-style'), WPE_IMG_WTM_VERSION );
			wp_enqueue_style( 'wpe-img-wtm-admin-style' );

		}	
	}	


    /**
	 * Adding Hooks
	 *
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
	public function add_hooks() {

	    //add style to back side for image watermark settings
		add_action( 'admin_enqueue_scripts', array( $this, 'wpe_img_wtm_admin_styles' ) );
		

		//add script to back side for image watermark settings
		add_action( 'admin_enqueue_scripts', array( $this, 'wpe_img_wtm_admin_scripts' ) );

	
	}

 }