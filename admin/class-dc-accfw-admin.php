<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/dcurasi
 * @since      1.0.0
 *
 * @package    Dc_Accfw
 * @subpackage Dc_Accfw/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dc_Accfw
 * @subpackage Dc_Accfw/admin
 * @author     Dario Curasì <curasi.d87@gmail.com>
 */
class Dc_Accfw_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dc-accfw-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dc-accfw-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Callback function for the admin settings page.
	 *
	 * @since    1.0.0
	 */
	public function create_admin_interface(){

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dc-accfw-admin-display.php';

	}

	/**
	 * Get the coupon response.
	 * @param  array  $data_coupon
	 * @param  object $coupon
	 * @return array
	 */
	public static function dc_response_coupon( $data_coupon, $coupon ) {
		$data_coupon['payment_methods'] = $coupon->payment_methods;
		$data_coupon['billing_countries']  = $coupon->billing_countries;
		$data_coupon['shipping_countries'] = $coupon->shipping_countries;
		$data_coupon['zip_codes'] = $coupon->zip_codes;
		$data_coupon['pc_inc_exc'] = $coupon->pc_inc_exc;
		return $data_coupon;
	}

	/**
	 * Create a coupon.
	 * @param int   $id
	 * @param array $data
	 */
	public static function dc_coupon_create( $id, $data ) {
		$payment_methods  = isset( $data['payment_methods'] ) ? wc_clean( $data['payment_methods'] ) : array();
		$billing_countries  = isset( $data['billing_countries'] ) ? wc_clean( $data['billing_countries'] ) : array();
		$shipping_countries = isset( $data['shipping_countries'] ) ? wc_clean( $data['shipping_countries'] ) : array();
		$zip_codes = isset( $_POST['zip_codes'] ) ? wc_clean( $_POST['zip_codes'] ) : '';
		$pc_inc_exc = isset( $_POST['pc_inc_exc'] ) ? wc_clean( $_POST['pc_inc_exc'] ) : 'include';

		// Save post meta.
		update_post_meta( $id, 'payment_methods', $payment_methods );
		update_post_meta( $id, 'billing_countries', $billing_countries );
		update_post_meta( $id, 'shipping_countries', $shipping_countries );
		update_post_meta( $post_id, 'zip_codes', $zip_codes );
		update_post_meta( $post_id, 'pc_inc_exc', $pc_inc_exc );
	}

	/**
	 * Edit a coupon.
	 * @param int   $id
	 * @param array $data
	 */
	public static function dc_coupon_edit( $id, $data ) {
		if ( isset( $data['payment_methods'] ) ) {
			update_post_meta( $id, 'payment_methods', wc_clean( $data['payment_methods'] ) );
		}

		if ( isset( $data['billing_countries'] ) ) {
			update_post_meta( $id, 'billing_countries', wc_clean( $data['billing_countries'] ) );
		}

		if ( isset( $data['shipping_countries'] ) ) {
			update_post_meta( $id, 'shipping_countries', wc_clean( $data['shipping_countries'] ) );
		}

		if ( isset( $data['zip_codes'] ) ) {
			update_post_meta( $id, 'zip_codes', wc_clean( $data['zip_codes'] ) );
		}

		if ( isset( $data['pc_inc_exc'] ) ) {
			update_post_meta( $id, 'pc_inc_exc', wc_clean( $data['pc_inc_exc'] ) );
		}
	}

	/**
	 * Save coupons usage restriction meta box data.
	 */
	public static function dc_coupon_save_options( $post_id ) {
		$payment_methods  = isset( $_POST['payment_methods'] ) ? wc_clean( $_POST['payment_methods'] ) : array();
		$billing_countries  = isset( $_POST['billing_countries'] ) ? wc_clean( $_POST['billing_countries'] ) : array();
		$shipping_countries = isset( $_POST['shipping_countries'] ) ? wc_clean( $_POST['shipping_countries'] ) : array();
		$zip_codes = isset( $_POST['zip_codes'] ) ? wc_clean( $_POST['zip_codes'] ) : '';
		$pc_inc_exc = isset( $_POST['pc_inc_exc'] ) ? wc_clean( $_POST['pc_inc_exc'] ) : 'include';

		// Save post meta.
		update_post_meta( $post_id, 'payment_methods', $payment_methods );
		update_post_meta( $post_id, 'billing_countries', $billing_countries );
		update_post_meta( $post_id, 'shipping_countries', $shipping_countries );
		update_post_meta( $post_id, 'zip_codes', $zip_codes );
		update_post_meta( $post_id, 'pc_inc_exc', $pc_inc_exc );
	}

	public function error_notice() {
		echo '<div class="notice notice-error is-dismissible">
        		<p>'.__('Advanced Coupon Condition for Woocommerce is active but does not work. You need to install WooCommerce because the plugin is working properly.', 'dc-accfw').'</p>
    		  </div>';
	}

}
