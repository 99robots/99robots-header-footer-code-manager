<?php

// function for submenu "Add snippet" page
function hfcm_create() {
    global $wpdb;
    global $current_user; 
    //get_currentuserinfo();
    
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
    if (!empty($_POST['data']["status"])) {
        $status = $_POST['data']["status"];
    } else {
        $status = "";
    }
    if (!empty($_POST['data']["lp_count"])) {
        $lp_count = $_POST['data']["lp_count"];
    } else {
        $lp_count = "";
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
    //Get Last inserted ID
    $lastinsertedid = $wpdb->get_results("SELECT script_id from $table_name ORDER BY script_id DESC LIMIT 0,1");
    if (empty($lastinsertedid)) {
        $shortcode = '[hfcm id="1"]';
    } else {
        $shortcode = '[hfcm id="' . ($lastinsertedid[0]->script_id + 1) . '"]';
    }

    //insert
    if (isset($_POST['insert'])) {
        global $wpdb;
        $wpdb->insert(
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
                    "created" => current_time("Y-m-d H:i:s"),
                    "created_by" => $current_user->display_name
                ), array("%s", "%s", "%s", "%s", "%s", "%s", "%d", "%s", "%s", "%s", "%s", "%s", "%s", "%s")
        );
        $message = "Script Added Successfully";
        $lastid = $wpdb->insert_id;
        echo "<script>window.location = '" . admin_url('admin.php?page=hfcm-update&id=' . $lastid) . "'</script>";
        exit;
    }
    ?>
    <link type="text/css" href="<?php echo plugins_url('assets/css/', __FILE__); ?>style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2><?php _e('Add New Snippet', '99robots-header-footer-code-manager'); ?></h2>
        <?php if (isset($message)): ?>
            <div class="updated">
                <p><?php echo $message; ?></p>
                <a href="<?php echo admin_url('admin.php?page=hfcm-list') ?>">&laquo; <?php _e('Back to list', '99robots-header-footer-code-manager'); ?></a>
            </div>
            <?php
            exit;
        endif;
        ?>
        <script type="text/javascript">
            // function to show dependent dropdowns for "Site Display" field.
            function showotherboxes(type) {
				var header = '<option value="header"><?php _e('Header', '99robots-header-footer-code-manager');?></option>',
					before_content = '<option value="before_content"><?php _e('Before Content', '99robots-header-footer-code-manager');?></option>',
					after_content = '<option value="after_content"><?php _e('After Content', '99robots-header-footer-code-manager');?></option>',
					footer = '<option value="footer"><?php _e('Footer', '99robots-header-footer-code-manager');?></option>',
					all_options = header + before_content + after_content + footer;
                if(type == "s_pages") {
                    jQuery("#s_pages, #locationtr").show();
                    jQuery("#data_location").html( all_options );
                    jQuery("#s_categories, #s_tags, #c_posttype, #lp_count, #s_posts").hide();
                } else if(type == "s_posts") {
                    jQuery("#s_posts, #locationtr").show();
                    jQuery("#data_location").html( all_options );
                    jQuery("#s_pages, #s_categories, #s_tags, #c_posttype, #lp_count").hide();
                } else if(type == "s_categories") {
                    jQuery("#s_categories, #locationtr").show();
                    jQuery("#data_location").html( header + footer );
                    jQuery("#s_pages, #s_tags, #c_posttype, #lp_count, #s_posts").hide();
                } else if(type == "s_custom_posts") {
                    jQuery("#c_posttype, #locationtr").show();
                    jQuery("#data_location").html( all_options );
                    jQuery("#s_categories, #s_tags, #s_pages, #lp_count, #s_posts").hide();
                } else if(type == "s_tags") {
                    jQuery("#data_location").html( all_options );
                    jQuery("#s_tags, #locationtr").show();
                    jQuery("#s_categories, #s_pages, #c_posttype, #lp_count, #s_posts").hide();
                } else if(type == "latest_posts") {
                    jQuery("#data_location").html( header + footer );
                    jQuery("#s_pages, #s_categories, #s_tags, #c_posttype, #s_posts").hide();
                    jQuery("#lp_count, #locationtr").show();
                } else if(type == "manual") {
                    jQuery("#s_pages, #s_categories, #s_tags, #c_posttype, #lp_count, #locationtr, #s_posts").hide();
                } else {
                    jQuery("#data_location").html( header + footer);
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
                <?php $darray = array("All" => "Site Wide", "s_posts" => "Specific Posts", "s_pages" => "Specific Pages", "s_categories" => "Specific Categories", "s_custom_posts" => "Specific Custom Post Types", "s_tags" => "Specific Tags", "latest_posts" => "Latest Posts", "manual" => "Shortcode Only"); ?>
                <tr>
                    <th class="hfcm-th-width"><?php _e('Site Display', '99robots-header-footer-code-manager'); ?></th>
                    <td>
						<select name="data[display_on]" onchange="showotherboxes(this.value);">
							<?php
							foreach ($darray as $dkey => $statusv) {
								if ($display_on == $dkey) {
									echo "<option value='" . $dkey . "' selected='selected'>" . __($statusv, '99robots-header-footer-code-manager') . "</option>";
								} else {
									echo "<option value='" . $dkey . "'>" . __($statusv, '99robots-header-footer-code-manager') . "</option>";
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
                    $spostsstyle = "display:block;";
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
                if ($display_on == "s_custom_posts") {
                    $cpostssstyle = "display:block;";
                } else {
                    $cpostssstyle = "display:none;";
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
                <tr id="lp_count" style="display:none;">
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
                <tr id="locationtr">
                    <th class="hfcm-th-width"><?php _e('Location', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <select name="data[location]" id="data_location">
                            <?php
                            foreach ($larray as $lkey => $statusv) {
                                if ($location == $lkey) {
                                    echo "<option value='" . $lkey . "' selected='selected'>" . __($statusv, '99robots-header-footer-code-manager') . "</option>";
                                } else {
                                    echo "<option value='" . $lkey . "'>" . __($statusv, '99robots-header-footer-code-manager') . "</option>";
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
                                    echo "<option value='" . $smkey . "' selected='selected'>" . __($typev, '99robots-header-footer-code-manager') . "</option>";
                                } else {
                                    echo "<option value='" . $smkey . "'>" . __($typev, '99robots-header-footer-code-manager') . "</option>";
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
                                    echo "<option value='" . $skey . "' selected='selected'>" . __($statusv, '99robots-header-footer-code-manager') . "</option>";
                                } else {
                                    echo "<option value='" . $skey . "'>" . __($statusv, '99robots-header-footer-code-manager') . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th class="hfcm-th-width"><?php _e('Shortcode', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <p><?php echo $shortcode; ?></p>
                    </td>
                </tr>
            </table>
            <div class="wrap">
                <h1><?php _e('Snippet', '99robots-header-footer-code-manager'); ?> / <?php _e('Code', '99robots-header-footer-code-manager'); ?></h1>
                <div class="wrap">
                    <textarea name="data[snippet]" aria-describedby="newcontent-description" id="newcontent" name="newcontent" rows="10"><?php echo $snippet; ?></textarea>
                    <div class="wp-core-ui">
                        <input type='submit' name="insert" value='<?php _e('Save', '99robots-header-footer-code-manager'); ?>' class='button button-primary button-large nnr-btnsave' />
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}