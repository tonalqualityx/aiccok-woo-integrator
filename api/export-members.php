<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

// Add an endpoing called aicco-memberships
add_action( 'rest_api_init', 'aiccok_memberships_endpoint' );
function aiccok_memberships_endpoint() {
    register_rest_route( 'aiccok/v1', '/export-members', array(
        'methods' => 'GET',
        'callback' => 'aiccok_memberships_callback',
        'permission_callback' => function () {
            return current_user_can( 'edit_others_posts' );
        }
    ));
}

function aiccok_memberships_callback( $data ) {

    return json_encode(wmd_get_all_members());
}