<?php
/* TODO Change the name & Description
 * Plugin Name: AICCOK
 * Plugin URI: https://whoismikedion.com
 * Description: Powers the membership shortcodes and creates endpoints for transition from Ultimate Member to WooCommerce
 * Author: Mike Dion
 * Version: 1.0.0
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

// TODO Modify includes as needed!
// Function includes
require_once( INDPL_ROOT_PATH . "/functions/functions.php" );
require_once( INDPL_ROOT_PATH . "/functions/ajax-functions.php" );

// Rest API includes
require_once( INDPL_ROOT_PATH . "/api/sample-endpoint.php" );

// Shortcode Includes
require_once( INDPL_ROOT_PATH . "/shortcodes/shortcode-template.php" );

// Class Includes
require_once( INDPL_ROOT_PATH . "/classes/SampleClass.php" );

// Database Includes
// require_once( INDPL_ROOT_PATH . "/database/register-tables.php" );


function indpl_enqueue(){
    wp_enqueue_style( 'indpl-style', INDPL_ROOT_URL . 'css/style.min.css', array(), INDPL_VERSION );
    wp_register_script( 'indpl-js', INDPL_ROOT_URL . 'js/app.min.js', array( 'jquery' ), INDPL_VERSION );
    wp_localize_script( 'indpl-js', 'indpl_ajax',
       array(
          'ajaxurl' => admin_url( 'admin-ajax.php' ),
          'nonce' => wp_create_nonce( 'INDPL_nonce', 'security' ),
       )
    );
    wp_enqueue_script( 'indpl-js' );
  }
  add_action( 'wp_enqueue_scripts', 'indpl_enqueue' );