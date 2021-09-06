<?php
/*
Plugin Name: Column demo
Plugin URI: http://example.com/
Description: 
Version: 1.0
Author: himel
Author URI: http://example.com/
License: GPLv2 or later
Text Domain: column-demo
Domain Path: /languages
*/

function coldemo_bootstrap(){
    load_plugin_textdomain("column-demo",false,dirname(__FILE__)."/languages");
}
add_action( 'plugin_loaded', 'coldemo_bootstrap' );

function coldemo_post_columns($columns){
    print_r($columns);
    unset($columns['tags']);
    unset($columns['comments']);
    // unset($columns['author']);
    // unset($columns['categories']);
    // unset($columns['date']);
    // $columns['date'] = 'Date';
    // $columns['comments'] = 'Comments';
    $columns['id'] = __('Post ID','column-demo');
    return $columns;
}
add_filter("manage_posts_columns","coldemo_post_columns");


function coldemo_post_column_data($column,$post_id){
    echo $post_id;
}
add_action( 'manage_posts_custom_column', 'coldemo_post_column_data', 10, 2 );





























