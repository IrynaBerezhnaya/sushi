<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;
?>
<table class="shop_table woocommerce-checkout-review-order-table">
	<thead>
	<tr>
		<th class="product-image"><?php esc_html_e( '', 'woocommerce' ); ?></th>
		<th class="product-name"><?php esc_html_e( '', 'woocommerce' ); ?></th>
		<th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
		<th class="product-quantity"><?php esc_html_e( 'Qty', 'woocommerce' ); ?></th>
		<th class="product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	do_action( 'woocommerce_review_order_before_cart_contents' );

	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
			?>
			<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
				<td class="product-image">
					<?php ib_show_custom_badges( $_product ); ?>
					<?php echo $_product->get_image( 'thumbnail' ); ?>
				</td>
				<td class="product-name">
					<?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
				<td class="product-price">
					<?php echo WC()->cart->get_product_price( $_product ); ?>
				</td>
				<td class="product-quantity">
					<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong>' . $cart_item['quantity'] . '</strong>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
				<td class="product-total">
					<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
			</tr>
			<?php
		}
	}

	do_action( 'woocommerce_review_order_after_cart_contents' );
	?>
	</tbody>
	<tfoot></tfoot>
</table>

