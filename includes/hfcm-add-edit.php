<?php

// Register the script
wp_register_script( 'hfcm_showboxes', plugins_url( 'js/nnr-hfcm-showboxes.js', dirname( __FILE__ ) ), array( 'jquery' ) );

// prepare ID (for AJAX)
if ( !isset( $id ) ) {
    $id = -1;
}

// Localize the script with new data
$translation_array = array(
    'header'         => __( 'Header', '99robots-header-footer-code-manager' ),
    'before_content' => __( 'Before Content', '99robots-header-footer-code-manager' ),
    'after_content'  => __( 'After Content', '99robots-header-footer-code-manager' ),
    'footer'         => __( 'Footer', '99robots-header-footer-code-manager' ),
    'id'             => $id,
    'security'       => wp_create_nonce( 'hfcm-get-posts' ),
);
wp_localize_script( 'hfcm_showboxes', 'hfcm_localize', $translation_array );

// Enqueued script with localized data.
wp_enqueue_script( 'hfcm_showboxes' );
?>

<div class="wrap">
    <h1>
        <?php echo $update ? esc_html__( 'Edit Snippet', '99robots-header-footer-code-manager' ) : esc_html__( 'Add New Snippet', '99robots-header-footer-code-manager' ) ?>
        <?php if ( $update ) : ?>
            <a href="<?php echo admin_url( 'admin.php?page=hfcm-create' ) ?>" class="page-title-action">
                <?php esc_html_e( 'Add New Snippet', '99robots-header-footer-code-manager' ) ?>
            </a>
        <?php endif; ?>
    </h1>
    <?php
    if ( !empty( $_GET['message'] ) ) :
        if ( 1 === $_GET['message'] ) :
            ?>
            <div class="updated">
                <p><?php esc_html_e( 'Script updated', '99robots-header-footer-code-manager' ); ?></p>
            </div>
            <a href="<?php echo admin_url( 'admin.php?page=hfcm-list' ) ?>">&laquo; <?php esc_html_e( 'Back to list', '99robots-header-footer-code-manager' ); ?></a>
        <?php elseif ( 6 === $_GET['message'] ) : ?>
            <div class="updated">
                <p><?php esc_html_e( 'Script Added Successfully', '99robots-header-footer-code-manager' ); ?></p>
            </div>
            <a href="<?php echo admin_url( 'admin.php?page=hfcm-list' ) ?>">&laquo; <?php esc_html_e( 'Back to list', '99robots-header-footer-code-manager' ); ?></a>
        <?php
        endif;
    endif;

    if ( $update ) :
        $hfcm_form_action = admin_url( 'admin.php?page=hfcm-request-handler&id=' . $id );
    else :
        $hfcm_form_action = admin_url( 'admin.php?page=hfcm-request-handler' );
    endif;
    ?>
    <form method="post" action="<?php echo $hfcm_form_action ?>">
        <?php
        if ( $update ) :
            wp_nonce_field( 'update-snippet_' . $id );
        else :
            wp_nonce_field( 'create-snippet' );
        endif;
        ?>
        <table class="wp-list-table widefat fixed hfcm-form-width form-table">
            <tr>
                <th class="hfcm-th-width"><?php esc_html_e( 'Snippet Name', '99robots-header-footer-code-manager' ); ?></th>
                <td><input type="text" name="data[name]" value="<?php echo esc_attr( $name ); ?>"
                           class="hfcm-field-width"/>
                </td>
            </tr>
            <?php
            $nnr_hfcm_snippet_type_array = array(
                'html' => esc_html__( 'HTML', '99robots-header-footer-code-manager' ),
                'css'  => esc_html__( 'CSS', '99robots-header-footer-code-manager' ),
                'js'   => esc_html__( 'Javascript', '99robots-header-footer-code-manager' )
            ); ?>
            <tr id="snippet_type">
                <th class="hfcm-th-width"><?php esc_html_e( 'Snippet Type', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select name="data[snippet_type]">
                        <?php
                        foreach ( $nnr_hfcm_snippet_type_array as $nnr_key => $nnr_item ) {
                            if ( $nnr_key === $nnr_snippet_type ) {
                                echo "<option value='" . esc_attr( $nnr_key ) . "' selected>" . esc_attr( $nnr_item ) . "</option>";
                            } else {
                                echo "<option value='" . esc_attr( $nnr_key ) . "'>" . esc_attr( $nnr_item ) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php
            $nnr_hfcm_display_array = array(
                'All'            => esc_html__( 'Site Wide', '99robots-header-footer-code-manager' ),
                's_posts'        => esc_html__( 'Specific Posts', '99robots-header-footer-code-manager' ),
                's_pages'        => esc_html__( 'Specific Pages', '99robots-header-footer-code-manager' ),
                's_categories'   => esc_html__( 'Specific Categories (Archive & Posts)', '99robots-header-footer-code-manager' ),
                's_custom_posts' => esc_html__( 'Specific Post Types (Archive & Posts)', '99robots-header-footer-code-manager' ),
                's_tags'         => esc_html__( 'Specific Tags (Archive & Posts)', '99robots-header-footer-code-manager' ),
                'latest_posts'   => esc_html__( 'Latest Posts', '99robots-header-footer-code-manager' ),
                'manual'         => esc_html__( 'Shortcode Only', '99robots-header-footer-code-manager' ),
            ); ?>
            <tr>
                <th class="hfcm-th-width"><?php esc_html_e( 'Site Display', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select name="data[display_on]" onchange="hfcm_showotherboxes(this.value);">
                        <?php
                        foreach ( $nnr_hfcm_display_array as $dkey => $statusv ) {
                            if ( $display_on === $dkey ) {
                                printf( '<option value="%1$s" selected="selected">%2$s</option>', $dkey, $statusv );
                            } else {
                                printf( '<option value="%1$s">%2$s</option>', $dkey, $statusv );
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php
            $nnr_hfcm_pages                      = get_pages();
            $nnr_hfcm_exclude_pages_style        = ('s_pages' === $display_on) ? 'display:none;' : '';
            $nnr_hfcm_exclude_posts_style        = ('s_posts' === $display_on) ? 'display:none;' : '';
            $nnr_hfcm_exclude_categories_style   = 's_categories' === $display_on ? 'display:none;' : '';
            $nnr_hfcm_exclude_tags_style         = 's_tags' === $display_on ? 'display:none;' : '';
            $nnr_hfcm_exclude_custom_posts_style = 's_custom_posts' === $display_on ? 'display:none;' : '';
            $nnr_hfcm_exclude_lp_count_style     = 'latest_posts' === $display_on ? 'display:none;' : '';
            $nnr_hfcm_exclude_manual_style       = 'manual' === $display_on ? 'display:none;' : '';
            ?>
            <tr id="ex_pages"
                style="<?php echo $nnr_hfcm_exclude_pages_style . $nnr_hfcm_exclude_posts_style . $nnr_hfcm_exclude_tags_style . $nnr_hfcm_exclude_custom_posts_style . $nnr_hfcm_exclude_categories_style . $nnr_hfcm_exclude_lp_count_style . $nnr_hfcm_exclude_manual_style; ?>">
                <th class="hfcm-th-width"><?php esc_html_e( 'Exclude Pages', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select name="data[ex_pages][]" multiple>
                        <?php
                        foreach ( $nnr_hfcm_pages as $pdata ) {
                            if ( in_array( $pdata->ID, $ex_pages ) ) {
                                printf( '<option value="%1$s" selected="selected">%2$s</option>', $pdata->ID, $pdata->post_title );
                            } else {
                                printf( '<option value="%1$s">%2$s</option>', $pdata->ID, $pdata->post_title );
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr id="ex_posts"
                style="<?php echo $nnr_hfcm_exclude_pages_style . $nnr_hfcm_exclude_posts_style . $nnr_hfcm_exclude_tags_style . $nnr_hfcm_exclude_custom_posts_style . $nnr_hfcm_exclude_categories_style . $nnr_hfcm_exclude_lp_count_style . $nnr_hfcm_exclude_manual_style; ?>">
                <th class="hfcm-th-width"><?php esc_html_e( 'Exclude Posts', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select class="nnr-wraptext" name="data[ex_posts][]" multiple>
                        <option disabled></option>
                    </select> <img id="loader"
                                   src="<?php echo plugins_url( 'images/ajax-loader.gif', dirname( __FILE__ ) ); ?>">
                </td>
            </tr>
            <?php
            $nnr_hfcm_pages       = get_pages();
            $nnr_hfcm_pages_style = ('s_pages' === $display_on) ? '' : 'display:none;';
            ?>
            <tr id="s_pages" style="<?php echo esc_attr( $nnr_hfcm_pages_style ); ?>">
                <th class="hfcm-th-width"><?php esc_html_e( 'Page List', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select name="data[s_pages][]" multiple>
                        <?php
                        foreach ( $nnr_hfcm_pages as $pdata ) {
                            if ( in_array( $pdata->ID, $s_pages ) ) {
                                printf( '<option value="%1$s" selected="selected">%2$s</option>', esc_attr( $pdata->ID ), esc_attr( $pdata->post_title ) );
                            } else {
                                printf( '<option value="%1$s">%2$s</option>', esc_attr( $pdata->ID ), esc_attr( $pdata->post_title ) );
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php $nnr_hfcm_posts_style = 's_posts' === $display_on ? '' : 'display:none;'; ?>
            <tr id="s_posts" style="<?php echo esc_attr( $nnr_hfcm_posts_style ); ?>">
                <th class="hfcm-th-width"><?php esc_html_e( 'Post List', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select class="nnr-wraptext" name="data[s_posts][]" multiple>
                        <option disabled>...</option>
                    </select>
                </td>
            </tr>
            <?php
            $args                = array( 'hide_empty' => 0 );
            $nnr_hfcm_categories = get_categories( $args );
            $nnr_hfcm_tags       = get_tags( $args );

            $nnr_hfcm_categories_style   = 's_categories' === $display_on ? '' : 'display:none;';
            $nnr_hfcm_tags_style         = 's_tags' === $display_on ? '' : 'display:none;';
            $nnr_hfcm_custom_posts_style = 's_custom_posts' === $display_on ? '' : 'display:none;';
            $nnr_hfcm_lpcount_style      = 'latest_posts' === $display_on ? '' : 'display:none;';
            $nnr_hfcm_location_style     = 'manual' === $display_on ? 'display:none;' : '';

            // Get all names of Post Types
            $args = array(
                'public' => true,
            );

            $output   = 'names';
            $operator = 'and';

            $nnr_hfcm_custom_post_types = get_post_types( $args, $output, $operator );
            $nnr_hfcm_post_types        = array( 'post' );
            foreach ( $nnr_hfcm_custom_post_types as $cpdata ) {
                $nnr_hfcm_post_types[] = $cpdata;
            }
            ?>
            <tr id="s_categories" style="<?php echo esc_attr( $nnr_hfcm_categories_style ); ?>">
                <th class="hfcm-th-width"><?php esc_html_e( 'Category List', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select name="data[s_categories][]" multiple>
                        <?php
                        foreach ( $nnr_hfcm_categories as $cdata ) {
                            if ( in_array( $cdata->term_id, $s_categories ) ) {
                                echo "<option value='" . esc_attr( $cdata->term_id ) . "' selected>" . esc_attr( $cdata->name ) . "</option>";
                            } else {
                                echo "<option value='" . esc_attr( $cdata->term_id ) . "'>" . esc_attr( $cdata->name ) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr id="s_tags" style="<?php echo esc_attr( $nnr_hfcm_tags_style ); ?>">
                <th class="hfcm-th-width"><?php esc_html_e( 'Tags List', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select name="data[s_tags][]" multiple>
                        <?php
                        foreach ( $nnr_hfcm_tags as $tdata ) {
                            if ( in_array( $tdata->term_id, $s_tags ) ) {
                                echo "<option value='" . esc_attr( $tdata->term_id ) . "' selected>" . esc_attr( $tdata->name ) . "</option>";
                            } else {
                                echo "<option value='" . esc_attr( $tdata->term_id ) . "'>" . esc_attr( $tdata->name ) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr id="c_posttype" style="<?php echo esc_attr( $nnr_hfcm_custom_posts_style ); ?>">
                <th class="hfcm-th-width"><?php esc_html_e( 'Post Types', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select name="data[s_custom_posts][]" multiple>
                        <?php
                        foreach ( $nnr_hfcm_custom_post_types as $cpkey => $cpdata ) {
                            if ( in_array( $cpkey, $s_custom_posts ) ) {
                                echo "<option value='" . esc_attr( $cpkey ) . "' selected>" . esc_attr( $cpdata ) . "</option>";
                            } else {
                                echo "<option value='" . esc_attr( $cpkey ) . "'>" . esc_attr( $cpdata ) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr id="lp_count" style="<?php echo $nnr_hfcm_lpcount_style; ?>">
                <th class="hfcm-th-width"><?php esc_html_e( 'Post Count', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select name="data[lp_count]">
                        <?php
                        for ( $i = 1; $i <= 20; $i++ ) {
                            if ( $i == $lp_count ) {
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
            if ( in_array( $display_on, array( 's_posts', 's_pages', 's_custom_posts', 's_tags', 'latest_posts' ) ) ) {
                $nnr_hfcm_locations = array( 'header'        => 'Header', 'before_content' => 'Before Content',
                                             'after_content' => 'After Content', 'footer' => 'Footer' );
            } else {
                $nnr_hfcm_locations = array( 'header' => 'Header', 'footer' => 'Footer' );
            }
            ?>
            <tr id="locationtr" style="<?php echo esc_attr( $nnr_hfcm_location_style ); ?>">
                <th class="hfcm-th-width"><?php esc_html_e( 'Location', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select name="data[location]" id="data_location">
                        <?php
                        foreach ( $nnr_hfcm_locations as $lkey => $statusv ) {
                            if ( $location === $lkey ) {
                                echo "<option value='" . esc_attr( $lkey ) . "' selected='selected'>" . esc_attr( $statusv ) . '</option>';
                            } else {
                                echo "<option value='" . esc_attr( $lkey ) . "'>" . esc_attr( $statusv ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <p><b><?php _e("Note", '99robots-header-footer-code-manager'); ?></b>: <?php _e("Snippet will only execute if the placement hook exists on the page", '99robots-header-footer-code-manager'); ?>.</p>
                </td>
            </tr>
            <?php $nnr_hfcm_device_type_array = array(
                    'both'   => __('Show on All Devices', '99robots-header-footer-code-manager'),
                    'desktop' => __('Only Desktop', '99robots-header-footer-code-manager'),
                    'mobile' => __('Only Mobile Devices', '99robots-header-footer-code-manager')
            ) ?>
            <?php $nnr_hfcm_status_array = array(
                    'active' => __('Active', '99robots-header-footer-code-manager'),
                    'inactive' => __('Inactive', '99robots-header-footer-code-manager')
            ) ?>
            <tr>
                <th class="hfcm-th-width"><?php esc_html_e( 'Device Display', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select name="data[device_type]">
                        <?php
                        foreach ( $nnr_hfcm_device_type_array as $smkey => $typev ) {
                            if ( $device_type === $smkey ) {
                                echo "<option value='" . esc_attr( $smkey ) . "' selected='selected'>" . esc_attr( $typev ) . '</option>';
                            } else {
                                echo "<option value='" . esc_attr( $smkey ) . "'>" . esc_attr( $typev ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th class="hfcm-th-width"><?php esc_html_e( 'Status', '99robots-header-footer-code-manager' ); ?></th>
                <td>
                    <select name="data[status]">
                        <?php
                        foreach ( $nnr_hfcm_status_array as $skey => $statusv ) {
                            if ( $status === $skey ) {
                                echo "<option value='" . esc_attr( $skey ) . "' selected='selected'>" . esc_attr( $statusv ) . '</option>';
                            } else {
                                echo "<option value='" . esc_attr( $skey ) . "'>" . esc_attr( $statusv ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php if ( $update ) : ?>
                <tr>
                    <th class="hfcm-th-width"><?php esc_html_e( 'Shortcode', '99robots-header-footer-code-manager' ); ?></th>
                    <td>
                        <p>[hfcm id="<?php echo esc_html( $id ); ?>"]</p>
                    </td>
                </tr>
                <tr>
                    <th class="hfcm-th-width"><?php esc_html_e( 'Changelog', '99robots-header-footer-code-manager' ); ?></th>
                    <td>
                        <p>
                            <?php esc_html_e( 'Snippet created by', '99robots-header-footer-code-manager' ); ?>
                            <b><?php echo esc_html( $createdby ); ?></b> <?php _e( 'on', '99robots-header-footer-code-manager' ) . ' ' . date_i18n( get_option( 'date_format' ), strtotime( $createdon ) ) . ' ' . __( 'at', '99robots-header-footer-code-manager' ) . ' ' . date_i18n( get_option( 'time_format' ), strtotime( $createdon ) ) ?>
                            <br/>
                            <?php if ( !empty( $lastmodifiedby ) ) : ?>
                                <?php esc_html_e( 'Last edited by', '99robots-header-footer-code-manager' ); ?>
                                <b><?php echo esc_html( $lastmodifiedby ); ?></b> <?php _e( 'on', '99robots-header-footer-code-manager' ) . ' ' . date_i18n( get_option( 'date_format' ), strtotime( $lastrevisiondate ) ) . ' ' . __( 'at', '99robots-header-footer-code-manager' ) . ' ' . date_i18n( get_option( 'time_format' ), strtotime( $lastrevisiondate ) ) ?>
                            <?php endif; ?>
                        </p>
                    </td>
                </tr>
            <?php endif; ?>
        </table>
        <div class="wrap">
            <h1><?php esc_html_e( 'Snippet', '99robots-header-footer-code-manager' ); ?>
                / <?php esc_html_e( 'Code', '99robots-header-footer-code-manager' ) ?></h1>
            <div class="wrap">
                    <textarea name="data[snippet]" aria-describedby="nnr-newcontent-description" id="nnr_newcontent"
                              rows="10"><?php echo html_entity_decode( $snippet ); ?></textarea>
                <div class="wp-core-ui">
                    <input type="submit"
                           name="<?php echo $update ? 'update' : 'insert'; ?>"
                           value="<?php echo $update ? esc_html__( 'Update', '99robots-header-footer-code-manager' ) : esc_html__( 'Save', '99robots-header-footer-code-manager' ) ?>"
                           class="button button-primary button-large nnr-btnsave">
                </div>
            </div>
        </div>
    </form>
</div>
