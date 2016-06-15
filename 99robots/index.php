<?php /*
Plugin Name: 99robots
Plugin URI: http://intellywp.com/tracking-code-manager/
Description: Header Footer Scripts.
Author: Aparajita
Version: 1
*/
register_activation_hook(__FILE__, 'HfActivate');
//(__FILE__, 'HfDectivate');
register_uninstall_hook(__FILE__, 'HfDectivate');
function HfActivate(){
    global $wpdb;
  
	//Activation function
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');   

	$plugin_prefix = 'hf';
	
            $table_name = array( 
							$wpdb->prefix . $plugin_prefix.'_scripts',
						
			);
	    //create db tables if not exist
	
	if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name[0]) != $table_name[0]) 
	 {
        $sql = 'CREATE TABLE ' . $table_name[0] . ' (
		id              INT NOT NULL AUTO_INCREMENT,
                PRIMARY KEY(id),        
		name            varchar(50),
		pageID   		INTEGER(10),
		postID    		INTEGER(10),
		catID  		    INTEGER(10),
		script 		     text,
		position         varchar(50),
		is_active 		tinyint(1)

	  )';
        dbDelta($sql);
 		
     }

	do_action('wp_head','add_before_head');
	do_action('wp_after_body');
	do_action( 'wp_footer', 'add_before_body' );
}
/*function HfDectivate() 
{

	global $wpdb;
}
*/
/*add_action( 'wp_loaded', 'loadall_scripts' );
function loadall_scripts()
{
	do_action('wp_head','add_before_head');
	do_action('wp_after_body');
	do_action( 'wp_footer', 'add_before_body' );
}
do_action( 'wp_loaded', $array, $int ); */

add_action( 'admin_menu', 'header_footer_menu' );

function header_footer_menu() {
	add_options_page( '99robots Header Footer Options', '99robots Header Footer', 'manage_options', 'my-unique-identifier', 'header_footer_menu_options' );
}
add_action('admin_init', 'header_footer_options_init' );
function header_footer_options_init(){	

	register_setting( 'header-footer-settings-group', 'header-footer-link', 'my_option');
}
function header_footer_menu_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}?><script  src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 

<div id ="ptext" style="display:none"> Data added Successfully </div>

	<form role="form" method="post" id="header_footer" action=''>
	
			<div class="checkbox">
			  <label><input type="checkbox" name="active" id="active" value="1" checked>Active</label>
			</div>
			
			<div class="form-group">
			  <label for="name">Name:</label>
			  <input type="text" class="form-control" id="name" name="name">
			  <span class="name_error"> </span>
			  
			</div>
			
			<div class="form-group">
			  <label for="name">Enter Script:</label>			
			  <textarea id="script" name="script"></textarea>
			  <span class="script_error"> </span>
			 
			</div>
						
			<div class="form-group">
			<label for="position">Position:</label>
				<select class="form-control" id="position" name="position">
					<option value="before_head">Before Head</option>
					<option value ="after_body">After Body</option>
					<option value="before_body">Before Body</option>			
				</select>
			
			</div>
			
			<div class="form-group">
			<label for="device">Device:</label>
			<select class="form-control" id="device" name="device">
			<option value="mobile">Mobile</option>
			<option value ="desktop">Desktop</option>
			<option value="tablet">Tablet</option>
			
			</select>
			</div>
			
			<p>In which page do you want to insert this code?</p>
			<div class="radio">
			  <label><input type="radio" name="website" id="website" checked>In the whole website (pages, posts and archives)</label>
			</div>

 
      <input class="button" type="submit" id="text_value" value="SEND" />
</form>
<script>
$(document).ready(function() {
	$('#text_value').click(function() {

		var active = $('#active').val();
		var name = $("#name").val();
		if(name =='') {
			$(".name_error").text('Please Enter Name');
		}else{
			
		}
		var script = $('textarea#script').val();
		if(script =='') {
			$(".script_error").text('Please Enter Script');
		}else{
			alert(text_value);
		}
		var position = $('#position :selected').text();
		var device = $('#device :selected').text();

		$.ajax({

                type: "POST",

                data: "active="+ active +"&name="+ name +"&script="+ script +"&position="+ position +"&device="+ device + "&action=add_scripts",

                url: ajaxurl,

                success: function(data) {

					if(data == "success")
					{

                      // $("#ptext").show().delay(5000).fadeOut();
                   
					}
				} 
        });

       return false;
	});

});
</script>
<?php } 
function add_before_head() {
	global $wpdb;
 
    $before_head = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "hf_scripts WHERE position = 'Before Head' AND is_active = 1");
    if($before_head)
    {   
    	foreach($before_head as $val)
    	{
          $unserialize_script = unserialize($val->script);
          $output  = $unserialize_script['script'];
          
          echo $output;
         
    	}
    } 
	
}
add_action( 'wp_head', 'add_before_head' );

function add_after_body() {

 global $wpdb;
 
    $after_head = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "hf_scripts WHERE position = 'After Body' AND is_active = 1");

    if($after_head)
    {   
    	foreach($after_head as $val)
    	{
          $unserialize_script = unserialize($val->script);
          $output  = $unserialize_script['script'];          
          echo $output;         
    	}
    }
}
add_action( 'wp_after_body', 'add_after_body' );

function add_before_body() {
 global $wpdb;
 
    $after_head = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "hf_scripts WHERE position = 'Before Body' AND is_active = 1");
    if($after_head)
    {   
    	foreach($after_head as $val)
    	{
          $unserialize_script = unserialize($val->script);
          $output  = $unserialize_script['script'];          
          echo $output;         
    	}
    }
}
add_action( 'wp_footer', 'add_before_body' );
function add_scripts() 
{
	 global $wpdb;

    $scripts = array("script" => $_POST['script'], "device" => $_POST['device']);
    $scripts = serialize($scripts);

    $insert = $wpdb->insert(

                $wpdb->prefix . 'hf_scripts', array(

						'name' 	=> $_POST['name'],
						'pageID' => '',
						'postID' => '',
						'catID' => '',
						'script' => $scripts,
						'position' => $_POST['position'],
						'is_active' =>  $_POST['active']

                )

        );

        if ($insert) {
        	do_action('wp_head','add_before_head');
        	do_action('wp_after_body');
        	do_action( 'wp_footer', 'add_before_body' );
            $result = 'success';

        } else {

            //$result = $wpdb->print_error();

        }
     echo $result;
    die();

}

//AJAX Actions

add_action('wp_ajax_add_scripts', 'add_scripts');
add_action('wp_ajax_hook_javascript', 'hook_javascript');