<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

/* Create ONE shortcode per file. 
 * Each shortcode should be included on the main file. This keeps things organized. My word, it keeps them organized.
 * 
 * Here's a sample to get you started. You can delete this file (and remove the include) when you're ready.
 * 
*/

function template_shortcode( $atts ) {
    $a = shortcode_atts( array(
        'in' => '',
        'out' => '',
    ), $atts );
    
    return $a['in'] . $a['out'];
}
add_shortcode( 'template', 'template_shortcode' );
