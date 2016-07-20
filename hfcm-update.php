<?php

// function for submenu "Update snippet" page
function hfcm_update() {
    global $wpdb;
    global $current_user;

    $table_name = $wpdb->prefix . "hfcm_scripts";
    $id = $_GET['id'];
    //update
    if (isset($_REQUEST['toggle']) && !empty($_REQUEST['togvalue'])) {
        if ($_REQUEST['togvalue'] == "on") {
            $status = "active";
        } else {
            $status = "inactive";
        }
        $wpdb->update(
                $table_name, //table
                array(
            "status" => $status,
                ), //data
                array('script_id' => $id), //where
                array('%s', '%s', '%s', '%s', '%s', '%s'), //data format
                array('%s') //where format
        );
        die;
    } else if (isset($_POST['update'])) {
        if (!empty($_POST['data']["name"])) {
            $name = $_POST['data']["name"];
        } else {
            $name = "";
        }
        if (!empty($_POST['data']["snippet"])) {
            $snippet = stripslashes_deep($_POST['data']["snippet"]);
        } else {
            $snippet = "";
        }
        if (!empty($_POST['data']["device_type"])) {
            $device_type = $_POST['data']["device_type"];
        } else {
            $device_type = "";
        }
        if (!empty($_POST['data']["display_on"])) {
            $display_on = $_POST['data']["display_on"];
        } else {
            $display_on = "";
        }
        if (!empty($_POST['data']["location"]) && $display_on != "manual") {
            $location = $_POST['data']["location"];
        } else {
            $location = "";
        }

        if (!empty($_POST['data']["lp_count"])) {
            $lp_count = $_POST['data']["lp_count"];
        } else {
            $lp_count = "";
        }
        if (!empty($_POST['data']["status"])) {
            $status = $_POST['data']["status"];
        } else {
            $status = "";
        }
        if (!empty($_POST['data']["s_pages"])) {
            $s_pages = $_POST['data']["s_pages"];
        } else {
            $s_pages = "";
        }
        if (!empty($_POST['data']["s_posts"])) {
            $s_posts = $_POST['data']["s_posts"];
        } else {
            $s_posts = "";
        }
        if (!is_array($s_pages)) {
            $s_pages = array();
        }
        if (!is_array($s_posts)) {
            $s_posts = array();
        }
        if (!empty($_POST['data']["s_custom_posts"])) {
            $s_custom_posts = $_POST['data']["s_custom_posts"];
        } else {
            $s_custom_posts = "";
        }
        if (!is_array($s_custom_posts)) {
            $s_custom_posts = array();
        }
        if (!empty($_POST['data']["s_categories"])) {
            $s_categories = $_POST['data']["s_categories"];
        } else {
            $s_categories = "";
        }
        if (!is_array($s_categories)) {
            $s_categories = array();
        }
        if (!empty($_POST['data']["s_tags"])) {
            $s_tags = $_POST['data']["s_tags"];
        } else {
            $s_tags = "";
        }
        if (!is_array($s_tags)) {
            $s_tags = array();
        }

        $wpdb->update(
                $table_name, //table
                array(
            "name" => $name,
            "snippet" => $snippet,
            "device_type" => $device_type,
            "location" => $location,
            "display_on" => $display_on,
            "status" => $status,
            "lp_count" => $lp_count,
            "s_pages" => serialize($s_pages),
            "s_posts" => serialize($s_posts),
            "s_custom_posts" => serialize($s_custom_posts),
            "s_categories" => serialize($s_categories),
            "s_tags" => serialize($s_tags),
            "last_revision_date" => date("Y-m-d h:i:s"),
            "last_modified_by" => $current_user->display_name
                ), //data
                array('script_id' => $id), //where
                array('%s', '%s', '%s', '%s', '%s', '%s'), //data format
                array('%s') //where format
        );
    }
    //delete
    else if (isset($_GET['delete'])) {
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE script_id = %s", $id));
    } else {
        //selecting value to update	
        $script = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where script_id=%s", $id));
        foreach ($script as $s) {
            $name = $s->name;
            $snippet = $s->snippet;
            $device_type = $s->device_type;
            $location = $s->location;
            $display_on = $s->display_on;
            $status = $s->status;
            $lp_count = $s->lp_count;
            $s_pages = unserialize($s->s_pages);
            if (!is_array($s_pages)) {
                $s_pages = array();
            }
            $s_posts = unserialize($s->s_posts);
            if (!is_array($s_posts)) {
                $s_posts = array();
            }
            $s_custom_posts = unserialize($s->s_custom_posts);
            if (!is_array($s_custom_posts)) {
                $s_custom_posts = array();
            }
            $s_categories = unserialize($s->s_categories);
            if (!is_array($s_categories)) {
                $s_categories = array();
            }
            $s_tags = unserialize($s->s_tags);
            if (!is_array($s_tags)) {
                $s_tags = array();
            }
            $createdby = $s->created_by;
            $lastmodifiedby = $s->last_modified_by;
            $createdon = $s->created;
            $lastrevisiondate = $s->last_revision_date;
        }
    }
    ?>
    <link type="text/css" href="<?php echo plugins_url('assets/css/', __FILE__); ?>style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h1><?php _e('Edit Snippet', '99robots-header-footer-code-manager'); ?>
            <a href="<?php echo admin_url('admin.php?page=hfcm-create'); ?>" class="page-title-action"><?php _e('Add New Snippet', '99robots-header-footer-code-manager'); ?></a>
        </h1>

        <?php if (!empty($_GET['delete'])) { ?>
            <div class="updated"><p><?php _e('Script deleted', '99robots-header-footer-code-manager'); ?></p></div>
            <a href="<?php echo admin_url('admin.php?page=hfcm-list') ?>">&laquo; <?php _e('Back to list', '99robots-header-footer-code-manager'); ?></a>

        <?php } else { ?>
            <?php if (!empty($_POST['update'])) { ?>
                <div class="updated"><p>Script updated</p></div>
                <a href="<?php echo admin_url('admin.php?page=hfcm-list') ?>">&laquo; <?php _e('Back to list', '99robots-header-footer-code-manager'); ?></a>

            <?php } ?>
            <script type="text/javascript">
                // function to show dependent dropdowns for "Site Display" field.
                function showotherboxes(type) {
                    if(type == "s_pages") {
                        jQuery("#s_pages, #locationtr").show();
                        jQuery("#data_location").html('<option value="header">Header</option><option value="before_content">Before Content</option><option value="after_content">After Content</option><option value="footer">Footer</option>');
                        jQuery("#s_categories, #s_tags, #c_posttype, #lp_count, #s_posts").hide();
                    } else if(type == "s_posts") {
                        jQuery("#s_posts, #locationtr").show();
                        jQuery("#data_location").html('<option value="header">Header</option><option value="before_content">Before Content</option><option value="after_content">After Content</option><option value="footer">Footer</option>');
                        jQuery("#s_pages, #s_categories, #s_tags, #c_posttype, #lp_count").hide();
                    } else if(type == "s_categories") {
                        jQuery("#s_categories, #locationtr").show();
                        jQuery("#data_location").html('<option value="header">Header</option><option value="footer">Footer</option>');
                        jQuery("#s_pages, #s_tags, #c_posttype, #lp_count, #s_posts").hide();
                    } else if(type == "s_custom_posts") {
                        jQuery("#c_posttype, #locationtr").show();
                        jQuery("#data_location").html('<option value="header">Header</option><option value="before_content">Before Content</option><option value="after_content">After Content</option><option value="footer">Footer</option>');
                        jQuery("#s_categories, #s_tags, #s_pages, #lp_count, #s_posts").hide();
                    } else if(type == "s_tags") {
                        jQuery("#data_location").html('<option value="header">Header</option><option value="before_content">Before Content</option><option value="after_content">After Content</option><option value="footer">Footer</option>');
                        jQuery("#s_tags, #locationtr").show();
                        jQuery("#s_categories, #s_pages, #c_posttype, #lp_count, #s_posts").hide();
                    } else if(type == "latest_posts") {
                        jQuery("#data_location").html('<option value="header">Header</option><option value="footer">Footer</option>');
                        jQuery("#s_pages, #s_categories, #s_tags, #c_posttype, #s_posts").hide();
                        jQuery("#lp_count, #locationtr").show();
                    } else if(type == "manual") {
                        jQuery("#s_pages, #s_categories, #s_tags, #c_posttype, #lp_count, #locationtr, #s_posts").hide();
                    } else {
                        jQuery("#data_location").html('<option value="header">Header</option><option value="footer">Footer</option>');
                        jQuery("#s_pages, #s_categories, #s_tags, #c_posttype, #lp_count, #s_posts").hide();
                        jQuery("#locationtr").show();
                    } 
                }
            </script>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <table class='wp-list-table widefat fixed hfcm-form-width form-table'>
                    <tr>
                        <th class="hfcm-th-width"><?php _e('Snippet Name', '99robots-header-footer-code-manager'); ?></th>
                        <td><input type="text" name="data[name]" value="<?php echo $name; ?>" class="hfcm-field-width" /></td>
                    </tr>
                    <?php $darray = array("All" => "All", "s_posts" => "Specific Posts", "s_pages" => "Specific Pages", "s_categories" => "Specific Categories", "s_custom_posts" => "Specific Custom Post Types", "s_tags" => "Specific Tags", "latest_posts" => "Latest Posts", "manual" => "Manual Placement"); ?>
                    <tr>
                        <th class="hfcm-th-width"><?php _e('Site Display', '99robots-header-footer-code-manager'); ?></th>
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
                    if ($display_on == "s_pages") {
                        $spagesstyle = "";
                    } else {
                        $spagesstyle = "display:none;";
                    }
                    ?>
                    <tr id="s_pages" style="<?php echo $spagesstyle; ?>">
                        <th class="hfcm-th-width"><?php _e('Page List', '99robots-header-footer-code-manager'); ?></th>
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
                    $posts = get_posts(array("post_type" => $posttypes, 'posts_per_page' => -1, 'numberposts' => -1, "orderby" => "title", "order" => "ASC"));
                    if ($display_on == "s_posts") {
                        $spostsstyle = "";
                    } else {
                        $spostsstyle = "display:none;";
                    }
                    ?>
                    <tr id="s_posts" style="<?php echo $spostsstyle; ?>">
                        <th class="hfcm-th-width"><?php _e('Post List', '99robots-header-footer-code-manager'); ?></th>
                        <td>
                            <select class="nnr-wraptext" name="data[s_posts][]" multiple>
                                <?php
                                foreach ($posts as $pkey => $pdata) {
                                    if (in_array($pdata->ID, $s_posts)) {
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
                    if ($display_on == "s_categories") {
                        $scategoriesstyle = "";
                    } else {
                        $scategoriesstyle = "display:none;";
                    }
                    $tags = get_tags($args);
                    if ($display_on == "s_tags") {
                        $stagsstyle = "";
                    } else {
                        $stagsstyle = "display:none;";
                    }

                    if ($display_on == "s_custom_posts") {
                        $cpostssstyle = "";
                    } else {
                        $cpostssstyle = "display:none;";
                    }
                    if ($display_on == "latest_posts") {
                        $lpcountstyle = "display:block;";
                    } else {
                        $lpcountstyle = "display:none;";
                    }
                    if ($display_on == "manual") {
                        $locationstyle = "display:none;";
                    } else {
                        $locationstyle = "";
                    }
                    ?>
                    <tr id="s_categories" style="<?php echo $scategoriesstyle; ?>">
                        <th class="hfcm-th-width"><?php _e('Category List', '99robots-header-footer-code-manager'); ?></th>
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
                        <th class="hfcm-th-width"><?php _e('Tags List', '99robots-header-footer-code-manager'); ?></th>
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
                        <th class="hfcm-th-width"><?php _e('Custom Post Types', '99robots-header-footer-code-manager'); ?></th>
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
                    <tr id="lp_count" style="<?php echo $lpcountstyle; ?>">
                        <th class="hfcm-th-width"><?php _e('Post Count', '99robots-header-footer-code-manager'); ?></th>
                        <td>
                            <select name="data[lp_count]">
                                <?php
                                for ($i = 1; $i <= 20; $i++) {
                                    if ($i == $lp_count) {
                                        echo "<option value='" . $i . "' selected>" . $i . "</option>";
                                    } else {
                                        echo "<option value='" . $i . "'>" . $i . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                    if (in_array($display_on, array("s_posts", "s_pages", "s_custom_posts"))) {
                        $larray = array("header" => "Header", "before_content" => "Before Content", "after_content" => "After Content", "footer" => "Footer");
                    } else {
                        $larray = array("header" => "Header", "footer" => "Footer");
                    }
                    ?>
                    <tr id="locationtr" style="<?php echo $locationstyle; ?>">
                        <th class="hfcm-th-width"><?php _e('Location', '99robots-header-footer-code-manager'); ?></th>
                        <td>
                            <select name="data[location]" id="data_location">
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
                    <?php $devicetypearray = array("both" => "Show on All Devices", "desktop" => "Only Desktop", "mobile" => "Only Mobile Devices"); ?>
                    <?php $statusarray = array("active" => "Active", "inactive" => "Inactive"); ?>

                    <tr>
                        <th class="hfcm-th-width"><?php _e('Device Display', '99robots-header-footer-code-manager'); ?></th>
                        <td>
                            <select name="data[device_type]">
                                <?php
                                foreach ($devicetypearray as $smkey => $typev) {
                                    if ($device_type == $smkey) {
                                        echo "<option value='" . $smkey . "' selected='selected'>" . $typev . "</option>";
                                    } else {
                                        echo "<option value='" . $smkey . "'>" . $typev . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th class="hfcm-th-width"><?php _e('Status', '99robots-header-footer-code-manager'); ?></th>
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
                    <tr>
                        <th class="hfcm-th-width"><?php _e('Shortcode', '99robots-header-footer-code-manager'); ?></th>
                        <td>
                            <p>[hfcm id="<?php echo $id; ?>"]</p>
                        </td>
                    </tr>
                    <tr>
                        <th class="hfcm-th-width">Created/Edited</th>
                        <td>
                            <p>
                                Snippet created by <b><?php echo $createdby; ?></b> on <?php echo date("d/m/Y", strtotime($createdon)); ?>
                                <br/>
                                Last edited by <b><?php echo $lastmodifiedby; ?></b> on <?php echo date("d/m/Y", strtotime($lastrevisiondate)); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                <div class="wrap">
                    <h1><?php _e('Snippet', '99robots-header-footer-code-manager'); ?> / <?php _e('Code', '99robots-header-footer-code-manager'); ?></h1>

                    <div class="wrap">
                        <textarea name="data[snippet]" aria-describedby="newcontent-description" id="newcontent" name="newcontent" rows="10"><?php echo $snippet; ?></textarea>

                        <div class="wp-core-ui">
                            <input type='submit' name="update" value='<?php _e('Update', '99robots-header-footer-code-manager'); ?>' class='button button-primary button-large nnr-btnsave' />
                        </div>
                    </div>
                </div>
            </form>
        <?php } ?>
    </div>
    <?php
}
?>
