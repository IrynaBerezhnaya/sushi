<?php

/**
 * Add AJAX handlers
 */
if ( wp_doing_ajax() ) {

	//Update office address checkout field
	add_action( 'wp_ajax_nopriv_mb_update_office_address_checkout_field', 'mb_update_office_address_checkout_field_ajax_handler' );
	add_action( 'wp_ajax_mb_update_office_address_checkout_field', 'mb_update_office_address_checkout_field_ajax_handler' );

	//Update user address details
	add_action( 'wp_ajax_nopriv_mb_update_user_address_details', 'mb_update_user_address_details_ajax_handler' );
	add_action( 'wp_ajax_mb_update_user_address_details', 'mb_update_user_address_details_ajax_handler' );

}