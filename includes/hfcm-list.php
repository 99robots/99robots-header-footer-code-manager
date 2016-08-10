<?php
if (!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class hfcm_Snippets_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct([
			'singular' => __('Snippet', '99robots-header-footer-code-manager'), //singular name of the listed records
			'plural' => __('Snippets', '99robots-header-footer-code-manager'), //plural name of the listed records
			'ajax' => false //does this table support ajax?
		]);
	}

	/**
	 * Retrieve snippets data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_snippets($per_page = 5, $page_number = 1, $customvar = 'all') {

		global $wpdb;
		$table_name = "{$wpdb->prefix}hfcm_scripts";
		$sql = "SELECT * FROM $table_name";
		if ( in_array( $customvar, array( 'inactive', 'active' ) ) ) {
			$sql .= " where status = '$customvar'";
		}
		if (!empty($_REQUEST['orderby'])) {
			$sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
			$sql .=!empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results($sql, 'ARRAY_A');
		return $result;
	}

	/**
	 * Delete a snipppet record.
	 *
	 * @param int $id snippet ID
	 */
	public static function delete_snippet($id) {
		global $wpdb;
		$table_name = "{$wpdb->prefix}hfcm_scripts";

		$wpdb->delete(
			$table_name, [ 'script_id' => $id], [ '%d']
		);
	}

	/**
	 * Activate a snipppet record.
	 *
	 * @param int $id snippet ID
	 */
	public static function activate_snippet($id) {
		global $wpdb;
		$table_name = "{$wpdb->prefix}hfcm_scripts";

		$wpdb->update(
			$table_name, array(
				'status' => 'active',
			), array('script_id' => $id), ['%s'], ['%d']
		);
	}

	/**
	 * Deactivate a snipppet record.
	 *
	 * @param int $id snippet ID
	 */
	public static function deactivate_snippet($id) {
		global $wpdb;
		$table_name = "{$wpdb->prefix}hfcm_scripts";

		$wpdb->update(
				$table_name, array(
					'status' => 'inactive',
				), array('script_id' => $id), ['%s'], ['%d']
		);
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count($customvar = 'all') {
		global $wpdb;
		$table_name = "{$wpdb->prefix}hfcm_scripts";
		$sql = "SELECT COUNT(*) FROM $table_name";
		if ( in_array( $customvar, array( 'inactive', 'active' ) ) ) {
			$sql .= " where status = '$customvar'";
		}

		return $wpdb->get_var($sql);
	}

	/** Text displayed when no snippet data is available */
	public function no_items() {
		_e('No Snippets avaliable.', '99robots-header-footer-code-manager');
	}
	
	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default($item, $column_name) {
		switch ($column_name) {
			case 'name':
				return esc_html($item[$column_name]);
			case 'display_on':
				$darray = array('All' => 'Site Wide', 's_posts' => 'Specific Posts', 's_pages' => 'Specific Pages', 's_categories' => 'Specific Categories', 's_custom_posts' => 'Specific Custom Post Types', 's_tags' => 'Specific Tags', 'latest_posts' => 'Latest Posts', 'manual' => 'Shortcode Only');
				if ( 's_posts' === $item[$column_name] ) {
					$s_posts = json_decode( $item['s_posts'] );

					$empty = 1;
					
					foreach ($s_posts as $ID) {
						if ( 'publish' === get_post_status($ID) ) {
							$empty = 0;
							break;
						}
					}
					if ($empty)
						return '<span class="hfcm-red">' . __('No post selected', '99robots-header-footer-code-manager') . '</span>';
				}
				return __(esc_html($darray[$item[$column_name]]), '99robots-header-footer-code-manager');
			case 'location':
				if (!$item[$column_name]) {
					return __('N/A', '99robots-header-footer-code-manager');
				}

				$larray = array('header' => 'Header', 'before_content' => 'Before Content', 'after_content' => 'After Content', 'footer' => 'Footer');
				return __(esc_html($larray[$item[$column_name]]), '99robots-header-footer-code-manager');
			case 'device_type':
				if ( 'both' === $item[$column_name] ) {
					return __('Show on All Devices', '99robots-header-footer-code-manager');
				} elseif ( 'mobile' === $item[$column_name] ) {
					return __('Only Mobile Devices', '99robots-header-footer-code-manager');
				} elseif ( 'desktop' === $item[$column_name] ) {
					return __('Only Desktop', '99robots-header-footer-code-manager');
				} else {
					return esc_html($item[$column_name]);
				}
			case 'status':
				if ( 'inactive' === $item[$column_name] ) {
					return '<div class="nnr-switch">
								<label for="nnr-round-toggle' . $item['script_id'] . '">OFF</label>
								<input id="nnr-round-toggle' . $item['script_id'] . '" class="round-toggle round-toggle-round-flat" type="checkbox" data-id="' . $item['script_id'] . '" />
								<label for="nnr-round-toggle' . $item['script_id'] . '"></label>
								<label for="nnr-round-toggle' . $item['script_id'] . '">ON</label>
							</div>
							';
				} elseif ( 'active' === $item[$column_name] ) {
					return '<div class="nnr-switch">
								<label for="nnr-round-toggle' . $item['script_id'] . '">OFF</label>
								<input id="nnr-round-toggle' . $item['script_id'] . '" class="round-toggle round-toggle-round-flat" type="checkbox" data-id="' . $item['script_id'] . '" checked="checked" />
								<label for="nnr-round-toggle' . $item['script_id'] . '"></label>
								<label for="nnr-round-toggle' . $item['script_id'] . '">ON</label>
							</div>
							';
				} else {
					return esc_html($item[$column_name]);
				}
			case 'script_id':
				return esc_html($item[$column_name]);
			case 'shortcode':
				return '[hfcm id="' . $item['script_id'] . '"]';
			default:
				return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb($item) {
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
	function column_name($item) {
		
		$delete_nonce = wp_create_nonce('hfcm_delete_snippet');
		$edit_nonce = wp_create_nonce('hfcm_edit_snippet');

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = [
			'edit' => sprintf('<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">' . __('Edit', '99robots-header-footer-code-manager') . '</a>', esc_attr("hfcm-update"), 'edit', absint($item['script_id']), $edit_nonce),
			'delete' => sprintf('<a href="?page=%s&action=%s&snippet=%s&_wpnonce=%s">' . __('Delete', '99robots-header-footer-code-manager') . '</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['script_id']), $delete_nonce)
		];

		return $title . $this->row_actions($actions);
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb' => '<input type="checkbox" />',
			'script_id' => __('ID', '99robots-header-footer-code-manager'),
			'status' => __('Status', '99robots-header-footer-code-manager'),
			'name' => __('Snippet Name', '99robots-header-footer-code-manager'),
			'display_on' => __('Display On', '99robots-header-footer-code-manager'),
			'location' => __('Location', '99robots-header-footer-code-manager'),
			'device_type' => __('Devices', '99robots-header-footer-code-manager'),
			'shortcode' => __('Shortcode', '99robots-header-footer-code-manager')
		];

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array('name', true),
			'script_id' => array('script_id', false)
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-activate' => __('Activate', '99robots-header-footer-code-manager'),
			'bulk-deactivate' => __('Deactivate', '99robots-header-footer-code-manager'),
			'bulk-delete' => __('Remove', '99robots-header-footer-code-manager'),
		];

		return $actions;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		//Retrieve $customvar for use in query to get items.
		$customvar = ( isset($_REQUEST['customvar']) ? $_REQUEST['customvar'] : 'all');
		$this->_column_headers = array($columns, $hidden, $sortable);

		/** Process bulk action */
		$this->process_bulk_action();
		$this->views();
		$per_page = $this->get_items_per_page('snippets_per_page', 5);
		$current_page = $this->get_pagenum();
		$total_items = self::record_count();

		$this->set_pagination_args([
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page' => $per_page //WE have to determine how many items to show on a page
		]);

		$this->items = self::get_snippets($per_page, $current_page, $customvar);
	}

	public function get_views() {
		$views = array();
		$current = (!empty($_REQUEST['customvar']) ? $_REQUEST['customvar'] : 'all');

		//All link
		$class = ( 'all' === $current ? ' class="current"' : '');
		$all_url = remove_query_arg('customvar');
		$views['all'] = "<a href='{$all_url }' {$class} >" . __('All', '99robots-header-footer-code-manager') . ' (' . $this->record_count() . ')</a>';

		//Foo link
		$foo_url = add_query_arg('customvar', 'active');
		$class = ( 'active' === $current ? ' class="current"' : '');
		$views['active'] = "<a href='{$foo_url}' {$class} >" . __('Active', '99robots-header-footer-code-manager') . ' (' . $this->record_count('active') . ')</a>';

		//Bar link
		$bar_url = add_query_arg('customvar', 'inactive');
		$class = ( 'inactive' === $current ? ' class="current"' : '');
		$views['inactive'] = "<a href='{$bar_url}' {$class} >" . __('Inactive', '99robots-header-footer-code-manager') . ' (' . $this->record_count('inactive') . ')</a>';

		return $views;
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr($_REQUEST['_wpnonce']);

			if ( !wp_verify_nonce($nonce, 'hfcm_delete_snippet') ) {
				die('Go get a life script kiddies');
			} else {
				self::delete_snippet(absint($_GET['snippet']));

				hfcm_redirect( admin_url('admin.php?page=hfcm-list') );
				return;
			}
		}

		// If the delete bulk action is triggered
		if ( ( isset($_POST['action']) && 'bulk-delete' === $_POST['action'] )
				|| ( isset($_POST['action2']) && 'bulk-delete' === $_POST['action2'] )
		) {

			$delete_ids = esc_sql($_POST['snippets']);

			// loop over the array of record IDs and delete them
			foreach ($delete_ids as $id) {
				self::delete_snippet($id);
			}

			hfcm_redirect( admin_url('admin.php?page=hfcm-list') );
			return;
		} elseif (( isset($_POST['action']) && 'bulk-activate' === $_POST['action'] )
				|| ( isset($_POST['action2']) && 'bulk-activate' === $_POST['action2'] )
		) {

			$activate_ids = esc_sql($_POST['snippets']);

			// loop over the array of record IDs and activate them
			foreach ($activate_ids as $id) {
				self::activate_snippet($id);
			}

			hfcm_redirect( admin_url('admin.php?page=hfcm-list') );
			return;
		} elseif (( isset($_POST['action']) && 'bulk-deactivate' === $_POST['action'] )
				|| ( isset($_POST['action2']) && 'bulk-deactivate' === $_POST['action2'] )
		) {

			$delete_ids = esc_sql($_POST['snippets']);

			// loop over the array of record IDs and deactivate them
			foreach ($delete_ids as $id) {
				self::deactivate_snippet($id);
			}
			
			hfcm_redirect( admin_url('admin.php?page=hfcm-list') );
			return;
		}
	}

}

/** Generate list of all snippets */
function hfcm_list() {

	global $wpdb;
	$table_name = $wpdb->prefix . 'hfcm_scripts';
	$activeclass = '';
	$inactiveclass = '';
	$allclass = 'current';
	$snippetObj = new hfcm_Snippets_List();

	if (!empty($_GET['script_status']) && in_array($_GET['script_status'], array('active', 'inactive'))) {
		$allclass = '';
		if ('active' === $_GET['script_status'] ) {
			$activeclass = 'current';
		}
		if ('inactive' === $_GET['script_status'] ) {
			$inactiveclass = 'current';
		}
	}
	?>
	<div class="wrap">
		<h1><?php _e('Snippets', '99robots-header-footer-code-manager'); ?> 
			<a href="<?php echo admin_url('admin.php?page=hfcm-create'); ?>" class="page-title-action"><?php _e('Add New Snippet', '99robots-header-footer-code-manager'); ?></a>
		</h1>

		<form method="post">
			<?php
			$snippetObj->prepare_items();
			$snippetObj->display();
			?>
		</form>

	</div>
	<?php
		
	// Register the script
	wp_register_script( 'hfcm_toggle', plugins_url( '../js/toggle.js', __FILE__ ) );

	// Localize the script with new data
	$translation_array = array(
		'url' => admin_url('admin.php'),
		'security' => wp_create_nonce( 'toggle-snippet' )
	);
	wp_localize_script( 'hfcm_toggle', 'hfcm_ajax', $translation_array );

	// Enqueued script with localized data.
	wp_enqueue_script( 'hfcm_toggle' );
}