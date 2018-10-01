<?php

// Function for submenu "Update snippet" page
function hfcm_update() {

	add_action( 'wp_enqueue_scripts', 'hfcm_selectize_enqueue' );

	// check user capabilities
	current_user_can( 'administrator' );

	if ( ! isset( $_GET['id'] ) ) {
		die( 'Missing ID parameter.' );
	}
	$id = $_GET['id'];

	global $wpdb;
	$table_name = $wpdb->prefix . 'hfcm_scripts';

	//selecting value to update
	$script = $wpdb->get_results( $wpdb->prepare( "SELECT * from $table_name where script_id=%s", $id ) );
	foreach ( $script as $s ) {
		$name = $s->name;
		$snippet = $s->snippet;
		$device_type = $s->device_type;
		$location = $s->location;
		$display_on = $s->display_on;
		$status = $s->status;
		$lp_count = $s->lp_count;
		$s_pages = json_decode( $s->s_pages );
		$ex_pages = json_decode( $s->ex_pages );
		$ex_posts = json_decode( $s->ex_posts );

		if ( ! is_array( $s_pages ) ) {
			$s_pages = array();
		}

		if ( ! is_array( $ex_pages ) ) {
			$ex_pages = array();
		}

		$s_posts = json_decode( $s->s_posts );
		if ( ! is_array( $s_posts ) ) {
			$s_posts = array();
		}

		$ex_posts = json_decode( $s->ex_posts );
		if ( ! is_array( $ex_posts ) ) {
			$ex_posts = array();
		}

		$s_custom_posts = json_decode( $s->s_custom_posts );
		if ( ! is_array( $s_custom_posts ) ) {
			$s_custom_posts = array();
		}

		$s_categories = json_decode( $s->s_categories );
		if ( ! is_array( $s_categories ) ) {
			$s_categories = array();
		}

		$s_tags = json_decode( $s->s_tags );
		if ( ! is_array( $s_tags ) ) {
			$s_tags = array();
		}

		$createdby = esc_html( $s->created_by );
		$lastmodifiedby = esc_html( $s->last_modified_by );
		$createdon = esc_html( $s->created );
		$lastrevisiondate = esc_html( $s->last_revision_date );
	}

	// escape for html output
	$name        = esc_textarea( $name );
	$snippet     = esc_textarea( $snippet );
	$device_type = esc_html( $device_type );
	$location    = esc_html( $location );
	$display_on  = esc_html( $display_on );
	$status      = esc_html( $status );
	$lp_count    = esc_html( $lp_count );
	$i    = esc_html( $lp_count );
	// Notify hfcm-add-edit.php to make necesary changes for update
	$update = true;

	require_once( plugin_dir_path( __FILE__ ) . 'hfcm-add-edit.php' );
}
