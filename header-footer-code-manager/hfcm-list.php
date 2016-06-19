<?php

function hfcm_list() {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    $rows = $wpdb->get_results("SELECT * from $table_name");
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/header-footer-code-manager/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Scripts</h2>
        <a href="<?php echo admin_url('admin.php?page=hfcm-create'); ?>">Add New</a>
        <table class='wp-list-table widefat fixed'>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Snippet</th>
            <th>Mobile Status</th>
            <th>Location</th>
            <th>Display On</th>
            <th>Script Status</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach ($rows as $row) { ?>
            <tr>
            <td><?php echo $row->script_id; ?></td>
            <td><?php echo $row->name; ?></td>
            <td><?php echo htmlspecialchars($row->snippet); ?></td>
            <td><?php echo $row->mobile_status; ?></td>
            <td><?php echo $row->location; ?></td>
            <td><?php echo $row->display_on; ?></td>
            <td><?php echo $row->status; ?></td>
            <td><a href="<?php echo admin_url('admin.php?page=hfcm-update&id=' . $row->script_id); ?>">Update</a></td>
            </tr>
        <?php } ?>
        </table>
    </div>
    <?php
}