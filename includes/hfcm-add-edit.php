<?php
// Register and localize the script for AJAX
wp_register_script('hfcm_showboxes', plugins_url('js/nnr-hfcm-showboxes.js', dirname(__FILE__)), array('jquery'));

if (!isset($id)) $id = -1;

wp_localize_script('hfcm_showboxes', 'hfcm_localize', [
    'header'         => __('Header', 'header-footer-code-manager'),
    'before_content' => __('Before Content', 'header-footer-code-manager'),
    'after_content'  => __('After Content', 'header-footer-code-manager'),
    'footer'         => __('Footer', 'header-footer-code-manager'),
    'id'             => absint($id),
    'security'       => wp_create_nonce('hfcm-get-posts'),
]);

wp_enqueue_script('hfcm_showboxes');

/** Helper function to render <option> elements for a <select>. */
function hfcm_render_options($options, $selected = null) {
    foreach ($options as $key => $label) {
        $is_selected = is_array($selected) ? in_array($key, $selected) : $selected === $key;
        printf(
            '<option value="%s"%s>%s</option>',
            esc_attr($key),
            $is_selected ? ' selected' : '',
            esc_html($label)
        );
    }
}
?>

<div class="wrap">
    <h1>
        <?php echo $update ? esc_html__('Edit Snippet', 'header-footer-code-manager') : esc_html__('Add New Snippet', 'header-footer-code-manager'); ?>
        <?php if ($update): ?>
            <a href="<?php echo admin_url('admin.php?page=hfcm-create'); ?>" class="page-title-action">
                <?php esc_html_e('Add New Snippet', 'header-footer-code-manager'); ?>
            </a>
        <?php endif; ?>
    </h1>
    <?php if (!empty($_GET['message'])): ?>
        <?php if (1 === (int)$_GET['message']): ?>
            <div class="updated"><p><?php esc_html_e('Script updated', 'header-footer-code-manager'); ?></p></div>
            <a href="<?php echo admin_url('admin.php?page=hfcm-list'); ?>">&laquo; <?php esc_html_e('Back to list', 'header-footer-code-manager'); ?></a>
        <?php elseif (6 === (int)$_GET['message']): ?>
            <div class="updated"><p><?php esc_html_e('Script Added Successfully', 'header-footer-code-manager'); ?></p></div>
            <a href="<?php echo admin_url('admin.php?page=hfcm-list'); ?>">&laquo; <?php esc_html_e('Back to list', 'header-footer-code-manager'); ?></a>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    $hfcm_form_action = admin_url('admin.php?page=hfcm-request-handler' . ($update ? '&id=' . absint($id) : ''));
    ?>

    <form method="post" action="<?php echo $hfcm_form_action; ?>">
        <?php
        $update ? wp_nonce_field('update-snippet_' . absint($id)) : wp_nonce_field('create-snippet');
        ?>

        <table class="wp-list-table widefat fixed hfcm-form-width form-table">
            <tr>
                <th class="hfcm-th-width"><?php esc_html_e('Snippet Name', 'header-footer-code-manager'); ?></th>
                <td>
                    <input type="text" name="data[name]" value="<?php echo esc_attr($name); ?>" class="hfcm-field-width" />
                </td>
            </tr>

            <?php
            // Type arrays
            $type_array = [
                'html' => esc_html__('HTML', 'header-footer-code-manager'),
                'css'  => esc_html__('CSS', 'header-footer-code-manager'),
                'js'   => esc_html__('Javascript', 'header-footer-code-manager')
            ];
            ?>
            <tr id="snippet_type">
                <th class="hfcm-th-width"><?php esc_html_e('Snippet Type', 'header-footer-code-manager'); ?></th>
                <td>
                    <select name="data[snippet_type]"><?php hfcm_render_options($type_array, $nnr_snippet_type); ?></select>
                </td>
            </tr>

            <?php
            $nnr_hfcm_display_array = [
                'All'            => esc_html__('Site Wide', 'header-footer-code-manager'),
                's_posts'        => esc_html__('Specific Posts', 'header-footer-code-manager'),
                's_pages'        => esc_html__('Specific Pages', 'header-footer-code-manager'),
                's_categories'   => esc_html__('Specific Categories (Archive & Posts)', 'header-footer-code-manager'),
                's_custom_posts' => esc_html__('Specific Post Types (Archive & Posts)', 'header-footer-code-manager'),
                's_tags'         => esc_html__('Specific Tags (Archive & Posts)', 'header-footer-code-manager'),
                's_is_home'      => esc_html__('Home Page', 'header-footer-code-manager'),
                's_is_search'    => esc_html__('Search Page', 'header-footer-code-manager'),
                's_is_archive'   => esc_html__('All Archive Pages', 'header-footer-code-manager'),
                'latest_posts'   => esc_html__('Latest Posts', 'header-footer-code-manager'),
                'manual'         => esc_html__('Shortcode Only', 'header-footer-code-manager')
            ];
            ?>
            <tr>
                <th class="hfcm-th-width"><?php esc_html_e('Site Display', 'header-footer-code-manager'); ?></th>
                <td>
                    <select name="data[display_on]" onchange="hfcm_showotherboxes(this.value);">
                        <?php hfcm_render_options($nnr_hfcm_display_array, $display_on); ?>
                    </select>
                </td>
            </tr>

            <?php
            $spt_display_on_style = $display_on === 's_custom_posts' ? '' : 'display:none;';
            $spt_options_array = [
                'both'     => esc_html__('Both', 'header-footer-code-manager'),
                'posts'    => esc_html__('Posts Only', 'header-footer-code-manager'),
                'archives' => esc_html__('Archives Only', 'header-footer-code-manager')
            ];
            ?>
            <tr id="nnr-spt-display-on" style="<?php echo esc_attr($spt_display_on_style); ?>">
                <th class="hfcm-th-width"><?php esc_html_e('Specific Post Type Display', 'header-footer-code-manager'); ?></th>
                <td>
                    <select name="data[spt_display_on]">
                        <?php hfcm_render_options($spt_options_array, $spt_display_on); ?>
                    </select>
                </td>
            </tr>

            <?php
            $pages = get_pages();
            $hide_styles = [
                'pages'        => $display_on === 's_pages' ? 'display:none;' : '',
                'posts'        => $display_on === 's_posts' ? 'display:none;' : '',
                'categories'   => $display_on === 's_categories' ? 'display:none;' : '',
                'tags'         => $display_on === 's_tags' ? 'display:none;' : '',
                'cposts'       => $display_on === 's_custom_posts' ? 'display:none;' : '',
                'latest_posts' => $display_on === 'latest_posts' ? 'display:none;' : '',
                'manual'       => $display_on === 'manual' ? 'display:none;' : ''
            ];
            $common_exclude_style = implode('', $hide_styles);
            ?>
            <tr id="ex_pages" style="<?php echo esc_attr($common_exclude_style); ?>">
                <th class="hfcm-th-width"><?php esc_html_e('Exclude Pages', 'header-footer-code-manager'); ?></th>
                <td>
                    <select name="data[ex_pages][]" multiple>
                        <?php
                        foreach ($pages as $pdata) {
                            $selected = in_array($pdata->ID, (array)$ex_pages) ? ' selected' : '';
                            printf('<option value="%s"%s>%s</option>', esc_attr($pdata->ID), $selected, esc_html($pdata->post_title));
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr id="ex_posts" style="<?php echo esc_attr($common_exclude_style); ?>">
                <th class="hfcm-th-width"><?php esc_html_e('Exclude Posts', 'header-footer-code-manager'); ?></th>
                <td>
                    <select class="nnr-wraptext" name="data[ex_posts][]" multiple>
                        <option disabled></option>
                    </select>
                    <img id="loader" src="<?php echo plugins_url('images/ajax-loader.gif', dirname(__FILE__)); ?>">
                </td>
            </tr>

            <?php
            $pages_style = $display_on === 's_pages' ? '' : 'display:none;';
            ?>
            <tr id="s_pages" style="<?php echo esc_attr($pages_style); ?>">
                <th class="hfcm-th-width"><?php esc_html_e('Page List', 'header-footer-code-manager'); ?></th>
                <td>
                    <select name="data[s_pages][]" multiple>
                        <?php
                        foreach ($pages as $pdata) {
                            $selected = in_array($pdata->ID, (array)$s_pages) ? ' selected' : '';
                            printf('<option value="%s"%s>%s</option>', esc_attr($pdata->ID), $selected, esc_attr($pdata->post_title));
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <?php $posts_style = $display_on === 's_posts' ? '' : 'display:none;'; ?>
            <tr id="s_posts" style="<?php echo esc_attr($posts_style); ?>">
                <th class="hfcm-th-width"><?php esc_html_e('Post List', 'header-footer-code-manager'); ?></th>
                <td>
                    <select class="nnr-wraptext" name="data[s_posts][]" multiple>
                        <option disabled>...</option>
                    </select>
                </td>
            </tr>

            <?php
            $categories = NNR_HFCM::hfcm_get_categories();
            $tags       = NNR_HFCM::hfcm_get_tags();
            $categories_style = $display_on === 's_categories' ? '' : 'display:none;';
            $tags_style       = $display_on === 's_tags' ? '' : 'display:none;';
            $cposts_style     = $display_on === 's_custom_posts' ? '' : 'display:none;';
            $lpcount_style    = $display_on === 'latest_posts' ? '' : 'display:none;';
            $location_style   = $display_on === 'manual' ? 'display:none;' : '';

            $args     = ['public' => true];
            $cpt      = get_post_types($args, 'names');
            ?>
            <tr id="s_categories" style="<?php echo esc_attr($categories_style); ?>">
                <th class="hfcm-th-width"><?php esc_html_e('Category List', 'header-footer-code-manager'); ?></th>
                <td>
                    <select name="data[s_categories][]" multiple>
                        <?php
                        foreach ($categories as $cat) {
                            foreach ($cat['terms'] as $term) {
                                $label = $cat['name'] . " - " . $term->name;
                                $selected = in_array($term->term_id, (array)$s_categories) ? ' selected' : '';
                                printf('<option value="%s"%s>%s</option>', esc_attr($term->term_id), $selected, esc_html($label));
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr id="s_tags" style="<?php echo esc_attr($tags_style); ?>">
                <th class="hfcm-th-width"><?php esc_html_e('Tags List', 'header-footer-code-manager'); ?></th>
                <td>
                    <select name="data[s_tags][]" multiple>
                        <?php
                        foreach ($tags as $tag) {
                            foreach ($tag['terms'] as $term) {
                                $label = $tag['name'] . " - " . $term->name;
                                $selected = in_array($term->term_id, (array)$s_tags) ? ' selected' : '';
                                printf('<option value="%s"%s>%s</option>', esc_attr($term->term_id), $selected, esc_html($label));
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr id="c_posttype" style="<?php echo esc_attr($cposts_style); ?>">
                <th class="hfcm-th-width"><?php esc_html_e('Post Types', 'header-footer-code-manager'); ?></th>
                <td>
                    <select name="data[s_custom_posts][]" multiple>
                        <?php
                        foreach ($cpt as $cpkey) {
                            $selected = in_array($cpkey, (array)$s_custom_posts) ? ' selected' : '';
                            printf('<option value="%1$s"%2$s>%1$s</option>', esc_attr($cpkey), $selected);
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr id="lp_count" style="<?php echo esc_attr($lpcount_style); ?>">
                <th class="hfcm-th-width"><?php esc_html_e('Post Count', 'header-footer-code-manager'); ?></th>
                <td>
                    <select name="data[lp_count]">
                        <?php
                        for ($i = 1; $i <= 20; $i++) {
                            printf('<option value="%1$d"%2$s>%1$d</option>', $i, ($i == $lp_count) ? ' selected' : '');
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <?php
            $locations = in_array($display_on, ['s_posts', 's_pages', 's_custom_posts', 's_tags', 'latest_posts'])
                ? [
                    'header'         => __('Header', 'header-footer-code-manager'),
                    'before_content' => __('Before Content', 'header-footer-code-manager'),
                    'after_content'  => __('After Content', 'header-footer-code-manager'),
                    'footer'         => __('Footer', 'header-footer-code-manager')
                ]
                : [
                    'header' => __('Header', 'header-footer-code-manager'),
                    'footer' => __('Footer', 'header-footer-code-manager')
                ];
            ?>
            <tr id="locationtr" style="<?php echo esc_attr($location_style); ?>">
                <th class="hfcm-th-width"><?php esc_html_e('Location', 'header-footer-code-manager'); ?></th>
                <td>
                    <select name="data[location]" id="data_location">
                        <?php hfcm_render_options($locations, $location); ?>
                    </select>
                    <p>
                        <b><?php _e("Note", 'header-footer-code-manager'); ?></b>: <?php _e("Not all locations (such as before content) exist on all page/post types. The location will only appear as an option if the appropriate hook exists on the page.", 'header-footer-code-manager'); ?>
                    </p>
                </td>
            </tr>

            <?php
            $device_type_array = [
                'both'    => __('Show on All Devices', 'header-footer-code-manager'),
                'desktop' => __('Only Desktop', 'header-footer-code-manager'),
                'mobile'  => __('Only Mobile Devices', 'header-footer-code-manager')
            ];
            $status_array = [
                'active'   => __('Active', 'header-footer-code-manager'),
                'inactive' => __('Inactive', 'header-footer-code-manager')
            ];
            ?>
            <tr>
                <th class="hfcm-th-width"><?php esc_html_e('Device Display', 'header-footer-code-manager'); ?></th>
                <td><select name="data[device_type]"><?php hfcm_render_options($device_type_array, $device_type); ?></select></td>
            </tr>
            <tr>
                <th class="hfcm-th-width"><?php esc_html_e('Status', 'header-footer-code-manager'); ?></th>
                <td><select name="data[status]"><?php hfcm_render_options($status_array, $status); ?></select></td>
            </tr>

            <?php if ($update): ?>
                <tr>
                    <th class="hfcm-th-width"><?php esc_html_e('Shortcode', 'header-footer-code-manager'); ?></th>
                    <td>
                        <p>
                            [hfcm id="<?php echo esc_html($id); ?>"]
                            <a data-shortcode='[hfcm id="<?php echo absint($id); ?>"]'
                               href="javascript:void(0);" class="nnr-btn-click-to-copy nnr-btn-copy-inline"
                               id="hfcm_copy_shortcode"><?php esc_html_e('Copy', 'header-footer-code-manager'); ?></a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th class="hfcm-th-width"><?php esc_html_e('Changelog', 'header-footer-code-manager'); ?></th>
                    <td>
                        <p>
                            <?php esc_html_e('Snippet created by', 'header-footer-code-manager'); ?>
                            <b><?php echo esc_html($createdby); ?></b>
                            <?php echo _e('on', 'header-footer-code-manager') . ' ' . date_i18n(get_option('date_format'), strtotime($createdon)) . ' ' . __('at', 'header-footer-code-manager') . ' ' . date_i18n(get_option('time_format'), strtotime($createdon)) ?>
                            <br />
                            <?php if (!empty($lastmodifiedby)): ?>
                                <?php esc_html_e('Last edited by', 'header-footer-code-manager'); ?>
                                <b><?php echo esc_html($lastmodifiedby); ?></b>
                                <?php echo _e('on', 'header-footer-code-manager') . ' ' . date_i18n(get_option('date_format'), strtotime($lastrevisiondate)) . ' ' . __('at', 'header-footer-code-manager') . ' ' . date_i18n(get_option('time_format'), strtotime($lastrevisiondate)) ?>
                            <?php endif; ?>
                        </p>
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <div class="nnr-mt-20">
            <h1><?php esc_html_e('Snippet', 'header-footer-code-manager'); ?> / <?php esc_html_e('Code', 'header-footer-code-manager'); ?></h1>
            <div class="nnr-mt-20 nnr-hfcm-codeeditor-box">
                <textarea name="data[snippet]" aria-describedby="nnr-newcontent-description" id="nnr_newcontent" rows="20"><?php echo html_entity_decode($snippet); ?></textarea>
                <p class="notice notice-warning nnr-padding10" id="nnr-snippet-warning">
                    <?php _e('Warning: Using improper code or untrusted sources code can break your site or create security risks. <a href="https://draftpress.com/security-risks-of-wp-plugins-that-allow-code-editing-or-insertion" target="_blank">Learn more</a>.', 'header-footer-code-manager'); ?>
                </p>
                <div class="wp-core-ui">
                    <input type="submit"
                           name="<?php echo $update ? 'update' : 'insert'; ?>"
                           value="<?php echo $update ? esc_html__('Update', 'header-footer-code-manager') : esc_html__('Save', 'header-footer-code-manager') ?>"
                           class="button button-primary button-large nnr-btnsave">
                    <?php if ($update): ?>
                        <?php $delete_nonce = wp_create_nonce('hfcm_delete_snippet'); ?>
                        <a onclick="return nnr_confirm_delete_snippet();"
                           href="<?php echo esc_url(admin_url('admin.php?page=hfcm-list&action=delete&_wpnonce=' . $delete_nonce . '&snippet=' . absint($id))); ?>"
                           class="button button-secondary button-large nnr-btndelete"><?php esc_html_e('Delete', 'header-footer-code-manager'); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
if (defined('DISALLOW_FILE_EDIT') && true === DISALLOW_FILE_EDIT && !get_user_meta(get_current_user_id(), 'hfcm_file_edit_plugin_notice_dismissed', true)): ?>
    <div id="file-editor-warning" class="notification-dialog-wrap file-editor-warning hide-if-no-js">
        <div class="notification-dialog-background"></div>
        <div class="notification-dialog">
            <div class="file-editor-warning-content">
                <div class="file-editor-warning-message">
                    <h1>Heads up!</h1>
                    <p>
                        <?php _e('Your site has <a href="https://draftpress.com/disallow-file-edit-setting-wordpress" target="_blank">disallow_file_edit</a> setting enabled inside the wp-config file to prevent file edits. By using this plugin, you acknowledge that you know what you’re doing and intend on adding code snippets only from trusted sources.', 'header-footer-code-manager'); ?>
                    </p>
                </div>
                <p>
                    <?php
                    $hfcm_file_edit_dismiss_action = $update
                        ? admin_url('admin.php?page=hfcm-update&hfcm-file-edit-notice-dismissed=1&id=' . absint($id))
                        : admin_url('admin.php?page=hfcm-create&hfcm-file-edit-notice-dismissed=1');
                    ?>
                    <a href="<?php echo $hfcm_file_edit_dismiss_action; ?>" class="file-editor-warning-dismiss button button-primary" id="nnr-dismiss-editor-warning">I understand</a>
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>
