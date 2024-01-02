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
					$cart_item_name .= '<div class="">' . $weight . ' ' . __( $weight_unit, 'woocommerce' ) . '</div>';
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