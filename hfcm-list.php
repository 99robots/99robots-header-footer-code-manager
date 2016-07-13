<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Snippets_List extends WP_List_Table {

    /** Class constructor */
    public function __construct() {

        parent::__construct([
            'singular' => __('Snippet', 'sp'), //singular name of the listed records
            'plural' => __('Snippets', 'sp'), //plural name of the listed records
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
    public static function get_snippets($per_page = 5, $page_number = 1, $customvar = "all") {

        global $wpdb;
        $table_name = "{$wpdb->prefix}hfcm_scripts";
        $sql = "SELECT * FROM $table_name";
        if (in_array($customvar, array("inactive", "active"))) {
            $sql .= " where status = '" . $customvar . "'";
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
                "$table_name", [ 'script_id' => $id], [ '%d']
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
                "$table_name", array(
            "status" => "active",
                ), array('script_id' => $id), [ '%s'], ["%d"]
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
                "$table_name", array(
            "status" => "inactive",
                ), array('script_id' => $id), [ '%s'], ["%d"]
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
        if (in_array($customvar, array("inactive", "active"))) {
            $sql .= " where status = '" . $customvar . "'";
        }

        return $wpdb->get_var($sql);
    }

    /** Text displayed when no snippet data is available */
    public function no_items() {
        _e('No Snippets avaliable.', 'sp');
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
                return $item[$column_name];
            case 'display_on':
                $darray = array("All" => "All", "s_posts" => "Specific Posts", "s_pages" => "Specific Pages", "s_categories" => "Specific Categories", "s_custom_posts" => "Specific Custom Post Types", "s_tags" => "Specific Tags", "latest_posts" => "Latest Posts", "manual" => "Manual Placement");
                return $darray[$item[$column_name]];
            case 'location':
				if (!$item[$column_name]) return 'N/A';
                return ucwords($item[$column_name]);
            case 'device_type':
                if ($item[$column_name] == "both") {
                    return 'Show on All Devices';
                } else if ($item[$column_name] == "mobile") {
                    return 'Only Mobile Devices';
                } else if ($item[$column_name] == "desktop") {
                    return 'Only Computers';
                } else {
                    return $item[$column_name];
                }
            case 'status':
                if ($item[$column_name] == "inactive") {
                    return '<div class="nnr-switch">
                                <label for="nnr-round-toggle' . $item['script_id'] . '">OFF</label>
                                <input id="nnr-round-toggle' . $item['script_id'] . '" class="round-toggle round-toggle-round-flat" type="checkbox" data-id="' . $item['script_id'] . '" />
                                <label for="nnr-round-toggle' . $item['script_id'] . '"></label>
                                <label for="nnr-round-toggle' . $item['script_id'] . '">ON</label>
                            </div>
                            ';
                } else if ($item[$column_name] == "active") {
                    return '<div class="nnr-switch">
                                <label for="nnr-round-toggle' . $item['script_id'] . '">OFF</label>
                                <input id="nnr-round-toggle' . $item['script_id'] . '" class="round-toggle round-toggle-round-flat" type="checkbox" data-id="' . $item['script_id'] . '" checked="checked" />
                                <label for="nnr-round-toggle' . $item['script_id'] . '"></label>
                                <label for="nnr-round-toggle' . $item['script_id'] . '">ON</label>
                            </div>
                            ';
                } else {
                    return $item[$column_name];
                }
            case 'script_id':
                return $item[$column_name];
            case 'shortcode':
                return '[hfcm id="' . $item["script_id"] . '"]';
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
            'edit' => sprintf('<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">Edit</a>', esc_attr("hfcm-update"), 'edit', absint($item['script_id']), $edit_nonce),
            'delete' => sprintf('<a href="?page=%s&action=%s&snippet=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['script_id']), $delete_nonce)
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
            'script_id' => __('ID', 'sp'),
            'status' => __('Status', 'sp'),
            'name' => __('Snippet Name', 'sp'),
            'display_on' => __('Display On', 'sp'),
            'location' => __('Location', 'sp'),
            'device_type' => __('Devices', 'sp'),
            'shortcode' => __('Shortcode', 'sp')
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
            'bulk-activate' => 'Activate',
            'bulk-deactivate' => 'Deactivate',
            'bulk-delete' => 'Remove',
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
        $class = ($current == 'all' ? ' class="current"' : '');
        $all_url = remove_query_arg('customvar');
        $views['all'] = "<a href='{$all_url }' {$class} >All (" . $this->record_count() . ")</a>";

        //Foo link
        $foo_url = add_query_arg('customvar', 'active');
        $class = ($current == 'active' ? ' class="current"' : '');
        $views['active'] = "<a href='{$foo_url}' {$class} >Active (" . $this->record_count('active') . ")</a>";

        //Bar link
        $bar_url = add_query_arg('customvar', 'inactive');
        $class = ($current == 'inactive' ? ' class="current"' : '');
        $views['inactive'] = "<a href='{$bar_url}' {$class} >Inactive (" . $this->record_count('inactive') . ")</a>";

        return $views;
    }

    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, 'hfcm_delete_snippet')) {
                die('Go get a life script kiddies');
            } else {
                self::delete_snippet(absint($_GET['snippet']));

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                echo "<script>window.location = '" . admin_url('admin.php?page=hfcm-list') . "'</script>";
                exit;
            }
        }

        // If the delete bulk action is triggered
        if (( isset($_POST['action']) && $_POST['action'] == 'bulk-delete' )
                || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql($_POST['snippets']);

            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_snippet($id);
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            echo "<script>window.location = '" . esc_url_raw(add_query_arg()) . "'</script>";
            exit;
        } else if (( isset($_POST['action']) && $_POST['action'] == 'bulk-activate' )
                || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-activate' )
        ) {

            $activate_ids = esc_sql($_POST['snippets']);

            // loop over the array of record IDs and activate them
            foreach ($activate_ids as $id) {
                self::activate_snippet($id);
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            echo "<script>window.location = '" . esc_url_raw(add_query_arg()) . "'</script>";
            exit;
        } else if (( isset($_POST['action']) && $_POST['action'] == 'bulk-deactivate' )
                || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-deactivate' )
        ) {

            $delete_ids = esc_sql($_POST['snippets']);

            // loop over the array of record IDs and deactivate them
            foreach ($delete_ids as $id) {
                self::deactivate_snippet($id);
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            echo "<script>window.location = '" . esc_url_raw(add_query_arg()) . "'</script>";
            exit;
        }
    }

}

function hfcm_list() {

    global $wpdb;
    $table_name = $wpdb->prefix . "hfcm_scripts";
    $activeclass = "";
    $inactiveclass = "";
    $allclass = "current";
    $snippetObj = new Snippets_List();

    if (!empty($_GET['script_status']) && in_array($_GET['script_status'], array("active", "inactive"))) {
        $allclass = "";
        if ($_GET['script_status'] == "active") {
            $activeclass = "current";
        }
        if ($_GET['script_status'] == "inactive") {
            $inactiveclass = "current";
        }
    }
    ?>
    <link type="text/css" href="<?php echo plugins_url('assets/css/', __FILE__); ?>style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h1>Snippets 
            <a href="<?php echo admin_url('admin.php?page=hfcm-create'); ?>" class="page-title-action">Add New Snippet</a>
        </h1>

        <form method="post">
            <?php
            $snippetObj->prepare_items();
            $snippetObj->display();
            ?>
        </form>

    </div>
    <script>
        jQuery('.nnr-switch input').click(function()
        {	var t=jQuery(this),
            togvalue = t.is(':checked')?'on':'off',
            scriptid = t.data('id');
    					
            jQuery.ajax(
            {	url: '<?php echo admin_url('admin.php'); ?>', 
                data:
                    {	page: 'hfcm-update',
                    toggle: true,
                    id: scriptid,
                    togvalue: togvalue
                }
            }
        );	
        }
    );
    </script>
    <?php
}
