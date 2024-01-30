<?php

// Register the script
wp_register_script('hfcm_showboxes', plugins_url('js/showboxes.js', dirname(__FILE__)), array('jquery'));

// prepare ID (for AJAX)
if (!isset($id)) {
    $id = -1;
}

// Localize the script with new data
$translation_array = array(
    'header' => __('Header', '99robots-header-footer-code-manager'),
    'before_content' => __('Before Content', '99robots-header-footer-code-manager'),
    'after_content' => __('After Content', '99robots-header-footer-code-manager'),
    'footer' => __('Footer', '99robots-header-footer-code-manager'),
    'id' => $id,
    'security' => wp_create_nonce('hfcm-get-posts'),
);
wp_localize_script('hfcm_showboxes', 'hfcm_localize', $translation_array);

// Enqueued script with localized data.
wp_enqueue_script('hfcm_showboxes');
?>

<div class="wrap">
    <h1>
        <?php echo $update ? esc_html__('Edit Snippet', '99robots-header-footer-code-manager') : esc_html__('Add New Snippet', '99robots-header-footer-code-manager') ?>
        <?php if ($update) : ?>
            <a href="<?php echo admin_url('admin.php?page=hfcm-create') ?>" class="page-title-action">
                <?php esc_html_e('Add New Snippet', '99robots-header-footer-code-manager') ?>
            </a>
        <?php endif; ?>
    </h1>
    <?php
    if (!empty($_GET['message'])) :
        if (1 === $_GET['message']) :
            ?>
            <div class="updated">
                <p><?php esc_html_e('Script updated', '99robots-header-footer-code-manager'); ?></p>
            </div>
            <a href="<?php echo admin_url('admin.php?page=hfcm-list') ?>">&laquo; <?php esc_html_e('Back to list', '99robots-header-footer-code-manager'); ?></a>
        <?php elseif (6 === $_GET['message']) : ?>
            <div class="updated">
                <p><?php esc_html_e('Script Added Successfully', '99robots-header-footer-code-manager'); ?></p>
            </div>
            <a href="<?php echo admin_url('admin.php?page=hfcm-list') ?>">&laquo; <?php esc_html_e('Back to list', '99robots-header-footer-code-manager'); ?></a>
        <?php
        endif;
    endif;

    if ($update) : ?>
    <form method="post" action="<?php echo admin_url('admin.php?page=hfcm-request-handler&id=' . $id) ?>">
        <?php
        wp_nonce_field('update-snippet_' . $id);
        else :
        ?>
        <form method="post" action="<?php echo admin_url('admin.php?page=hfcm-request-handler') ?>">
            <?php
            wp_nonce_field('create-snippet');
            endif;
            ?>
            <table class="wp-list-table widefat fixed hfcm-form-width form-table">
                <tr>
                    <th class="hfcm-th-width"><?php esc_html_e('Snippet Name', '99robots-header-footer-code-manager'); ?></th>
                    <td><input type="text" name="data[name]" value="<?php echo $name; ?>" class="hfcm-field-width"/>
                    </td>
                </tr>
                <?php
                $darray = array(
                    'All' => esc_html__('Site Wide', '99robots-header-footer-code-manager'),
                    's_posts' => esc_html__('Specific Posts', '99robots-header-footer-code-manager'),
                    's_pages' => esc_html__('Specific Pages', '99robots-header-footer-code-manager'),
                    's_categories' => esc_html__('Specific Categories', '99robots-header-footer-code-manager'),
                    's_custom_posts' => esc_html__('Specific Post Types', '99robots-header-footer-code-manager'),
                    's_tags' => esc_html__('Specific Tags', '99robots-header-footer-code-manager'),
                    'latest_posts' => esc_html__('Latest Posts', '99robots-header-footer-code-manager'),
                    'manual' => esc_html__('Shortcode Only', '99robots-header-footer-code-manager'),
                ); ?>
                <tr>
                    <th class="hfcm-th-width"><?php esc_html_e('Site Display', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <select name="data[display_on]" onchange="hfcm_showotherboxes(this.value);">
                            <?php
                            foreach ($darray as $dkey => $statusv) {
                                if ($display_on === $dkey) {
                                    printf('<option value="%1$s" selected="selected">%2$s</option>', $dkey, $statusv);
                                } else {
                                    printf('<option value="%1$s">%2$s</option>', $dkey, $statusv);
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php
                $pages = get_pages();
                $expagesstyle = ('s_pages' === $display_on) ? 'display:none;' : '';
                $expostsstyle = ('s_posts' === $display_on) ? 'display:none;' : '';
                $excategoriesstyle = 's_categories' === $display_on ? 'display:none;' : '';
                $extagsstyle = 's_tags' === $display_on ? 'display:none;' : '';
                $excpostssstyle = 's_custom_posts' === $display_on ? 'display:none;' : '';
                $exlpcountstyle = 'latest_posts' === $display_on ? 'display:none;' : '';
                $exmanualstyle = 'manual' === $display_on ? 'display:none;' : '';
                ?>
                <tr id="ex_pages"
                    style="<?php echo $expagesstyle . $expostsstyle . $extagsstyle . $excpostssstyle . $excategoriesstyle . $exlpcountstyle . $exmanualstyle; ?>">
                    <th class="hfcm-th-width"><?php esc_html_e('Exclude Pages', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <select name="data[ex_pages][]" multiple>
                            <?php
                            foreach ($pages as $pdata) {
                                if (in_array($pdata->ID, $ex_pages)) {
                                    printf('<option value="%1$s" selected="selected">%2$s</option>', $pdata->ID, $pdata->post_title);
                                } else {
                                    printf('<option value="%1$s">%2$s</option>', $pdata->ID, $pdata->post_title);
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr id="ex_posts"
                    style="<?php echo $expagesstyle . $expostsstyle . $extagsstyle . $excpostssstyle . $excategoriesstyle . $exlpcountstyle . $exmanualstyle; ?>">
                    <th class="hfcm-th-width"><?php esc_html_e('Exclude Posts', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <div>

                            <?php

                            $post_type = '';
                            $taxonomy = '';
                            $filters = ['search', 'post_type', 'taxonomy'];

                            // filters
                            $filter_count = count($filters);
                            $filter_post_type_choices = array();
                            $filter_taxonomy_choices = array();

                            // vars
                            $post_types = array();

                            // extract special arg
                            $exclude = ['page'];
                            $exclude[] = 'acf-field';
                            $exclude[] = 'acf-field-group';

                            // get post type objects
                            $objects = get_post_types([], 'objects');

                            // loop
                            foreach ($objects as $i => $object) {

                                // bail early if is exclude
                                if (in_array($i, $exclude)) continue;

                                // bail early if is builtin (WP) private post type
                                // - nav_menu_item, revision, customize_changeset, etc
                                if ($object->_builtin && !$object->public) continue;

                                // append
                                $filter_post_type_choices[] = $i;
                            }

                            // defaults
                            $args = wp_parse_args([], array(
                                'taxonomy' => null,
                                'hide_empty' => false,
                                'update_term_meta_cache' => false,
                            ));

                            $taxonomies = acf_get_taxonomies();


                            // vars
                            $ref = array();
                            $data = array();

                            // loop
                            foreach ($taxonomies as $taxonomy) {

                                // vars
                                $object = get_taxonomy($taxonomy);
                                $label = $object->labels->singular_name;

                                // append
                                $data[$taxonomy] = $label;

                                // increase counter
                                if (!isset($ref[$label])) {
                                    $ref[$label] = 0;
                                }
                                $ref[$label]++;
                            }

                            // show taxonomy name next to label for shared labels
                            foreach ($data as $taxonomy => $label) {
                                if ($ref[$label] > 1) {
                                    $data[$taxonomy] .= ' (' . $taxonomy . ')';
                                }
                            }
                            $terms = get_terms($args);

                            foreach ($data as $taxonomy => $label) {

                                // vars
                                $this_terms = array();

                                // populate $this_terms
                                foreach ($terms as $term) {
                                    if ($term->taxonomy == $taxonomy) {
                                        $this_terms[] = $term;
                                    }
                                }

                                // bail early if no $items
                                if (empty($this_terms)) continue;

                                // sort into hierachial order
                                // this will fail if a search has taken place because parents wont exist
                                if (is_taxonomy_hierarchical($taxonomy) && empty($args['s'])) {

                                    // get all terms from this taxonomy
                                    $all_terms = get_terms(array_merge($args, array(
                                        'number' => 0,
                                        'offset' => 0,
                                        'taxonomy' => $taxonomy
                                    )));

                                    // vars
                                    $length = count($this_terms);
                                    $offset = 0;

                                    // find starting point (offset)
                                    foreach ($all_terms as $i => $term) {
                                        if ($term->term_id == $this_terms[0]->term_id) {
                                            $offset = $i;
                                            break;
                                        }
                                    }

                                    // order terms
                                    $parent = acf_maybe_get($args, 'parent', 0);
                                    $parent = acf_maybe_get($args, 'child_of', $parent);
                                    $ordered_terms = _get_term_children($parent, $all_terms, $taxonomy);

                                    // compare aray lengths
                                    // if $ordered_posts is smaller than $all_posts, WP has lost posts during the get_page_children() function
                                    // this is possible when get_post( $args ) filter out parents (via taxonomy, meta and other search parameters)
                                    if (count($ordered_terms) == count($all_terms)) {
                                        $this_terms = array_slice($ordered_terms, $offset, $length);
                                    }
                                }

                                // populate group
                                $data[$label] = array();
                                foreach ($this_terms as $term) {
                                    $data[$label][$term->term_id] = $term;
                                }
                            }
                            $filter_taxonomy_choices = $data;

                            /* filters */
                            if ($filter_count): ?>
                                <div class="filters -f<?php echo esc_attr($filter_count); ?>">
                                    <?php

                                    /* search */
                                    if (in_array('search', $filters)): ?>
                                        <span class="filter-search">
                                            <input placeholder="Search" type="text" name="ex_s"/>
                                        </span>
                                    <?php endif;


                                    /* post_type */
                                    if (in_array('post_type', $filters)): ?>
                                        <span class="filter-post_type">
                                            <select onchange="fetchExcludePosts(0);" name="ex_filter_post_type">
                                                <option value="">Select post type</option>
                                                <?php foreach ($filter_post_type_choices as $keyP => $itemP) { ?>
                                                    <option value="<?php echo $itemP; ?>"><?php echo $itemP; ?></option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                    <?php endif;


                                    /* post_type */
                                    if (in_array('taxonomy', $filters)): ?>
                                        <span class="filter-taxonomy">
                                            <select onchange="fetchExcludePosts(0);" name="ex_filter_taxonomy">
                                                <option value="">Select taxonomy</option>
                                                <?php
                                                foreach ($filter_taxonomy_choices as $keyT => $itemsT) {
                                                    if (!is_array($itemsT)) {
                                                        continue;
                                                    }
                                                    ?>
                                                    <optgroup label="<?php echo $keyT; ?>">
                                                        <?php foreach ($itemsT as $keyST => $itemsST) {
                                                            if (empty($itemsST)) {
                                                                continue;
                                                            }
                                                            ?>
                                                            <option value="<?php echo $itemsST->taxonomy; ?>:<?php echo $itemsST->slug; ?>">
                                                                <?php echo $itemsST->name; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </optgroup>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <select class="nnr-wraptext" name="data[ex_posts][]" multiple>
                            <option disabled></option>
                        </select> <img id="loader"
                                       src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>">
                    </td>
                </tr>
                <?php
                $pages = get_pages();
                $spagesstyle = ('s_pages' === $display_on) ? '' : 'display:none;';
                ?>
                <tr id="s_pages" style="<?php echo $spagesstyle; ?>">
                    <th class="hfcm-th-width"><?php esc_html_e('Page List', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <select name="data[s_pages][]" multiple>
                            <?php
                            foreach ($pages as $pdata) {
                                if (in_array($pdata->ID, $s_pages)) {
                                    printf('<option value="%1$s" selected="selected">%2$s</option>', $pdata->ID, $pdata->post_title);
                                } else {
                                    printf('<option value="%1$s">%2$s</option>', $pdata->ID, $pdata->post_title);
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php $spostsstyle = 's_posts' === $display_on ? '' : 'display:none;'; ?>
                <tr id="s_posts" style="<?php echo $spostsstyle; ?>">
                    <th class="hfcm-th-width"><?php esc_html_e('Post List', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <div>

                            <?php
                            /* filters */
                            if ($filter_count): ?>
                                <div class="filters -f<?php echo esc_attr($filter_count); ?>">
                                    <?php
                                    /* search */
                                    if (in_array('search', $filters)): ?>
                                        <span class="filter-search">
                                            <input placeholder="Search" type="text" name="q"/>
                                        </span>
                                    <?php endif;


                                    /* post_type */
                                    if (in_array('post_type', $filters)): ?>
                                        <span class="filter-post_type">
                                            <select onchange="fetchPosts(0);" name="filter_post_type">
                                                <option value="">Select post type</option>
                                                <?php foreach ($filter_post_type_choices as $keyP => $itemP) { ?>
                                                    <option value="<?php echo $itemP; ?>"><?php echo $itemP; ?></option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                    <?php endif;


                                    /* post_type */
                                    if (in_array('taxonomy', $filters)): ?>
                                        <span class="filter-taxonomy">
                                            <select onchange="fetchPosts(0);" name="filter_taxonomy">
                                                <option value="">Select taxonomy</option>
                                                <?php
                                                foreach ($filter_taxonomy_choices as $keyT => $itemsT) {
                                                    if (!is_array($itemsT)) {
                                                        continue;
                                                    }
                                                    ?>
                                                    <optgroup label="<?php echo $keyT; ?>">
                                                        <?php foreach ($itemsT as $keyST => $itemsST) {
                                                            if (empty($itemsST)) {
                                                                continue;
                                                            }
                                                            ?>
                                                            <option value="<?php echo $itemsST->taxonomy; ?>:<?php echo $itemsST->slug; ?>">
                                                                <?php echo $itemsST->name; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </optgroup>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <select class="nnr-wraptext" name="data[s_posts][]" multiple>
                            <option disabled>...</option>
                        </select>
                    </td>
                </tr>
                <?php
                $args = array('hide_empty' => 0);
                $categories = get_categories($args);
                $tags = get_tags($args);

                $scategoriesstyle = 's_categories' === $display_on ? '' : 'display:none;';
                $stagsstyle = 's_tags' === $display_on ? '' : 'display:none;';
                $cpostssstyle = 's_custom_posts' === $display_on ? '' : 'display:none;';
                $lpcountstyle = 'latest_posts' === $display_on ? '' : 'display:none;';
                $locationstyle = 'manual' === $display_on ? 'display:none;' : '';

                // Get all names of Post Types
                $args = array(
                    'public' => true,
                );

                $output = 'names';
                $operator = 'and';

                $c_posttypes = get_post_types($args, $output, $operator);
                $posttypes = array('post');
                foreach ($c_posttypes as $cpdata) {
                    $posttypes[] = $cpdata;
                }
                ?>
                <tr id="s_categories" style="<?php echo $scategoriesstyle; ?>">
                    <th class="hfcm-th-width"><?php esc_html_e('Category List', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <select name="data[s_categories][]" multiple>
                            <?php
                            foreach ($categories as $cdata) {
                                if (in_array($cdata->term_id, $s_categories)) {
                                    echo "<option value='{$cdata->term_id}' selected>{$cdata->name}</option>";
                                } else {
                                    echo "<option value='{$cdata->term_id}'>{$cdata->name}</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr id="s_tags" style="<?php echo $stagsstyle; ?>">
                    <th class="hfcm-th-width"><?php esc_html_e('Tags List', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <select name="data[s_tags][]" multiple>
                            <?php
                            foreach ($tags as $tdata) {
                                if (in_array($tdata->term_id, $s_tags)) {
                                    echo "<option value='{$tdata->term_id}' selected>{$tdata->name}</option>";
                                } else {
                                    echo "<option value='{$tdata->term_id}'>{$tdata->name}</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr id="c_posttype" style="<?php echo $cpostssstyle; ?>">
                    <th class="hfcm-th-width"><?php esc_html_e('Post Types', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <select name="data[s_custom_posts][]" multiple>
                            <?php
                            foreach ($c_posttypes as $cpkey => $cpdata) {
                                if (in_array($cpkey, $s_custom_posts)) {
                                    echo "<option value='$cpkey' selected>$cpdata</option>";
                                } else {
                                    echo "<option value='$cpkey'>$cpdata</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr id="lp_count" style="<?php echo $lpcountstyle; ?>">
                    <th class="hfcm-th-width"><?php esc_html_e('Post Count', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <select name="data[lp_count]">
                            <?php echo "<option value='{$i}'>{$i}</option>"; ?>
                            <?php
                            for ($i = 1; $i <= 20; $i++) {
                                if ($i === $lp_count) {
                                    echo "<option value='{$i}' selected>{$i}</option>";
                                } else {
                                    echo "<option value='{$i}'>{$i}</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php
                if (in_array($display_on, array('s_posts', 's_pages', 's_categories', 's_custom_posts', 's_tags', 'latest_posts'))) {
                    $larray = array('header' => 'Header', 'before_content' => 'Before Content', 'after_content' => 'After Content', 'footer' => 'Footer');
                } else {
                    $larray = array('header' => 'Header', 'footer' => 'Footer');
                }
                ?>
                <tr id="locationtr" style="<?php echo $locationstyle; ?>">
                    <th class="hfcm-th-width"><?php esc_html_e('Location', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <select name="data[location]" id="data_location">
                            <?php
                            foreach ($larray as $lkey => $statusv) {
                                if ($location === $lkey) {
                                    echo "<option value='" . $lkey . "' selected='selected'>" . $statusv . '</option>';
                                } else {
                                    echo "<option value='" . $lkey . "'>" . $statusv . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php $devicetypearray = array('both' => 'Show on All Devices', 'desktop' => 'Only Desktop', 'mobile' => 'Only Mobile Devices') ?>
                <?php $statusarray = array('active' => 'Active', 'inactive' => 'Inactive') ?>
                <tr>
                    <th class="hfcm-th-width"><?php esc_html_e('Device Display', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <select name="data[device_type]">
                            <?php
                            foreach ($devicetypearray as $smkey => $typev) {
                                if ($device_type === $smkey) {
                                    echo "<option value='" . $smkey . "' selected='selected'>" . $typev . '</option>';
                                } else {
                                    echo "<option value='" . $smkey . "'>" . $typev . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th class="hfcm-th-width"><?php esc_html_e('Status', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                        <select name="data[status]">
                            <?php
                            foreach ($statusarray as $skey => $statusv) {
                                if ($status === $skey) {
                                    echo "<option value='" . $skey . "' selected='selected'>" . $statusv . '</option>';
                                } else {
                                    echo "<option value='" . $skey . "'>" . $statusv . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php if ($update) : ?>
                    <tr>
                        <th class="hfcm-th-width"><?php esc_html_e('Shortcode', '99robots-header-footer-code-manager'); ?></th>
                        <td>
                            <p>[hfcm id="<?php echo $id; ?>"]</p>
                        </td>
                    </tr>
                    <tr>
                        <th class="hfcm-th-width"><?php esc_html_e('Changelog', '99robots-header-footer-code-manager'); ?></th>
                        <td>
                            <p>
                                <?php esc_html_e('Snippet created by', '99robots-header-footer-code-manager'); ?>
                                <b><?php echo $createdby; ?></b> <?php echo __('on', '99robots-header-footer-code-manager') . ' ' . date_i18n(get_option('date_format'), strtotime($createdon)) . ' ' . __('at', '99robots-header-footer-code-manager') . ' ' . date_i18n(get_option('time_format'), strtotime($createdon)) ?>
                                <br/>
                                <?php if (!empty($lastmodifiedby)) : ?>
                                    <?php esc_html_e('Last edited by', '99robots-header-footer-code-manager'); ?>
                                    <b><?php echo $lastmodifiedby; ?></b> <?php echo __('on', '99robots-header-footer-code-manager') . ' ' . date_i18n(get_option('date_format'), strtotime($lastrevisiondate)) . ' ' . __('at', '99robots-header-footer-code-manager') . ' ' . date_i18n(get_option('time_format'), strtotime($lastrevisiondate)) ?>
                                <?php endif; ?>
                            </p>
                        </td>
                    </tr>
                    
                <?php endif; ?>
                <tr>
                    <th class="hfcm-th-width"><?php esc_html_e('Exclude Posts', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                    <?php
                        // Initialize CMB2.
                
                        $exclude_posts = new_cmb2_box( array(
                            'id'           => 'cmb2_attached_posts_field',
                            'title'        => __( 'Exclude Posts', '99robots-header-footer-code-manager' ),
                            'object_types' => array( 'options-page' ),
                            'option_key'    => 'hfcm-create',
                            'context'      => 'normal',
                            'priority'     => 'high',
                            'show_names'   => false, // Show field names on the left.
                        ) );
                    
                        $exclude_posts->add_field( array(
                            'name'    => __( 'Exclude Posts', '99robots-header-footer-code-manager' ),
                            'desc'    => __( 'Drag posts from the left column to the right column to attach them to this page.<br />You may rearrange the order of the posts in the right column by dragging and dropping.', '99robots-header-footer-code-manager' ),
                            'default' => $ex_posts,
                            'id'      => 'data[hfcm_attached_cmb2_attached_posts]',
                            'type'    => 'custom_attached_posts',
                            'column'  => true, // Output in the admin post-listing as a custom column. https://github.com/CMB2/CMB2/wiki/Field-Parameters#column.
                            'options' => array(
                                'show_thumbnails' => true, // Show thumbnails on the left.
                                'filter_boxes'    => true, // Show a text box for filtering the results.
                                'query_args'      => array(
                                    'posts_per_page' => -1,
                                    //'post_type'      => array('post', 'page', 'attachment'),
                                    'post_type'      => array( 'post' ),
                                    'post_status'    => array('publish', 'inherit'),

                                ),
                            ),
                        ) );
                    

                        // Output CMB2 options page fields.
                        $exclude_posts->show_form();
                        ?>
                        
                    </td>
                </tr>
                <tr>
                    <th class="hfcm-th-width"><?php esc_html_e('Exclude Pages', '99robots-header-footer-code-manager'); ?></th>
                    <td>
                    <?php
                        $exclude_pages = new_cmb2_box( array(
                            'id'           => 'cmb2_attached_pages_field',
                            'title'        => __( 'Exclude Pages', '99robots-header-footer-code-manager' ),
                            'object_types' => array( 'options-page' ),
                            'option_key'    => 'hfcm-create',
                            'context'      => 'normal',
                            'priority'     => 'high',
                            'show_names'   => false, // Show field names on the left.
                        ) );
                    
                        $exclude_pages->add_field( array(
                            'name'    => __( 'Exclude Pages', '99robots-header-footer-code-manager' ),
                            'default' => $ex_pages,
                            'id'      => 'data[hfcm_attached_cmb2_attached_pages]',
                            'type'    => 'custom_attached_posts',
                            'column'  => true,
                            'options' => array(
                                'show_thumbnails' => true, 
                                'filter_boxes'    => true, 
                                'query_args'      => array(
                                    'posts_per_page' => -1,
                                    'post_type'      => array( 'page' ),
                                    'post_status'    => array( 'publish' ),
            
                                ),
                            ),
                        ) );
            
                        // Output CMB2 options page fields.
                        $exclude_pages->show_form();
                        
                        ?>
                        
                    </td>
                </tr>
            </table>

            <div class="wrap">
                <h1><?php esc_html_e('Snippet', '99robots-header-footer-code-manager'); ?>
                    / <?php esc_html_e('Code', '99robots-header-footer-code-manager') ?></h1>
                <div class="wrap">
                    <textarea name="data[snippet]" aria-describedby="newcontent-description" id="newcontent"
                              name="newcontent" rows="10"><?php echo $snippet; ?></textarea>
                    <div class="wp-core-ui">
                        <input type="submit"
                               name="<?php echo $update ? 'update' : 'insert'; ?>"
                               value="<?php echo $update ? esc_html__('Update', '99robots-header-footer-code-manager') : esc_html__('Save', '99robots-header-footer-code-manager') ?>"
                               class="button button-primary button-large nnr-btnsave">
                    </div>
                </div>
            </div>
        </form>
</div>
