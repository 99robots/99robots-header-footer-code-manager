<?php

function hfcm_request_handler() {

	// check user capabilities
	current_user_can( 'administrator' );

	if ( !isset( $_POST['insert'] ) ) {
		if ( !isset( $_REQUEST['id'] ) ) {
			die('Missing ID parameter.');
		}
		$id = (int) $_REQUEST['id'];
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'hfcm_scripts';

	// handle AJAX on/off toggle for snippets
	if ( isset( $_REQUEST['toggle'] ) && !empty( $_REQUEST['togvalue'] ) ) {

		// check nonce
		check_ajax_referer( 'toggle-snippet', 'security' );

		if ( 'on' === $_REQUEST['togvalue'] ) {
			$status = 'active';
		} else {
			$status = 'inactive';
		}
		$wpdb->update(
				$table_name, //table
				array( 'status' => $status ), //data
				array( 'script_id' => $id ), //where
				array( '%s', '%s', '%s', '%s', '%s', '%s' ), //data format
				array( '%s' ) //where format
		);

	// create new snippet
	} elseif ( isset( $_POST['insert'] ) ) {

		// check nonce
		check_admin_referer( 'create-snippet' );

		if ( !empty( $_POST['data']['name'] ) ) {
			$name = sanitize_text_field( $_POST['data']['name'] );
		} else {
			$name = '';
		}
		if ( !empty( $_POST['data']['snippet'] ) ) {
			$snippet = stripslashes_deep( $_POST['data']['snippet'] );
		} else {
			$snippet = '';
		}
		if ( !empty( $_POST['data']['device_type'] ) ) {
			$device_type = sanitize_text_field( $_POST['data']['device_type'] );
		} else {
			$device_type = '';
		}
		if ( !empty( $_POST['data']['display_on'] ) ) {
			$display_on = sanitize_text_field( $_POST['data']['display_on'] );
		} else {
		$display_on = '';
		}
		if ( !empty( $_POST['data']['location'] ) && $display_on != 'manual' ) {
			$location = sanitize_text_field( $_POST['data']['location'] );
		} else {
			$location = '';
		}
		if ( !empty( $_POST['data']['status'] ) ) {
			$status = sanitize_text_field( $_POST['data']['status'] );
		} else {
			$status = '';
		}
		if ( !empty( $_POST['data']['lp_count'] ) ) {
			$lp_count = sanitize_text_field( $_POST['data']['lp_count'] );
		} else {
			$lp_count = '';
		}
		if ( !empty( $_POST['data']['s_pages'] ) ) {
			$s_pages = $_POST['data']['s_pages'];
		} else {
			$s_pages = '';
		}
		if ( !empty( $_POST['data']['s_posts'] ) ) {
			$s_posts = $_POST['data']['s_posts'];
		} else {
			$s_posts = '';
		}
		if ( !is_array( $s_pages ) ) {
			$s_pages = array();
		}
		array_map( 'absint', $s_pages );
		if ( !is_array( $s_posts ) ) {
			$s_posts = array();
		}
		array_map( 'absint', $s_posts );
		if ( !empty( $_POST['data']['s_custom_posts'] ) ) {
			$s_custom_posts = $_POST['data']['s_custom_posts'];
		} else {
			$s_custom_posts = '';
		}
		if ( !is_array( $s_custom_posts ) ) {
			$s_custom_posts = array();
		}
		array_map( 'absint', $s_custom_posts );
		if ( !empty( $_POST['data']['s_categories'] ) ) {
			$s_categories = $_POST['data']['s_categories'];
		} else {
			$s_categories = '';
		}
		if ( !is_array( $s_categories ) ) {
			$s_categories = array();
		}
		array_map( 'absint', $s_categories );
		if ( !empty( $_POST['data']['s_tags'] ) ) {
			$s_tags = $_POST['data']['s_tags'];
		} else {
			$s_tags = '';
		}
		if ( !is_array( $s_tags ) ) {
			$s_tags = array();
		}
		array_map( 'absint', $s_tags );
		
		global $current_user;

		$wpdb->insert( $table_name, //table
			array(
				'name' => $name,
				'snippet' => $snippet,
				'device_type' => $device_type,
				'location' => $location,
				'display_on' => $display_on,
				'status' => $status,
				'lp_count' => $lp_count,
				's_pages' => wp_json_encode( $s_pages ),
				's_posts' => wp_json_encode( $s_posts ),
				's_custom_posts' => wp_json_encode( $s_custom_posts ),
				's_categories' => wp_json_encode( $s_categories ),
				's_tags' => wp_json_encode( $s_tags ),
				'created' => current_time( 'Y-m-d H:i:s' ),
				'created_by' => sanitize_text_field( $current_user->display_name ) 
			), array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s' 
			)
		);
		$lastid = $wpdb->insert_id;
		hfcm_redirect( admin_url( 'admin.php?page=hfcm-update&message=6&id=' . $lastid ) );
		
	// update snippet
	} elseif ( isset( $_POST['update'] ) ) {

		// check nonce
		check_admin_referer( 'update-snippet_' . $id );

		if ( !empty( $_POST['data']['name'] ) ) {
			$name = sanitize_text_field( $_POST['data']['name'] );
		} else {
			$name = '';
		}
		if ( !empty( $_POST['data']['snippet'] ) ) {
			$snippet = stripslashes_deep( $_POST['data']['snippet'] );
		} else {
			$snippet = '';
		}
		if ( !empty( $_POST['data']['device_type'] ) ) {
			$device_type = sanitize_text_field( $_POST['data']['device_type'] );
		} else {
			$device_type = '';
		}
		if ( !empty( $_POST['data']['display_on'] ) ) {
			$display_on = sanitize_text_field( $_POST['data']['display_on'] );
		} else {
			$display_on = '';
		}
		if ( !empty( $_POST['data']['location'] ) && $display_on != 'manual' ) {
			$location = sanitize_text_field( $_POST['data']['location'] );
		} else {
			$location = '';
		}
		if ( !empty( $_POST['data']['lp_count'] ) ) {
			$lp_count = max( 1, (int) $_POST['data']['lp_count'] );
		} else {
			$lp_count = '';
		}
		if ( !empty( $_POST['data']['status'] ) ) {
			$status = sanitize_text_field( $_POST['data']['status'] );
		} else {
			$status = '';
		}
		if ( !empty( $_POST['data']['s_pages'] ) ) {
			$s_pages = $_POST['data']['s_pages'];
		} else {
			$s_pages = '';
		}
		if ( !empty( $_POST['data']['s_posts'] ) ) {
			$s_posts = $_POST['data']['s_posts'];
		} else {
			$s_posts = '';
		}
		if ( !is_array( $s_pages ) ) {
			$s_pages = array();
		}
		array_map( 'absint', $s_pages );
		if ( !is_array( $s_posts ) ) {
			$s_posts = array();
		}
		array_map( 'absint', $s_posts );
		if ( !empty( $_POST['data']['s_custom_posts'] ) ) {
			$s_custom_posts = $_POST['data']['s_custom_posts'];
		} else {
			$s_custom_posts = '';
		}
		if ( !is_array( $s_custom_posts ) ) {
			$s_custom_posts = array();
		}
		array_map( 'absint', $s_custom_posts );
		if ( !empty( $_POST['data']['s_categories'] ) ) {
			$s_categories = $_POST['data']['s_categories'];
		} else {
			$s_categories = '';
		}
		if ( !is_array( $s_categories ) ) {
			$s_categories = array();
		}
		array_map( 'absint', $s_categories );
		if ( !empty( $_POST['data']['s_tags'] ) ) {
			$s_tags = $_POST['data']['s_tags'];
		} else {
			$s_tags = '';
		}
		if ( !is_array( $s_tags ) ) {
			$s_tags = array();
		}
		array_map( 'absint', $s_tags );

		global $current_user;

		$wpdb->update( $table_name, //table
			array( // data
				'name' => $name,
				'snippet' => $snippet,
				'device_type' => $device_type,
				'location' => $location,
				'display_on' => $display_on,
				'status' => $status,
				'lp_count' => $lp_count,
				's_pages' => wp_json_encode( $s_pages ),
				's_posts' => wp_json_encode( $s_posts ),
				's_custom_posts' => wp_json_encode( $s_custom_posts ),
				's_categories' => wp_json_encode( $s_categories ),
				's_tags' => wp_json_encode( $s_tags ),
				'last_revision_date' => current_time( 'Y-m-d H:i:s' ),
				'last_modified_by' => sanitize_text_field( $current_user->display_name ) 
			),
			array( // where
				'script_id' => $id 
			), 
			array( // data format
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s' 
			), 
			array( // where format
				'%s' 
			) 
		);
		hfcm_redirect( admin_url( 'admin.php?page=hfcm-update&message=1&id=' . $id ) );

	// JSON return posts for AJAX
	} elseif ( isset( $_POST['get_posts'] ) ) {

		// check nonce
		//check_admin_referer( 'hfcm-get-posts' );

		// get all selected posts
		
		if ( id===-1 ) {
			$s_posts = array();
		} else {

			//selecting value to update	
			$script = $wpdb->get_results( $wpdb->prepare( "SELECT s_posts from $table_name where script_id=%s", $id ) );
			foreach ($script as $s) {
				$s_posts = json_decode( $s->s_posts );
				if ( !is_array( $s_posts ) ) {
					$s_posts = array();
				}
			}

		}
		
		
		
		// get all posts
		$args = array(
			'public' => true,
			'_builtin' => false,
		);
		
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		
		$c_posttypes = get_post_types($args, $output, $operator);
		$posttypes = array('post');
		foreach ($c_posttypes as $cpdata) {
			$posttypes[] = $cpdata;
		}
		$posts = get_posts(array('post_type' => $posttypes, 'posts_per_page' => -1, 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
		
		$json_output = array( 
				'selected' => array(),
				'posts' => array()
			);
		
		foreach ($posts as $pdata) {
			
			if ( in_array( $pdata->ID, $s_posts ) ) {
				$json_output['selected'][] = $pdata->ID;
			}
			$json_output['posts'][] = array(
				'text'  => sanitize_text_field( $pdata->post_title ),
				'value' => $pdata->ID,
			);
		
		
			/*// old
			if (in_array($pdata->ID, $s_posts)) {
				echo "<option value='{$pdata->ID}' selected>{$pdata->post_title}</option>";
			} else {
				echo "<option value='{$pdata->ID}'>{$pdata->post_title}</option>";
			}*/
		}
		
		echo wp_json_encode( $json_output );
		wp_die();

	}
}