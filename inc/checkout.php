<?php

add_action( 'woocommerce_review_order_before_payment', function () { ?>
	<h3><span class="title-number">3</span><?php esc_html_e( 'Оплата', 'woocommerce' ); ?></h3>
<?php } );

/**
 * Display sale price in cart item
 */
function mb_display_sale_price_in_cart_item( $product_price_html, $product ) {
	if ( $product ) {
		$product_price_html = $product->is_on_sale() ? wc_format_sale_price( $product->get_regular_price(), $product->get_sale_price() ) : wc_price( $product->get_price() );
	}

	return $product_price_html;
}

add_filter( 'woocommerce_cart_product_price', 'mb_display_sale_price_in_cart_item', 10, 2 );

/**
 * Change Woo cart item name
 */
function mb_change_woocommerce_cart_item_name( $cart_item_name, $cart_item, $cart_item_key ) {

	//add weight unit:
	$weight_unit = get_option( 'woocommerce_weight_unit' );
	if ( ! empty( $weight_unit ) ) {
		if ( $cart_item ) {
			$product = $cart_item['data'] ?? false;
			if ( $product ) {
				$weight = $product->get_weight();
				if ( ! empty( $weight ) ) {
					$cart_item_name .= '<p class="weight">' . $weight . ' ' . __( $weight_unit, 'woocommerce' ) . '</p>';
				}
			}
		}
	}

	return $cart_item_name;

}

add_filter( 'woocommerce_cart_item_name', 'mb_change_woocommerce_cart_item_name', 10, 3 );

/**
 * Change default Woo form input[type=number] HTML
 */
function mb_change_woocommerce_form_field_number_html( $field, $key, $args, $value ) {

	//change CSS class to allow JS usage for the input[type=number]
	if ( str_contains( $field, 'woocommerce-input-wrapper' ) ) {
		$field = str_replace( 'woocommerce-input-wrapper', 'woocommerce-input-wrapper__mb_js_allowed', $field );
	}

	$input_start = '<input type=';
	$input_end   = '/>';

	$minus = mb_display_minus_quantity_input_button( true );
	$plus  = mb_display_plus_quantity_input_button( true );

	$start_position = strpos( $field, $input_start );
	$end_position   = strpos( $field, $input_end );

	if ( $start_position !== false && $end_position !== false ) {
		// Insert '-' before $input_start
		$field = substr_replace( $field, $minus, $start_position, 0 );

		// Insert '+' after $input_end
		$field = substr_replace( $field, $plus, $end_position + strlen( $minus ) + strlen( $input_end ), 0 );
	}

	return $field;

}

add_filter( 'woocommerce_form_field_number', 'mb_change_woocommerce_form_field_number_html', 10, 4 );

/**
 * Display MINUS quantity input button
 */
function mb_display_minus_quantity_input_button( $return = false ) {
	if ( $return ) {
		ob_start();
	}

	echo '<span class="qty-button mb-qty-minus qty_minus__js">-</span>';

	if ( $return ) {
		return ob_get_clean();
	}
}

add_action( 'woocommerce_before_quantity_input_field', 'mb_display_minus_quantity_input_button' );


/**
 * Display PLUS quantity input button
 */
function mb_display_plus_quantity_input_button( $return = false ) {
	if ( $return ) {
		ob_start();
	}
	echo '<span class="qty-button mb-qty-plus qty_plus__js">+</span>';

	if ( $return ) {
		return ob_get_clean();
	}
}

add_action( 'woocommerce_after_quantity_input_field', 'mb_display_plus_quantity_input_button' );

/**
 * Display custom Checkout Total section
 */
function mb_display_custom_checkout_total_section( $return = false ) {

	if ( $return ) {
		ob_start();
	} ?>
	<!--Custom Checkout Total section-->
	<div class="checkout-total" id="mb_checkout_total">
		<h3 class="">Разом</h3>

		<?php $cart_items_qty = WC()->cart->get_cart_contents_count(); ?>
		<?php $s = $cart_items_qty > 1 ? 'и' : ''; ?>

		<div class="row">
			<p class="col col-1"><?php echo $cart_items_qty; ?> товар<?php echo $s; ?> на суму </p>
			<p class="col col-2"><?php wc_cart_totals_subtotal_html(); ?> </p>

		</div>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="row cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<p class="col col-1"><?php wc_cart_totals_coupon_label( $coupon ); ?></p>
				<p class="col col-2"><?php wc_cart_totals_coupon_html( $coupon ); ?></p>
			</div>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<div class="row shipping">
				<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

				<?php wc_cart_totals_shipping_html(); ?>

				<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
			</div>
		<?php endif; ?>


		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="row fee">
				<p class="col col-1"><?php echo esc_html( $fee->name ); ?></p>
				<p class="col col-2"><?php wc_cart_totals_fee_html( $fee ); ?></p>
			</div>
		<?php endforeach; ?>


		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<div class="row tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<p class="col col-1"><?php echo esc_html( $tax->label ); ?></p>
						<p class="col col-2"><?php echo wp_kses_post( $tax->formatted_amount ); ?></p>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="row tax-total">
					<p class="col col-1"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></p>
					<p class="col col-2"><?php wc_cart_totals_taxes_total_html(); ?></p>
				</div>
			<?php endif; ?>
		<?php endif; ?>


		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<div class="row order-total">
			<p class="col col-1"><?php esc_html_e( 'До сплати', 'woocommerce' ); ?></p>
			<p class="col col-2"><?php wc_cart_totals_order_total_html(); ?></p>
		</div>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>


		<button class="button btn-primary"><?php _e( 'Place order', 'woocommerce' ); ?></button>

		<?php
		//store location:
		$store_city         = get_option( 'woocommerce_store_city' );
		$store_street       = get_option( 'woocommerce_store_address' );
		$store_house_number = get_option( 'woocommerce_store_address_2' );

		if ( ! empty( $store_city ) && ! empty( $store_street ) ) { ?>
			<p class="store-location">
				<strong><?php echo $store_city; ?>,</strong>
				<?php esc_html_e( 'вул.', 'woocommerce' ); ?>
				<?php echo $store_street; ?>
				<?php echo ! empty( $store_house_number ) ? ' ' . $store_house_number : ''; ?>
			</p>
		<?php } ?>
	</div>
	<?php if ( $return ) {
		return ob_get_clean();
	}
}

add_action( 'woocommerce_checkout_after_order_review', 'mb_display_custom_checkout_total_section' );

/**
 * Add custom Checkout Total section to order review fragments (for automatic AJAX updating)
 */
function mb_add_custom_checkout_total_section_to_order_review_fragments( $fragments ) {

	$fragments['#mb_checkout_total'] = mb_display_custom_checkout_total_section( true );

	return $fragments;
}

add_filter( 'woocommerce_update_order_review_fragments', 'mb_add_custom_checkout_total_section_to_order_review_fragments' );

/**
 * Change woocommerce shipping package name
 */
function mb_change_woocommerce_shipping_package_name( $shipping_package_name, $i, $package ) {

	return 'Вартість доставки';
}

add_filter( 'woocommerce_shipping_package_name', 'mb_change_woocommerce_shipping_package_name', 10, 3 );

/**
 * Change Free Shipping label
 */
function mb_change_free_shipping_label( $label, $method ) {

	if ( $method ) {
		$method_id = $method->method_id ?? '';
		if ( $method_id === 'free_shipping' ) {
			$label = '<span class="col-2">Безкоштовно</span>';
		}
	}

	return $label;
}

add_filter( 'woocommerce_cart_shipping_method_full_label', 'mb_change_free_shipping_label', 10, 2 );

/**
 * Set checkout fields default values
 *
 * @param $fields
 *
 * @return array $fields
 */
function mb_set_checkout_fields_default_values( $fields ) {

	//set default values for Billing City and Billing Postcode:
	if ( isset( $fields['billing']['billing_city'] ) && isset( $fields['billing']['billing_postcode'] ) ) {

		$shipping_zones = mb_get_all_shipping_zones();
		if ( ! empty( $shipping_zones ) ) {
			$default_shipping_zone = $shipping_zones[0];
			if ( $default_shipping_zone ) {
				$zone_locations = $default_shipping_zone->get_zone_locations();
				if ( ! empty( $zone_locations ) ) {
					foreach ( $zone_locations as $zone_location ) {

						$location_type = $zone_location->type ?? '';
						$postcode      = $zone_location->code ?? '';
						if ( $location_type === 'postcode' && ! empty( $postcode ) ) {
							$fields['billing']['billing_city']['default'] = $default_shipping_zone->get_zone_name();;
							$fields['billing']['billing_postcode']['default'] = $postcode;
						}
					}
				}
			}
		}
	}

	return $fields;
}

add_filter( 'woocommerce_checkout_fields', 'mb_set_checkout_fields_default_values', 999999 );