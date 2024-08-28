<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

function aicco_get_active_members_for_membership($memberships){
    global $wpdb;

    // Getting all User IDs and data for a membership plan
    return $wpdb->get_results( "
        SELECT DISTINCT um.user_id, u.user_email, u.display_name, p2.post_title, p2.post_type
        FROM {$wpdb->prefix}posts AS p
        LEFT JOIN {$wpdb->prefix}posts AS p2 ON p2.ID = p.post_parent
        LEFT JOIN {$wpdb->prefix}users AS u ON u.id = p.post_author
        LEFT JOIN {$wpdb->prefix}usermeta AS um ON u.id = um.user_id
        WHERE p.post_type = 'wc_user_membership'
        AND p.post_status IN ('wcm-active')
        AND p2.post_type = 'wc_membership_plan'
        AND p2.post_title LIKE '$memberships'
    ");
}