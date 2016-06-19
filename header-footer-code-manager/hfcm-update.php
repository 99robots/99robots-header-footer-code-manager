<?php

function hfcm_update() {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    $id = $_GET['id'];
    //update
    if (isset($_POST['update'])) {
        $name = $_POST['data']["name"];
        $snippet = stripslashes_deep($_POST['data']["snippet"]);
        $mobile_status = $_POST['data']["mobile_status"];
        $location = $_POST['data']["location"];
        $display_on = $_POST['data']["display_on"];
        $status = $_POST['data']["status"];
        $s_pages = $_POST['data']["s_pages"];
        //echo "<pre>";print_r($_POST['data']);echo "</pre>";
        $s_custom_posts = $_POST['data']["s_custom_posts"];
        $s_categories = $_POST['data']["s_categories"];
        $s_tags = $_POST['data']["s_tags"];

        $wpdb->update(
                $table_name, //table
                array(
            "name" => $name,
            "snippet" => $snippet,
            "mobile_status" => $mobile_status,
            "location" => $location,
            "display_on" => $display_on,
            "status" => $status,
            "s_pages" => serialize($_POST['data']['s_pages']),
            "s_custom_posts" => serialize($_POST['data']['s_custom_posts']),
            "s_categories" => serialize($_POST['data']['s_categories']),
            "s_tags" => serialize($_POST['data']['s_tags']),
                ), //data
                array('script_id' => $id), //where
                array('%s', '%s', '%s', '%s', '%s', '%s'), //data format
                array('%s') //where format
        );
    }
    //delete
    else if (isset($_POST['delete'])) {
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE script_id = %s", $id));
    } else {
        //selecting value to update	
        $script = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where script_id=%s", $id));
        foreach ($script as $s) {
            $name = $s->name;
            $snippet = $s->snippet;
            $mobile_status = $s->mobile_status;
            $location = $s->location;
            $display_on = $s->display_on;
            $status = $s->status;
            $s_pages = unserialize($s->s_pages);
            if(!is_array($s_pages)) {
                $s_pages = array();
            }
            $s_custom_posts = unserialize($s->s_custom_posts);
            if(!is_array($s_custom_posts)) {
                $s_custom_posts = array();
            }
            $s_categories = unserialize($s->s_categories);
            if(!is_array($s_categories)) {
                $s_categories = array();
            }
            $s_tags = unserialize($s->s_tags);
            if(!is_array($s_tags)) {
                $s_tags = array();
            }
        }
    }
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/header-footer-code-manager/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Script</h2>

        <?php if (!empty($_POST['delete'])) { ?>
            <div class="updated"><p>Script deleted</p></div>
            <a href="<?php echo admin_url('admin.php?page=hfcm-list') ?>">&laquo; Back to list</a>

        <?php } else if (!empty($_POST['update'])) { ?>
            <div class="updated"><p>Script updated</p></div>
            <a href="<?php echo admin_url('admin.php?page=hfcm-list') ?>">&laquo; Back to list</a>

        <?php } else { ?>
            <script type="text/javascript">
                function showotherboxes(type) {
                    if(type == "s_pages") {
                        jQuery("#s_pages").show();
                        jQuery("#s_categories, #s_tags, #c_posttype").hide();
                    } else if(type == "s_categories") {
                        jQuery("#s_categories").show();
                        jQuery("#s_pages, #s_tags, #c_posttype").hide();
                    } else if(type == "s_custom_posts") {
                        jQuery("#c_posttype").show();
                        jQuery("#s_categories, #s_tags, #s_pages").hide();
                    } else if(type == "s_tags") {
                        jQuery("#s_tags").show();
                        jQuery("#s_categories, #s_pages, #c_posttype").hide();
                    } else {
                        jQuery("#s_pages, #s_categories, #s_tags, #c_posttype").hide();
                    } 
                }
            </script>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <table class='wp-list-table widefat fixed'>
                    <tr>
                        <th>Script Name</th>
                        <td><input type="text" name="data[name]" value="<?php echo $name; ?>"/></td>
                    </tr>
                    <tr>
                        <th>Snippet / Code</th>
                        <td><textarea name="data[snippet]"><?php echo $snippet; ?></textarea></td>
                    </tr>
                    <?php 
                    $statusarray = array("active" => "Active", "inactive" => "Inactive"); ?>
                    <tr>
                        <th>Mobile Status</th>
                        <td>
                            <select name="data[mobile_status]">
                                <?php
                                foreach ($statusarray as $smkey => $statusv) {
                                    if ($mobile_status == $smkey) {
                                        echo "<option value='" . $smkey . "' selected='selected'>" . $statusv . "</option>";
                                    } else {
                                        echo "<option value='" . $smkey . "'>" . $statusv . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php $larray = array("header" => "Header", "footer" => "Footer"); ?>
                    <tr>
                        <th>Location</th>
                        <td>
                            <select name="data[location]">
                                <?php
                                foreach ($larray as $lkey => $statusv) {
                                    if ($location == $lkey) {
                                        echo "<option value='" . $lkey . "' selected='selected'>" . $statusv . "</option>";
                                    } else {
                                        echo "<option value='" . $lkey . "'>" . $statusv . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php $darray = array("All" => "All", "s_pages" => "Specific pages", "s_categories" => "Specific Categories", "s_custom_posts" => "Specific Custom Post Types", "s_tags" => "Specific Tags", "latest_posts" => "Latest Posts"); ?>
                    <tr>
                        <th>Display on</th>
                        <td>
                            <select name="data[display_on]" onchange="js:showotherboxes(this.value);">
                                <?php
                                foreach ($darray as $dkey => $statusv) {
                                    if ($display_on == $dkey) {
                                        echo "<option value='" . $dkey . "' selected='selected'>" . $statusv . "</option>";
                                    } else {
                                        echo "<option value='" . $dkey . "'>" . $statusv . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                    $pages = get_pages();
                    if($display_on == "s_pages") {
                        $spagesstyle = "display:block;";
                    } else {
                        $spagesstyle = "display:none;";
                    }
                    ?>
                    <tr id="s_pages" style="<?php echo $spagesstyle; ?>">
                        <th>Page List</th>
                        <td>
                            <select name="data[s_pages][]" multiple>
                                <?php
                                foreach ($pages as $pkey => $pdata) {
                                    if (in_array($pdata->ID, $s_pages)) {
                                        echo "<option value='" . $pdata->ID . "' selected>" . $pdata->post_title . "</option>";
                                    } else {
                                        echo "<option value='" . $pdata->ID . "'>" . $pdata->post_title . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                    $args = array(
                        'hide_empty' => 0
                    );
                    $categories = get_categories($args);
                    if($display_on == "s_categories") {
                        $scategoriesstyle = "display:block;";
                    } else {
                        $scategoriesstyle = "display:none;";
                    }
                    $tags = get_tags($args);
                    if($display_on == "s_tags") {
                        $stagsstyle = "display:block;";
                    } else {
                        $stagsstyle = "display:none;";
                    }
                    $args = array(
                        'public' => true,
                        '_builtin' => false,
                    );

                    $output = 'names'; // names or objects, note names is the default
                    $operator = 'and'; // 'and' or 'or'

                    $c_posttypes = get_post_types($args, $output, $operator);
                    if($display_on == "s_custom_posts") {
                        $cpostssstyle = "display:block;";
                    } else {
                        $cpostssstyle = "display:none;";
                    }
                    ?>
                    <tr id="s_categories" style="<?php  echo $scategoriesstyle; ?>">
                        <th>Category List</th>
                        <td>
                            <select name="data[s_categories][]" multiple>
                                <?php
                                foreach ($categories as $ckey => $cdata) {
                                    if (in_array($cdata->term_id, $s_categories)) {
                                        echo "<option value='" . $cdata->term_id . "' selected>" . $cdata->name . "</option>";
                                    } else {
                                        echo "<option value='" . $cdata->term_id . "'>" . $cdata->name . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="s_tags" style="<?php echo $stagsstyle; ?>">
                        <th>Tags List</th>
                        <td>
                            <select name="data[s_tags][]" multiple>
                                <?php
                                foreach ($tags as $tkey => $tdata) {
                                    if (in_array($tdata->slug, $s_tags)) {
                                        echo "<option value='" . $tdata->slug . "' selected>" . $tdata->name . "</option>";
                                    } else {
                                        echo "<option value='" . $tdata->slug . "'>" . $tdata->name . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="c_posttype" style="<?php echo $cpostssstyle; ?>">
                        <th>Custom Post Types</th>
                        <td>
                            <select name="data[s_custom_posts][]" multiple>
                                <?php
                                foreach ($c_posttypes as $cpkey => $cpdata) {
                                    if (in_array($cpkey, $s_custom_posts)) {
                                        echo "<option value='" . $cpkey . "' selected>" . $cpdata . "</option>";
                                    } else {
                                        echo "<option value='" . $cpkey . "'>" . $cpdata . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <select name="data[status]">
                                <?php
                                foreach ($statusarray as $skey => $statusv) {
                                    if ($status == $skey) {
                                        echo "<option value='" . $skey . "' selected='selected'>" . $statusv . "</option>";
                                    } else {
                                        echo "<option value='" . $skey . "'>" . $statusv . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <input type='submit' name="update" value='Save' class='button'>
                <input type='submit' name="delete" value='Delete' class='button' onclick="return confirm('Are you sure, you want to delete this script?')">
            </form>
        <?php } ?>

    </div>
    <?php
}
?>
