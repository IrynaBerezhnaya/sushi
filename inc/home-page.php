<?php
/**
 * Change Priority
 */
add_action( 'homepage', 'storefront_popular_products', 30 );
add_action( 'homepage', 'storefront_recent_products', 50 );

/**
 * Add Promotion Banner in Home Page
 */
function ib_promotion_banner() {
    $banner = get_field('promotion_banner');
    $_product_1 = $banner['product_1'];
    $_product_2 = $banner['product_2'];
    $date = $banner['period'];
    $discount = $banner['discount'];

    if ($_product_1 && $_product_2) {
        $product_1_id = $_product_1->ID;
        $product_1 = wc_get_product($product_1_id);

        $product_2_id = $_product_2->ID;
        $product_2 = wc_get_product($product_2_id);

        if ($product_1 && $product_2) {

            $original_price = $product_1->regular_price;
            $discount_amount = $original_price * ($discount / 100);
            $reduced_price = $original_price - $discount_amount;

            $second_price = $product_2->get_price();
            $total = $original_price + $second_price;
            $total_discount = $reduced_price + $second_price;

            $terms = get_the_terms($product_1_id, 'product_tag');
            $term_array = array();
            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $term_array[] = $term->name;
                }
            }

            if ($date) { ?>
               <div class="banner">
			<div class="banner__section">
			<div class="banner__section-left"> <?php echo $product_1->get_image(); ?>
			<h2><?php echo $product_1->get_name(); ?></h2>
			<p class="ingredients"><?php esc_html_e( 'Cклад: ', 'woocommerce' ); ?><?php echo implode(', ', $term_array); ?></p>
			<p class="price"><del><?php echo wc_price($original_price); ?></del> <span class="new-price"><?php echo wc_price($reduced_price); ?></span></p>
			</div>
			<div class="plus"></div>
			<div class="banner__section-right">
			<span class="total"><del><?php echo $total; ?></del></span><span class="total-discount"><?php echo $total_discount; ?></span>
			<h2><?php echo $product_2->get_name(); ?></h2>
                <?php echo $product_2->get_image(); ?>
			</div></div>
			<p class="date"><?php esc_html_e( 'Акція діє до ', 'woocommerce' ); ?> <?php echo $date; ?></p>
			</div>
          <?php  }
        }
    }

}

add_action('storefront_homepage', 'ib_promotion_banner');

/**
 * Display Sets Section on Home Page
 */
function ib_display_sets() {
    if (is_front_page()) :
	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => 6,
		'product_cat'    => 'sets',
	);

	$products = new WP_Query( $args );
	$category = get_term_by('slug', 'sets', 'product_cat');

	if ( $products->have_posts() ) {
		echo '<div class="col-full">';
	    echo '<section class="storefront-product-section storefront-'.$category->slug.'-products">';
	    echo '<h2 class="section-title">'.$category->name.'</h2>';
		echo '<div class="woocommerce columns-3">';
		echo '<ul class="products columns-3">';
		while ( $products->have_posts() ) {
			$products->the_post();
			wc_get_template_part( 'content', 'product' );
		}
		echo '</ul>';
		echo '</div>';
		echo '</section>';
		echo '</div>';
	} else {
		echo 'No products found';
	}

	wp_reset_postdata();
    endif;
}
add_action('storefront_before_footer', 'ib_display_sets', 20);