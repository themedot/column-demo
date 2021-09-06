<?php
/*
Plugin Name: Column demo
Plugin URI: http://example.com/
Description: 
Version: 1.0
Author: himel
Author URI: http://example.com/
License: GPLv2 or later
Text Domain: 
Domain Path: /languages
*/

function coldemo_bootstrap(){
    load_plugin_textdomain("column-demo",false,dirname(__FILE__)."/languages");
}
add_action( 'plugin_loaded', 'coldemo_bootstrap' );