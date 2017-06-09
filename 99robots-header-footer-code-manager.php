<?php
/**
 * Plugin Name: Header Footer Code Manager
 * Plugin URI: https://99robots.com/products
 * Description: Header Footer Code Manager by 99 Robots is a quick and simple way for you to add tracking code snippets, conversion pixels, or other scripts required by third party services for analytics, tracking, marketing, or chat functions. For detailed documentation, please visit the plugin's <a href="https://99robots.com/"> official page</a>.
 * Version: 1.0.3
 * Author: 99robots
 * Author URI: https://99robots.com/
 * Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
 * Text Domain: 99robots-header-footer-code-manager
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $hfcm_db_version;
$hfcm_db_version = '1.0';

// function to create the DB / Options / Defaults
function hfcm_options_install() {

	global $wpdb;
	global $hfcm_db_version;

	$table_name      = $wpdb->prefix . 'hfcm_scripts';
	$charset_collate = $wpdb->get_charset_collate();
	$sql =
		"CREATE TABLE IF NOT EXISTS $table_name(
			`script_id` int(10) NOT NULL AUTO_INCREMENT,
			`name` varchar(100) DEFAULT NULL,
			`snippet` text,
			`device_type` enum('mobile','desktop', 'both') DEFAULT 'both',
			`location` varchar(100) NOT NULL,
			`display_on` enum('All','s_pages', 's_posts','s_categories','s_custom_posts','s_tags','latest_posts','manual') NOT NULL DEFAULT 'All',
			`lp_count` int(10) DEFAULT NULL,
			`s_pages` varchar(300) DEFAULT NULL,
			`s_posts` varchar(1000) DEFAULT NULL,
			`s_custom_posts` varchar(300) DEFAULT NULL,
			`s_categories` varchar(300) DEFAULT NULL,
			`s_tags` varchar(300) DEFAULT NULL,
			`status` enum('active','inactive') NOT NULL DEFAULT 'active',
			`created_by` varchar(300) DEFAULT NULL,
			`last_modified_by` varchar(300) DEFAULT NULL,
			`created` datetime DEFAULT NULL,
			`last_revision_date` datetime DEFAULT NULL,
			PRIMARY KEY (`script_id`)
		) $charset_collate; ";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'hfcm_db_version', $hfcm_db_version );
}
register_activation_hook( __FILE__, 'hfcm_options_install' );

/*
 * Enqueue style-file, if it exists.
 */
function hfcm_enqueue_assets( $hook ) {

	$allowed_pages = array(
		'toplevel_page_hfcm-list',
		'header-footer-code-manager_page_hfcm-create',
		'admin_page_hfcm-update',
	);

	if ( in_array( $hook, $allowed_pages ) ) {
		// Plugin's CSS
		wp_register_style( 'hfcm_assets', plugins_url( 'css/style-admin.css', __FILE__ ) );
		wp_enqueue_style( 'hfcm_assets' );
	}

	// Remove hfcm-list from $allowed_pages
	array_shift( $allowed_pages );

	if ( in_array( $hook, $allowed_pages ) ) {
		// selectize.js plugin CSS and JS files
		wp_register_style( 'selectize-css', plugins_url( 'css/selectize.bootstrap3.css', __FILE__ ) );
		wp_enqueue_style( 'selectize-css' );

		wp_register_script( 'selectize-js', plugins_url( 'js/selectize.min.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'selectize-js' );
	}
}
add_action( 'admin_enqueue_scripts', 'hfcm_enqueue_assets' );

/*
 * This function loads plugins translation files
 */
function hfcm_load_translation_files() {
	load_plugin_textdomain( '99robots-header-footer-code-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'hfcm_load_translation_files' );

// function to create menu page, and submenu pages.
function hfcm_modifymenu() {

	// This is the main item for the menu
	add_menu_page(
		__( 'Header Footer Code Manager', '99robots-header-footer-code-manager' ),
		__( 'HFCM', '99robots-header-footer-code-manager' ),
		'manage_options',
		'hfcm-list',
		'hfcm_list',
		plugins_url( 'images/', __FILE__ ) . '99robots.png'
	);

	// This is a submenu
	add_submenu_page(
		'hfcm-list',
		__( 'All Snippets', '99robots-header-footer-code-manager' ),
		__( 'All Snippets', '99robots-header-footer-code-manager' ),
		'manage_options',
		'hfcm-list',
		'hfcm_list'
	);

	// This is a submenu
	add_submenu_page(
		'hfcm-list',
		__( 'Add New Snippet', '99robots-header-footer-code-manager' ),
		__( 'Add New', '99robots-header-footer-code-manager' ),
		'manage_options',
		'hfcm-create',
		'hfcm_create'
	);

	// This submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(
		null,
		__( 'Update Script', '99robots-header-footer-code-manager' ),
		__( 'Update', '99robots-header-footer-code-manager' ),
		'manage_options',
		'hfcm-update',
		'hfcm_update'
	);

	// This submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(
		null,
		__( 'Request Handler Script', '99robots-header-footer-code-manager' ),
		__( 'Request Handler', '99robots-header-footer-code-manager' ),
		'manage_options',
		'hfcm-request-handler',
		'hfcm_request_handler'
	);
}
add_action( 'admin_menu', 'hfcm_modifymenu' );

// Files containing submenu functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/hfcm-list.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/hfcm-create.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/hfcm-update.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/hfcm-request-handler.php' );

// Function to render the snippet
function hfcm_render_snippet( $scriptdata ) {
	$output = "<!-- HFCM by 99 Robots - Snippet # {$scriptdata->script_id}: {$scriptdata->name} -->\n{$scriptdata->snippet}\n<!-- /end HFCM by 99 Robots -->\n";

	return $output;
}

// Function to implement shortcode
function hfcm_shortcode( $atts ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'hfcm_scripts';
	if ( ! empty( $atts['id'] ) ) {
		$id          = (int) $atts['id'];
		$hide_device = wp_is_mobile() ? 'desktop' : 'mobile';
		$script      = $wpdb->get_results( $wpdb->prepare( "SELECT * from $table_name where status='active' AND device_type!='$hide_device' AND script_id=%s", $id ) );
		if ( ! empty( $script ) ) {
			return hfcm_render_snippet( $script[0] );
		}
	}
}
add_shortcode( 'hfcm', 'hfcm_shortcode' );

// Function to json_decode array and check if empty
function hfcm_not_empty( $scriptdata, $prop_name ) {
	$data = json_decode( $scriptdata->{$prop_name} );
	if ( empty( $data ) ) {
		return false;
	}
	return true;
}

// Function to decide which snippets to show - triggered by hooks
function hfcm_add_snippets( $location = '', $content = '' ) {
	global $wpdb;

	$beforecontent = '';
	$aftercontent  = '';

	if ( $location && in_array( $location, array( 'header', 'footer' ) ) ) {
		$display_location = "location='$location'";
	} else {
		$display_location = "location NOT IN ( 'header', 'footer' )";
	}

	$table_name  = $wpdb->prefix . 'hfcm_scripts';
	$hide_device = wp_is_mobile() ? 'desktop' : 'mobile';
	$script      = $wpdb->get_results( "SELECT * from $table_name where $display_location AND status='active' AND device_type!='$hide_device'" );

	if ( ! empty( $script ) ) {
		foreach ( $script as $key => $scriptdata ) {
			$out = '';
			switch ( $scriptdata->display_on ) {
				case 'All':
					$out = hfcm_render_snippet( $scriptdata );
					break;
				case 'latest_posts':
					if ( is_single() ) {
						$args        = array(
							'public' => true,
							'_builtin' => false,
						);
						$output      = 'names'; // names or objects, note names is the default
						$operator    = 'and'; // 'and' or 'or'
						$c_posttypes = get_post_types( $args, $output, $operator );
						$posttypes   = array( 'post' );
						foreach ( $c_posttypes as $cpkey => $cpdata ) {
							$posttypes[] = $cpdata;
						}
						if ( ! empty( $scriptdata->lp_count ) ) {
							$latestposts = wp_get_recent_posts( array(
								'numberposts' => $scriptdata->lp_count,
								'post_type' => $posttypes,
							) );
						} else {
							$latestposts = wp_get_recent_posts( array( 'post_type' => $posttypes ) );
						}

						$islatest = false;
						foreach ( $latestposts as $key => $lpostdata ) {
							if ( get_the_ID() == $lpostdata['ID'] ) {
								$islatest = true;
							}
						}

						if ( $islatest ) {
							$out = hfcm_render_snippet( $scriptdata );
						}
					}
					break;
				case 's_categories':
					if ( hfcm_not_empty( $scriptdata, 's_categories' ) && in_category( json_decode( $scriptdata->s_categories ) ) ) {
						$out = hfcm_render_snippet( $scriptdata );
					}
					break;
				case 's_custom_posts':
					if ( hfcm_not_empty( $scriptdata, 's_custom_posts' ) && is_singular( json_decode( $scriptdata->s_custom_posts ) ) ) {
						$out = hfcm_render_snippet( $scriptdata );
					}
					break;
				case 's_posts':
					if ( hfcm_not_empty( $scriptdata, 's_posts' ) && is_single( json_decode( $scriptdata->s_posts ) ) ) {
						$out = hfcm_render_snippet( $scriptdata );
					}
					break;
				case 's_pages':
					if ( hfcm_not_empty( $scriptdata, 's_pages' ) && is_page( json_decode( $scriptdata->s_pages ) ) ) {
						$out = hfcm_render_snippet( $scriptdata );
					}
					break;
				case 's_tags':
					if ( hfcm_not_empty( $scriptdata, 's_tags' ) && has_tag( json_decode( $scriptdata->s_tags ) ) ) {
						$out = hfcm_render_snippet( $scriptdata );
					}
			}

			switch ( $scriptdata->location ) {
				case 'before_content':
					$beforecontent .= $out;
					break;
				case 'after_content':
					$aftercontent  .= $out;
					break;
				default:
					echo $out;
			}
		}
	}
	// Return results after the loop finishes
	return $beforecontent . $content . $aftercontent;
}

// Function to add snippets in the header
function hfcm_header_scripts() {
	hfcm_add_snippets( 'header' );
}
add_action( 'wp_head', 'hfcm_header_scripts' );

// Function to add snippets in the footer
function hfcm_footer_scripts() {
	hfcm_add_snippets( 'footer' );
}
add_action( 'wp_footer', 'hfcm_footer_scripts' );


// Function to add snippets before/after the content
function hfcm_content_scripts( $content ) {
	return hfcm_add_snippets( false, $content );
}
add_action( 'the_content', 'hfcm_content_scripts' );

// Load redirection Javascript code
function hfcm_redirect( $url = '' ) {
	// Register the script
	wp_register_script( 'hfcm_redirection', plugins_url( 'js/location.js', __FILE__ ) );

	// Localize the script with new data
	$translation_array = array( 'url' => $url );
	wp_localize_script( 'hfcm_redirection', 'hfcm_location', $translation_array );

	// Enqueued script with localized data.
	wp_enqueue_script( 'hfcm_redirection' );
}

// Handle AJAX requests
add_action( 'wp_ajax_hfcm-request', 'hfcm_request_handler' );

// Function to sanitize POST data
function hfcm_sanitize_text( $key, $sanitize = true ) {

	if ( ! empty( $_POST['data'][ $key ] ) ) {
		$out = stripslashes_deep( $_POST['data'][ $key ] );
		if ( $sanitize ) {
			$out = sanitize_text_field( $out );
		}
		return $out;
	}

	return '';
}

// Function to sanitize strings within POST data arrays
function hfcm_sanitize_array( $key, $type = 'integer' ) {
	if ( ! empty( $_POST['data'][ $key ] ) ) {

		$arr = $_POST['data'][ $key ];

		if ( ! is_array( $arr ) ) {
			return array();
		}

		if ( 'integer' === $type ) {
			return array_map( 'absint', $arr );
		} else { // strings
			$new_array = array();
			foreach ( $arr as $val ) {
				$new_array[] = sanitize_text_field( $val );
			}
		}

		return $new_array;
	}

	return array();
}
