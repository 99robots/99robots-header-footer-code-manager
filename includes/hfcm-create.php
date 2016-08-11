<?php

// function for submenu "Add snippet" page
function hfcm_create() {

	// check user capabilities
	current_user_can('administrator');

	global $wpdb;
	$table_name = $wpdb->prefix . 'hfcm_scripts';

	// Register the script
	wp_register_script('hfcm_showboxes', plugins_url('../js/showboxes.js', __FILE__));

	// Localize the script with new data
	$translation_array = array(
		'header' => __('Header', '99robots-header-footer-code-manager'),
		'before_content' => __('Before Content', '99robots-header-footer-code-manager'),
		'after_content' => __('After Content', '99robots-header-footer-code-manager'),
		'footer' => __('Footer', '99robots-header-footer-code-manager')
	);
	wp_localize_script('hfcm_showboxes', 'hfcm_localize', $translation_array);

	// Enqueued script with localized data.
	wp_enqueue_script('hfcm_showboxes');
	
	// prepare variables for includes/hfcm-add-edit.php
	$name           = '';
	$snippet        = '';
	$device_type    = '';
	$location       = '';
	$display_on     = '';
	$status         = '';
	$lp_count       = 5;
	$s_pages        = array();
	$s_posts        = array();
	$s_custom_posts = array();
	$s_categories   = array();
	$s_tags         = array();
	$update         = false;

	require_once( plugin_dir_path( __FILE__ ) . 'hfcm-add-edit.php' );
}