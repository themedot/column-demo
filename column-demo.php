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
    $columns['thumbnail'] = __('Thumbnail','column-demo');
    $columns['wordcount'] = __('Word Count','column-demo');
    return $columns;
}
add_filter("manage_posts_columns","coldemo_post_columns");
add_filter("manage_pages_columns","coldemo_post_columns");


function coldemo_post_column_data($column,$post_id){
    if('id' == $column){
        echo $post_id;
    }elseif('thumbnail' == $column){
        $thumbnail = get_the_post_thumbnail($post_id,array(100,100));
        echo $thumbnail;
    }elseif('wordcount' == $column){
        $_post = get_post($post_id);
        $content = $_post->post_content;
        $wordn = str_word_count(strip_tags($content));
        echo $wordn;
    }
}
add_action( 'manage_posts_custom_column', 'coldemo_post_column_data', 10, 2 );
add_action( 'manage_pages_custom_column', 'coldemo_post_column_data', 10, 2 );


function codemo_sortable_column($columns){
    $columns['wordcount']='wordn';
    return $columns;
}

add_filter( 'manage_edit-post_sortable_columns', 'codemo_sortable_column' ); 





























