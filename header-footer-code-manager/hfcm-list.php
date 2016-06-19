<?php

function hfcm_list() {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    $rows = $wpdb->get_results("SELECT * from $table_name");
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/header-footer-code-manager/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Scripts</h2>
        <div class="tablenav top">
            <div class="alignleft actions">
                <a href="<?php echo admin_url('admin.php?page=hfcm-create'); ?>">Add New</a>
            </div>
            <br class="clear">
        </div>
        <table class='wp-list-table widefat fixed striped posts'>
            <thead>
                <tr>
                    <th class="manage-column hfcm-list-width">ID</th>
                    <th class="manage-column hfcm-list-width">Name</th>
                    <th class="manage-column hfcm-list-width">Snippet</th>
                    <th class="manage-column hfcm-list-width">Mobile Status</th>
                    <th class="manage-column hfcm-list-width">Location</th>
                    <th class="manage-column hfcm-list-width">Display On</th>
                    <th class="manage-column hfcm-list-width">Script Status</th>
                    <th class="manage-column hfcm-list-width">&nbsp;</th>
                </tr>
            </thead>

            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td class="manage-column hfcm-list-width"><?php echo $row->script_id; ?></td>
                    <td class="manage-column hfcm-list-width"><?php echo $row->name; ?></td>
                    <td class="manage-column hfcm-list-width"><?php echo htmlspecialchars($row->snippet); ?></td>
                    <td class="manage-column hfcm-list-width"><?php echo $row->mobile_status; ?></td>
                    <td class="manage-column hfcm-list-width"><?php echo $row->location; ?></td>
                    <td class="manage-column hfcm-list-width"><?php echo $row->display_on; ?></td>
                    <td class="manage-column hfcm-list-width"><?php echo $row->status; ?></td>
                    <td class="manage-column hfcm-list-width"><a href="<?php echo admin_url('admin.php?page=hfcm-update&id=' . $row->script_id); ?>">Update</a></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <?php
}