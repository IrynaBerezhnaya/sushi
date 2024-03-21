<?php
/**
 * Connecting scripts and styles
 */
function add_scripts_and_styles() {
	wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0-rc.0');
	wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', 'jquery', '4.1.0-rc.0');

	wp_enqueue_script( 'contact-form', get_stylesheet_directory_uri() . '/assets/js/contact-form.js', 'jquery', '1.0.0');

	wp_localize_script( 'contact-form', 'ib_localize', array(
			'admin_url' => admin_url( 'admin-ajax.php' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'add_scripts_and_styles' );


/**
 * Contact Form ajax handler
 */
function ib_submit_contact_form_ajax_handler() {
	$name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
	$email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
	$phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
	$city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
	$password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
	$confirm_password = isset($_POST['confirm_password']) ? sanitize_text_field($_POST['confirm_password']) : '';

	$errors = [];
	if (empty($name)) {
		$errors[] = 'Name is required.';
	}

	if (!is_email($email)) {
		$errors[] = 'Email is invalid.';
	}

	if (!empty($password) && $password !== $confirm_password) {
		$errors[] = 'Passwords do not match.';
	}

	if (!empty($errors)) {
		wp_send_json_error(implode(' ', $errors));
		wp_die();
	}

	$subject = "Form Submission Confirmation";
	$message = "Hello, $name.\n\nThank you for your submission.\nHere are the details we received:\nName: $name\nEmail: $email\nPhone: $phone\nCity: $city\n\nPlease verify your information.";
	$headers = 'From: Test <test@test.com>';

	if (wp_mail($email, $subject, $message, $headers)) {
		wp_send_json_success('Email sent successfully.');
	} else {
		wp_send_json_error('Failed to send email.');
	}

	wp_die();
}
add_action( 'wp_ajax_nopriv_ib_submit_contact_form', 'ib_submit_contact_form_ajax_handler' );
add_action( 'wp_ajax_ib_submit_contact_form', 'ib_submit_contact_form_ajax_handler' );