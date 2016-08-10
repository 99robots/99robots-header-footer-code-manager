<?php

// function for submenu "Update snippet" page
function hfcm_update() {

	// check user capabilities
	current_user_can('administrator');
	
	if ( !isset( $_GET['id'] ) ) die( 'Missing ID parameter.' );
	$id = $_GET['id'];
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'hfcm_scripts';


	//selecting value to update	
	$script = $wpdb->get_results( $wpdb->prepare( "SELECT * from $table_name where script_id=%s", $id ) );
	foreach ($script as $s) {
		$name = $s->name;
		$snippet = $s->snippet;
		$device_type = $s->device_type;
		$location = $s->location;
		$display_on = $s->display_on;
		$status = $s->status;
		$lp_count = $s->lp_count;
		$s_pages = json_decode( $s->s_pages );
		if ( !is_array( $s_pages ) ) {
			$s_pages = array();
		}
		$s_posts = json_decode( $s->s_posts );
		if ( !is_array( $s_posts ) ) {
			$s_posts = array();
		}
		$s_custom_posts = json_decode( $s->s_custom_posts );
		if ( !is_array( $s_custom_posts ) ) {
			$s_custom_posts = array();
		}
		$s_categories = json_decode( $s->s_categories );
		if ( !is_array( $s_categories ) ) {
			$s_categories = array();
		}
		$s_tags = json_decode( $s->s_tags );
		if ( !is_array( $s_tags ) ) {
			$s_tags = array();
		}
		$createdby = esc_html( $s->created_by );
		$lastmodifiedby = esc_html( $s->last_modified_by );
		$createdon = esc_html( $s->created );
		$lastrevisiondate = esc_html( $s->last_revision_date );
	}

	// Register the script
	wp_register_script( 'hfcm_showboxes', plugins_url('../js/showboxes.js', __FILE__ ) );

	// Localize the script with new data
	$translation_array = array(
		'header' => __('Header', '99robots-header-footer-code-manager'),
		'before_content' => __('Before Content', '99robots-header-footer-code-manager'),
		'after_content' => __('After Content', '99robots-header-footer-code-manager'),
		'footer' => __('Footer', '99robots-header-footer-code-manager')
	);
	wp_localize_script( 'hfcm_showboxes', 'hfcm_localize', $translation_array );

	// Enqueued script with localized data.
	wp_enqueue_script('hfcm_showboxes');

	// escape for html output
	$name        = esc_textarea( $name );
	$snippet     = esc_textarea( $snippet );
	$device_type = esc_html( $device_type );
	$location    = esc_html( $location );
	$display_on  = esc_html( $display_on );
	$status      = esc_html( $status );
	$lp_count    = esc_html( $lp_count );
	$update      = true;
	
	require_once( plugin_dir_path( __FILE__ ) . 'hfcm-add-edit.php' );
}