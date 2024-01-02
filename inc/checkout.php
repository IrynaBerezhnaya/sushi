<?php

add_action( 'woocommerce_review_order_before_payment', function () { ?>
	<h3><?php esc_html_e( 'Оплата', 'woocommerce' ); ?></h3>
<?php } );

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