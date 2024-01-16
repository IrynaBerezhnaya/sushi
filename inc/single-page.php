<?php
// Right column
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 10 );
add_action( 'woocommerce_after_add_to_cart_quantity', 'woocommerce_template_single_price', 25 );

/**
 * Move product attributes before product summary
 */
function ib_move_attributes() {
	global $product;
	wc_display_product_attributes( $product );
}

add_action( 'woocommerce_single_product_summary', 'ib_move_attributes', 25 );

/**
 * Disable Link on Single Product Image
 */
function ib_remove_product_link( $html ) {

	return strip_tags( $html, '<div><img>' );
}

add_filter( 'woocommerce_single_product_image_thumbnail_html', 'ib_remove_product_link' );

function ib_woocommerce_format_weight( $weight_string, $weight ) {
	$weight_unit   = get_option( 'woocommerce_weight_unit' );
	$weight_string = $weight . ' ' . __( $weight_unit, 'woocommerce' );

	return $weight_string;
}

add_filter( 'woocommerce_format_weight', 'ib_woocommerce_format_weight', 10, 2 );

/**
 * Display Gift Product
 */
function ib_display_gift_product() {
	if ( get_field( 'display_gift' ) ) {
		$text    = get_field( 'gift_text' );
		$product = get_field( 'gift_product' );

		if ( $product && $text ) {
			$image_url = get_the_post_thumbnail_url( $product->ID, 'full' );
			echo '<section class="gift-product">';
			echo '<h3>' . $text . '</h3>';
			if ( $image_url ) {
				echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $product->post_title ) . '" />';
			}
			echo '</section>';
		}
	}
}

add_action( 'woocommerce_after_add_to_cart_button', 'ib_display_gift_product' );