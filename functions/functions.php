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

    // First check the meta field user_$type_photo
    $profile = get_user_meta( $user->ID, $type . '_photo_id', true );


    // This will return an attaachment ID, so we need to get the image URL
    if ( $profile ) {

        $profile = wp_get_attachment_image_src( $profile, 'full' );
        
        // Create the img tag
        $profile = '<img src="' . $profile[0] . '" class="'. $type . '" alt="' . $user->display_name . ' Profile Photo" />';

    } else {

        $profile = false;
        $profile_photo_path = ABSPATH . 'wp-content/uploads/aiccok-members/' . $user->ID . '/' . $type . '_photo.jpg';
        $profile_photo_path_png = ABSPATH . 'wp-content/uploads/aiccok-members/' . $user->ID . '/' . $type . '_photo.png';

        if ( file_exists( $profile_photo_path ) ) {
            $profile = '<img src="' . site_url() . '/wp-content/uploads/aiccok-members/' . $user->ID . '/' . $type . '_photo.jpg" class="'. $type . '" alt="' . $user->display_name . ' Profile Photo" />';
        } elseif ( file_exists( $profile_photo_path_png ) ) {
            $profile = '<img src="' . site_url() . '/wp-content/uploads/aiccok-members/' . $user->ID . '/' . $type . '_photo.png" class="'. $type . '" alt="' . $user->display_name . ' Profile Photo" />';
        }

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

function aiccok_register_custom_user_meta() {
    register_meta('user', 'ai-chapter', [
        'type' => 'string',
        'description' => 'AI Chapter',
        'single' => true,
        'show_in_rest' => true,
    ]);
}
add_action('init', 'aiccok_register_custom_user_meta');

function aiccok_display_custom_user_meta( $user ) {
    $ai_chapter = get_user_meta($user->ID, 'ai-chapter', true);

    $ai_chapter = $ai_chapter ? $ai_chapter : '';
    echo '<p><label for="ai-chapter">Chapter:</label></p>';
    echo '<p><select name="ai-chapter" id="ai-chapter">';
    echo '<option value="OKC" ' . selected( $ai_chapter, 'OKC', false ) . '>OKC</option>';
    echo '<option value="Southeast" ' . selected( $ai_chapter, 'Southeast', false ) . '>Southeast</option>';
    echo '<option value="Tulsa" ' . selected( $ai_chapter, 'Tulsa', false ) . '>Tulsa</option>';
    echo '<option value="Eastern" ' . selected( $ai_chapter, 'Eastern', false ) . '>Eastern</option>';
    echo '<option value="Southwest" ' . selected( $ai_chapter, 'Southwest', false ) . '>Southwest</option>';
    echo '<option value="State" ' . selected( $ai_chapter, 'State', false ) . '>State</option>';
    echo '<option value="North Central" ' . selected( $ai_chapter, 'North Central', false ) . '>North Central</option>';
    echo '<option value="North East" ' . selected( $ai_chapter, 'North East', false ) . '>North East</option>';
    echo '</select></p>';
}
add_action('show_user_profile', 'aiccok_display_custom_user_meta');
add_action('edit_user_profile', 'aiccok_display_custom_user_meta');

function aiccok_save_custom_user_meta( $user_id ) {
    if ( !current_user_can('edit_user', $user_id) ) {
        return false;
    }

    if ( isset( $_POST['ai-chapter'] ) ) {
        update_user_meta( $user_id, 'ai-chapter', $_POST['ai-chapter'] );
    }
}
add_action('personal_options_update', 'aiccok_save_custom_user_meta');
add_action('edit_user_profile_update', 'aiccok_save_custom_user_meta');

// When a user purchases a product, check for the attribute "Chapter Designation" and update the user meta to match the chapter
function aiccok_update_user_chapter( $order_id ) {

    $order = wc_get_order( $order_id );
    $items = $order->get_items();

    foreach ( $items as $item ) {
        $product_id = $item->get_product_id();
        $product = wc_get_product( $product_id );
        $attributes = $product->get_attributes();

        if ( isset( $attributes['chapter-designation'] ) ) {
            $chapter = $item->get_meta('chapter-designation');
            $user_id = $order->get_user_id();
            update_user_meta( $user_id, 'ai-chapter', $chapter );
        }
    }
}
add_action('woocommerce_thankyou', 'aiccok_update_user_chapter', 10, 1);

// Show the ai-chapter user meta field in the user table
function aiccok_user_table_head( $columns ) {
    $columns['ai-chapter'] = 'Chapter';
    $columns['ai-company'] = 'Company Name';
    $columns['renewal-date'] = 'Renewal Date';
    return $columns;
}
add_filter('manage_users_columns', 'aiccok_user_table_head');

function aiccok_user_table_content( $value, $column_name, $user_id ) {
    if ( 'ai-chapter' == $column_name ) {
        return get_user_meta( $user_id, 'ai-chapter', true );
    }
    if ( 'ai-company' == $column_name ) {
        return get_user_meta( $user_id, 'ai-company', true );
    }
    if( 'renewal-date' == $column_name ) {
        $renewal_date = wmd_get_renewal_date( $user_id );
        if( $renewal_date ) {
            if( $renewal_date == 'Expired' ) {
                return 'Expired';
            }
            return date('F j, Y', strtotime($renewal_date));
        } else {
            return '-';
        }
    }
    return $value;
}
add_filter('manage_users_custom_column', 'aiccok_user_table_content', 10, 3);

function add_upload_capability_to_roles() {

    // Get all the roles other than "subscriber" and "administrator"
    $roles = get_editable_roles();
    $roles = array_keys($roles);

    // Loop through each role and add the capability
    foreach ($roles as $role_name) {
        $role = get_role($role_name);

        // If the role is subscriber, skip it
        if ($role_name == 'subscriber') {
            continue;
        }
        
        if ($role) {
            $role->add_cap('upload_files');
        }
    }
}
add_action('init', 'add_upload_capability_to_roles');

function wmd_get_renewal_date( $user_id ) {
    $memberships = wc_memberships_get_user_memberships( $user_id, ['status' => 'active'] );

    if ( count( $memberships ) > 0 ) {
        foreach ( $memberships as $membership ) {
            $expiration_date = $membership->get_end_date();
            if ( $expiration_date && strtotime( $expiration_date ) < time() ) {
                return 'Expired';
            }
        }
        return $expiration_date;
    }

    return false;
}