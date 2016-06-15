<?php
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
        <label><input type="radio" name="page" value="website" id="website" checked>In the whole website (pages, posts and archives)</label>
        <br>  <label><input type="radio" name="page" value="page" id="page" > Select pages
        

              <?php wp_dropdown_pages('id=select_page'); ?>
            
          </label><br>
        <label><input type="radio" name="page" value="post" id="post" > Select posts
          <select name="archive-dropdown" id="select_post" >

            <option value=\"\"><?php echo esc_attr(__('Article')); ?></option>

            <?php wp_get_archives('type=postbypost&format=option&show_post_count=0'); ?>

          </select>
         </label><br> 
            <label><input type="radio" name="page" value="category" id="category" > Select category 
            <?php wp_dropdown_categories(array('taxonomy'=> 'post_tag','hide_empty' => 0, 'name' => 'my_tags','show_option_none' => 'All', 'id' => 'select_cat')); ?>
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
    var page = $("input[name='page']:checked").val();
    var selected_page = "";
    var selected_post = "";
    var selected_cat = "";
    var website = "";

    if(page == "page")
    {
       selected_page = $('#select_page :selected').val();
    }
    else if(page == "post")
    {
      selected_post = $('#select_post :selected').text();

    }else if(page == "category")
    {
      selected_cat = $('#select_cat :selected').val();
    }
    else
    {
      website ="website";
    }
    
   

    $.ajax({

                type: "POST",

                data: "active="+ active +"&name="+ name +"&script="+ script +"&position="+ position +"&device="+ device +"&select_page="+ selected_page +"&selected_post="+ selected_post +"&selected_post="+ selected_post+"&website="+ website+ "&action=add_scripts",

                url: ajaxurl,

                success: function(data) {
                  console.log(data);

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



function add_scripts() 
{
   global $wpdb;
  
  $data = array('active' =>$_POST['active'], 
                'name' => $_POST['name'], 
                'script' => $_POST['script'],
                'position' =>$_POST['position'], 
                'device' =>$_POST['device'], 
                'select_page' => $_POST['select_page'],
                'selected_post' => $_POST['selected_post'],
                'selected_cat' => $_POST['selected_cat'],
                'website' => $_POST['website']);
  $data = json_encode($data);

     $status = update_option( 'nnrhf', $data, 'yes' );
     if($status)
     {
        do_action('wp_head','inject_script');
        do_action('wp_after_body');
        do_action( 'wp_footer', 'inject_script' );
        $result = "success";

     }
     else {
       $result = "fail";
     } 
  echo $result; 
  die();

}

function inject_script() {

 global $wpdb;
 
    $after_head = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "options WHERE option_name = 'nnrhf'");


    if($after_head)
    {   
      foreach($after_head as $val)
      {
        
          $unserialize_script = json_decode($val->option_value);
          
          if($unserialize_script->position == "After Body")
          {   
               //check for page:
               $page = $unserialize_script->select_page;
               if(!empty($page) && is_page($page))
                { 
                    $output  = $unserialize_script->script;
                }

                //check for post:
               $post = $unserialize_script->selected_post;
               if(!empty($post) && is_single($post))
               {
                  $output  = $unserialize_script->script;
               }

               //check for post:
               $category = $unserialize_script->selected_cat;
               if(!empty($category) && is_category($category))
               {
                  $output  = $unserialize_script->script;
               }

               //check for website:
               $website  = $unserialize_script->website;
               if(!empty($website))
               {
                $output  = $unserialize_script->script;
               }
                            
          }
          elseif($unserialize_script->position == "Before Body")
          {
               //check for page:
               $page = $unserialize_script->select_page;
               if(!empty($page) && is_page($page))
                { 
                    $output  = $unserialize_script->script;
                }

                //check for post:
               $post = $unserialize_script->selected_post;
               if(!empty($post) && is_single($post))
               {
                  $output  = $unserialize_script->script;
               }

               //check for post:
               $category = $unserialize_script->selected_cat;
               if(!empty($category) && is_category($category))
               {
                  $output  = $unserialize_script->script;
               }

               //check for website:
               $website  = $unserialize_script->website;
               if(!empty($website))
               {
                $output  = $unserialize_script->script;
               }  
              
          }elseif($unserialize_script->position == "Before Head")
          {
                //check for page:
               $page = $unserialize_script->select_page;
               if(!empty($page) && is_page($page))
                { 
                    $output  = $unserialize_script->script;
                }

                //check for post:
               $post = $unserialize_script->selected_post;
               if(!empty($post) && is_single($post))
               {
                  $output  = $unserialize_script->script;
               }

               //check for post:
               $category = $unserialize_script->selected_cat;
               if(!empty($category) && is_category($category))
               {
                  $output  = $unserialize_script->script;
               }

               //check for website:
               $website  = $unserialize_script->website;
               if(!empty($website))
               {
                $output  = $unserialize_script->script;
               }
          }
          echo $output;
                 
      }
    }
}
add_action( 'wp_after_body', 'inject_script' );
add_action( 'wp_footer', 'inject_script' );
add_action( 'wp_head', 'inject_script' );

//AJAX Actions

add_action('wp_ajax_add_scripts', 'add_scripts');
add_action('wp_ajax_hook_javascript', 'hook_javascript');