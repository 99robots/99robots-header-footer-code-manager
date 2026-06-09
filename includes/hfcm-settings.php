<?php
?>

<div class="wrap">
    <h1><?php esc_html_e('Settings', 'header-footer-code-manager'); ?></h1>

    <?php if (!empty($_GET['settings-updated'])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e('Security settings saved.', 'header-footer-code-manager'); ?></p>
        </div>
    <?php endif; ?>

    <div class="hfcm-meta-box-wrap hfcm-grid">
        <div id="normal-sortables" class="meta-box-sortables">
            <div id="hfcm-admin-setting-security" class="postbox ">
                <div class="postbox-header">
                    <h2 class="hndle"><?php esc_html_e('Security Settings', 'header-footer-code-manager'); ?></h2>
                </div>
                <div class="inside">
                    <form method="post">
                        <p>
                            <?php esc_html_e('Choose whether this plugin should enforce the DISALLOW_UNFILTERED_HTML constant from wp-config.php.', 'header-footer-code-manager'); ?>
                        </p>
                        <p>
                            <label for="hfcm_enforce_disallow_unfiltered_html">
                                <input type="checkbox"
                                       id="hfcm_enforce_disallow_unfiltered_html"
                                       name="hfcm_enforce_disallow_unfiltered_html"
                                       value="1" <?php checked(!empty($hfcm_enforce_disallow_unfiltered_html)); ?> />
                                <?php esc_html_e('Enforce DISALLOW_UNFILTERED_HTML and block snippet changes when it is enabled.', 'header-footer-code-manager'); ?>
                            </label>
                        </p>
                        <p class="description">
                            <?php esc_html_e('If unchecked, this plugin will ignore DISALLOW_UNFILTERED_HTML and allow snippet management for users with the required capability.', 'header-footer-code-manager'); ?>
                        </p>
                        <p class="hfcm-submit">
                            <button type="submit" name="hfcm_save_security_settings" class="button button-primary" value="1">
                                <?php esc_html_e('Save Settings', 'header-footer-code-manager'); ?>
                            </button>
                        </p>
                        <?php wp_nonce_field('hfcm-security-settings'); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

