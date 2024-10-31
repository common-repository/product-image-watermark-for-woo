<?php 

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Renderer Class
 *
 * To handles some small HTML content for front end and backend
 * 
 * @package Product Image Watermark for Woocommerce
 * @since 1.0.0
 */
class Wte_Wtm_Renderer {
	
	public $model;
	
	public function __construct() {
		
		global $wpe_img_wtm_model;
        $this->model = $wpe_img_wtm_model;
       
	}

	/**
	 * Image Alignment Callback
	 *
	 * Renders Radio button options for - Alignment selection.
	 *
	 * @since 1.0.0
	 * @package Product Image Watermark for Woocommerce
	 */

	 public function wpe_img_wtm_render_image_alignment($field) {

		
		global $woo_watermark_options;
		$upload_dir	= wp_upload_dir();
		$value = '';
		if ( isset($field['id'])) {

			$value = get_option( $field['id'] );
			$value = !empty($value) ? $value : '';
		}
		?>
         <tr valign="top">
					<th scope="row" class="titledesc">
						<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo wp_kses_post( $field['title'] ); ?></label>
					</th>	
					<td class="forminp forminp-text">
     					<table id="watermark_position" border="1">
							<?php foreach( array('t','m','b') as $y ) { ?>
								<tr>
									<?php foreach( array('l','c','r') as $x ) { 		
										$woo_img_wtm_pos_val = $y . $x;
									 ?>
    								 <td><input id="<?php echo $field['id'];  ?>" type="radio" name="<?php echo $field['id'];  ?>" value="<?php echo $woo_img_wtm_pos_val;  ?>" <?php echo ($woo_img_wtm_pos_val == $value) ?  "checked" : "";  ?> /></td>	
									<?php } ?>
								</tr>
							<?php } ?>
							<tr><td colspan="3"><input id="<?php echo esc_attr($field['id']);  ?>" type="radio" name="<?php echo esc_attr($field['id']); ?>" value="<?php echo "no-watermark"; ?>" <?php echo ("no-watermark" == $value) ?  "checked" : "";  ?>/><?php echo esc_html__('No watermark','wpe_img_wtm'); ?></td></tr>
						</table> 
					</td>	
         </tr> 
		<?php
	 }

	/**
	 * Preview Upload Callback
	 *
	 * Renders upload fields.
	 *
	 * @since 1.0.0
	 * @package Product Image Watermark for Woocommerce
	 */

     public function wpe_img_wtm_render_preview_upload_callback( $field ) {

		global $woo_watermark_options;
			
		if ( isset( $field['title'] ) && isset( $field['id'] ) ) {

			$upload_dir = wp_upload_dir();
			$base_url 	= $upload_dir['baseurl'];
			$file_val	= get_option( $field['id'] );
			$file_val	= !empty($file_val) ? $file_val : '';
			


			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo wp_kses_post( $field['title'] ); ?></label>
				</th>
				<td class="forminp forminp-text">
					<fieldset>      
					   <input class="regular-text" id="<?php echo esc_attr($field['id']);  ?>" type="text" name="<?php echo esc_attr($field['id']);  ?>" value="<?php echo esc_attr($file_val); ?>" />
					   <input type="button" class="woo-wtm-upload-preview-button button-secondary" id="<?php echo esc_attr($field['id'])."_btn";  ?>" value="<?php esc_html_e( 'Choose', 'wpe_img_wtm' );?>"/> 
					</fieldset>

 					<div id="<?php echo esc_attr($field['id'])."_view";  ?>" class="woo-img-wtm-img-view" ><img src='<?php echo !empty( $file_val ) ? esc_url($base_url.$file_val) : esc_url(WPE_IMG_WTM_URL).'includes/images/preview.png';  ?>' height="200" width="200"/></div>
 					<span id="<?php echo esc_attr($field['id']);  ?>"  class="description"><?php echo $field['desc'];?></span><br />
				</td>
			</tr>
			<?php
			}
		}
    }