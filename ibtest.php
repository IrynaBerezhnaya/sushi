<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'ib_woocommerce_widget_shopping_cart_subtotal' ) ) {
/**
* Output to view cart total.
*
* @since 3.7.0
*/
function ib_woocommerce_widget_shopping_cart_subtotal() {
echo '<strong>' . esc_html__( 'Total:', 'woocommerce' ) . '</strong> ' . WC()->cart->get_cart_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
}

//add_action ('woocommerce_widget_shopping_cart_total', 'ib_woocommerce_widget_shopping_cart_subtotal');

