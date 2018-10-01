<?php

// function for submenu "Add snippet" page
function hfcm_create() {

	// check user capabilities
	current_user_can( 'administrator' );

	// prepare variables for includes/hfcm-add-edit.php
	$name           = '';
	$snippet        = '';
	$device_type    = '';
	$location       = '';
	$display_on     = '';
	$status         = '';
	$lp_count       = 5; // Default value
	$s_pages        = array();
	$ex_pages        = array();
	$s_posts        = array();
	$ex_posts        = array();
	$s_custom_posts = array();
	$s_categories   = array();
	$s_tags         = array();

	// Notify hfcm-add-edit.php NOT to make changes for update
	$update = false;

	require_once( plugin_dir_path( __FILE__ ) . 'hfcm-add-edit.php' );
}
