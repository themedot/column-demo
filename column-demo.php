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

    function coldemo_update_wordcount_on_save_post( $post_id ) {
        $p       = get_post( $post_id );
        $content = $p->post_content;
        $wordn   = str_word_count( strip_tags( $content ) );
        update_post_meta( $p->ID, 'wordn', $wordn );
    }
    add_action( 'save_post', 'coldemo_update_wordcount_on_save_post' );

    function coldemo_filter() {
        if ( isset( $_GET['post_type'] ) && $_GET['post_type'] != 'post' ) {
            return;
        }
        $filter_value = isset( $_GET['DEMOFILTER'] ) ? $_GET['DEMOFILTER'] : '';
        $values       = [
            '0' => __( 'Select Status', 'column-demo' ),
            '1' => __( 'Some Posts', 'column-demo' ),
            '2' => __( 'Some Post++', 'column-demo' ),
        ]
    ?>
        <select name="DEMOFILTER">
            <?php
                foreach ( $values as $key => $value ) {
                        printf( "<option value='%s' %s>%s</option>", $key,
                            $key == $filter_value ? "selected = selected" : '',
                            $value
                        );
                    }
                ?>
        </select>
    <?php
        }
        add_action( 'restrict_manage_posts', 'coldemo_filter' );

        function coldmo_filter_data( $wpquery ) {
            if ( !is_admin() ) {
                return;
            }
            $filter_value = isset( $_GET['DEMOFILTER'] ) ? $_GET['DEMOFILTER'] : '';

            if ( '1' == $filter_value ) {
                $wpquery->set( 'post__in', [ 650, 643, 575 ] );
            } else if ( '2' == $filter_value ) {
                $wpquery->set( 'post__in', [ 1, 416, 472 ] );
            }
        }
    add_action( 'pre_get_posts', 'coldmo_filter_data' );


    function coldemo_thumbnail_filter() {
        if ( isset( $_GET['post_type'] ) && $_GET['post_type'] != 'post' ) {
            return;
        }
        $filter_value = isset( $_GET['THUMBNAIL'] ) ? $_GET['THUMBNAIL'] : '';
        $values       = [
            '0' => __( 'Select thumbnail', 'column-demo' ),
            '1' => __( 'Has Thumbnail', 'column-demo' ),
            '2' => __( 'No Thumbnail', 'column-demo' ),
        ]
    ?>
        <select name="THUMBNAIL">
            <?php
                foreach ( $values as $key => $value ) {
                        printf( "<option value='%s' %s>%s</option>", $key,
                            $key == $filter_value ? "selected = selected" : '',
                            $value
                        );
                    }
                ?>
        </select>
     <?php
        }
        add_action( 'restrict_manage_posts', 'coldemo_thumbnail_filter' );

        function coldmo_thumbnail_filter_data( $wpquery ) {
            if ( !is_admin() ) {
                return;
            }
            $filter_value = isset( $_GET['THUMBNAIL'] ) ? $_GET['THUMBNAIL'] : '';
            $wpquery-> set('posts_per_page',5);
            if ( '1' == $filter_value ) {
                $wpquery->set( 'meta_query',array(
                    array(
                        'key' => '_thumbnail_id',
                        'compare' => 'EXISTS'
                    )
                ) );
            } else if ( '2' == $filter_value ) {
                $wpquery->set( 'meta_query',array(
                    array(
                        'key' => '_thumbnail_id',
                        'compare' => 'NOT EXISTS'
                    )
                ) );
            }
        }
    add_action( 'pre_get_posts', 'coldmo_thumbnail_filter_data' );



    function coldemo_word_count_filter() {
        if ( isset( $_GET['post_type'] ) && $_GET['post_type'] != 'post' ) {
            return;
        }
        $filter_value = isset( $_GET['WCFILTER'] ) ? $_GET['WCFILTER'] : '';
        $values       = [
            '0' => __( 'Word count', 'column-demo' ),
            '1' => __( 'Above 400', 'column-demo' ),
            '2' => __( '200 to 400', 'column-demo' ),
            '3' => __( 'Below 200', 'column-demo' ),
        ]
     ?>
        <select name="WCFILTER">
            <?php
                foreach ( $values as $key => $value ) {
                        printf( "<option value='%s' %s>%s</option>", $key,
                            $key == $filter_value ? "selected = selected" : '',
                            $value
                        );
                    }
                ?>
        </select>
    <?php
        }
        add_action( 'restrict_manage_posts', 'coldemo_word_count_filter' );


        function coldmo_wc_filter_data( $wpquery ) {
            if ( !is_admin() ) {
                return;
            }
            $filter_value = isset( $_GET['WCFILTER'] ) ? $_GET['WCFILTER'] : '';

            if ( '1' == $filter_value ) {
                $wpquery->set( 'meta_query',array(
                    array(
                        'key' => 'wordn',
                        'value' => 400,
                        'compare' => '>=',
                        'type' => 'NUMERIC'
                    )
                ) );
            } else if ( '2' == $filter_value ) {
                $wpquery->set( 'meta_query',array(
                    array(
                        'key' => 'wordn',
                        'value' => array(200,400),
                        'compare' => 'BETWEEN',
                        'type' => 'NUMERIC'
                    )
                ) );
            } else if ( '3' == $filter_value ) {
                $wpquery->set( 'meta_query',array(
                    array(
                        'key' => 'wordn',
                        'value' => 200,
                        'compare'=> '<=',
                        'type' => 'NUMERIC'
                    )
                ) );
            }
        }
    add_action( 'pre_get_posts', 'coldmo_wc_filter_data' );