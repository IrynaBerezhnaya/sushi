<?php

function ib_promotion_banner() {
	$banner     = get_field( 'promotion_banner' );
	$_product_1 = $banner['product_1'];
	$_product_2 = $banner['product_2'];
	$date       = $banner['period'];
	$discount   = $banner['discount'];

	write_log($_product_1, '$_product_1');

	if ( $_product_1 && $_product_2 ) {
		$product_1_id = $_product_1->ID;
		$product_1    = wc_get_product( $product_1_id );

		$product_2_id = $_product_2->ID;
		$product_2    = wc_get_product( $product_2_id );

		if ( $product_1 && $product_2 ) {

			$original_price  = $product_1->get_price();
			$discount_amount = $original_price * ( $discount / 100 );
			$reduced_price   = $original_price - $discount_amount;

			$second_price   = $product_2->get_price();
			$total          = $original_price + $second_price;
			$total_discount = $reduced_price + $second_price;

			$terms      = get_the_terms( $product_1_id, 'product_tag' );
			$term_array = array();
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$term_array[] = $term->name;
				}
			}

			if ( $date ) {
				$output = '<div class="banner">
			<div class="banner__section">
			<div class="banner__section-left">' . $product_1->get_image() . '
			<h2>' . $product_1->get_name() . '</h2>
			<p class="ingredients">Cклад:  ' . implode( ', ', $term_array ) . '</p>
			<p class="price"><del>' . $product_1->get_price_html() . '</del> <span class="new-price">' . wc_price( $reduced_price ) . '</span></p>
			</div>
			<div class="plus"></div>
			<div class="banner__section-right">
			<span class="total"><del>' . $total . '</del></span><span class="total-discount">' . $total_discount . '</span>
			<h2>' . $product_2->get_name() . '</h2>
			' . $product_2->get_image() . '
			</div></div>
			<p class="date">Акція діє до ' . $date . '</p>
			</div>';
			}
		}
	}

	return $output;
}