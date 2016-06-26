<?php

// function for submenu "All Snippets/Codes" page
function hfcm_list() {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    $rows = $wpdb->get_results("SELECT * from $table_name");
    $activecount = $wpdb->get_results("SELECT COUNT(*) as count from $table_name where status = 'active' ");
    $inactivecount = $wpdb->get_results("SELECT COUNT(*) as count from $table_name where status = 'inactive'");
    ?>
    <link type="text/css" href="<?php echo plugins_url('assets/css/', __FILE__); ?>style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h1>Snippets 
            <a href="<?php echo admin_url('admin.php?page=hfcm-create'); ?>" class="page-title-action">Add New Snippet</a>
        </h1>
        <ul class="subsubsub">
            <li class="all">Active <span class="count">(<?php echo $activecount[0]->count; ?>)</span> |</li>
            <li class="publish">Inactive <span class="count">(<?php echo $inactivecount[0]->count; ?>)</span></li>
        </ul>
        <table class='wp-list-table widefat fixed striped posts'>
            <thead>
                <tr>
                    <th class="check-column padding20 manage-column hfcm-list-width">ID</th>
                    <th class="manage-column column-title column-primary">Code Name</th>
                    <th class="manage-column hfcm-list-width">Display On</th>
                    <th class="manage-column hfcm-list-width">Location</th>
                    <th class="manage-column hfcm-list-width">Display on Desktop?</th>
                    <th class="manage-column hfcm-list-width">Display on Mobile?</th>
                    <th class="manage-column hfcm-list-width">Status</th>
                </tr>
            </thead>

            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td class="check-column padding20 manage-column hfcm-list-width"><?php echo $row->script_id; ?></td>
                    <td class="manage-column column-title column-primary">
                        <?php echo $row->name; ?>
                        <div class="row-actions">
                            <span class="edit">
                                <a title="Edit this item" href="<?php echo admin_url('admin.php?page=hfcm-update&id=' . $row->script_id); ?>">
                                    Edit
                                </a> | 
                            </span>
                            <span class="trash">
                                <a href="<?php echo admin_url('admin.php?page=hfcm-update&delete=true&id=' . $row->script_id); ?>" title="Delete this item" class="submitdelete">
                                    Trash
                                </a>
                            </span>
                        </div>
                    </td>
                    <td class="manage-column hfcm-list-width">
                        <?php
                        $darray = array("All" => "All", "s_pages" => "Specific pages", "s_categories" => "Specific Categories", "s_custom_posts" => "Specific Custom Post Types", "s_tags" => "Specific Tags", "latest_posts" => "Latest Posts");
                        echo $darray[$row->display_on];
                        ?>
                    </td>
                    <td class="manage-column hfcm-list-width">
                        <?php
                        $larray = array("header" => "Header", "footer" => "Footer", "before_content" => "Before Content", "after_content" => "After Content");
                        echo $larray[$row->location];
                        ?>
                    </td>
                    <td class="manage-column hfcm-list-width"><?php echo $row->mobile_status; ?></td>
                    <td class="manage-column hfcm-list-width"><?php echo $row->desktop_status; ?></td>
<!--                    <td class="manage-column hfcm-list-width"><?php //echo $row->status; ?></td>-->
                    <?php if ($row->status == "active") { ?>
                        <td class="manage-column hfcm-list-width" id="toggleScript"><img src="<?php echo plugins_url('assets/images/', __FILE__); ?>on.png" onclick="togglefunction('off', <?php echo $row->script_id; ?>);" /></td>
                    <?php } else { ?>
                        <td class="manage-column hfcm-list-width" id="toggleScript"><img src="<?php echo plugins_url('assets/images/', __FILE__); ?>off.png" onclick="togglefunction('on', <?php echo $row->script_id; ?>);" /></td>
                     <?php } ?>
                </tr>
            <?php } ?>
        </table>
        <script>
            function togglefunction(togvalue, scriptid) {
                if(togvalue == "on") {
                    jQuery.ajax({
                        url: "<?php echo admin_url('admin.php?page=hfcm-update&toggle=true&id='); ?>"+scriptid, 
                        data:{togvalue:togvalue},
                        success: function(result){
                            jQuery("#toggleScript").html('<img src="<?php echo plugins_url('assets/images/', __FILE__); ?>on.png" onclick="togglefunction(\'off\');" />');
                        }
                    });
                } else {
                    jQuery.ajax({
                        url: "<?php echo admin_url('admin.php?page=hfcm-update&toggle=true&id='); ?>"+scriptid,
                        data:{togvalue:togvalue},
                        success: function(result){
                            jQuery("#toggleScript").html('<img src="<?php echo plugins_url('assets/images/', __FILE__); ?>off.png" onclick="togglefunction(\'on\');" />');
                        }
                    });
                }
            }
        </script>
    </div>
    <?php
}