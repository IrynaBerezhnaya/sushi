<?php
function ib_display_best_sellers() {
	ob_start();
	$args = array(
		'post_type' => 'product',
		'meta_key' => 'total_sales',
		'orderby' => 'meta_value_num',
		'posts_per_page' => 9,
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => array( 'drinks', 'business-lanch' ),
				'operator' => 'NOT IN',
			),
		),
	);
	$loop = new WP_Query( $args ); ?>
	<div class="best-sellers">
		<h2>ПОПУЛЯРНІ РОЛИ</h2>
		<div class="best-sellers__container products">
		<?php

		while ( $loop->have_posts() ) : $loop->the_post();
			global $product;
			$product_id = $product->ID;
			$grams = $product->get_attribute('grams');
			$terms = get_the_terms($product_id, 'product_tag' );
			$term_array = array();
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$term_array[] = $term->name;
				}
			}
			?>
			<a href="<?php the_permalink(); ?>" class="best-sellers__item">
				<?php echo get_the_post_thumbnail( $loop->post->ID, 'shop_catalog' ); ?>
				<p class="title"><?php the_title(); ?></p>
				<p class="ingredients">Cклад: <?php echo  implode(', ', $term_array); ?></p>
				<p class="price"><?php echo $product->get_price(); ?> &#8372;</p>
				<?php if ($grams) { ?><p class="grams"><?php echo $grams; ?></p><?php } ?>
				<?php do_action('front_cart_button'); ?>
			</a>
		<?php
		endwhile; ?>
		</div>
		<?php $shop_page_url = get_permalink( wc_get_page_id( 'shop' ) ); ?>
		<a href="<?php echo esc_url( $shop_page_url ); ?>" class="show-more-btn">ПОКАЗАТИ БІЛЬШЕ</a>
	</div>
	<?php
	wp_reset_query();
	return ob_get_clean();
}
add_shortcode( 'best_sellers', 'ib_display_best_sellers' );