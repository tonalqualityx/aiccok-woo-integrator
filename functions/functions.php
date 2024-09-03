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

function aiccok_has_active_membership( $user_id ) {
    $memberships = wc_memberships_get_user_memberships( $user_id, ['status' => 'active'] );

    if ( count( $memberships ) > 0 ) {
        foreach ( $memberships as $membership ) {
            $expiration_date = $membership->get_end_date();
            if ( $expiration_date && strtotime( $expiration_date ) < time() ) {
                return false;
            }
        }
        return true;
    }

    return false;
}

function aiccok_get_display_name( $user_meta) {
    
    if (isset($user_meta['ai-company'][0]) && !empty($user_meta['ai-company'][0])) {
        $display_name = $user_meta['ai-company'][0];
    } else {
        $display_name = $user_data->first_name . ' ' . $user_data->last_name;
    }

    return $display_name;
}

function aiccok_get_profile( $user, $type = "cover" ) {

    $profile = false;
    $profile_photo_path = ABSPATH . 'wp-content/uploads/aiccok-members/' . $user->ID . '/' . $type . '_photo.jpg';
    $profile_photo_path_png = ABSPATH . 'wp-content/uploads/aiccok-members/' . $user->ID . '/' . $type . '_photo.png';

    if ( file_exists( $profile_photo_path ) ) {
        $profile = '<img src="' . site_url() . '/wp-content/uploads/aiccok-members/' . $user->ID . '/' . $type . '_photo.jpg" class="'. $type . '" alt="' . $user->display_name . ' Profile Photo" />';
    } elseif ( file_exists( $profile_photo_path_png ) ) {
        $profile = '<img src="' . site_url() . '/wp-content/uploads/aiccok-members/' . $user->ID . '/' . $type . '_photo.png" class="'. $type . '" alt="' . $user->display_name . ' Profile Photo" />';
    }

    return $profile;
}

function aiccok_get_user_logo( $user_meta ) {

    $logo = false;

    if( isset( $user_meta['logo'] )) {
        $logo_attachment_id = $user_meta['logo'][0];
    }

    // This is an attachment ID, so we need to get the attachment and we want it in medium size for the front end
    if ( isset( $logo_attachment_id ) ) {
        $logo = wp_get_attachment_image_src( $logo_attachment_id, 'medium' );
    }

    return $logo;
}