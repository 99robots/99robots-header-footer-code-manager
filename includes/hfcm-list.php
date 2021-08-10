<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Hfcm_Snippets_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => esc_html__( 'Snippet', '99robots-header-footer-code-manager' ),
				'plural' => esc_html__( 'Snippets', '99robots-header-footer-code-manager' ),
				'ajax' => false,
			)
		);
	}

	/**
	 * Retrieve snippets data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_snippets( $per_page = 20, $page_number = 1, $customvar = 'all' ) {

		global $wpdb;
		$table_name = "{$wpdb->prefix}hfcm_scripts";
		$sql = "SELECT * FROM $table_name";

		if ( in_array( $customvar, array( 'inactive', 'active' ) ) ) {
			$sql .= " where status = '$customvar'";
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		return $result;
	}

	/**
	 * Delete a snipppet record.
	 *
	 * @param int $id snippet ID
	 */
	public static function delete_snippet( $id ) {

		global $wpdb;
		$table_name = "{$wpdb->prefix}hfcm_scripts";

		$wpdb->delete(
			$table_name, array( 'script_id' => $id ), array( '%d' )
		);
	}

	/**
	 * Activate a snipppet record.
	 *
	 * @param int $id snippet ID
	 */
	public static function activate_snippet( $id ) {

		global $wpdb;
		$table_name = "{$wpdb->prefix}hfcm_scripts";

		$wpdb->update(
			$table_name, array(
				'status' => 'active',
			), array( 'script_id' => $id ), array( '%s' ), array( '%d' )
		);
	}

	/**
	 * Deactivate a snipppet record.
	 *
	 * @param int $id snippet ID
	 */
	public static function deactivate_snippet( $id ) {

		global $wpdb;
		$table_name = "{$wpdb->prefix}hfcm_scripts";

		$wpdb->update(
			$table_name, array(
				'status' => 'inactive',
			), array( 'script_id' => $id ), array( '%s' ), array( '%d' )
		);
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count( $customvar = 'all' ) {

		global $wpdb;
		$table_name = "{$wpdb->prefix}hfcm_scripts";
		$sql = "SELECT COUNT(*) FROM $table_name";

		if ( in_array( $customvar, array( 'inactive', 'active' ) ) ) {
			$sql .= " where status = '$customvar'";
		}

		return $wpdb->get_var( $sql );
	}

	/** Text displayed when no snippet data is available */
	public function no_items() {
		esc_html_e( 'No Snippets avaliable.', '99robots-header-footer-code-manager' );
	}

	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'name':
				return esc_html( $item[ $column_name ] );

			case 'display_on':
                $nnr_hfcm_display_array = array(
					'All'            => esc_html__( 'Site Wide', '99robots-header-footer-code-manager' ),
					's_posts'        => esc_html__( 'Specific Posts', '99robots-header-footer-code-manager' ),
					's_pages'        => esc_html__( 'Specific Pages', '99robots-header-footer-code-manager' ),
					's_categories'   => esc_html__( 'Specific Categories', '99robots-header-footer-code-manager' ),
					's_custom_posts' => esc_html__( 'Specific Custom Post Types', '99robots-header-footer-code-manager' ),
					's_tags'         => esc_html__( 'Specific Tags', '99robots-header-footer-code-manager' ),
					'latest_posts'   => esc_html__( 'Latest Posts', '99robots-header-footer-code-manager' ),
					'manual'         => esc_html__( 'Shortcode Only', '99robots-header-footer-code-manager' ),
				);

				if ( 's_posts' === $item[ $column_name ] ) {

					$empty = 1;
					$s_posts = json_decode( $item['s_posts'] );

					foreach ( $s_posts as $id ) {
						if ( 'publish' === get_post_status( $id ) ) {
							$empty = 0;
							break;
						}
					}
					if ( $empty ) {
						return '<span class="hfcm-red">' . esc_html__( 'No post selected', '99robots-header-footer-code-manager' ) . '</span>';
					}
				}

				return esc_html( $nnr_hfcm_display_array[ $item[ $column_name ] ] );

			case 'location':

				if ( ! $item[ $column_name ] ) {
					return esc_html__( 'N/A', '99robots-header-footer-code-manager' );
				}

				$nnr_hfcm_locations = array(
					'header'         => esc_html__( 'Header', '99robots-header-footer-code-manager' ),
					'before_content' => esc_html__( 'Before Content', '99robots-header-footer-code-manager' ),
					'after_content'  => esc_html__( 'After Content', '99robots-header-footer-code-manager' ),
					'footer'         => esc_html__( 'Footer', '99robots-header-footer-code-manager' ),
				);
				return esc_html( $nnr_hfcm_locations[ $item[ $column_name ] ] );

			case 'device_type':

				if ( 'both' === $item[ $column_name ] ) {
					return esc_html__( 'Show on All Devices', '99robots-header-footer-code-manager' );
				} elseif ( 'mobile' === $item[ $column_name ] ) {
					return esc_html__( 'Only Mobile Devices', '99robots-header-footer-code-manager' );
				} elseif ( 'desktop' === $item[ $column_name ] ) {
					return esc_html__( 'Only Desktop', '99robots-header-footer-code-manager' );
				} else {
					return esc_html( $item[ $column_name ] );
				}

			case 'status':

				if ( 'inactive' === $item[ $column_name ] ) {
					return '<div class="nnr-switch">
								<label for="nnr-round-toggle' . $item['script_id'] . '">OFF</label>
								<input id="nnr-round-toggle' . $item['script_id'] . '" class="round-toggle round-toggle-round-flat" type="checkbox" data-id="' . $item['script_id'] . '" />
								<label for="nnr-round-toggle' . $item['script_id'] . '"></label>
								<label for="nnr-round-toggle' . $item['script_id'] . '">ON</label>
							</div>
							';
				} elseif ( 'active' === $item[ $column_name ] ) {
					return '<div class="nnr-switch">
								<label for="nnr-round-toggle' . $item['script_id'] . '">OFF</label>
								<input id="nnr-round-toggle' . $item['script_id'] . '" class="round-toggle round-toggle-round-flat" type="checkbox" data-id="' . $item['script_id'] . '" checked="checked" />
								<label for="nnr-round-toggle' . $item['script_id'] . '"></label>
								<label for="nnr-round-toggle' . $item['script_id'] . '">ON</label>
							</div>
							';
				} else {
					return esc_html( $item[ $column_name ] );
				}

			case 'script_id':
				return esc_html( $item[ $column_name ] );

			case 'shortcode':
				return '[hfcm id="' . $item['script_id'] . '"]';

			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="snippets[]" value="%s" />', $item['script_id']
		);
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'hfcm_delete_snippet' );
		$edit_nonce = wp_create_nonce( 'hfcm_edit_snippet' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = array(
			'edit' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">' . esc_html__( 'Edit', '99robots-header-footer-code-manager' ) . '</a>', esc_attr( 'hfcm-update' ), 'edit', absint( $item['script_id'] ), $edit_nonce ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&snippet=%s&_wpnonce=%s">' . esc_html__( 'Delete', '99robots-header-footer-code-manager' ) . '</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['script_id'] ), $delete_nonce ),
		);

		return $title . $this->row_actions( $actions );
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'script_id'   => esc_html__( 'ID', '99robots-header-footer-code-manager' ),
			'status'      => esc_html__( 'Status', '99robots-header-footer-code-manager' ),
			'name'        => esc_html__( 'Snippet Name', '99robots-header-footer-code-manager' ),
			'display_on'  => esc_html__( 'Display On', '99robots-header-footer-code-manager' ),
			'location'    => esc_html__( 'Location', '99robots-header-footer-code-manager' ),
			'device_type' => esc_html__( 'Devices', '99robots-header-footer-code-manager' ),
			'shortcode'   => esc_html__( 'Shortcode', '99robots-header-footer-code-manager' ),
		);

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {

		return array(
			'name' => array( 'name', true ),
			'script_id' => array( 'script_id', false ),
		);
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {

		return array(
			'bulk-activate'   => esc_html__( 'Activate', '99robots-header-footer-code-manager' ),
			'bulk-deactivate' => esc_html__( 'Deactivate', '99robots-header-footer-code-manager' ),
			'bulk-delete'     => esc_html__( 'Remove', '99robots-header-footer-code-manager' ),
		);
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		//Retrieve $customvar for use in query to get items.
		$customvar = ( isset( $_REQUEST['customvar'] ) ? $_REQUEST['customvar'] : 'all');
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/** Process bulk action */
		$this->process_bulk_action();
		$this->views();
		$per_page = $this->get_items_per_page( 'snippets_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items = self::record_count();

		$this->set_pagination_args(array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
		));

		$this->items = self::get_snippets( $per_page, $current_page, $customvar );
	}

	public function get_views() {
		$views = array();
		$current = ( ! empty( $_REQUEST['customvar'] ) ? $_REQUEST['customvar'] : 'all');

		//All link
		$class = 'all' === $current ? ' class="current"' : '';
		$all_url = remove_query_arg( 'customvar' );
		$views['all'] = "<a href='{$all_url }' {$class} >" . esc_html__( 'All', '99robots-header-footer-code-manager' ) . ' (' . $this->record_count() . ')</a>';

		//Foo link
		$foo_url = add_query_arg( 'customvar', 'active' );
		$class = ( 'active' === $current ? ' class="current"' : '');
		$views['active'] = "<a href='{$foo_url}' {$class} >" . esc_html__( 'Active', '99robots-header-footer-code-manager' ) . ' (' . $this->record_count( 'active' ) . ')</a>';

		//Bar link
		$bar_url = add_query_arg( 'customvar', 'inactive' );
		$class = ( 'inactive' === $current ? ' class="current"' : '');
		$views['inactive'] = "<a href='{$bar_url}' {$class} >" . esc_html__( 'Inactive', '99robots-header-footer-code-manager' ) . ' (' . $this->record_count( 'inactive' ) . ')</a>';

		return $views;
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'hfcm_delete_snippet' ) ) {
				die( 'Go get a life script kiddies' );
			} else {
				self::delete_snippet( absint( $_GET['snippet'] ) );

				NNR_HFCM::hfcm_redirect( admin_url( 'admin.php?page=hfcm-list' ) );
				return;
			}
		}

		// If the delete bulk action is triggered
		if (
			( isset( $_POST['action'] ) && 'bulk-delete' === $_POST['action'] ) ||
			( isset( $_POST['action2'] ) && 'bulk-delete' === $_POST['action2'] )
		) {
			$delete_ids = esc_sql( $_POST['snippets'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_snippet( $id );
			}

			NNR_HFCM::hfcm_redirect( admin_url( 'admin.php?page=hfcm-list' ) );
			return;
		} elseif (
			( isset( $_POST['action'] ) && 'bulk-activate' === $_POST['action'] ) ||
			( isset( $_POST['action2'] ) && 'bulk-activate' === $_POST['action2'] )
		) {

			$activate_ids = esc_sql( $_POST['snippets'] );

			// loop over the array of record IDs and activate them
			foreach ( $activate_ids as $id ) {
				self::activate_snippet( $id );
			}

            NNR_HFCM::hfcm_redirect( admin_url( 'admin.php?page=hfcm-list' ) );
			return;
		} elseif (
			( isset( $_POST['action'] ) && 'bulk-deactivate' === $_POST['action'] ) ||
			( isset( $_POST['action2'] ) && 'bulk-deactivate' === $_POST['action2'] )
		) {

			$delete_ids = esc_sql( $_POST['snippets'] );

			// loop over the array of record IDs and deactivate them
			foreach ( $delete_ids as $id ) {
				self::deactivate_snippet( $id );
			}

            NNR_HFCM::hfcm_redirect( admin_url( 'admin.php?page=hfcm-list' ) );

			return;
		}
	}
}

