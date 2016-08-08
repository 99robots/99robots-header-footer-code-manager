<?php

function hfcm_request_handler() {
	global $wpdb;
	global $current_user;

	$table_name = $wpdb->prefix . 'hfcm_scripts';
	// check user capabilities
	current_user_can('administrator');
	//insert
	if (isset($_POST['insert'])) {
		// check nonce
		check_admin_referer('create-snippet');

		if (!empty($_POST['data']['name'])) {
			$name = sanitize_text_field($_POST['data']['name']);
		} else {
			$name = '';
		}
		if (!empty($_POST['data']['snippet'])) {
			$snippet = stripslashes_deep($_POST['data']['snippet']);
		} else {
			$snippet = '';
		}
		if (!empty($_POST['data']['device_type'])) {
			$device_type = sanitize_text_field($_POST['data']['device_type']);
		} else {
			$device_type = '';
		}
		if (!empty($_POST['data']['display_on'])) {
			$display_on = sanitize_text_field($_POST['data']['display_on']);
		} else {
			$display_on = '';
		}
		if (!empty($_POST['data']['location']) && $display_on != 'manual') {
			$location = sanitize_text_field($_POST['data']['location']);
		} else {
			$location = '';
		}
		if (!empty($_POST['data']['status'])) {
			$status = sanitize_text_field($_POST['data']['status']);
		} else {
			$status = '';
		}
		if (!empty($_POST['data']['lp_count'])) {
			$lp_count = sanitize_text_field($_POST['data']['lp_count']);
		} else {
			$lp_count = '';
		}
		if (!empty($_POST['data']['s_pages'])) {
			$s_pages = hfcm_arr2int($_POST['data']['s_pages']);
		} else {
			$s_pages = '';
		}
		if (!empty($_POST['data']['s_posts'])) {
			$s_posts = hfcm_arr2int($_POST['data']['s_posts']);
		} else {
			$s_posts = '';
		}
		if (!is_array($s_pages)) {
			$s_pages = array();
		}
		array_map('absint', $s_pages);
		if (!is_array($s_posts)) {
			$s_posts = array();
		}
		array_map('absint', $s_posts);
		if (!empty($_POST['data']['s_custom_posts'])) {
			$s_custom_posts = hfcm_arr2int($_POST['data']['s_custom_posts']);
		} else {
			$s_custom_posts = '';
		}
		if (!is_array($s_custom_posts)) {
			$s_custom_posts = array();
		}
		array_map('absint', $s_custom_posts);
		if (!empty($_POST['data']['s_categories'])) {
			$s_categories = hfcm_arr2int($_POST['data']['s_categories']);
		} else {
			$s_categories = '';
		}
		if (!is_array($s_categories)) {
			$s_categories = array();
		}
		array_map('absint', $s_categories);
		if (!empty($_POST['data']['s_tags'])) {
			$s_tags = hfcm_arr2int($_POST['data']['s_tags']);
		} else {
			$s_tags = '';
		}
		if (!is_array($s_tags)) {
			$s_tags = array();
		}
		array_map('absint', $s_tags);

		$wpdb->insert(
				$table_name, //table
				array(
			'name' => $name,
			'snippet' => $snippet,
			'device_type' => $device_type,
			'location' => $location,
			'display_on' => $display_on,
			'status' => $status,
			'lp_count' => $lp_count,
			's_pages' => wp_json_encode($s_pages),
			's_posts' => wp_json_encode($s_posts),
			's_custom_posts' => wp_json_encode($s_custom_posts),
			's_categories' => wp_json_encode($s_categories),
			's_tags' => wp_json_encode($s_tags),
			'created' => current_time('Y-m-d H:i:s'),
			'created_by' => sanitize_text_field($current_user->display_name)
				), array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
		);
		$lastid = $wpdb->insert_id;
		hfcm_redirect(admin_url('admin.php?page=hfcm-update&created=1&id=' . $lastid));
	} else if (isset($_POST['update'])) {
		$id = $_GET['id'];
		// check nonce
		check_admin_referer('update-snippet_' . $id);
		if (!empty($_POST['data']['name'])) {
			$name = sanitize_text_field($_POST['data']['name']);
		} else {
			$name = '';
		}
		if (!empty($_POST['data']['snippet'])) {
			$snippet = stripslashes_deep($_POST['data']['snippet']);
		} else {
			$snippet = '';
		}
		if (!empty($_POST['data']['device_type'])) {
			$device_type = sanitize_text_field($_POST['data']['device_type']);
		} else {
			$device_type = '';
		}
		if (!empty($_POST['data']['display_on'])) {
			$display_on = sanitize_text_field($_POST['data']['display_on']);
		} else {
			$display_on = '';
		}
		if (!empty($_POST['data']['location']) && $display_on != 'manual') {
			$location = sanitize_text_field($_POST['data']['location']);
		} else {
			$location = '';
		}

		if (!empty($_POST['data']['lp_count'])) {
			$lp_count = max(1, (int) $_POST['data']['lp_count']);
		} else {
			$lp_count = '';
		}
		if (!empty($_POST['data']['status'])) {
			$status = sanitize_text_field($_POST['data']['status']);
		} else {
			$status = '';
		}
		if (!empty($_POST['data']['s_pages'])) {
			$s_pages = hfcm_arr2int($_POST['data']['s_pages']);
		} else {
			$s_pages = '';
		}
		if (!empty($_POST['data']['s_posts'])) {
			$s_posts = hfcm_arr2int($_POST['data']['s_posts']);
		} else {
			$s_posts = '';
		}
		if (!is_array($s_pages)) {
			$s_pages = array();
		}
		array_map('absint', $s_pages);
		if (!is_array($s_posts)) {
			$s_posts = array();
		}
		array_map('absint', $s_posts);
		if (!empty($_POST['data']['s_custom_posts'])) {
			$s_custom_posts = hfcm_arr2int($_POST['data']['s_custom_posts']);
		} else {
			$s_custom_posts = '';
		}
		if (!is_array($s_custom_posts)) {
			$s_custom_posts = array();
		}
		array_map('absint', $s_custom_posts);
		if (!empty($_POST['data']['s_categories'])) {
			$s_categories = hfcm_arr2int($_POST['data']['s_categories']);
		} else {
			$s_categories = '';
		}
		if (!is_array($s_categories)) {
			$s_categories = array();
		}
		array_map('absint', $s_categories);
		if (!empty($_POST['data']['s_tags'])) {
			$s_tags = hfcm_arr2int($_POST['data']['s_tags']);
		} else {
			$s_tags = '';
		}
		if (!is_array($s_tags)) {
			$s_tags = array();
		}
		array_map('absint', $s_tags);

		$wpdb->update(
				$table_name, //table
				array(
			'name' => $name,
			'snippet' => $snippet,
			'device_type' => $device_type,
			'location' => $location,
			'display_on' => $display_on,
			'status' => $status,
			'lp_count' => $lp_count,
			's_pages' => wp_json_encode($s_pages),
			's_posts' => wp_json_encode($s_posts),
			's_custom_posts' => wp_json_encode($s_custom_posts),
			's_categories' => wp_json_encode($s_categories),
			's_tags' => wp_json_encode($s_tags),
			'last_revision_date' => current_time('Y-m-d H:i:s'),
			'last_modified_by' => sanitize_text_field($current_user->display_name)
				), //data
				array('script_id' => $id), //where
				array('%s', '%s', '%s', '%s', '%s', '%s'), //data format
				array('%s') //where format
		);
		hfcm_redirect(admin_url('admin.php?page=hfcm-update&updated=1&id=' . $id));
	}
}