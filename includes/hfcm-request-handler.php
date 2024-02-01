<?php

function hfcm_request_handler() {

	// Check user capabilities
	current_user_can( 'administrator' );

	if ( isset( $_POST['insert'] ) ) {
		// Check nonce
		check_admin_referer( 'create-snippet' );
	} else {
		if ( ! isset( $_REQUEST['id'] ) ) {
			die( 'Missing ID parameter.' );
		}
		$id = (int) $_REQUEST['id'];
	}
	if ( isset( $_POST['update'] ) ) {
		// Check nonce
		check_admin_referer( 'update-snippet_' . $id );
	}

	// Handle AJAX on/off toggle for snippets
	if ( isset( $_REQUEST['toggle'] ) && ! empty( $_REQUEST['togvalue'] ) ) {

		// Check nonce
		check_ajax_referer( 'hfcm-toggle-snippet', 'security' );

		if ( 'on' === $_REQUEST['togvalue'] ) {
			$status = 'active';
		} else {
			$status = 'inactive';
		}

		// Global vars
		global $wpdb;
		$table_name = $wpdb->prefix . 'hfcm_scripts';

		$wpdb->update(
			$table_name, //table
			array( 'status' => $status ), //data
			array( 'script_id' => $id ), //where
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ), //data format
			array( '%s' ) //where format
		);

	} elseif ( isset( $_POST['insert'] ) || isset( $_POST['update'] ) ) {

		// Create / update snippet.

		// Sanitize fields
		$name           = hfcm_sanitize_text( 'name' );
		$snippet        = hfcm_sanitize_text( 'snippet', false );
		$device_type    = hfcm_sanitize_text( 'device_type' );
		$display_on     = hfcm_sanitize_text( 'display_on' );
		$location       = hfcm_sanitize_text( 'location' );
		$lp_count       = hfcm_sanitize_text( 'lp_count' );
		$status         = hfcm_sanitize_text( 'status' );
		$s_pages        = hfcm_sanitize_array( 's_pages' );
		$ex_pages        = hfcm_sanitize_array( 'ex_pages' );
		//$ex_pages        = hfcm_sanitize_array( 'hfcm_attached_cmb2_attached_pages' );
		$s_posts        = hfcm_sanitize_array( 's_posts' );
		//$ex_posts        = hfcm_sanitize_array( 'ex_posts' );
		$ex_posts        = hfcm_sanitize_array( 'ex_posts_list' );
		$s_custom_posts = hfcm_sanitize_array( 's_custom_posts', 'string' );
		$s_categories   = hfcm_sanitize_array( 's_categories' );
		$s_tags         = hfcm_sanitize_array( 's_tags' );

		if ( 'manual' === $display_on ) {
			$location = '';
		}
		$lp_count = max( 1, (int) $lp_count );

		// Global vars
		global $wpdb;
		global $current_user;
		$table_name = $wpdb->prefix . 'hfcm_scripts';

		// Update snippet
		if ( isset( $id ) ) {

			$wpdb->update( $table_name, //table
				// Data
				array(
					'name' => $name,
					'snippet' => $snippet,
					'device_type' => $device_type,
					'location' => $location,
					'display_on' => $display_on,
					'status' => $status,
					'lp_count' => $lp_count,
					's_pages' => wp_json_encode( $s_pages ),
					'ex_pages' => wp_json_encode( $ex_pages ),
					's_posts' => wp_json_encode( $s_posts ),
					'ex_posts' => wp_json_encode( $ex_posts ),
					's_custom_posts' => wp_json_encode( $s_custom_posts ),
					's_categories' => wp_json_encode( $s_categories ),
					's_tags' => wp_json_encode( $s_tags ),
					'last_revision_date' => current_time( 'Y-m-d H:i:s' ),
					'last_modified_by' => sanitize_text_field( $current_user->display_name ),
				),
				// Where
				array( 'script_id' => $id ),
				// Data format
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
				),
				// Where format
				array( '%s' )
			);
			hfcm_redirect( admin_url( 'admin.php?page=hfcm-update&message=1&id=' . $id ) );
		} else {

			// Create new snippet
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
					'ex_pages' => wp_json_encode( $ex_pages ),
					's_posts' => wp_json_encode( $s_posts ),
					'ex_posts' => wp_json_encode( $ex_posts ),
					's_custom_posts' => wp_json_encode( $s_custom_posts ),
					's_categories' => wp_json_encode( $s_categories ),
					's_tags' => wp_json_encode( $s_tags ),
					'created' => current_time( 'Y-m-d H:i:s' ),
					'created_by' => sanitize_text_field( $current_user->display_name ),
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
					'%s',
					'%s',
					'%s',
				)
			);
			$lastid = $wpdb->insert_id;
			hfcm_redirect( admin_url( 'admin.php?page=hfcm-update&message=6&id=' . $lastid ) );
		}
	} elseif ( isset( $_POST['getPosts'] ) ) {

		// JSON return posts for AJAX

		// Check nonce
		check_ajax_referer( 'hfcm-get-posts', 'security' );

		// Global vars
		global $wpdb;
		$table_name = $wpdb->prefix . 'hfcm_scripts';

		// Get all selected posts
		if ( -1 === $id ) {
			$s_posts = array();
			$ex_posts = array();
		} else {

			// Select value to update
			$script = $wpdb->get_results( $wpdb->prepare( "SELECT s_posts from $table_name where script_id=%s", $id ) );
			foreach ( $script as $s ) {
				$s_posts = json_decode( $s->s_posts );
				if ( ! is_array( $s_posts ) ) {
					$s_posts = array();
				}
			}

			$script_ex = $wpdb->get_results( $wpdb->prepare( "SELECT ex_posts from $table_name where script_id=%s", $id ) );
			foreach ( $script_ex as $s ) {
				$ex_posts = json_decode( $s->ex_posts );
				if ( ! is_array( $ex_posts ) ) {
					$ex_posts = array();
				}
			}
		}

		// Get all posts
		$args = array(
			'public' => true,
			'_builtin' => false,
		);

		$offset = 0;
        if(!empty($_POST['page'])) {
            $offset = 100 * sanitize_text_field($_POST['page']);
        }
        if(!empty($_POST['postType'])) {
            $postTypes = array(sanitize_text_field($_POST['postType']));
        } else {
            $postTypes = array_diff(
                array_merge(array( 'post' ), get_post_types( $args, 'names', 'and' )), array('page')
            );
        }

        $argsPost = array(
            'post_type' => $postTypes,
            'posts_per_page' => 100,
            'orderby' => 'title',
            'order' => 'ASC',
            'offset' => $offset,
            'post_status' => ['publish']
        );

        $searchQuery = "";
        if(!empty($_POST['s'])) {
            $argsPost['s'] = sanitize_text_field($_POST['s']);
        }

        if(!empty($_POST['taxonomy'])) {
            $taxonomySearch =  sanitize_text_field($_POST['taxonomy']);
            $taxonomySearch =  explode(':', $taxonomySearch);
            if(!empty($taxonomySearch)) {
                $argsPost['tax_query'] = array(
                    array(
                        'taxonomy' => $taxonomySearch[0],
                        'field' => 'slug',
                        'terms' => $taxonomySearch[1],
                    )
                );
            }
        }

		$posts = get_posts($argsPost);

		$json_output = array(
			'selected' => array(),
			'posts' => '',
			'excluded' => array(),
		);

        $selectOptions = "";
		foreach ( $posts as $pdata ) {
		    $post_title = sanitize_text_field( $pdata->post_title );
            $selectOptions .= '<option value="'.$pdata->ID.'">'.sanitize_text_field( $pdata->post_title ).'</option>';
		}
        $json_output['posts'] = $selectOptions;
		$json_output['count'] = count($posts);

		echo wp_json_encode( $json_output );
		wp_die();
	}
}


function hfcm_request_handler_example() {

	// Check user capabilities
	current_user_can( 'administrator' );

	if ( isset( $_POST['insert'] ) ) {
		// Check nonce
		check_admin_referer( 'create-snippet' );
	} else {
		if ( ! isset( $_REQUEST['id'] ) ) {
			die( 'Missing ID parameter.' );
		}
		$id = (int) $_REQUEST['id'];
	}
	if ( isset( $_POST['update'] ) ) {
		// Check nonce
		check_admin_referer( 'update-snippet_' . $id );
	}

	// Handle AJAX on/off toggle for snippets
	if ( isset( $_POST['getPosts'] ) ) {

		// JSON return posts for AJAX

		// Check nonce
		check_ajax_referer( 'hfcm-get-posts', 'security' );

		// Global vars
		global $wpdb;
		$table_name = $wpdb->prefix . 'hfcm_scripts';

		// Get all selected posts
		if ( -1 === $id ) {
			$s_posts = array();
			$ex_posts = array();
		} else {

			// Select value to update.
			$script = $wpdb->get_results( $wpdb->prepare( "SELECT s_posts from $table_name where script_id=%s", $id ) );
			foreach ( $script as $s ) {
				$s_posts = json_decode( $s->s_posts );
				if ( ! is_array( $s_posts ) ) {
					$s_posts = array();
				}
			}

			$script_ex = $wpdb->get_results( $wpdb->prepare( "SELECT ex_posts from $table_name where script_id=%s", $id ) );
			foreach ( $script_ex as $s ) {
				$ex_posts = json_decode( $s->ex_posts );
				if ( ! is_array( $ex_posts ) ) {
					$ex_posts = array();
				}
			}

		}

		// Get all posts
		$args = array(
			'public' => true,
			'_builtin' => false,
		);

		$offset = 0;
        if(!empty($_POST['page'])) {
            $offset = 100 * sanitize_text_field($_POST['page']);
        }
        if(!empty($_POST['postType'])) {
            $postTypes = array(sanitize_text_field($_POST['postType']));
        } else {
            $postTypes = array_diff(
                array_merge(array( 'post' ), get_post_types( $args, 'names', 'and' )), array('page')
            );
        }

		if( isset( $_POST['page'] ) && 1 === $_POST['page'] ) {		
			$page = 1;
		}
		else {
			$page = $_POST['page'];
		}
		
		
        $argsPost = array(
            'post_type' => $postTypes,
            'posts_per_page' => 5,
			'paged' => $page,
            'orderby' => 'title',
			// orderby created date
			'order' => 'DESC',
            //'order' => 'ASC',
            //'offset' => $offset,
            'post_status' => ['publish']
        );

	
        $searchQuery = "";
        if(!empty($_POST['s'])) {
            $argsPost['s'] = sanitize_text_field($_POST['s']);
        }

        if(!empty($_POST['taxonomy'])) {
            $taxonomySearch =  sanitize_text_field($_POST['taxonomy']);
            $taxonomySearch =  explode(':', $taxonomySearch);
            if(!empty($taxonomySearch)) {
                $argsPost['tax_query'] = array(
                    array(
                        'taxonomy' => $taxonomySearch[0],
                        'field' => 'slug',
                        'terms' => $taxonomySearch[1],
                    )
                );
            }
        }


		$posts = get_posts($argsPost);

		$json_output = array(
			'selected' => array(),
			'posts' => '',
			'excluded' => array(),
		);

        $selectOptions = "";
		foreach ( $posts as $pdata ) {
		    $post_title = sanitize_text_field( $pdata->post_title );

			// check both arrays $ex_posts and $_POST['disabledOptions']
			// if post id is in both arrays, then disable the option
			// if post id is in $ex_posts, then disable the option

			if( $_POST['runFetchPosts'] ){

				if( is_array( $ex_posts ) && !empty( $ex_posts ) && in_array( $pdata->ID, $ex_posts ) ){
					$selectOptions .= '<option class="left-side-option clone-button button-id-'.$pdata->ID.'" value="'.$pdata->ID.'|'.$pdata->post_title.'" disabled>'.sanitize_text_field( $pdata->post_title ).' + </option>';
				}
				else {
					$selectOptions .= '<option class="left-side-option clone-button button-id-'.$pdata->ID.'" value="'.$pdata->ID.'|'.$pdata->post_title.'">'.sanitize_text_field( $pdata->post_title ).' + </option>';
				}

			}
			else {
				if( is_array( $_POST['disabledOptions'] ) && !empty( $_POST['disabledOptions'] ) && in_array( $pdata->ID, $_POST['disabledOptions'] ) ){
					$selectOptions .= '<option class="left-side-option clone-button button-id-'.$pdata->ID.'" value="'.$pdata->ID.'|'.$pdata->post_title.'" disabled>'.sanitize_text_field( $pdata->post_title ).' + </option>';
				}
				else {
					$selectOptions .= '<option class="left-side-option clone-button button-id-'.$pdata->ID.'" value="'.$pdata->ID.'|'.$pdata->post_title.'">'.sanitize_text_field( $pdata->post_title ).' + </option>';
				}
			}
			

			//$selectOptions .= '<option class="left-side-option clone-button button-id-'.$pdata->ID.'" value="'.$pdata->ID.'|'.$pdata->post_title.'">'.sanitize_text_field( $pdata->post_title ).' + </option>';
		}
        $json_output['posts'] = $selectOptions;
		$json_output['count'] = count($posts);
		echo wp_json_encode( $json_output );
		wp_die();
	}
}
