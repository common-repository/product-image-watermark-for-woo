<?php
/*
 * Plugin Name: Product Image Watermark for Woo
 * Plugin URI: https://wordpress.org/plugins/product-image-watermark-for-woo/
 * Description: Add Watermark to the images on Woocommerce Downloadable Products.
 * Version: 1.0.5
 * Author: WP Plugin Experts
 * Author URI: https://wppluginexperts.com/
 * Text Domain: wpe_img_wtm
 * WC tested up to: 9.0.2
 * Tested up to: 6.6
 * Domain Path: languages
 */

/**
 * Basic plugin definitions 
 * 
 * @package Product Image Watermark for Woocommerce
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

// Freemius SDK integration
if( ! function_exists('wpe_img_wtm_freemius') ) {
    // Create a helper function for easy SDK access.
	function wpe_img_wtm_freemius() {
		global $wpe_img_wtm_freemius;

		if ( ! isset( $wpe_img_wtm_freemius ) ) {
            // Include Freemius SDK.
			require_once dirname(__FILE__) . '/freemius/start.php';

			$wpe_img_wtm_freemius = fs_dynamic_init( array(
				'id'				=> '6366',
				'slug'				=> 'product-image-watermark-for-woo',
				'type'				=> 'plugin',
				'public_key'		=> 'pk_31f5f4b03c935615426965d36c8b9',
				'is_premium'		=> false,
				'has_addons'		=> false,
				'has_paid_plans'	=> false,
				'menu'				=> array(
					'first-path'	=> 'plugins.php',
					'account'		=> false,
					'contact'		=> false,
					'support'		=> false,
				),
			) );
		}

		return $wpe_img_wtm_freemius;
	}

	// Init Freemius.
	wpe_img_wtm_freemius();

	// Signal that SDK was initiated.
	do_action( 'wpe_img_wtm_freemius_loaded' );
}

if (!defined('WPE_IMG_WTM_VERSION')) {
    define('WPE_IMG_WTM_VERSION', '1.0.1'); // Plugin version
}
if (!defined('WPE_IMG_WTM_URL')) {
    define('WPE_IMG_WTM_URL', plugin_dir_url(__FILE__)); // plugin url
}
if (!defined('WPE_IMG_WTM_DIR')) {
    define('WPE_IMG_WTM_DIR', dirname(__FILE__)); // plugin dir
}
if (!defined('WPE_IMG_WTM_ADMIN')) {
    define('WPE_IMG_WTM_ADMIN', WPE_IMG_WTM_DIR . '/includes/admin'); // plugin admin dir
}

if (!defined('WPE_IMG_WTM_URL')) {
    define('WPE_IMG_WTM_URL', WPE_IMG_WTM_URL . '/includes/images'); // plugin admin dir
}

if (!defined('WPE_IMG_WTM_BACKUP_PREFIX')) {
    define('WPE_IMG_WTM_BACKUP_PREFIX', '_wpe_img_wtm_');
}
if (!defined('WPE_IMG_WTM_MAIN_POSTTYPE')) { // Plugin main post type
    define('WPE_IMG_WTM_MAIN_POSTTYPE', 'product');
}
if (!defined('WPE_IMG_WTM_BASENAME')) {
    define('WPE_IMG_WTM_BASENAME', basename(WPE_IMG_WTM_DIR)); // base name
}
if (!defined('WPE_IMG_WTM_PLUGIN_KEY')) {
    define('WPE_IMG_WTM_PLUGIN_KEY', 'wpe_img_wtm');
}


/**
 * Add Settings link - on plugin page
 * 
 * @package Product Image Watermark for Woocommerce
 * @since 1.0.0
 */
function wpe_img_wtm_plugin_settings_link($links){


    $links[] = '<a href="' .
		admin_url( 'admin.php?page=wc-settings&tab=wpe-img-wtm-settings' ) .
		'">' . __('Settings') . '</a>';
	return $links;


}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wpe_img_wtm_plugin_settings_link');


/**
 * Admin notices
 * 
 * @package Product Image Watermark for Woocommerce
 * @since 1.0.0
 */

function wpe_img_wtm_admin_notices() {

    if (!class_exists('WooCommerce')) {

        echo '<div class="error">';
        echo "<p><strong>" . esc_html__('WooCommerce needs to be activated to be able to use the Download Image Watermark.', 'wpe_img_wtm') . "</strong></p>";
        echo '</div>';
    }
}

/**
 * Check for Woocommerce plugin Plugin
 *
 * Handles to check Woocommerce plugin
 * if not activated then deactivate our plugin
 *
 * @package Product Image Watermark for Woocommerce
 * @since 1.0.0
 */

function wpe_img_wtm_check_activation() {

    if (!class_exists('WooCommerce')) {

        // is this plugin active?
        if (is_plugin_active(plugin_basename(__FILE__))) {
            // deactivate the plugin
            deactivate_plugins(plugin_basename(__FILE__));
            // unset activation notice
            if(isset($_GET['activate'])) {

                unset($_GET['activate']);

            }  
            // display notice
            add_action('admin_notices', 'wpe_img_wtm_admin_notices');
        }
    }

}    

//Check Woocmmerce plugin is Activated or not
add_action('admin_init', 'wpe_img_wtm_check_activation');

//check if Woocommerce plugin is activated or not
if (class_exists('WooCommerce')) {

    // loads the Misc Functions file
    require_once ( WPE_IMG_WTM_DIR . '/includes/woo-img-wtm-misc-functions.php' );


    /**
     * Activation Hook
     *
     * Register plugin activation hook.
     *
     * @package Product Image Watermark for Woocommerce
     * @since 1.0
     */

    register_activation_hook(__FILE__, 'wpe_img_wtm_install');

    /**
     * Plugin Setup (On Activation)
     *
     * Does the initial setup,
     * stest default values for the plugin options.
     *
     * @package Product Image Watermark for Woocommerce
     * @since 1.0.0
     */

     function wpe_img_wtm_install(){

        global $wpdb, $woo_watermark_options;
       $udpopt = false;

        $img_types = wpe_img_wtm_get_types();
        foreach ($img_types as $img_type) {

            //check watermark image not set
            $get_alignment_value = get_option('wpe_img_wtm_'.$img_type.'_align');
            $is_repeater_value = get_option('wpe_img_wtm_'.$img_type.'_repeated_on_image');
            $water_mark_path = get_option('wpe_img_wtm_'.$img_type.'_img');

            if (!isset($get_alignment_value)) {

               $get_alignment_value = '';
               update_option('wpe_img_wtm_'.$img_type.'_align',$get_alignment_value);
            
            }

            if (!isset($is_repeater_value)) {

                $is_repeater_value = '';
                update_option('wpe_img_wtm_'.$img_type.'_repeated_on_image',$is_repeater_value);
             
             }

             if (!isset($water_mark_path)) {

                $water_mark_path = '';
                update_option('wpe_img_wtm_'.$img_type.'_img', $water_mark_path);
             
             }
            
             //end if
        }

     }

}

/**
 * Load Text Domain
 *
 * This gets the plugin ready for translation.
 *
 * @package  Product Image Watermark for Woocommerce
 * @since 1.0.0
 */
function wpe_img_wtm_load_text_domain() {

    $wpe_img_wtm_lang_dir = dirname(plugin_basename(__FILE__)) . '/languages/';
    $wpe_img_wtm_lang_dir = apply_filters('wpe_img_wtm_languages_directory',$wpe_img_wtm_lang_dir);

    // Traditional WordPress plugin locale filter
    $locale = apply_filters('plugin_locale', get_locale(), 'wpe_img_wtm');
    $mofile = sprintf('%1$s-%2$s.mo', 'wpe_img_wtm', $locale);

    // Setup paths to current locale file
    $mofile_local =  $wpe_img_wtm_lang_dir . $mofile;
    $mofile_global = WP_LANG_DIR . '/' . WPE_IMG_WTM_BASENAME . '/' . $mofile;

    if (file_exists($mofile_global)) { // Look in global /wp-content/languages/product-image-watermark-for-woo
        load_textdomain('wpe_img_wtm', $mofile_global);
    } elseif (file_exists($mofile_local)) { // Look in local /wp-content/plugins/product-image-watermark-for-woo/languages/ folder
        load_textdomain('wpe_img_wtm', $mofile_local);
    } else { // Load the default language files
        load_plugin_textdomain('wpe_img_wtm', false, $wpe_img_wtm_lang_dir);
    }


}    


//add action to load plugin
add_action('plugins_loaded', 'wpe_img_wtm_plugin_loaded');

/**
 * Load Plugin
 * 
 * Handles to load plugin after
 * dependent plugin is loaded 
 * successfully
 *
 * @package Product Image Watermark for Woocommerce
 * @since 1.0.0
 * */

function wpe_img_wtm_plugin_loaded() {

//check Woocommerce is activated or not
    if (class_exists('WooCommerce')) {

         // load first plugin text domain
         wpe_img_wtm_load_text_domain();

        /**
         * Deactivation Hook
         *
         * Register plugin deactivation hook.
         *
         * @package Product Image Watermark for Woocommerce
         * @since 1.0.0
         */
        register_deactivation_hook(__FILE__, 'wpe_img_wtm_uninstall');

        /**
         * Plugin Setup (On Deactivation)
         *
         * Delete  plugin options.
         *
         * @package Product Image Watermark for Woocommerce
         * @since 1.0.0
         */

        function wpe_img_wtm_uninstall() {

            global $wpdb, $woo_watermark_options;

        }

        /**
         * Includes Files
         * 
         * Includes some required files for plugin
         *
         * @package Product Image Watermark for Woocommerce
         * @since 1.0.0
         */
        global $wpe_img_wtm_model, $wpe_img_wtm_scripts, $wpe_wtm_admin, $wpe_img_wtm_public,$wpe_wtm_renderer;
        //Model Class for generic functions
        require_once( WPE_IMG_WTM_DIR . '/includes/class-woo-img-wtm-model.php' );
        $wpe_img_wtm_model = new Wte_Img_Wtm_Model();

        //Scripts Class for scripts / styles
        require_once( WPE_IMG_WTM_DIR . '/includes/class-woo-img-wtm-scripts.php' );
        $wpe_img_wtm_scripts = new Wte_Img_Wtm_Scripts();
        $wpe_img_wtm_scripts->add_hooks();

        //Handles Html of custom fields in settings 
        require_once( WPE_IMG_WTM_DIR . '/includes/class-wtm-renderer.php' );
        $wpe_wtm_renderer = new Wte_Wtm_Renderer();
        


        //Admin Pages Class for admin side
        require_once( WPE_IMG_WTM_ADMIN . '/class-woo-img-wtm-admin.php' );
        $wpe_wtm_admin = new Wte_Img_Wtm_Admin();
        $wpe_wtm_admin->add_hooks();

        //Public Pages Class for public side
        require_once(WPE_IMG_WTM_DIR . '/includes/class-woo-img-wtm-public.php' );
        $wpe_img_wtm_public = new Wte_Img_Wtm_Public();
        $wpe_img_wtm_public->add_hooks();

        $current_user = get_current_user_id();
        $wpe_img_wtm_user_enable = get_the_author_meta('wpe_img_wtm_user_enable', $current_user);
        $wpe_img_wtm_user_enable = !empty($wpe_img_wtm_user_enable) ? $wpe_img_wtm_user_enable : '';

        

        if ($wpe_img_wtm_user_enable != '1') {

            //add filter for generate image watermark 
            add_filter('wp_generate_attachment_metadata', array($wpe_wtm_admin, 'wpe_img_wtm_generate_image_watermark'));
        }

    }    

}    