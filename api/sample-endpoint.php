<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

/* Use this class as a sample to get you started.
 * 
 * Please remove it when you no longer need it!
*/

/* 
 * This code registers a new REST API endpoint with the namespace "indelible/v1" and route "/grail-posts". 
 * The endpoint is registered to accept only GET requests. When a GET request is made to this endpoint, 
 * the function "get_grail_posts" will be called to handle the request. This code is hooked into the 
 * "rest_api_init" action, which fires when the REST API is initialized.
 */

add_action( 'rest_api_init', function () {
    register_rest_route( 'indelible/v1', '/grail-posts', array(
        'methods' => 'GET',
        'callback' => 'get_grail_posts',
    ) );
} );

function get_grail_posts() {
    $args = array(
        'meta_query' => array(
            array(
                'key' => 'Quest',
                'value' => 'seek the grail',
                'compare' => '=',
            ),
        ),
        'post_type' => 'post',
        'posts_per_page' => -1,
    );

    $posts = get_posts( $args );

    $results = array();

    foreach ( $posts as $post ) {
        $result = array(
            'title' => $post->post_title,
            'author' => get_the_author_meta( 'display_name', $post->post_author ),
            'content' => $post->post_content,
            'featured_image' => get_the_post_thumbnail_url( $post ),
        );

        $results[] = $result;
    }

    return $results;
}
