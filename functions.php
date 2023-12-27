<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Connecting scripts and styles
 */
function add_scripts_and_styles() {
	wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), null, true );
	wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), false );
	wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), false );

	wp_localize_script( 'main', 'mb_localize', array(
			'admin_url'   => admin_url( 'admin-ajax.php' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'add_scripts_and_styles' );

/**
 * Add custom CSS files to admin page */
function mb_admin_enqueue_scripts() {
//	wp_enqueue_style( 'admin', get_stylesheet_directory_uri() . '/assets/admin_css/admin.css', array(), wp_get_theme()->get( 'Version' ) );
	wp_enqueue_script( 'admin', get_stylesheet_directory_uri() . '/assets/admin_js/admin.js', array( 'jquery' ), '1.0.1', true );
}
add_action( 'admin_enqueue_scripts', 'mb_admin_enqueue_scripts' );

require 'inc/checkout.php';
require 'inc/home-page.php';
require 'inc/woocommerce.php';

//master test


/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
//	require_once get_stylesheet_directory() . '/woocommerce/templates/cart/mini-cart.php';
}

/**
 * Add acf options page
 */
if (function_exists('acf_add_options_page')) {
	acf_add_options_page();
}

if ( ! function_exists( 'write_log' ) ) {

	function write_log( $variable, $text_before = '' ) {

		if ( ini_get( 'log_errors' ) === 'On' ) {
			error_log( '------' . $text_before . '------' );
			error_log( gettype( $variable ) );
			if ( is_array( $variable ) || is_object( $variable ) ) {
				error_log( print_r( $variable, true ) );
			} else {
				error_log( $variable );
			}
		}
	}
}


function mb_var_dump( $variable, $text_before = '' ) {
	$text_before = ! empty ( $text_before ) ? $text_before . ': ' : '';

	echo '<pre class="' . $text_before . '">' . $text_before;
	var_dump( $variable );
	echo '</pre>';
}


/**
 * Hide shipping rates when free shipping is available, but keep "Local pickup"
 * Updated to support WooCommerce 2.6 Shipping Zones
 *
 * @param $rates
 *
 * @return array
 */
function hide_shipping_when_free_is_available( $rates ) {
	$new_rates = array();
	foreach ( $rates as $rate_id => $rate ) {
		if ( 'free_shipping' === $rate->method_id ) {
			$new_rates[ $rate_id ] = $rate;
			break;
		}
	}

	if ( ! empty( $new_rates ) ) {
		foreach ( $rates as $rate_id => $rate ) {
			if ( 'local_pickup' === $rate->method_id ) {
				$new_rates[ $rate_id ] = $rate;
				break;
			}
		}

		return $new_rates;
	}

	return $rates;
}

add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 10, 2 );

/**
 *  Moved 2 fields using priority
 *
 * @param $fields
 *
 * @return array $fields
 */

function ib_default_address_fields( $fields ) {
	$fields['billing_email']['priority'] = 8;
	$fields['billing_phone']['priority'] = 9;

	return $fields;
}

add_filter( 'woocommerce_billing_fields', 'ib_default_address_fields' );

/**
 * Remove the 'billing_company' field
 *
 * @param $fields
 *
 * @return array $fields
 */
function ib_override_checkout_fields( $fields ) {
	unset( $fields['billing']['billing_company'] );

	return $fields;
}

add_filter( 'woocommerce_checkout_fields', 'ib_override_checkout_fields' );

/**
 * Add a new checkout field
 *
 * @param $checkout
 */
function mb_add_new_checkout_field( $checkout ) {

	echo '<div id="office_address_field_container">';
	echo mb_get_office_address_checkout_field();
	echo '</div>';

}

add_action( 'woocommerce_after_checkout_billing_form', 'mb_add_new_checkout_field' );


/**
 * For Ajax
 */
add_action( 'wp_ajax_nopriv_mb_update_office_address_checkout_field', 'mb_update_office_address_checkout_field_ajax_handler' );
add_action( 'wp_ajax_mb_update_office_address_checkout_field', 'mb_update_office_address_checkout_field_ajax_handler' );

function mb_update_office_address_checkout_field_ajax_handler() {
	$field = mb_get_office_address_checkout_field();

	echo $field;
	wp_die();

}

function mb_get_office_address_checkout_field() {

	$chosen_shipping_method_ids = wc_get_chosen_shipping_method_ids();
	$chosen_shipping_method     = $chosen_shipping_method_ids[0];


	$required = $chosen_shipping_method === 'local_pickup' ? true : false;
	$type     = $chosen_shipping_method === 'local_pickup' ? 'select' : 'hidden';
	$label    = $chosen_shipping_method === 'local_pickup' ? mb_get_office_address_checkout_field_label() : '';

	$WooCommerce = new WooCommerce();
	$checkout    = $WooCommerce->checkout();

	$field = woocommerce_form_field( 'office_address', array(
		'type'     => $type,
		'required' => $required,
		'class'    => array( ' form-row-wide ' ),
		'label'    => $label,
		'clear'    => true,
		'options'  => array(
			''                  => __( 'Select', 'wps' ),
			'75 Green Lane'     => __( '75 Green Lane', 'wps' ),
			'68 King Street'    => __( '68 King Street', 'wps' ),
			'16 Alexander Road' => __( '16 Alexander Road', 'wps' ),
			'8 Manor Road'      => __( '8 Manor Road', 'wps' ),
			'53 Broadway'       => __( '53 Broadway', 'wps' ),
		),
		'return'   => true,
	), $checkout->get_value( 'office_address' ) );


	$additional_field = woocommerce_form_field( 'office_address_need_to_validate', array(
		'type'        => 'hidden',
		'required'    => false,
		'class'       => array( '' ),
		'label'       => '',
		'label_class' => '',
		'return'      => true,
	), intval( $required ) );

	$field .= $additional_field;

	return $field;
}


function mb_get_office_address_checkout_field_label() {
	$label = 'Local office address';

	return $label;
}


/**
 * Process the checkout
 * Check if set, if its not set add an error.
 */
function order_comments_field_validation() {

	$office_address_need_to_validate = isset ( $_POST['office_address_need_to_validate'] ) ? $_POST['office_address_need_to_validate'] : '';

	if ( ! empty ( $office_address_need_to_validate ) && $office_address_need_to_validate === '1' ) {
		if ( ! $_POST['office_address'] ) {

			wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), mb_get_office_address_checkout_field_label() ), 'error' );
		}
	}
}

add_action( 'woocommerce_checkout_process', 'order_comments_field_validation' );

/**
 * Update the order meta with field value
 *
 * @param $order_id
 */
function ib_custom_checkout_field_update_order_meta( $order_id ) {
	if ( ! empty( $_POST['office_address'] ) ) {
		update_post_meta( $order_id, '_ib_office_address', sanitize_text_field( $_POST['office_address'] ) );
	}
}

add_action( 'woocommerce_checkout_update_order_meta', 'ib_custom_checkout_field_update_order_meta' );


/**
 * Display field value on the order admin page
 *
 * @param object $order
 */
function ib_custom_checkout_field_display_admin_order_meta( $order ) {

	$office_address = get_post_meta( $order->id, '_ib_office_address', true );

	if ( $office_address !== '' ) {
		echo '<p><strong>' . __( 'Office address' ) . ':</strong> ' . $office_address . '</p>';
	}
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'ib_custom_checkout_field_display_admin_order_meta', 10, 1 );

/**
 * Display field value on the order-received page
 *
 * @param object $order
 */

function ib_display_order_data( $order ) {

	$office_address = get_post_meta( $order->get_id(), '_ib_office_address', true );

	if ( $office_address !== '' ) { ?>

        <tr>
            <th>Selected address:</th>
            <td class="ib-background-custom-field"><?php echo $office_address; ?></td>
        </tr>

		<?php
	}
}

add_action( 'woocommerce_order_details_after_order_table_items', 'ib_display_order_data', 999 );


/**
 * Remove all Instances of edit_post_link
 */

add_filter( 'edit_post_link', '__return_false' );

/**
 * Make checkbox '... terms and conditions' checked by default, in Woocommerce checkout
 */

add_filter( 'woocommerce_terms_is_checked_default', '__return_true' );


/**
 * Show custom badge: "Top Sale!"
 */
function ib_show_custom_badge(  ) {
	global $post;
	
	if ( get_field( 'ib_top_sale' ) ) {
		echo '<span class="ib-badge promo-label">TOP SALE!</span>';
	}
	$product = wc_get_product( $post->ID );
	$availability = $product->get_availability();  //Array ([availability] => [class] => in-stock
	$stock_status = $availability['class'];        // string  in-stock

	if ( $stock_status == 'out-of-stock' ) {
		echo '<span class="ib-badge promo-label ib-bgr-gray">Sold Out!</span>';
	}

	if( $product->is_on_sale() && empty(get_field( 'ib_top_sale' ))) {
		echo '<span class="ib-badge promo-label ib-bgr-green">Sale!</span>';
	}

}

add_action( 'woocommerce_after_shop_loop_item_title', 'ib_show_custom_badge');
add_action( 'woocommerce_single_product_summary', 'ib_show_custom_badge' );


/**
 * Change btn "Add to cart" text on "Pre-Order" on single product page and on product archives(Collection) page
 *
 * @param string $text
 *
 * @return string
 */
function ib_change_add_to_cart_text( $text, $product ) {

	if ( get_field( 'ib_pre_order' ) ) {

		if ( $product->is_type( 'variable' ) ) {

			$pre_order_variations = get_field( 'ib_pre_order_product_id' );  // Array [0] => 163 id selected field

			$pre_order = __( "Pre Order", "woocommerce" );
			$add_cart  = __( "Add to cart", "woocommerce" );

			if ( empty( $pre_order_variations ) ) {
				$text = __( "Pre Order", "woocommerce" );

				return $text;
			}

			?>
            <script>
                jQuery(document).ready(function ($) {


                    $('input.variation_id').change(function () {

                        var $variation_id = $('input.variation_id').val();

						<?php  foreach ( $pre_order_variations as $pre_order_variation_id ) { ?>

                        if ($variation_id === '<?php echo $pre_order_variation_id; ?>') {

                            $('button.single_add_to_cart_button').html('<?php echo $pre_order; ?>');

                        } else if ($variation_id === '') {

                            $('button.single_add_to_cart_button').html('<?php echo $add_cart; ?>');
                        }
						<?php } ?>
                    });

                });
            </script>
			<?php
		} else {
			$text = __( "Pre Order", "woocommerce" );
		}
	}

	$sold_out = __( "Sold Out", "woocommerce" );

	$availability = $product->get_availability();  //Array ([availability] => [class] => in-stock
	$stock_status = $availability['class'];        // string  in-stock

	if ( $stock_status == 'out-of-stock' ) {
		$text = $sold_out;
	}


	return $text;
}

add_filter( 'woocommerce_product_single_add_to_cart_text', 'ib_change_add_to_cart_text', 10, 2 );
add_filter( 'woocommerce_product_add_to_cart_text', 'ib_change_add_to_cart_text', 10, 2 );

/**
 * Display availability date of pre-order product on single product page
 */
function ib_add_availability_date() {

	if ( get_field( 'ib_pre_order' ) ) {

		global $product;
		$pre_order_variations = get_field( 'ib_pre_order_product_id' );
		$date                 = get_field( 'ib_availability_date' );

		if ( $product->is_type( 'variable' ) ) {

			if ( empty( $pre_order_variations ) && ! empty( $date ) ) {
				echo '<span class="ib-pre-order-inscrip">Available since: ' . $date . '</span>';

				return;
			} ?>

            <script>
                jQuery(document).ready(function ($) {


                    $('input.variation_id').change(function () {

                        var $variation_id = $('input.variation_id').val();

						<?php  foreach ( $pre_order_variations as $pre_order_variation_id ) { ?>

                        if ($variation_id === '<?php echo $pre_order_variation_id; ?>') {

                            $(".single_variation_wrap").append("<p class='available'>Available since: <?php echo $date; ?></p>");

                        } else if ($variation_id === '') {
                            $("p.available").remove();
                        }
						<?php } ?>
                    });


                });
            </script>
			<?php

		} else {
			echo '<span class="ib-pre-order-inscrip">Available since: ' . $date . '</span>';
		}
	}
}

add_action( 'woocommerce_after_add_to_cart_button', 'ib_add_availability_date' );


/**
 *  Set max selected dates in the past and in the future within ACF
 */
function ib_date_picker() {
	?>
    <script type="text/javascript">
        (function ($) {

            acf.add_filter('date_picker_args', function (args, $field) {

                args['minDate'] = '0';  //For example, "+1m +7d" represents one month and seven days from today.
                // args['maxDate'] = '30';

                return args;

            });

        })(jQuery);
    </script>
	<?php
}

add_action( 'acf/input/admin_footer', 'ib_date_picker' );

/**
 * Choose of the required products or variations in wp-admin/post (custom field)
 *
 * @param $field
 *
 * @return mixed
 */
function acf_load_pre_order_product_id_field_choices( $field ) {

	global $current_screen;

	if ( $current_screen && isset( $current_screen->id ) && $current_screen->id === 'product' ) {

		global $post;

		if ( isset( $post->post_type ) && $post->post_type === 'product' ) {

			$product = wc_get_product( $post->ID );
			if ( ! $product ) {
				return $field;
			}

			// reset choices
			$field['choices'] = array();

			$product_id   = $product->get_id();
			$product_name = $product->get_name();

			if ( $product->is_type( 'variable' ) ) {
				$variations = $product->get_available_variations( 'objects' );
				if ( ! $variations ) {
					return $field;
				}

				foreach ( $variations as $variation ) {

					$variation_id   = $variation->get_id();
					$variation_name = $variation->get_name();

					$field['choices'][ $variation_id ] = $variation_name;
				}


			} else {
				$field['choices'][ $product_id ] = $product_name;
				$field['default_value']          = $product_id;
			}
		}
	}

	return $field;

}

add_filter( 'acf/load_field/name=ib_pre_order_product_id', 'acf_load_pre_order_product_id_field_choices' );


/**
 * Registration New Order Statuses in admin
 */
function ib_registration_pre_order_status() {

	register_post_status( 'wc-pre-order', array(
		'label'                     => 'Pre order',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Pre order (%s)', 'Pre order (%s)' )
	) );

}

add_action( 'init', 'ib_registration_pre_order_status' );


/**
 *  Add New Order Statuses
 *
 * @param $order_statuses
 *
 * @return mixed
 */
function ib_add_pre_order_statuses( $order_statuses ) {
	$new_order_statuses = array();

	// add new order status after processing
	foreach ( $order_statuses as $key => $status ) {

		$new_order_statuses[ $key ] = $status;

		if ( 'wc-processing' === $key ) {
			$new_order_statuses['wc-pre-order'] = 'Pre order';
		}
	}

	return $new_order_statuses;
}

add_filter( 'wc_order_statuses', 'ib_add_pre_order_statuses' );


/**
 *  Show Order Status in the Dropdown "Bulk Actions"
 *
 * @param $bulk_actions
 *
 * @return mixed
 */
function ib_get_custom_order_status_bulk( $bulk_actions ) {

	$bulk_actions['mark_pre-order'] = 'Change status to pre-order';

	return $bulk_actions;
}

add_filter( 'bulk_actions-edit-shop_order', 'ib_get_custom_order_status_bulk' );


/**
 * Change background colour for a New Order Status
 */
function ib_styling_admin_order_list() {

	if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'shop_order' ) {

		?>
        <style>
            .order-status.status-pre-order {
                background: #d7bff8;
                color: #680e94;
            }
        </style>
		<?php
	}
}

add_action( 'admin_head', 'ib_styling_admin_order_list' );


/**
 * Auto complete order in admin
 *
 * @param $order_id
 * @param $order
 */
function ib_auto_complete_order( $order_id, $order ) {

	if ( ! $order_id || ! $order ) {
		return;
	}

	$order_items = $order->get_items();
	foreach ( $order_items as $order_item ) {
		$product_id           = $order_item->get_product_id();
		$product_variation_id = $order_item->get_variation_id();

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			continue;
		}

		$pre_order            = get_field( 'ib_pre_order', $product_id );
		$pre_order_variations = get_field( 'ib_pre_order_product_id', $product_id );

		if ( $order->has_status( 'processing' ) && ! empty( $pre_order ) ) {

			if ( $product->is_type( 'variable' ) ) {

				if ( empty( $pre_order_variations ) ) {
					$order->update_status( 'pre-order' );
				}

				foreach ( $pre_order_variations as $pre_order_variation_id ) {

					if ( $product_variation_id == $pre_order_variation_id ) {
						$order->update_status( 'pre-order' );
					} else {
						$order->update_status( 'processing' );
					}
				}

			} else {
				$order->update_status( 'pre-order' );
			}
		}
	}

}

add_action( 'woocommerce_order_status_processing', 'ib_auto_complete_order', 10, 2 );


/**
 * Mark the status 'pre-order' as paid
 *
 * @param $statuses
 *
 * @return array
 */
function ib_added_pre_order_is_paid_statuses( $statuses ) {

	$statuses[] = 'pre-order';

	return $statuses;
}

add_filter( 'woocommerce_order_is_paid_statuses', 'ib_added_pre_order_is_paid_statuses' );

/**
 * Add text 'Pre Order' in email before order table
 *
 * @param $order
 * @param $sent_to_admin
 * @param $plain_text
 * @param $email
 */
function ib_add_pre_order_email_before_order_table( $order, $sent_to_admin, $plain_text, $email ) {
	if ( ! $order ) {
		return;
	}

	if ( $order->has_status( 'pre-order' ) ) {
		?>
        <h3>Pre Order</h3>
		<?php
	}

}

add_action( 'woocommerce_email_before_order_table', 'ib_add_pre_order_email_before_order_table', 10, 4 );

/**
 * Add availability date in email order table
 *
 * @param $item_id
 * @param $item
 * @param $order
 * @param $plain_text
 */
function ib_add_availability_date_email_order_table( $item_id, $item, $order, $plain_text ) {

	if ( $order->has_status( 'pre-order' ) ) {

		$product_id           = $item->get_product_id();
		$product_variation_id = $item->get_variation_id();   //163

		$product              = wc_get_product( $product_id );
		$date                 = get_field( 'ib_availability_date', $product_id );
		$pre_order_variations = get_field( 'ib_pre_order_product_id', $product_id );
		$pre_order            = get_field( 'ib_pre_order', $product_id );

		if ( $product->is_type( 'variable' ) && ! empty( $pre_order ) ) {

			if ( empty( $pre_order_variations ) && ! empty( $date ) ) {
				echo '<h5>Will be available since ' . $date . '</h5>';
			}

			foreach ( $pre_order_variations as $pre_order_variation_id ) {

				if ( $product_variation_id == $pre_order_variation_id ) {
					echo '<h5>Will be available since ' . $date . '</h5>';
				}
			}


		} else if ( ! empty( $date ) ) {
			echo '<h5>Will be available since ' . $date . '</h5>';
		}

	}
}

add_action( 'woocommerce_order_item_meta_end', 'ib_add_availability_date_email_order_table', 10, 4 );


/**
 * Changing the maximum quantity to 9 for Cart page
 *
 * @param $args
 * @param $product
 *
 * @return mixed
 */
function ib_change_quantity_input_args( $args, $product ) {

	if ( ! ( is_cart() || is_product() ) ) {
		return $args;
	}

	$args['max_value'] = 9;

	return $args;
}

add_filter( 'woocommerce_quantity_input_args', 'ib_change_quantity_input_args', 10, 2 );


/**
 * Changing the maximum quantity to 9 for Single product page (variation)
 *
 * @param $data
 * @param $product
 * @param $variation
 *
 * @return mixed
 */
function ib_change_qty_available_variation_args( $data, $product, $variation ) {

	if ( ! ( is_cart() || is_product() ) ) {
		return $data;
	}

	$data['max_qty']   = 9;
	$args['max_value'] = 9;

	return $data;
}

add_filter( 'woocommerce_available_variation', 'ib_change_qty_available_variation_args', 10, 3 );

/**
 * Remove SALE! Badge
 */
add_filter( 'woocommerce_sale_flash', '__return_null' );

if ( ! function_exists( 'storefront_primary_navigation' ) ) {
	/**
	 * Display Primary Navigation
	 *
	 */
	function storefront_primary_navigation() { ?>

        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'storefront' ); ?>">

            <button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><span><?php echo esc_html( apply_filters( 'storefront_menu_toggle_text', __( 'Menu', 'storefront' ) ) ); ?></span></button>
			<?php
			wp_nav_menu(
				array(
					'theme_location'  => 'primary',
					'container_class' => 'primary-navigation',
				)
			);

			wp_nav_menu(
				array(
					'theme_location'  => 'handheld',
					'container_class' => 'handheld-navigation',
				)
			);
			?>
        </nav><!-- #site-navigation -->
		<?php
	}
}

/**
 * Remove functions
 */
function ib_remove_functions() {
	remove_action( 'storefront_header', 'storefront_site_branding', 20 );
	remove_action( 'storefront_header', 'storefront_product_search', 40 );
	remove_action( 'storefront_header', 'storefront_header_cart', 60 );
	remove_action( 'storefront_homepage', 'storefront_homepage_header', 10 );
    remove_action( 'homepage', 'storefront_product_categories', 20 );
    remove_action( 'homepage', 'storefront_featured_products', 40 );
    remove_action( 'homepage', 'storefront_on_sale_products', 60 );
    remove_action( 'homepage', 'storefront_best_selling_products', 70 );
    remove_action( 'homepage', 'storefront_popular_products', 50 );
    remove_action( 'homepage', 'storefront_recent_products', 30 );
}
add_action('init' , 'ib_remove_functions' );

