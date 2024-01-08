<?php

/**
 * Add Top Bar
 */
function ib_display_top_bar() {
	$top_bar = get_field( 'top_bar', 'option' );
	if ( ! empty( $top_bar ) ) : ?>
		<div class="top-bar">
			<?php echo $top_bar; ?>
		</div>
	<?php endif;
}

add_action( 'storefront_before_header', 'ib_display_top_bar' );

/**
 * Add Header section
 */
function ib_display_header() {
	$header_logo     = get_field( 'header_logo', 'option' );
	$header_schedule = get_field( 'header_schedule', 'option' );
	$header_socials  = get_field( 'header_socials', 'option' );

	if ( ! empty( $header_logo ) or ! empty( $header_schedule ) or ! empty( $header_socials ) ) : ?>
		<div class="navigation-branding">
			<?php if ( ! empty( $header_logo ) ) : ?>
				<div class="site-logo">
					<a href="<?php echo home_url(); ?>" title="home" rel="home">
						<img src="<?php echo $header_logo['url']; ?>" height="80" width="287"
						     alt="<?php bloginfo( 'name' ); ?>"/>
					</a>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $header_schedule ) ) : ?>
				<div class="header-schedule">
                                        <span><img
		                                        src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/time.svg"
		                                        alt="time"></span>
					<div><?php echo $header_schedule; ?></div>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $header_socials ) ) : ?>
				<div class="header-socials">
					<?php foreach ( $header_socials as $item ) : ?>
						<?php if ( ! empty( $item['link'] or $item['text'] ) ) : ?>
							<div class="social__list">
								<a href="<?php echo ! empty( $item['link'] ) ? $item['link'] : $item['text']; ?>">
									<img src="<?php echo esc_url( $item['icon']['url'] ); ?>"
									     alt="<?php echo $item['icon']['alt']; ?>">
								</a>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif;
}

add_action( 'storefront_header', 'ib_display_header' );


/**
 * Display product categories with thumbnails in the menu
 */
function ib_display_categories_menu() {
	$specific_categories = array(
		'actions',
		'sets',
		'rolls',
		'baked-rolls',
		'wok',
		'sushi',
		'salad',
		'soup',
		'business-lanch',
		'drinks',
		'desserts',
		'other'
	);
	$args                = array(
		'taxonomy'   => 'product_cat',
		'title_li'   => '',
		'hide_empty' => false,
		'slug'       => $specific_categories,
	);

	$product_categories = get_categories( $args );

	if ( $product_categories ) {
		echo '<div class="categories-menu categories-slide_js swiper">';
		echo '<ul class="categories-menu__container swiper-wrapper">';
		$is_first_item = true;

		foreach ( $product_categories as $category ) {
			$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
			$image_active = get_field( 'thumbnail_hover', 'product_cat_' . $category->term_id );
			$image        = wp_get_attachment_url( $thumbnail_id );
			$is_current   = is_product_category( $category->slug );

			echo '<li class="' . ( $is_current ? 'active' : '' ) . ' swiper-slide">';
			echo '<a href="' . get_term_link( $category ) . '" class="categories-menu__link">';

			if ( $is_first_item ) {
				echo $category->slug === 'actions' ? $category->name . '</a>' : '<span class="categories-menu__category-name">' . $category->name . '</span></a>';
			} else {
				if ( $is_current && $image_active ) {
					echo '<img src="' . $image_active['url'] . '" class="style-svg" alt="' . $category->name . '" />';
				} elseif ( $image ) {
					echo '<img src="' . $image . '" class="style-svg" alt="' . $category->name . '" />';
				}
				echo $category->slug === 'actions' ? $category->name . '</a>' : '<span class="categories-menu__category-name">' . $category->name . '</span></a>';
			}

			echo '</li>';
			$is_first_item = false;
		}
		echo '</ul>';
		echo '</div>';
	}
}

add_action( 'storefront_header', 'ib_display_categories_menu' );


/**
 * Modify Display New Products
 */
function ib_recent_products( $args ) {
	$args = array(
		'orderby'        => 'date',
		'limit'          => 6,
		'posts_per_page' => 6,
		'columns'        => 3,
		'title'          => __( 'New In', 'storefront' ),
	);

	return $args;
}

add_filter( 'storefront_recent_products_args', 'ib_recent_products' );

/**
 * Modify Display Popular Products
 */
function ib_popular_products( $args ) {
	$args = array(
		'post_type'      => 'product',
		'meta_key'       => 'total_sales',
		'orderby'        => 'meta_value_num',
		'posts_per_page' => 6,
		'limit'          => 6,
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => array( 'drinks', 'business-lanch' ),
				'operator' => 'NOT IN',
			),
		),
		'title'          => __( 'Fan Favorites', 'storefront' ),
	);

	return $args;
}

add_filter( 'storefront_popular_products_args', 'ib_popular_products' );

/**
 * Add display ingredients for Products Loop
 */
function ib_display_ingredients() {
	global $product;
	$product_id = $product->ID;
	$terms      = get_the_terms( $product_id, 'product_tag' );
	$term_array = array();
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$term_array[] = $term->name;
		}
	}
	if ( $term_array ) { ?>
		<p class="ingredients"><?php esc_html_e( 'Cклад: ', 'woocommerce' ); ?><?php echo implode( ', ', $term_array ); ?></p>
	<?php }
}

add_action( 'woocommerce_after_shop_loop_item_title', 'ib_display_ingredients', 9 );

/**
 * Add display grams for Products Loop price
 */
function ib_display_grams() {
	global $product;
	$product = wc_get_product( get_the_ID() );
	$weight  = $product->get_weight();

	if ( $weight ) { ?>
		<p class="grams"><?php echo $weight;
			esc_html_e( ' г', 'woocommerce' ); ?></p>
	<?php }
}

add_action( 'woocommerce_after_shop_loop_item', 'ib_display_grams' );

/**
 * Show custom badges for Products
 */
function ib_show_custom_badges( $product = false ) {

	if ( ! $product ) {
		global $product;
	}

	if ( is_a( $product, 'WC_Product' ) ) {
		$product_id         = $product->get_id();
		$product_categories = wc_get_product_term_ids( $product_id, 'product_cat' );

		if ( $product_categories ) {
			echo '<div class="badges">';
			foreach ( $product_categories as $category_id ) {
				$show_badge   = get_field( 'show_badge', 'product_cat_' . $category_id );
				$thumbnail_id = get_term_meta( $category_id, 'thumbnail_id', true );
				$image        = wp_get_attachment_url( $thumbnail_id );
				$term         = get_term_by( 'id', $category_id, 'product_cat', 'ARRAY_A' );;

				if ( $show_badge == '1' && $image ) {
					echo '<a href="' . get_term_link( $category_id ) . '" class="item-link">';
					echo '<img src="' . esc_url( $image ) . '" class="item-img" alt="' . $term['slug'] . ' badge" />';
					echo '<span class="item-name">' . $term['name'] . '</span>';
					echo '</a>';
				}
			}
			echo '</div>';
		}
	}
}

add_action( 'woocommerce_after_shop_loop_item_title', 'ib_show_custom_badges' );
add_action( 'woocommerce_product_thumbnails', 'ib_show_custom_badges' );

/**
 * Change buy button text for Products
 */
function ib_change_buy_button_text( $button_text ) {
	$button_text = 'БЕРУ';

	return $button_text;
}

add_filter( 'woocommerce_product_add_to_cart_text', 'ib_change_buy_button_text' );


/**
 * Display popup overlay
 */
function mb_display_popup_overlay() {
	echo '<div class="popup-overlay active_" id="popup_overlay"></div>';
}

add_action( 'storefront_before_site', 'mb_display_popup_overlay' );


/**
 * Display Shipping City popup
 */
function mb_display_shipping_city_popup() {
	echo '<div class="popup popup__js" id="shipping_city_popup">';

	echo '<h3>' . __( 'Select a city' ) . '</h3>';

	$shipping_zones = mb_get_all_shipping_zones();
	if ( ! empty( $shipping_zones ) ) {

		echo '<div class="shipping-zones">';
		foreach ( $shipping_zones as $zone ) {

			//get shipping zones:
			$zone_locations = $zone->get_zone_locations();
			if ( ! empty( $zone_locations ) ) {
				foreach ( $zone_locations as $zone_location ) {

					//get postcode:
					$location_type = $zone_location->type ?? '';
					$post_code     = $zone_location->code ?? '';
					if ( $location_type === 'postcode' && ! empty( $post_code ) ) {
						$zone_name = $zone->get_zone_name();

						echo '<button class="button btn-secondary">' . $zone_name . '</button>';
						break;
					}
				}
			}
		}

		echo '</div>';
		echo '<button class="button btn-primary center">' . __( 'Confirm' ) . '</button>';

	}

	echo '</div>';
}

add_action( 'storefront_before_site', 'mb_display_shipping_city_popup' );

/**
 * Get all shipping zones
 */
function mb_get_all_shipping_zones() {
	$data_store = WC_Data_Store::load( 'shipping-zone' );
	$raw_zones  = $data_store->get_zones();
	foreach ( $raw_zones as $raw_zone ) {
		$zones[] = new WC_Shipping_Zone( $raw_zone );
	}
	$zones[] = new WC_Shipping_Zone( 0 ); // ADD ZONE "0" MANUALLY

	return $zones;
}