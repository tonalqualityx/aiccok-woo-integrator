<?php
/* 
 * Plugin Name: AICCOK Woo Integrator
 * Plugin URI: https://whoismikedion.com
 * Description: Integrates the existing AICCOK website with WooCommerce & provides shortcodes for membership display
 * Author: Mike Dion
 * Version: 1.0.8
 * Author URI: https://whoismikedion.com
 * License: GPL2+
 */

defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

if( !function_exists('get_plugin_data') ){
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// TODO UPDATE THE CONSTANT!
define( 'INDPL_PLUGIN', __FILE__ );
define( 'INDPL_ROOT_PATH', plugin_dir_path(__FILE__) );
define( 'INDPL_ROOT_URL', plugin_dir_url(__FILE__) );

$plugin_data = get_plugin_data( __FILE__ );
$version_number = $plugin_data['Version'];

define( 'INDPL_VERSION', $version_number );

require_once( INDPL_ROOT_PATH . '/includes/includes.php' );

// Turn off PHP warnings and errors
error_reporting(0);
ini_set('display_errors', 0);