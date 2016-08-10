<?php

// function for submenu "Add snippet" page
function hfcm_create() {

	// check user capabilities
	current_user_can('administrator');


	global $wpdb;

	$table_name = $wpdb->prefix . 'hfcm_scripts';


	//Get Last inserted ID
	$lastinsertedid = $wpdb->get_results("SELECT script_id from $table_name ORDER BY script_id DESC LIMIT 0,1");
	if (empty($lastinsertedid)) {
		$shortcode = '[hfcm id="1"]';
	} else {
		$shortcode = '[hfcm id="' . ($lastinsertedid[0]->script_id + 1) . '"]';
	}
	$display_on = '';

	// Register the script
	wp_register_script('hfcm_showboxes', plugins_url('../js/showboxes.js', __FILE__));

	// Localize the script with new data
	$translation_array = array(
		'header' => __('Header', '99robots-header-footer-code-manager'),
		'before_content' => __('Before Content', '99robots-header-footer-code-manager'),
		'after_content' => __('After Content', '99robots-header-footer-code-manager'),
		'footer' => __('Footer', '99robots-header-footer-code-manager')
	);
	wp_localize_script('hfcm_showboxes', 'hfcm_localize', $translation_array);

	// Enqueued script with localized data.
	wp_enqueue_script('hfcm_showboxes');
	?>
	<div class="wrap">
		<h1><?php _e('Add New Snippet', '99robots-header-footer-code-manager'); ?></h1>

		<form method="post" action="<?php echo admin_url('admin.php?page=hfcm-request-handler'); ?>">
			<?php wp_nonce_field('create-snippet'); ?>

			<table class='wp-list-table widefat fixed hfcm-form-width form-table'>
				<tr>
					<th class="hfcm-th-width"><?php _e('Snippet Name', '99robots-header-footer-code-manager'); ?></th>
					<td><input type="text" name="data[name]" class="hfcm-field-width" /></td>
				</tr>
				<?php $darray = array('All' => 'Site Wide', 's_posts' => 'Specific Posts', 's_pages' => 'Specific Pages', 's_categories' => 'Specific Categories', 's_custom_posts' => 'Specific Custom Post Types', 's_tags' => 'Specific Tags', 'latest_posts' => 'Latest Posts', 'manual' => 'Shortcode Only'); ?>
				<tr>
					<th class="hfcm-th-width"><?php _e('Site Display', '99robots-header-footer-code-manager'); ?></th>
					<td>
						<select name="data[display_on]" onchange="hfcm_showotherboxes(this.value);">
							<?php
							foreach ($darray as $dkey => $statusv) {
								echo "<option value='" . $dkey . "'>" . __($statusv, '99robots-header-footer-code-manager') . "</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<?php
				$pages = get_pages();
				if ('s_pages' === $display_on) {
					$spagesstyle = 'display:block;';
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
								echo "<option value='" . $pdata->ID . "'>" . $pdata->post_title . '</option>';
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
				$posttypes = array('post');
				foreach ($c_posttypes as $cpkey => $cpdata) {
					$posttypes[] = $cpdata;
				}
				$posts = get_posts(array('post_type' => $posttypes, 'posts_per_page' => -1, 'numberposts' => -1, "orderby" => "title", "order" => "ASC"));
				if ('s_posts' === $display_on) {
					$spostsstyle = 'display:block;';
				} else {
					$spostsstyle = 'display:none;';
				}
				?>
				<tr id="s_posts" style="<?php echo $spostsstyle; ?>">
					<th class="hfcm-th-width"><?php _e('Post List', '99robots-header-footer-code-manager'); ?></th>
					<td>
						<select class="nnr-wraptext" name="data[s_posts][]" multiple>
							<?php
							foreach ($posts as $pkey => $pdata) {
								echo "<option value='" . $pdata->ID . "'>" . $pdata->post_title . '</option>';
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
				if ('s_categories' === $display_on) {
					$scategoriesstyle = 'display:block;';
				} else {
					$scategoriesstyle = 'display:none;';
				}
				$tags = get_tags($args);
				if ('s_tags' === $display_on) {
					$stagsstyle = 'display:block;';
				} else {
					$stagsstyle = 'display:none;';
				}
				if ('s_custom_posts' === $display_on) {
					$cpostssstyle = 'display:block;';
				} else {
					$cpostssstyle = 'display:none;';
				}
				?>
				<tr id="s_categories" style="<?php echo $scategoriesstyle; ?>">
					<th class="hfcm-th-width"><?php _e('Category List', '99robots-header-footer-code-manager'); ?></th>
					<td>
						<select name="data[s_categories][]" multiple>
							<?php
							foreach ($categories as $ckey => $cdata) {
								echo "<option value='" . $cdata->term_id . "'>" . $cdata->name . '</option>';
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
								echo "<option value='" . $tdata->slug . "'>" . $tdata->name . '</option>';
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
								echo "<option value='" . $cpkey . "'>" . $cpdata . '</option>';
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
								echo "<option value='" . $i . "'>" . $i . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<?php
				if (in_array($display_on, array('s_posts', 's_pages', 's_custom_posts'))) {
					$larray = array('header' => 'Header', 'before_content' => 'Before Content', 'after_content' => 'After Content', 'footer' => 'Footer');
				} else {
					$larray = array('header' => 'Header', 'footer' => 'Footer');
				}
				?>
				<tr id="locationtr">
					<th class="hfcm-th-width"><?php _e('Location', '99robots-header-footer-code-manager'); ?></th>
					<td>
						<select name="data[location]" id="data_location">
							<?php
							foreach ($larray as $lkey => $statusv) {
								echo "<option value='" . $lkey . "'>" . __($statusv, '99robots-header-footer-code-manager') . '</option>';
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
								echo "<option value='" . $smkey . "'>" . __($typev, '99robots-header-footer-code-manager') . '</option>';
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
								echo "<option value='" . $skey . "'>" . __($statusv, '99robots-header-footer-code-manager') . '</option>';
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
					<textarea name="data[snippet]" aria-describedby="newcontent-description" id="newcontent" name="newcontent" rows="10"></textarea>
					<div class="wp-core-ui">
						<input type='submit' name="insert" value='<?php _e('Save', '99robots-header-footer-code-manager'); ?>' class='button button-primary button-large nnr-btnsave' />
					</div>
				</div>
			</div>
		</form>
	</div>
	<?php
}