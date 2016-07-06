<?php

/*
  Plugin Name: Header & Footer Code Manager plugin
  Plugin URI: 99robots.com
  Description: Header & Footer Code Manager.
  Author: 99robots
  Version: 1.0.0
 */

global $hfcm_db_version;
$hfcm_db_version = '1.0';

// function to create the DB / Options / Defaults					
function hfcm_options_install() {

    global $wpdb;
    global $hfcm_db_version;

    $table_name = $wpdb->prefix . "hfcm_scripts";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name( 
            `script_id` int(10) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) DEFAULT NULL,
            `snippet` text,
            `device_type` enum('mobile','desktop', 'both') DEFAULT 'both',
            `location` varchar(100) NOT NULL,
            `display_on` enum('All','s_pages','s_categories','s_custom_posts','s_tags','latest_posts') NOT NULL DEFAULT 'All',
            `lp_count` int(10) DEFAULT NULL,
            `s_pages` varchar(300) DEFAULT NULL,
            `s_custom_posts` varchar(300) DEFAULT NULL,
            `s_categories` varchar(300) DEFAULT NULL,
            `s_tags` varchar(300) DEFAULT NULL,
            `status` enum('active','inactive') NOT NULL DEFAULT 'active',
            `created` datetime DEFAULT NULL,
            PRIMARY KEY (`script_id`)
          ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);

    add_option('hfcm_db_version', $hfcm_db_version);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'hfcm_options_install');

add_action('admin_menu', 'hfcm_modifymenu');

// function to create menu page, and submenu pages.
function hfcm_modifymenu() {

    //this is the main item for the menu
    add_menu_page('Header & Footer Code Manager', //page title
            'Header & Footer Code Manager', //menu title
            'manage_options', //capabilities
            'hfcm-list', //menu slug
            'hfcm_list', //function
            plugins_url('assets/images/', __FILE__) . '99robots.png'
    );
    //this is a submenu
    add_submenu_page('hfcm-list', //parent slug
            'All Snippets', //page title
            'All Snippets', //menu title
            'manage_options', //capability
            'hfcm-list', //menu slug
            'hfcm_list'); //function
    //this is a submenu
    add_submenu_page('hfcm-list', //parent slug
            'Add New Snippet', //page title
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

// files containing submenu functions
require_once(plugin_dir_path(__FILE__) . 'hfcm-list.php');
require_once(plugin_dir_path(__FILE__) . 'hfcm-create.php');
require_once(plugin_dir_path(__FILE__) . 'hfcm-update.php');

// function to implement shortcode
function hfcm_shortcode($atts) {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    if (!empty($atts['id'])) {
        $id = (int) $atts['id'];
        $script = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where status='active' AND script_id=%s", $id));
        if (!empty($script)) {
            if ($script[0]->device_type == "mobile" && wp_is_mobile()) {
                echo "<!-- HFCM by 99robots - Snippet #" . $script[0]->script_id . ": " . $script[0]->name . " -->\n" . $script[0]->snippet . "\n<!-- /end HFCM by 99robots -->";
            } else if ($script[0]->device_type == "desktop" && !wp_is_mobile()) {
                echo "<!-- HFCM by 99robots - Snippet #" . $script[0]->script_id . ": " . $script[0]->name . " -->\n" . $script[0]->snippet . "\n<!-- /end HFCM by 99robots -->";
            } else if ($script[0]->device_type == "both") {
                echo "<!-- HFCM by 99robots - Snippet #" . $script[0]->script_id . ": " . $script[0]->name . " -->\n" . $script[0]->snippet . "\n<!-- /end HFCM by 99robots -->";
            }
        }
    }
}

add_shortcode("hfcm", "hfcm_shortcode");

add_action('wp_head', 'hfcm_header_scripts');

// function to add snippets in the header
function hfcm_header_scripts() {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    $script = $wpdb->get_results("SELECT * from $table_name where location='header' AND status='active'");
    if (!empty($script)) {
        foreach ($script as $key => $scriptdata) {
            if (wp_is_mobile() && in_array($script[0]->device_type, array("mobile", "both"))) {
                if ($scriptdata->display_on == "All") {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "latest_posts" && is_single()) {
                    $args = array(
                        'public' => true,
                        '_builtin' => false,
                    );
                    $output = 'names'; // names or objects, note names is the default
                    $operator = 'and'; // 'and' or 'or'
                    $c_posttypes = get_post_types($args, $output, $operator);
                    $posttypes = array("post");
                    foreach ($c_posttypes as $cpkey => $cpdata) {
                        $posttypes[] = $cpdata;
                    }
                    if (!empty($scriptdata->lp_count)) {
                        $latestposts = wp_get_recent_posts(array("numberposts" => $scriptdata->lp_count, "post_type" => $posttypes));
                    } else {
                        $latestposts = wp_get_recent_posts(array("post_type" => $posttypes));
                    }

                    $islatest = false;
                    foreach ($latestposts as $key => $lpostdata) {
                        if (get_the_ID() == $lpostdata['ID']) {
                            $islatest = true;
                        }
                    }
                    if ($islatest) {
                        echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    }
                } else if ($scriptdata->display_on == "s_categories" && !empty($scriptdata->s_categories) && in_category(unserialize($scriptdata->s_categories))) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "s_custom_posts" && is_singular(unserialize($scriptdata->s_custom_posts)) && !empty($scriptdata->s_custom_posts)) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "s_pages" && !empty($scriptdata->s_pages) && is_page(unserialize($scriptdata->s_pages))) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "s_tags" && !empty($scriptdata->s_tags) && is_tag(unserialize($scriptdata->s_tags))) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                }
            } else if (!wp_is_mobile() && in_array($script[0]->device_type, array("desktop", "both"))) {
                if ($scriptdata->display_on == "All") {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "latest_posts" && is_single()) {
                    $args = array(
                        'public' => true,
                        '_builtin' => false,
                    );
                    $output = 'names'; // names or objects, note names is the default
                    $operator = 'and'; // 'and' or 'or'
                    $c_posttypes = get_post_types($args, $output, $operator);
                    $posttypes = array("post");
                    foreach ($c_posttypes as $cpkey => $cpdata) {
                        $posttypes[] = $cpdata;
                    }
                    if (!empty($scriptdata->lp_count)) {
                        $latestposts = wp_get_recent_posts(array("numberposts" => $scriptdata->lp_count, "post_type" => $posttypes));
                    } else {
                        $latestposts = wp_get_recent_posts(array("post_type" => $posttypes));
                    }
                    $islatest = false;
                    foreach ($latestposts as $key => $lpostdata) {
                        if (get_the_ID() == $lpostdata['ID']) {
                            $islatest = true;
                        }
                    }
                    if ($islatest) {
                        echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    }
                } else if ($scriptdata->display_on == "s_categories" && !empty($scriptdata->s_categories) && in_category(unserialize($scriptdata->s_categories))) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "s_custom_posts" && is_singular(unserialize($scriptdata->s_custom_posts)) && !empty($scriptdata->s_custom_posts)) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "s_pages" && !empty($scriptdata->s_pages) && is_page(unserialize($scriptdata->s_pages))) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "s_tags" && !empty($scriptdata->s_tags) && is_tag(unserialize($scriptdata->s_tags))) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                }
            }
        }
    }
}

add_action('wp_footer', 'hfcm_footer_scripts');

// function to add snippets in the footer
function hfcm_footer_scripts() {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    $script = $wpdb->get_results("SELECT * from $table_name where location='footer' AND status='active'");
    if (!empty($script)) {
        foreach ($script as $key => $scriptdata) {
            if (wp_is_mobile() && in_array($script[0]->device_type, array("mobile", "both"))) {
                if ($scriptdata->display_on == "All") {
                    echo $scriptdata->snippet;
                } else if ($scriptdata->display_on == "latest_posts" && is_single()) {
                    $args = array(
                        'public' => true,
                        '_builtin' => false,
                    );
                    $output = 'names'; // names or objects, note names is the default
                    $operator = 'and'; // 'and' or 'or'
                    $c_posttypes = get_post_types($args, $output, $operator);
                    $posttypes = array("post");
                    foreach ($c_posttypes as $cpkey => $cpdata) {
                        $posttypes[] = $cpdata;
                    }
                    if (!empty($scriptdata->lp_count)) {
                        $latestposts = wp_get_recent_posts(array("numberposts" => $scriptdata->lp_count, "post_type" => $posttypes));
                    } else {
                        $latestposts = wp_get_recent_posts(array("post_type" => $posttypes));
                    }
                    $islatest = false;
                    foreach ($latestposts as $key => $lpostdata) {
                        if (get_the_ID() == $lpostdata['ID']) {
                            $islatest = true;
                        }
                    }
                    if ($islatest) {
                        echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    }
                } else if ($scriptdata->display_on == "s_categories" && !empty($scriptdata->s_categories) && in_category(unserialize($scriptdata->s_categories))) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "s_custom_posts" && is_singular(unserialize($scriptdata->s_custom_posts)) && !empty($scriptdata->s_custom_posts)) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "s_pages" && !empty($scriptdata->s_pages) && is_page(unserialize($scriptdata->s_pages))) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "s_tags" && !empty($scriptdata->s_tags) && is_tag(unserialize($scriptdata->s_tags))) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                }
            } else if (!wp_is_mobile() && in_array($script[0]->device_type, array("desktop", "both"))) {
                if ($scriptdata->display_on == "All") {
                    echo $scriptdata->snippet;
                } else if ($scriptdata->display_on == "latest_posts" && is_single()) {
                    $args = array(
                        'public' => true,
                        '_builtin' => false,
                    );
                    $output = 'names'; // names or objects, note names is the default
                    $operator = 'and'; // 'and' or 'or'
                    $c_posttypes = get_post_types($args, $output, $operator);
                    $posttypes = array("post");
                    foreach ($c_posttypes as $cpkey => $cpdata) {
                        $posttypes[] = $cpdata;
                    }
                    if (!empty($scriptdata->lp_count)) {
                        $latestposts = wp_get_recent_posts(array("numberposts" => $scriptdata->lp_count, "post_type" => $posttypes));
                    } else {
                        $latestposts = wp_get_recent_posts(array("post_type" => $posttypes));
                    }
                    $islatest = false;
                    foreach ($latestposts as $key => $lpostdata) {
                        if (get_the_ID() == $lpostdata['ID']) {
                            $islatest = true;
                        }
                    }
                    if ($islatest) {
                        echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    }
                } else if ($scriptdata->display_on == "s_categories" && !empty($scriptdata->s_categories) && in_category(unserialize($scriptdata->s_categories))) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "s_custom_posts" && is_singular(unserialize($scriptdata->s_custom_posts)) && !empty($scriptdata->s_custom_posts)) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "s_pages" && !empty($scriptdata->s_pages) && is_page(unserialize($scriptdata->s_pages))) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                } else if ($scriptdata->display_on == "s_tags" && !empty($scriptdata->s_tags) && is_tag(unserialize($scriptdata->s_tags))) {
                    echo "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                }
            }
        }
    }
}

add_action('the_content', 'hfcm_content_scripts');

// function to add snippets before/after the content
function hfcm_content_scripts($content) {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    $script = $wpdb->get_results("SELECT * from $table_name where location NOT IN ('footer', 'header') AND status='active'");
    if (!empty($script)) {
        foreach ($script as $key => $scriptdata) {
            if (wp_is_mobile() && in_array($script[0]->device_type, array("mobile", "both"))) {
                if ($scriptdata->display_on == "s_custom_posts" && !empty($scriptdata->s_custom_posts) && is_singular(unserialize($scriptdata->s_custom_posts))) {
                    if ($scriptdata->location == "before_content") {
                        return "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->" . $content;
                    } else if ($scriptdata->location == "after_content") {
                        return $content . "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    } else {
                        return $content;
                    }
                } else if ($scriptdata->display_on == "s_pages" && !empty($scriptdata->s_pages) && is_page(unserialize($scriptdata->s_pages))) {
                    if ($scriptdata->location == "before_content") {
                        return "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->" . $content;
                    } else if ($scriptdata->location == "after_content") {
                        return $content . "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    } else {
                        return $content;
                    }
                } else {
                    return $content;
                }
            } else if (!wp_is_mobile() && in_array($script[0]->device_type, array("desktop", "both"))) {
                if ($scriptdata->display_on == "s_custom_posts" && !empty($scriptdata->s_custom_posts) && is_singular(unserialize($scriptdata->s_custom_posts))) {
                    if ($scriptdata->location == "before_content") {
                        return "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->" . $content;
                    } else if ($scriptdata->location == "after_content") {
                        return $content . "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    } else {
                        return $content;
                    }
                } else if ($scriptdata->display_on == "s_pages" && !empty($scriptdata->s_pages) && is_page(unserialize($scriptdata->s_pages))) {
                    if ($scriptdata->location == "before_content") {
                        return "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->" . $content;
                    } else if ($scriptdata->location == "after_content") {
                        return $content . "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    } else {
                        return $content;
                    }
                } else {
                    return $content;
                }
            } else {
                return $content;
            }
        }
    } else {
        return $content;
    }
}