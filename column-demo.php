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

function coldemo_bootstrap() {
    load_plugin_textdomain( "column-demo", false, dirname( __FILE__ ) . "/languages" );
}
add_action( 'plugin_loaded', 'coldemo_bootstrap' );

function coldemo_post_columns( $columns ) {
    print_r( $columns );
    unset( $columns['tags'] );
    unset( $columns['comments'] );
    // unset($columns['author']);
    // unset($columns['categories']);
    // unset($columns['date']);
    // $columns['date'] = 'Date';
    // $columns['comments'] = 'Comments';
    $columns['id']        = __( 'Post ID', 'column-demo' );
    $columns['thumbnail'] = __( 'Thumbnail', 'column-demo' );
    $columns['wordcount'] = __( 'Word Count', 'column-demo' );
    return $columns;
}
add_filter( "manage_posts_columns", "coldemo_post_columns" );
add_filter( "manage_pages_columns", "coldemo_post_columns" );

function coldemo_post_column_data( $column, $post_id ) {
    if ( 'id' == $column ) {
        echo $post_id;
    } elseif ( 'thumbnail' == $column ) {
        $thumbnail = get_the_post_thumbnail( $post_id, [100, 100] );
        echo $thumbnail;
    } elseif ( 'wordcount' == $column ) {
        // $_post = get_post($post_id);
        // $content = $_post->post_content;
        // $wordn = str_word_count(strip_tags($content));
        $wordn = get_post_meta( $post_id, 'wordn', true );
        echo $wordn;
    }
}
add_action( 'manage_posts_custom_column', 'coldemo_post_column_data', 10, 2 );
add_action( 'manage_pages_custom_column', 'coldemo_post_column_data', 10, 2 );

function coldemo_sortable_column( $columns ) {
    $columns['wordcount'] = 'wordn';
    return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'coldemo_sortable_column' );

// function coldemo_set_word_count() {
//     $posts = get_posts( [
//         'posts_per_page' => -1,
//         'post_type'      => 'post',
//         'post_status'    => 'any',
//     ] );
//     foreach ( $posts as $p ) {
//         $content = $p->post_content;
//         $wordn   = str_word_count( strip_tags( $content ) );
//         update_post_meta( $p->ID, 'wordn', $wordn );
//     }
// }
// add_action( 'init', 'coldemo_set_word_count' );

function coldemo_sort_column_data( $wpquery ) {
    if ( !is_admin() ) {
        return;
    }
    $orderby = $wpquery->get( 'orderby' );
    if ( 'wordn' == $orderby ) {
        $wpquery->set( 'meta_key', 'wordn' );
        $wpquery->set( 'orderby', 'meta_value_num' );
    }
}
add_action( 'pre_get_posts', 'coldemo_sort_column_data' );

function coldemo_update_wordcount_on_save_post($post_id){
    $p = get_post($post_id);
        $content = $p->post_content;
        $wordn   = str_word_count( strip_tags( $content ) );
        update_post_meta( $p->ID, 'wordn', $wordn );
}
add_action( 'save_post', 'coldemo_update_wordcount_on_save_post' );



