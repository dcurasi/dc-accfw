<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/dcurasi
 * @since      1.0.0
 *
 * @package    Dc_Accfw
 * @subpackage Dc_Accfw/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dc_Accfw
 * @subpackage Dc_Accfw/public
 * @author     Dario Curasì <curasi.d87@gmail.com>
 */
class Dc_Accfw_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dc_Accfw_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dc_Accfw_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dc-accfw-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dc_Accfw_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dc_Accfw_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dc-accfw-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Coupon message code.
	 * @var integer
	 */
	const E_DC_WACC_INVALID = 99;

	/**
	 * Populates an order from the loaded post data.
	 * @param $coupon
	 */
	public static function dc_loaded_coupon( $coupon ) {
		$coupon->payment_methods  = get_post_meta( $coupon->id, 'payment_methods', true );
		$coupon->billing_countries  = get_post_meta( $coupon->id, 'billing_countries', true );
		$coupon->shipping_countries = get_post_meta( $coupon->id, 'shipping_countries', true );
		$coupon->zip_codes = get_post_meta( $coupon->id, 'zip_codes', true );
		$coupon->pc_inc_exc = get_post_meta( $coupon->id, 'pc_inc_exc', true );
	}


	/**
	 * Check if coupon is valid for payment methods.
	 * @return bool
	 */
	public static function dc_is_valid_payment_methods( $valid, $coupon ) {
		$valid = false;
		if ( ! WC()->cart->is_empty() ) {
			//print_r(WC()->session->chosen_payment_method);
			//do_action('woocommerce_update_order_review_fragments');
			if ( in_array( WC()->session->chosen_payment_method, $coupon->payment_methods ) ) {
				$valid = true;
			}
		}
		return $valid;
	}

	/**
	 * Check if coupon is valid for the countries.
	 * @return bool
	 */
	public static function dc_is_valid_countries( $valid, $coupon ) {
		$valid = false;
		if ( ! WC()->cart->is_empty() ) {
			if ( in_array( WC()->customer->country, $coupon->billing_countries ) || in_array( WC()->customer->shipping_country, $coupon->shipping_countries ) ) {
				$valid = true;
			}
		}
		return $valid;
	}

	/**
	 * Check if coupon is valid for the postal codes.
	 * @return bool
	 */
	public static function dc_is_valid_postal_codes( $valid, $coupon ) {
		$valid = false;
		if ( ! WC()->cart->is_empty() ) {
			$code_list = str_replace(' ', '', $coupon->zip_codes);
			$zc = explode(',', $code_list);
			$num_zc = count($zc);
			$count = 0;
			foreach ($zc as $key => $code) {
				$val = explode('|', $code);
				if($coupon->pc_inc_exc == 'exclude') {
					if(count($val) > 1) {
						if ( WC()->customer->shipping_postcode < $val[0] || WC()->customer->shipping_postcode > $val[1] ) {
							$count++;
						}
					}
					else {
						if ( WC()->customer->shipping_postcode != $val[0] ) {
							$count++;
						}
					}
				}
				else {
					if(count($val) > 1) {
						if ( WC()->customer->shipping_postcode >= $val[0] && WC()->customer->shipping_postcode <= $val[1] ) {
							$valid = true;
						}
					}
					else {
						if ( WC()->customer->shipping_postcode == $val[0] ) {
							$valid = true;
						}
					}
				}
			}
			if($coupon->pc_inc_exc == 'exclude' && $count == $num_zc) {
				$valid = true;
			}
		}
		return $valid;
	}

	/**
	 * Check if coupon is valid.
	 * @return bool
	 */
	public static function dc_is_valid( $valid_for_cart, $coupon ) {
		$valid_for_cart = false;
		$payment_methods = true;
		$countries = true;
		$zip_codes = true;
		if ( sizeof( $coupon->payment_methods ) > 0 ) {
			$payment_methods = self::dc_is_valid_payment_methods( $valid_for_cart, $coupon );
		}
		if ( sizeof( $coupon->billing_countries ) > 0 || sizeof( $coupon->shipping_countries ) > 0) {
			$countries = self::dc_is_valid_countries( $valid_for_cart, $coupon );
		}
		if ( $coupon->zip_codes != '' ) {
			$zip_codes = self::dc_is_valid_postal_codes( $valid_for_cart, $coupon );
		}
		if(!($payment_methods && $countries && $zip_codes)) {
			throw new Exception( self::E_DC_WACC_INVALID );
		}
		else {
			$valid_for_cart = true;
		}
		return $valid_for_cart;
	}

	/**
	 * Map error codes to an error string.
	 * @param  string $err Error message.
	 * @param  int $err_code Error code
	 * @return string| Error string
	 */
	public static function dc_get_error_coupon( $err, $err_code, $coupon ) {
		if ( self::E_DC_WACC_INVALID == $err_code ) {
			$err = sprintf( 'Sorry, coupon "%s" is not applicable.', $coupon->code );
		}

		return $err;
	}

	//updated checkout on change payment method
	public static function dc_update_checkout_on_payment_method() {
		echo '<script type="text/javascript">
				jQuery( "#order_review" ).on( "change", "input[name=payment_method]", function() {
						jQuery("body").trigger("update_checkout");
				});
			</script>';
	}

}
