<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function wp_contacts_by_sms($description = null, $show_group = true) {
	global $wpdb, $table_prefix;
	if(!$description)
		$description = __('Enter your phone for sending Contacts by SMS', 'wp-c2sms');
	
	wp_enqueue_script('functions', plugin_dir_url(__FILE__) . '../assets/js/functions.js', true, '1.0');
	//wp_enqueue_script('inputmask', plugin_dir_url(__FILE__) . '../assets/js/jquery.inputmask.js', true, '1.0');
	
	include_once dirname( __FILE__ ) . "/templates/wp-c2sms-contact-form.php";
}
add_shortcode('contacts_by_sms', 'wp_contacts_by_sms');

