<?php

/**
 * Add AJAX handlers
 */
if ( wp_doing_ajax() ) {

	//Update office address checkout field
	add_action( 'wp_ajax_nopriv_mb_update_office_address_checkout_field', 'mb_update_office_address_checkout_field_ajax_handler' );
	add_action( 'wp_ajax_mb_update_office_address_checkout_field', 'mb_update_office_address_checkout_field_ajax_handler' );

}