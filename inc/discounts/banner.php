<?php

function ib_promotion_banner() {
	$banner = get_field('promotion_banner');
	$product = $banner['product_1'];
	$second = $banner['product_2'];
	$date = $banner['period'];
	$discount = $banner['discount'];

	if ($product && $second) {
		$product_id = $product->ID;
		$product = wc_get_product($product_id);

		$second_id = $second->ID;
		$second = wc_get_product($second_id);
	}

	$original_price = $product->get_price();
	$discount_amount = $original_price * ($discount / 100);
	$reduced_price = $original_price - $discount_amount;

	$second_price = $second->get_price();
	$total = $original_price + $second_price;
	$total_discount = $reduced_price + $second_price;

	$terms = get_the_terms($product_id, 'product_tag' );
	$term_array = array();
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$term_array[] = $term->name;
		}
	}

	if ( $product && $second && $date) :
		$output = '<div class="banner">
		<div class="banner__section">
		<div class="banner__section-left">'. $product->get_image() .'
		<h2>' . $product->get_name() . '</h2>
		<p class="ingredients">Cклад:  ' . implode(', ', $term_array) . '</p>
		<p class="price"><del>' . $product->get_price_html() . '</del> <span class="new-price">' . wc_price($reduced_price) . '</span></p>
		</div>
		<div class="plus"></div>
		<div class="banner__section-right">
		<span class="total"><del>'. $total .'</del></span><span class="total-discount">'. $total_discount .'</span>
		<h2>' . $second->get_name() . '</h2>
		'. $second->get_image() .'
		</div></div>
		<p class="date">Акція діє до '. $date .'</p>
		</div>';
	endif;

	return $output;
}
add_shortcode( 'promotion_banner', 'ib_promotion_banner' );
