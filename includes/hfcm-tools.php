<?php

// Register the script
wp_register_script('hfcm_showboxes', plugins_url('js/nnr-hfcm-showboxes.js', dirname(__FILE__)), array('jquery'));


// Localize the script with new data
$translation_array = array(
    'header' => __('Header', '99robots-header-footer-code-manager'),
    'before_content' => __('Before Content', '99robots-header-footer-code-manager'),
    'after_content' => __('After Content', '99robots-header-footer-code-manager'),
    'footer' => __('Footer', '99robots-header-footer-code-manager'),
    'security' => wp_create_nonce('hfcm-get-posts'),
);
wp_localize_script('hfcm_showboxes', 'hfcm_localize', $translation_array);

// Enqueued script with localized data.
wp_enqueue_script('hfcm_showboxes');
?>

<div class="wrap">
    <h1>
        Tools
    </h1>
    <div class="hfcm-meta-box-wrap hfcm-grid">
        <div id="normal-sortables" class="meta-box-sortables">
            <div id="hfcm-admin-tool-export" class="postbox ">
                <div class="postbox-header">
                    <h2 class="hndle">Export Snippets</h2>
                </div>
                <div class="inside">
                    <form method="post">
                        <p>Select the snippets you would like to export and then select your export method. Use the
                            download button to export to a .json file which you can then import to another HFCM
                            installation.</p>
                        <div class="hfcm-notice notice-warning">
                            <p><?php _e('NOTE: Import/Export Functionality is only intended to operate within the same website.  Using the export/import to move snippets from one website to a different site, may result in inconsistent behavior, particularly if you have specific elements as criteria such as pages, posts, categories, or tags.', '99robots-header-footer-code-manager'); ?></p>
                        </div>
                        <div class="hfcm-fields">
                            <div class="hfcm-field hfcm-field-checkbox" data-name="keys" data-type="checkbox">
                                <div class="hfcm-label">
                                    <label for="keys">Select Snippets</label></div>
                                <div class="hfcm-input">
                                    <input type="hidden" name="keys">
                                    <ul class="hfcm-checkbox-list hfcm-bl">
                                        <?php if (!empty($nnr_hfcm_snippets)) {
                                            foreach ($nnr_hfcm_snippets as $nnr_key => $nnr_hfcm_snippet) {
                                                ?>
                                                <li>
                                                    <label>
                                                        <input type="checkbox" id="keys-snippet_<?php echo $nnr_hfcm_snippet->script_id; ?>"
                                                               name="nnr_hfcm_snippets[]" value="snippet_<?php echo $nnr_hfcm_snippet->script_id; ?>"> <?php echo $nnr_hfcm_snippet->name; ?>
                                                    </label>
                                                </li>
                                                <?php
                                            }
                                        } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <p class="hfcm-submit">
                            <button type="submit" name="action" class="button button-primary" value="download">
                                Export File
                            </button>
                        </p>
                        <?php wp_nonce_field('hfcm-nonce'); ?>
                    </form>
                </div>
            </div>
            <div id="hfcm-admin-tool-import" class="postbox ">
                <div class="postbox-header">
                    <h2 class="hndle">Import Snippets</h2>
                </div>
                <div class="inside">
                    <form method="post" enctype="multipart/form-data">
                        <p>Select the HFCM JSON file you would like to import. When you click the import button below,
                            HFCM will import the field groups.</p>
                        <div class="hfcm-fields">
                            <div class="hfcm-field hfcm-field-file" data-name="hfcm_import_file" data-type="file">
                                <div class="hfcm-label">
                                    <label for="hfcm_import_file">Select File</label></div>
                                <div class="hfcm-input">
                                    <div class="hfcm-file-uploader" data-library="all" data-mime_types=""
                                         data-uploader="basic">
                                        <div class="hide-if-value">
                                            <label class="hfcm-basic-uploader">
                                                <input type="file" name="nnr_hfcm_import_file" id="nnr_hfcm_import_file">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="hfcm-submit">
                            <input type="submit" class="button button-primary" value="import">
                        </p>
                        <?php wp_nonce_field('hfcm-nonce'); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
