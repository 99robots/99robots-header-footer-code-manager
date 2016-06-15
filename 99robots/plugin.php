<?php
/*
Plugin Name: 99robots
Plugin URI: http://intellywp.com/tracking-code-manager/
Description: Header Footer Scripts.
Author: Aparajita
Version: 1
*/


function nnrhf_activate() {

    do_action( 'nnr_hf_activate' );
    do_action('wp_head','inject_script');
    do_action('wp_after_body');
    do_action( 'wp_footer', 'inject_script' );
}
register_activation_hook( __FILE__, 'nnrhf_activate' );

function nnrhf_deactivation() {
 
    flush_rewrite_rules(); 
}
register_deactivation_hook( __FILE__, 'nnrhf_deactivation' );

include('form.php');