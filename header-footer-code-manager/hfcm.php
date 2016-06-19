<?php

/*
  Plugin Name: Header & Footer Code Manager plugin
  Plugin URI: 99robots.com
  Description: Header & Footer Code Manager.
  Author: 99robots
  Version: 1.0.0
 */

global $tp_db_version;
$tp_db_version = '1.0';

// function to create the DB / Options / Defaults					
function hfcm_options_install() {

    global $wpdb;
    global $tp_db_version;

    $table_name = $wpdb->prefix . "hfcm_scripts";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name( 
            `script_id` int(10) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) DEFAULT NULL,
            `snippet` text,
            `mobile_status` enum('active','inactive') DEFAULT 'active',
            `location` varchar(100) NOT NULL,
            `display_on` enum('All','s_pages','s_categories','s_custom_posts','s_tags','latest_posts') NOT NULL DEFAULT 'All',
            `s_pages` varchar(300) DEFAULT NULL,
            `s_custom_posts` varchar(300) DEFAULT NULL,
            `s_categories` varchar(300) DEFAULT NULL,
            `s_tags` varchar(300) DEFAULT NULL,
            `status` enum('active','inactive') NOT NULL DEFAULT 'active',
            PRIMARY KEY (`script_id`)
          ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);

    add_option('tp_db_version', $tp_db_version);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'hfcm_options_install');

add_action('admin_menu', 'hfcm_modifymenu');

function hfcm_modifymenu() {

    //this is the main item for the menu
    add_menu_page('Header & Footer Code Manager', //page title
            'Header & Footer Code Manager', //menu title
            'manage_options', //capabilities
            'hfcm-list', //menu slug
            'hfcm_list' //function
    );

    //this is a submenu
    add_submenu_page('hfcm-list', //parent slug
            'Add New Script', //page title
            'Add New', //menu title
            'manage_options', //capability
            'hfcm-create', //menu slug
            'hfcm_create'); //function
    //this submenu is HIDDEN, however, we need to add it anyways
    add_submenu_page(null, //parent slug
            'Update Script', //page title
            'Update', //menu title
            'manage_options', //capability
            'hfcm-update', //menu slug
            'hfcm_update'); //function
}

require_once(plugin_dir_path(__FILE__) . 'hfcm-list.php');
require_once(plugin_dir_path(__FILE__) . 'hfcm-create.php');
require_once(plugin_dir_path(__FILE__) . 'hfcm-update.php');

function hfcm_shortcode($atts) {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    if (!empty($atts['id'])) {
        $id = (int) $atts['id'];
        $script = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where status='active' AND script_id=%s", $id));
        if (!empty($script)) {
            if ((wp_is_mobile() && $script[0]->mobile_status == "active") || !wp_is_mobile()) {
                echo $script[0]->snippet;
            }
        }
    }
}

add_shortcode("hfcm", "hfcm_shortcode");

add_action('wp_head', 'hfcm_header_scripts');

function hfcm_header_scripts() {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    $script = $wpdb->get_results("SELECT * from $table_name where location='header' AND status='active'");
    if (!empty($script)) {
        foreach ($script as $key => $scriptdata) {
            if ((wp_is_mobile() && $scriptdata->mobile_status == "active") || !wp_is_mobile()) {
                if ($scriptdata->display_on == "All") {
                    echo $scriptdata->snippet;
                } else if ($scriptdata->display_on == "latest_posts") {
                    $latestposts = wp_get_recent_posts();
                    $islatest = false;
                    foreach ($latestposts as $key => $lpostdata) {
                        if (get_the_ID() == $lpostdata['ID']) {
                            $islatest = true;
                        }
                    }
                    if ($islatest) {
                        echo $scriptdata->snippet;
                    }
                } else if ($scriptdata->display_on == "s_categories" && !empty($scriptdata->s_categories) && in_category(unserialize($scriptdata->s_categories))) {
                    echo $scriptdata->snippet;
                } else if ($scriptdata->display_on == "s_custom_posts" && !empty($scriptdata->s_custom_posts) && in_array(get_post_type(), unserialize($scriptdata->s_custom_posts))) {
                    echo $scriptdata->snippet;
                } else if ($scriptdata->display_on == "s_pages" && !empty($scriptdata->s_pages) && is_page(unserialize($scriptdata->s_pages))) {
                    echo $scriptdata->snippet;
                } else if ($scriptdata->display_on == "s_tags" && !empty($scriptdata->s_tags) && is_tag(unserialize($scriptdata->s_tags))) {
                    echo $scriptdata->snippet;
                }
            }
        }
    }
}

add_action('wp_footer', 'hfcm_footer_scripts');

function hfcm_footer_scripts() {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    $script = $wpdb->get_results("SELECT * from $table_name where location='footer' AND status='active'");
    if (!empty($script)) {
        foreach ($script as $key => $scriptdata) {
            if ((wp_is_mobile() && $scriptdata->mobile_status == "active") || !wp_is_mobile()) {
                if ($scriptdata->display_on == "All") {
                    echo $scriptdata->snippet;
                } else if ($scriptdata->display_on == "latest_posts") {
                    $latestposts = wp_get_recent_posts();
                    $islatest = false;
                    foreach ($latestposts as $key => $lpostdata) {
                        if (get_the_ID() == $lpostdata['ID']) {
                            $islatest = true;
                        }
                    }
                    if ($islatest) {
                        echo $scriptdata->snippet;
                    }
                } else if ($scriptdata->display_on == "s_categories" && !empty($scriptdata->s_categories) && in_category(unserialize($scriptdata->s_categories))) {
                    echo $scriptdata->snippet;
                } else if ($scriptdata->display_on == "s_custom_posts" && !empty($scriptdata->s_custom_posts) && in_array(get_post_type(), unserialize($scriptdata->s_custom_posts))) {
                    echo $scriptdata->snippet;
                } else if ($scriptdata->display_on == "s_pages" && !empty($scriptdata->s_pages) && is_page(unserialize($scriptdata->s_pages))) {
                    echo $scriptdata->snippet;
                } else if ($scriptdata->display_on == "s_tags" && !empty($scriptdata->s_tags) && is_tag(unserialize($scriptdata->s_tags))) {
                    echo $scriptdata->snippet;
                }
            }
        }
    }
}