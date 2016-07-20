<?php

/*
  Plugin Name: Header & Footer Code Manager
  Plugin URI: https://99robots.com/products
  Description: Header & Footer Code Manager by 99 Robots is a quick and simple way for you to add tracking code snippets, conversion pixels, or other scripts required by third party services for analytics, marketing, or chat functions. For detailed documentation, please visit the plugin's <a href="https://99robots.com/"> official page</a>.
  Author: 99robots
  Author URI: https://99robots.com/
  Version: 1.0.0
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
  Text Domain: 99robots-header-footer-code-manager
  Domain Path: /languages
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
    dbDelta($sql);

    add_option('hfcm_db_version', $hfcm_db_version);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'hfcm_options_install');

add_action('admin_menu', 'hfcm_modifymenu');

/*
 * this function loads plugins translation files
 */
function load_hfcm_translation_files() {
    load_plugin_textdomain('99robots-header-footer-code-manager', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

//add action to load plugin files
add_action('plugins_loaded', 'load_hfcm_translation_files');

// function to create menu page, and submenu pages.
function hfcm_modifymenu() {

    //this is the main item for the menu
    add_menu_page(__('Header & Footer Code Manager', '99robots-header-footer-code-manager'), //page title
            __('Header & Footer Code Manager', '99robots-header-footer-code-manager'), //menu title
            'manage_options', //capabilities
            'hfcm-list', //menu slug
            'hfcm_list', //function
            plugins_url('assets/images/', __FILE__) . '99robots.png'
    );
    //this is a submenu
    add_submenu_page('hfcm-list', //parent slug
            __('All Snippets', '99robots-header-footer-code-manager'), //page title
            __('All Snippets', '99robots-header-footer-code-manager'), //menu title
            'manage_options', //capability
            'hfcm-list', //menu slug
            'hfcm_list'); //function
    //this is a submenu
    add_submenu_page('hfcm-list', //parent slug
            __('Add New Snippet', '99robots-header-footer-code-manager'), //page title
            __('Add New', '99robots-header-footer-code-manager'), //menu title
            'manage_options', //capability
            'hfcm-create', //menu slug
            'hfcm_create'); //function
    //this submenu is HIDDEN, however, we need to add it anyways
    add_submenu_page(null, //parent slug
            __('Update Script', '99robots-header-footer-code-manager'), //page title
            __('Update', '99robots-header-footer-code-manager'), //menu title
            'manage_options', //capability
            'hfcm-update', //menu slug
            'hfcm_update'); //function
}

// files containing submenu functions
require_once(plugin_dir_path(__FILE__) . 'hfcm-list.php');
require_once(plugin_dir_path(__FILE__) . 'hfcm-create.php');
require_once(plugin_dir_path(__FILE__) . 'hfcm-update.php');

// function to render the snippet
function hfcm_render_snippet($scriptdata, $content = '', $ret = false) {
    $output = "<!-- HFCM by 99 Robots - Snippet # {$scriptdata->script_id}: {$scriptdata->name} -->\n{$scriptdata->snippet}\n<!-- /end HFCM by 99 Robots -->\n";

    switch ($scriptdata->location) {
        case 'before_content':
            return $output . $content;
            break;
        case 'after_content':
            return $content . $output;
            break;
        default:
            if ($ret)
                return $output;
            echo $output;
    }

    return $content;
}

// function to implement shortcode
function hfcm_shortcode($atts) {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    if (!empty($atts['id'])) {
        $id = (int) $atts['id'];
        $hide_device = wp_is_mobile() ? 'desktop' : 'mobile';
        $script = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where status='active' AND device_type!='$hide_device' AND script_id=%s", $id));
        if (!empty($script) && hfcm_device_check($script[0])) {
            return hfcm_render_snippet($script[0], '', true);
        }
    }
}

// function to unserialize array and check if empty
function hfcm_not_empty($scriptdata, $prop_name) {
    $data = unserialize($scriptdata->{$prop_name});
    if (empty($data))
        return false;
    return true;
}

add_shortcode("hfcm", "hfcm_shortcode");

add_action('wp_head', 'hfcm_header_scripts');

// function to add snippets in the header
function hfcm_header_scripts($content) {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    $hide_device = wp_is_mobile() ? 'desktop' : 'mobile';
    $script = $wpdb->get_results("SELECT * from $table_name where location='header' AND status='active' AND device_type!='$hide_device'");
    if (!empty($script)) {
        foreach ($script as $key => $scriptdata) {
            switch ($scriptdata->display_on) {
                case 'All':
                    hfcm_render_snippet($scriptdata);
                    break;
                case 'latest_posts':
                    if (is_single()) {
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
                        if ($islatest)
                            hfcm_render_snippet($scriptdata);
                    }
                    break;
                case 's_categories':
                    if (hfcm_not_empty($scriptdata, 's_categories') && in_category(unserialize($scriptdata->s_categories))) {
                        hfcm_render_snippet($scriptdata);
                    }
                    break;
                case 's_custom_posts':
                    if (hfcm_not_empty($scriptdata, 's_custom_posts') && is_singular(unserialize($scriptdata->s_custom_posts))) {
                        hfcm_render_snippet($scriptdata);
                    }
                    break;
                case 's_posts':
                    if (hfcm_not_empty($scriptdata, 's_posts') && is_single(unserialize($scriptdata->s_posts))) {
                        hfcm_render_snippet($scriptdata);
                    }
                    break;
                case 's_pages':
                    if (hfcm_not_empty($scriptdata, 's_pages') && is_page(unserialize($scriptdata->s_pages))) {
                        hfcm_render_snippet($scriptdata);
                    }
                    break;
                case 's_tags':
                    if (hfcm_not_empty($scriptdata, 's_tags') && is_page(unserialize($scriptdata->s_tags))) {
                        hfcm_render_snippet($scriptdata);
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
                } else if ($scriptdata->display_on == "s_posts" && !empty($scriptdata->s_posts) && is_single(unserialize($scriptdata->s_posts))) {
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
                } else if ($scriptdata->display_on == "s_posts" && !empty($scriptdata->s_posts) && is_single(unserialize($scriptdata->s_posts))) {
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
    
    $beforecontent = "";
    $aftercontent = "";
    
    $table_name = $wpdb->prefix . "hfcm_scripts";
    $script = $wpdb->get_results("SELECT * from $table_name where location NOT IN ('footer', 'header') AND status='active'");
    if (!empty($script)) {
        foreach ($script as $key => $scriptdata) {
            if (wp_is_mobile() && in_array($script[0]->device_type, array("mobile", "both"))) {
                if ($scriptdata->display_on == "s_custom_posts" && !empty($scriptdata->s_custom_posts) && is_singular(unserialize($scriptdata->s_custom_posts))) {
                    if ($scriptdata->location == "before_content") {
                        $beforecontent .= "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->\n";
                    } else if ($scriptdata->location == "after_content") {
                        $aftercontent .= "\n<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    }
                } else if ($scriptdata->display_on == "s_pages" && !empty($scriptdata->s_pages) && is_page(unserialize($scriptdata->s_pages))) {
                    if ($scriptdata->location == "before_content") {
                        $beforecontent .= "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->\n";
                    } else if ($scriptdata->location == "after_content") {
                        $aftercontent .= "\n<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    }
                } else if ($scriptdata->display_on == "s_posts" && !empty($scriptdata->s_posts) && is_single(unserialize($scriptdata->s_posts))) {
                    if ($scriptdata->location == "before_content") {
                        $beforecontent .= "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->\n";
                    } else if ($scriptdata->location == "after_content") {
                        $aftercontent .= "\n<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    }
                }
            } else if (!wp_is_mobile() && in_array($script[0]->device_type, array("desktop", "both"))) {
                if ($scriptdata->display_on == "s_custom_posts" && !empty($scriptdata->s_custom_posts) && is_singular(unserialize($scriptdata->s_custom_posts))) {
                    if ($scriptdata->location == "before_content") {
                        $beforecontent .= "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->\n";
                    } else if ($scriptdata->location == "after_content") {
                        $aftercontent .= "\n<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    }
                } else if ($scriptdata->display_on == "s_pages" && !empty($scriptdata->s_pages) && is_page(unserialize($scriptdata->s_pages))) {
                    if ($scriptdata->location == "before_content") {
                        $beforecontent .= "<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->\n";
                    } else if ($scriptdata->location == "after_content") {
                        $aftercontent .= "\n<!-- HFCM by 99robots - Snippet #" . $scriptdata->script_id . ": " . $scriptdata->name . " -->\n" . $scriptdata->snippet . "\n<!-- /end HFCM by 99robots -->";
                    }
                }
            }
        }
    }
    
    return $beforecontent.$content.$aftercontent;
}