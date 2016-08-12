<?php

// Register the script
wp_register_script( 'hfcm_showboxes', plugins_url('js/showboxes.js', dirname( __FILE__ ) ), array( 'jquery' ) );

// prepare ID (for AJAX)
if ( !isset($id) ) {
	$id = -1;
}

// Localize the script with new data
$translation_array = array(
	'header'         => __( 'Header', '99robots-header-footer-code-manager' ),
	'before_content' => __( 'Before Content', '99robots-header-footer-code-manager' ),
	'after_content'  => __( 'After Content', '99robots-header-footer-code-manager' ),
	'footer'         => __( 'Footer', '99robots-header-footer-code-manager' ),
	'id'             => $id,
);
wp_localize_script( 'hfcm_showboxes', 'hfcm_localize', $translation_array );

// Enqueued script with localized data.
wp_enqueue_script( 'hfcm_showboxes' );

?>

<div class="wrap">
	<h1><?php _e( ( $update ? 'Edit Snippet' : 'Add New Snippet' ), '99robots-header-footer-code-manager'); ?>
		<?php if( $update ) :?><a href="<?php echo admin_url('admin.php?page=hfcm-create'); ?>" class="page-title-action"><?php _e('Add New Snippet', '99robots-header-footer-code-manager'); ?></a><?php endif; ?>
	</h1>
	<?php 
		if ( !empty( $_GET['message'] ) ) :
			if ( $_GET['message'] == 1 ) : 
				?>
				<div class="updated">
					<p><?php _e('Script updated', '99robots-header-footer-code-manager'); ?></p>
				</div>
				<a href="<?php echo admin_url('admin.php?page=hfcm-list') ?>">&laquo; <?php _e('Back to list', '99robots-header-footer-code-manager'); ?></a>
				<?php 
			elseif ( $_GET['message'] == 6 ) : 
				?>
				<div class="updated">
					<p><?php _e('Script Added Successfully', '99robots-header-footer-code-manager'); ?></p>
				</div>
				<a href="<?php echo admin_url('admin.php?page=hfcm-list') ?>">&laquo; <?php _e('Back to list', '99robots-header-footer-code-manager'); ?></a>
				<?php
			endif;
		endif;
		
		if( $update ) :
			?>
			<form method="post" action="<?php echo admin_url('admin.php?page=hfcm-request-handler&id=' . $id); ?>">
			<?php 
			wp_nonce_field('update-snippet_' . $id); 
		else :
			?>
			<form method="post" action="<?php echo admin_url('admin.php?page=hfcm-request-handler'); ?>">
		<?php
			wp_nonce_field('create-snippet'); 
		endif; 
		?>
		<table class="wp-list-table widefat fixed hfcm-form-width form-table">
			<tr>
				<th class="hfcm-th-width"><?php _e('Snippet Name', '99robots-header-footer-code-manager'); ?></th>
				<td><input type="text" name="data[name]" value="<?php echo $name; ?>" class="hfcm-field-width" /></td>
			</tr>
			<?php $darray = array('All' => 'Site Wide', 's_posts' => 'Specific Posts', 's_pages' => 'Specific Pages', 's_categories' => 'Specific Categories', 's_custom_posts' => 'Specific Custom Post Types', 's_tags' => 'Specific Tags', 'latest_posts' => 'Latest Posts', 'manual' => 'Shortcode Only'); ?>
			<tr>
				<th class="hfcm-th-width"><?php _e('Site Display', '99robots-header-footer-code-manager'); ?></th>
				<td>
					<select name="data[display_on]" onchange="hfcm_showotherboxes(this.value);">
					<?php
						foreach ($darray as $dkey => $statusv) {
							if ($display_on === $dkey) {
								echo "<option value='$dkey' selected='selected'>" . __($statusv, '99robots-header-footer-code-manager') . '</option>';
							} else {
								echo "<option value='$dkey'>" . __($statusv, '99robots-header-footer-code-manager') . '</option>';
							}
						}
						?>
					</select>
				</td>
			</tr>
			<?php
				$pages = get_pages();
				if ('s_pages' === $display_on) {
					$spagesstyle = '';
				} else {
					$spagesstyle = 'display:none;';
				}
				?>
			<tr id="s_pages" style="<?php echo $spagesstyle; ?>">
				<th class="hfcm-th-width"><?php _e('Page List', '99robots-header-footer-code-manager'); ?></th>
				<td>
					<select name="data[s_pages][]" multiple>
					<?php
						foreach ($pages as $pkey => $pdata) {
							if (in_array($pdata->ID, $s_pages)) {
								echo "<option value='{$pdata->ID}' selected>{$pdata->post_title}</option>";
							} else {
								echo "<option value='{$pdata->ID}'>{$pdata->post_title}</option>";
							}
						}
						?>
					</select>
				</td>
			</tr>
			<?php
				if ('s_posts' === $display_on) {
					$spostsstyle = '';
				} else {
					$spostsstyle = 'display:none;';
				}
			?>
			<tr id="s_posts" style="<?php echo $spostsstyle; ?>">
				<th class="hfcm-th-width"><?php _e('Post List', '99robots-header-footer-code-manager'); ?></th>
				<td>
					<select class="nnr-wraptext" name="data[s_posts][]" multiple></select>
				</td>
			</tr>
			<?php
				$args = array(
					'hide_empty' => 0
				);
				$categories = get_categories($args);
				if ('s_categories' === $display_on) {
					$scategoriesstyle = '';
				} else {
					$scategoriesstyle = 'display:none;';
				}
				$tags = get_tags($args);
				if ('s_tags' === $display_on) {
					$stagsstyle = '';
				} else {
					$stagsstyle = 'display:none;';
				}
				if ('s_custom_posts' === $display_on) {
					$cpostssstyle = '';
				} else {
					$cpostssstyle = 'display:none;';
				}
				if ('latest_posts' === $display_on) {
					$lpcountstyle = '';
				} else {
					$lpcountstyle = 'display:none;';
				}
				if ('manual' === $display_on) {
					$locationstyle = 'display:none;';
				} else {
					$locationstyle = '';
				}
				?>
			<tr id="s_categories" style="<?php echo $scategoriesstyle; ?>">
				<th class="hfcm-th-width"><?php _e('Category List', '99robots-header-footer-code-manager'); ?></th>
				<td>
					<select name="data[s_categories][]" multiple>
					<?php
						foreach ($categories as $ckey => $cdata) {
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
				<th class="hfcm-th-width"><?php _e('Tags List', '99robots-header-footer-code-manager'); ?></th>
				<td>
					<select name="data[s_tags][]" multiple>
					<?php
						foreach ($tags as $tkey => $tdata) {
							if (in_array($tdata->slug, $s_tags)) {
								echo "<option value='{$tdata->slug}' selected>{$tdata->name}</option>";
							} else {
								echo "<option value='{$tdata->slug}'>{$tdata->name}</option>";
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
				<th class="hfcm-th-width"><?php _e('Post Count', '99robots-header-footer-code-manager'); ?></th>
				<td>
					<select name="data[lp_count]">
					<?php
						for ($i = 1; $i <= 20; $i++) {
							if ($i == $lp_count) {
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
				if ( in_array( $display_on, array( 's_posts', 's_pages', 's_categories', 's_custom_posts', 's_tags', 'latest_posts' ) ) ) {
					$larray = array('header' => 'Header', 'before_content' => 'Before Content', 'after_content' => 'After Content', 'footer' => 'Footer');
				} else {
					$larray = array('header' => 'Header', 'footer' => 'Footer');
				}
				?>
			<tr id="locationtr" style="<?php echo $locationstyle; ?>">
				<th class="hfcm-th-width"><?php _e('Location', '99robots-header-footer-code-manager'); ?></th>
				<td>
					<select name="data[location]" id="data_location">
					<?php
						foreach ($larray as $lkey => $statusv) {
							if ($location === $lkey) {
								echo "<option value='" . $lkey . "' selected='selected'>" . __($statusv, '99robots-header-footer-code-manager') . '</option>';
							} else {
								echo "<option value='" . $lkey . "'>" . __($statusv, '99robots-header-footer-code-manager') . '</option>';
							}
						}
						?>
					</select>
				</td>
			</tr>
			<?php $devicetypearray = array('both' => 'Show on All Devices', 'desktop' => 'Only Desktop', 'mobile' => 'Only Mobile Devices'); ?>
			<?php $statusarray = array('active' => 'Active', 'inactive' => 'Inactive'); ?>
			<tr>
				<th class="hfcm-th-width"><?php _e('Device Display', '99robots-header-footer-code-manager'); ?></th>
				<td>
					<select name="data[device_type]">
					<?php
						foreach ($devicetypearray as $smkey => $typev) {
							if ($device_type === $smkey) {
								echo "<option value='" . $smkey . "' selected='selected'>" . __($typev, '99robots-header-footer-code-manager') . '</option>';
							} else {
								echo "<option value='" . $smkey . "'>" . __($typev, '99robots-header-footer-code-manager') . '</option>';
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
							if ($status === $skey) {
								echo "<option value='" . $skey . "' selected='selected'>" . __($statusv, '99robots-header-footer-code-manager') . '</option>';
							} else {
								echo "<option value='" . $skey . "'>" . __($statusv, '99robots-header-footer-code-manager') . '</option>';
							}
						}
						?>
					</select>
				</td>
			</tr>
			<?php if ( $update ) : ?>
			<tr>
				<th class="hfcm-th-width"><?php _e('Shortcode', '99robots-header-footer-code-manager'); ?></th>
				<td>
					<p>[hfcm id="<?php echo $id; ?>"]</p>
				</td>
			</tr>
			<tr>
				<th class="hfcm-th-width"><?php _e('Changelog', '99robots-header-footer-code-manager'); ?></th>
				<td>
					<p>
						<?php _e('Snippet created by', '99robots-header-footer-code-manager'); ?> <b><?php echo $createdby; ?></b> <?php echo __('on', '99robots-header-footer-code-manager') . ' ' . date_i18n(get_option('date_format'), strtotime($createdon)) . ' ' . __('at', '99robots-header-footer-code-manager') . ' ' . date_i18n(get_option('time_format'), strtotime($createdon)); ?>
						<br/>
						<?php if (!empty($lastmodifiedby)) : ?>
						<?php _e('Last edited by', '99robots-header-footer-code-manager'); ?> <b><?php echo $lastmodifiedby; ?></b> <?php echo __('on', '99robots-header-footer-code-manager') . ' ' . date_i18n(get_option('date_format'), strtotime($lastrevisiondate)) . ' ' . __('at', '99robots-header-footer-code-manager') . ' ' . date_i18n(get_option('time_format'), strtotime($lastrevisiondate)); ?>
						<?php endif; ?>
					</p>
				</td>
			</tr>
			<?php endif; ?>
		</table>
		<div class="wrap">
			<h1><?php _e('Snippet', '99robots-header-footer-code-manager'); ?> / <?php _e('Code', '99robots-header-footer-code-manager'); ?></h1>
			<div class="wrap">
				<textarea name="data[snippet]" aria-describedby="newcontent-description" id="newcontent" name="newcontent" rows="10"><?php echo $snippet; ?></textarea>
				<div class="wp-core-ui">
					<input type="submit" 
						name="<?php echo $update ? 'update' : 'insert'; ?>" 
						value="<?php _e( ( $update ? 'Update' : 'Save' ), '99robots-header-footer-code-manager'); ?>"
						class="button button-primary button-large nnr-btnsave"
						/>
				</div>
			</div>
		</div>
	</form>
</div>