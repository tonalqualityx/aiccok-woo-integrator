<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

// TODO Modify includes as needed!


$includes = array(
    "/functions/functions.php",
    "/functions/ajax-functions.php",

    "/includes/enqueues.php",
    "/includes/user-fields.php",

    // Rest API includes
    "/api/test.php",

    // Shortcode Includes  
    "/shortcodes/members-list.php",
    "/shortcodes/listing-management.php",

    // Class Includes
    // "/classes/SampleClass.php",

    // Database Includes
    //  "/database/register-tables.php",
);

// Now includes them
foreach( $includes as $include ){
    require_once( INDPL_ROOT_PATH . "/$include" );
}