<?php
// If uninstall is not called from WordPress, exit
if (! defined('WP_UNINSTALL_PLUGIN') ) {
    exit;
}

$option_name = 'hfcm_db_version';
delete_option($option_name);
delete_option('hfcm_activation_date');
delete_option('hfcm_enforce_disallow_unfiltered_html');
delete_metadata('user', 0, 'hfcm_plugin_notice_dismissed', '', true);
delete_metadata('user', 0, 'hfcm_file_edit_plugin_notice_dismissed', '', true);
delete_metadata('user', 0, 'hfcm_disallow_unfiltered_html_notice_dismissed', '', true);

// Drop a custom db table
global $wpdb;
$table_name = $wpdb->prefix . 'hfcm_scripts';

$wpdb->query("DROP TABLE IF EXISTS $table_name");
