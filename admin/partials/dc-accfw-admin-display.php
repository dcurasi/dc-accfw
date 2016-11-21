<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/dcurasi
 * @since      1.0.0
 *
 * @package    Dc_Accfw
 * @subpackage Dc_Accfw/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php global $post; ?>

<div class="options_group">
	<p class="form-field">
		<label for="payment_methods">Payment methods</label>
		<select id="payment_methods" class="wc-enhanced-select dc-select-width" name="payment_methods[]" multiple="multiple" data-placeholder="Any payment methods">
			<?php
				$payment_methods = (array) get_post_meta( $post->ID, 'payment_methods', true );
				$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

				if ( $available_gateways ) foreach ( $available_gateways as $key => $val ) {
					echo '<option value="' . esc_attr( $val->id ) . '"' . selected( in_array( $val->id, $payment_methods ), true, false ) . '>' . esc_html( $val->title ) . '</option>';
				}
			?>
		</select> <?php echo wc_help_tip( 'List of allowed payment methods to check against the customer\'s payment method for the coupon to remain valid.' ); ?>
	</p>
</div>

<div class="options_group">
	<p class="form-field">
		<label for="billing_countries">Billing countries</label>
		<select id="billing_countries" class="wc-enhanced-select dc-select-width" name="billing_countries[]" multiple="multiple" data-placeholder="Any countries">
			<?php
				$locations = (array) get_post_meta( $post->ID, 'billing_countries', true );
				$countries = WC()->countries->countries;

				if ( $countries ) foreach ( $countries as $key => $val ) {
					echo '<option value="' . esc_attr( $key ) . '"' . selected( in_array( $key, $locations ), true, false ) . '>' . esc_html( $val ) . '</option>';
				}
			?>
		</select> <?php echo wc_help_tip( 'List of allowed countries to check against the customer\'s billing country for the coupon to remain valid.' ); ?>
	</p>

	<p class="form-field">
		<label for="shipping_countries">Shipping countries</label>
		<select id="shipping_countries" class="wc-enhanced-select dc-select-width" name="shipping_countries[]" multiple="multiple" data-placeholder="Any countries">
			<?php
				$locations = (array) get_post_meta( $post->ID, 'shipping_countries', true );
				$countries = WC()->countries->countries;

				if ( $countries ) foreach ( $countries as $key => $val ) {
					echo '<option value="' . esc_attr( $key ) . '"' . selected( in_array( $key, $locations ), true, false ) . '>' . esc_html( $val ) . '</option>';
				}
			?>
		</select> <?php echo wc_help_tip( 'List of allowed countries to check against the customer\'s shipping country for the coupon to remain valid.' ); ?>
	</p>

	<p class="form-field">
		<label for="zip_codes">Postal Codes</label>
		<input type="text" class="" name="zip_codes" id="zip_codes" value="<?php echo get_post_meta( $post->ID, 'zip_codes', true ); ?>" placeholder="Any postal codes" multiple="multiple">
		<?php echo wc_help_tip( 'List of permitted postal codes, to be compared with that buyer\'s shipping. Separate postal codes with commas. You can enter ranges of postal codes. e.g: 90100|90133 to include all the codes between 90100 and 90133. Check "exclude" to include all postal codes except those inserted' ); ?>
		<input name="pc_inc_exc" value="include" type="radio" class="dc-radio-margin" <?php checked(get_post_meta( $post->ID, 'pc_inc_exc', true ), 'include'); if(get_post_meta( $post->ID, 'pc_inc_exc', true ) == null) echo 'checked="checked"'; ?>> include
		<input name="pc_inc_exc" value="exclude" type="radio" class="dc-radio-margin" <?php checked(get_post_meta( $post->ID, 'pc_inc_exc', true ), 'exclude'); ?> > exclude
	</p>
</div>