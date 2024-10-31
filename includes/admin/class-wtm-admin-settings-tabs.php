<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (class_exists('WooCommerce')) :

    // loads the Misc Functions file
	require_once ( WPE_IMG_WTM_DIR . '/includes/woo-img-wtm-misc-functions.php' );

endif;	

if ( ! class_exists( 'Woo_Watermark_Settings' ) ) :

/**
 * Setting page Class
 * 
 * Handles Settings page functionality of plugin
 * 
 * @package Product Image Watermark for Woocommerce
 * @since 1.0.0
 */

class Woo_Watermark_Settings extends WC_Settings_Page {


    /**
	 * Constructor
	 * 
	 * Handles to add hooks for adding settings
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
    var $model,$render;
    public function __construct() {
    
        global $wpe_img_wtm_model,$wpe_wtm_renderer;
        $this->model = $wpe_img_wtm_model;
		$this->id    	= 'wpe-img-wtm-settings'; // Get id
		$this->render 	= $wpe_wtm_renderer;
		$this->label 	= esc_html__( 'Product Watermark', 'wpe_img_wtm' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		
		// Add action to show output
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'wpe_img_wtm_output' ) );
		

		//// Add action for saving data
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'wpe_img_wtm_save' ) );

		// Add action for adding sections
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );

		// Add action to add custom field for setting page
		add_action( 'woocommerce_admin_field_wtm_preview_upload', array( $this->render, 'wpe_img_wtm_render_preview_upload_callback' ) );
		add_action( 'woocommerce_admin_field_wtm_image_alignment', array( $this->render, 'wpe_img_wtm_render_image_alignment' ) );


	}
	
	/**
	 * Handles to get setting
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
	public function get_settings( $current_section = '' ) {


				$img_types_settings = array(
		
					array(

						'id'	=> 'woo_img_wtm_main',
						'title' => esc_html__( 'Image Watermark Options', 'wpe_img_wtm' ),
						'type' 	=> 'title',
					)
			   );

			   $img_types = wpe_img_wtm_get_types();
			   foreach ( $img_types as $img_type ) {
					
					$img_types_settings[] = array(

						'id'	=> 'wpe_img_wtm_'.$img_type.'_img',
						'name'	=> sprintf(esc_html__('%s Watermark Image','wpe_img_wtm'),ucwords($img_type)),
						'desc'		=> sprintf(esc_html__( 'Select watermark image. This watermark image is applied to the %s image on a Product page.%sNote%s: Please use a PNG image for the watermark.', 'eddimgwtm' ), $img_type, '<br /><strong>', '</strong>'),
						'type'		=> 'wtm_preview_upload',
						'size'		=> 'regular'

					);   

					$img_types_settings[] = array(

						'id'   => 'wpe_img_wtm_'.$img_type.'_repeated_on_image',
						'name' => sprintf(esc_html__('Repeat %s Watermark Image','wpe_img_wtm'),ucwords($img_type)),
						'desc' => sprintf(esc_html__('Check this box to repeat watermark on %s image','wpe_img_wtm'),$img_type).'<br><p><strong>'. esc_html__('Note: ', 'wpe_img_wtm') .'</strong>'.esc_html__('If you enable this option, watermark image alignment setting will not work.','eddimgwtm').'</p>',
						'type'		=> 'checkbox'

					);

					$img_types_settings[] = array(

						'id'		=> 'wpe_img_wtm_'.$img_type.'_align',
						'name'		=> sprintf(esc_html__('%s Watermark Image Alignment','wpe_img_wtm'),ucwords($img_type)),
						'desc'		=> esc_html__('Choose watermark alignment.','wpe_img_wtm'),
						'type'		=> 'wtm_image_alignment',
						
					);

			   }	

			   $img_types_settings[] = array(

				'type' => 'sectionend', 'id' => 'woo_img_wtm_main'

			   );
				 
			   

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $img_types_settings, $current_section );
	
	}

	/**
	 * Handles to save woocommerce settings
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
	public function wpe_img_wtm_save($option) {

		global $current_section;
		$settings = $this->get_settings($current_section);	
        WC_Admin_Settings::save_fields($settings);
 
	}	 
		
	
	/**
	 * Handles to output data
	 * 
	 * @package Product Image Watermark for Woocommerce
	 * @since 1.0.0
	 */
	public function wpe_img_wtm_output() {

		// Get global variable
		global $current_section;

		// Get settings for current section
		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::output_fields( $settings );
	}

}   

endif;

return new Woo_Watermark_Settings();