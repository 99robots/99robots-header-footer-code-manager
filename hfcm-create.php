<?php

// function for submenu "Add snippet" page
function hfcm_create() {
    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
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
    if (!empty($_POST['data']["mobile_status"])) {
        $mobile_status = $_POST['data']["mobile_status"];
    } else {
        $mobile_status = "";
    }
    if (!empty($_POST['data']["desktop_status"])) {
        $desktop_status = $_POST['data']["desktop_status"];
    } else {
        $desktop_status = "";
    }
    if (!empty($_POST['data']["location"])) {
        $location = $_POST['data']["location"];
    } else {
        $location = "";
    }
    if (!empty($_POST['data']["display_on"])) {
        $display_on = $_POST['data']["display_on"];
    } else {
        $display_on = "";
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
    if (!is_array($s_pages)) {
        $s_pages = array();
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
    //insert
    if (isset($_POST['insert'])) {
        global $wpdb;
        $wpdb->insert(
                $table_name, //table
                array(
            "name" => $name,
            "snippet" => $snippet,
            "mobile_status" => $mobile_status,
            "desktop_status" => $desktop_status,
            "location" => $location,
            "display_on" => $display_on,
            "status" => $status,
            "s_pages" => serialize($s_pages),
            "s_custom_posts" => serialize($s_custom_posts),
            "s_categories" => serialize($s_categories),
            "s_tags" => serialize($s_tags),
            "created" => date("Y-m-d h:i:s")
                )
        );
        $message = "Script Added Successfully";
    }
    ?>
    <link type="text/css" href="<?php echo plugins_url('assets/css/', __FILE__); ?>style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Add New Snippet</h2>
        <?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
        <script type="text/javascript">
            // function to show dependent dropdowns for "Display on" field.
            function showotherboxes(type) {
                if(type == "s_pages") {
                    jQuery("#s_pages").show();
                    jQuery("#data_location").html('<option value="header">Header</option><option value="before_content">Before Content</option><option value="after_content">After Content</option><option value="footer">Footer</option>');
                    jQuery("#s_categories, #s_tags, #c_posttype").hide();
                } else if(type == "s_categories") {
                    jQuery("#s_categories").show();
                    jQuery("#data_location").html('<option value="header">Header</option><option value="footer">Footer</option>');
                    jQuery("#s_pages, #s_tags, #c_posttype").hide();
                } else if(type == "s_custom_posts") {
                    jQuery("#c_posttype").show();
                    jQuery("#data_location").html('<option value="header">Header</option><option value="before_content">Before Content</option><option value="after_content">After Content</option><option value="footer">Footer</option>');
                    jQuery("#s_categories, #s_tags, #s_pages").hide();
                } else if(type == "s_tags") {
                    jQuery("#data_location").html('<option value="header">Header</option><option value="before_content">Before Content</option><option value="after_content">After Content</option><option value="footer">Footer</option>');
                    jQuery("#s_tags").show();
                    jQuery("#s_categories, #s_pages, #c_posttype").hide();
                } else {
                    jQuery("#data_location").html('<option value="header">Header</option><option value="footer">Footer</option>');
                    jQuery("#s_pages, #s_categories, #s_tags, #c_posttype").hide();
                } 
            }
        </script>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <table class='wp-list-table widefat fixed hfcm-form-width form-table'>
                <tr>
                    <th class="hfcm-th-width">Snippet Name</th>
                    <td><input type="text" name="data[name]" value="<?php echo $name; ?>" class="hfcm-field-width" /></td>
                </tr>
                <?php $darray = array("All" => "All", "s_pages" => "Specific pages", "s_categories" => "Specific Categories", "s_custom_posts" => "Specific Custom Post Types", "s_tags" => "Specific Tags", "latest_posts" => "Latest Posts"); ?>
                <tr>
                    <th class="hfcm-th-width">Display on</th>
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
                    $spagesstyle = "display:block;";
                } else {
                    $spagesstyle = "display:none;";
                }
                ?>
                <tr id="s_pages" style="<?php echo $spagesstyle; ?>">
                    <th class="hfcm-th-width">Page List</th>
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
                if ($display_on == "s_categories") {
                    $scategoriesstyle = "display:block;";
                } else {
                    $scategoriesstyle = "display:none;";
                }
                $tags = get_tags($args);
                if ($display_on == "s_tags") {
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
                if ($display_on == "s_custom_posts") {
                    $cpostssstyle = "display:block;";
                } else {
                    $cpostssstyle = "display:none;";
                }
                ?>
                <tr id="s_categories" style="<?php echo $scategoriesstyle; ?>">
                    <th class="hfcm-th-width">Category List</th>
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
                    <th class="hfcm-th-width">Tags List</th>
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
                    <th class="hfcm-th-width">Custom Post Types</th>
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
                <?php
                if (in_array($display_on, array("s_pages", "s_custom_posts"))) {
                    $larray = array("header" => "Header", "before_content" => "Before Content", "after_content" => "After Content", "footer" => "Footer");
                } else {
                    $larray = array("header" => "Header", "footer" => "Footer");
                }
                ?>
                <tr>
                    <th class="hfcm-th-width">Location</th>
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
                <?php $mobilestatusarray = array("yes" => "Yes", "no" => "No"); ?>
                <?php $statusarray = array("active" => "Active", "inactive" => "Inactive"); ?>
                <tr>
                    <th class="hfcm-th-width">Display on Desktop?</th>
                    <td>
                        <select name="data[desktop_status]">
                            <?php
                            foreach ($mobilestatusarray as $smkey => $statusv) {
                                if ($desktop_status == $smkey) {
                                    echo "<option value='" . $smkey . "' selected='selected'>" . $statusv . "</option>";
                                } else {
                                    echo "<option value='" . $smkey . "'>" . $statusv . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th class="hfcm-th-width">Display on Mobile?</th>
                    <td>
                        <select name="data[mobile_status]">
                            <?php
                            foreach ($mobilestatusarray as $smkey => $statusv) {
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
                <tr>
                    <th class="hfcm-th-width">Status</th>
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
            <h1>Snippet / Code</h1>
            <textarea name="data[snippet]" aria-describedby="newcontent-description" id="newcontent" name="newcontent" rows="10" cols="70"><?php echo $snippet; ?></textarea>
            <div class="wp-core-ui">
                <input type='submit' name="insert" value='Publish' class='button button-primary button-large btnsave' />
            </div>
        </form>
    </div>
    <?php
}