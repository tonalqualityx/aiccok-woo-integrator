<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

// TODO Modify includes as needed!


$includes = array(
    "/functions/functions.php",
    "/functions/ajax-functions.php",

    // Rest API includes
    "/api/sample-endpoint.php",

    // Shortcode Includes  
    "/shortcodes/shortcode-template.php",

    // Class Includes
    "/classes/SampleClass.php",

    // Database Includes
    //  "/database/register-tables.php",
);

// Now includes them
foreach( $includes as $include ){
    require_once( INDPL_ROOT_PATH . "/$include" );
}