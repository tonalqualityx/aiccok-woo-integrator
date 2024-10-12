<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

function aiccok_listing_management_shortcode() {

    if( !is_user_logged_in() ){
        $output = '<p>You must be <a href="' . wp_login_url() . '">logged in</a> to view this page.</p>';
        $output .= wp_login_form(array(
            'redirect' => $_SERVER['REQUEST_URI'],
            'label_username' => 'Username',
            'label_password' => 'Password',
            'label_remember' => 'Remember Me',
            'label_log_in' => 'Log In',
            'form_id' => 'login-form-account',
            'id_username' => 'user-login',
            'id_password' => 'user-pass',
            'id_remember' => 'rememberme',
            'id_submit' => 'wp-submit',
            'remember' => true,
            'value_username' => '',
            'value_remember' => false
        ));
        return $output;
    }

    // Check if the user has an active membership
    if (!aiccok_has_active_membership($user_id)) {
        $output = '<p>You need to renew your membership in order to edit your directory listing.</p>';
        return $output;
    }

    $user_id = get_current_user_id();
    $user_data = get_userdata($user_id);
    $user_meta = get_user_meta($user_id);


    // Check if the user has a cover photo by looking in /uploads/aicco-members/user_id/cover.jpg
    $saved_cover_photo = aiccok_get_profile($user_data, 'cover');

    // Check if the user has a profile photo by looking in /uploads/aicco-members/user_id/profile_photo.jpg
    $saved_profile_photo = aiccok_get_profile($user_data, 'profile');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $display_name = isset($_POST['display_name']) ? sanitize_text_field($_POST['display_name']) : '';
        $description = isset($_POST['description']) ? sanitize_text_field($_POST['description']) : '';
        $logo = isset($_FILES['logo']) ? $_FILES['logo']['name'] : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $website = isset($_POST['website']) ? esc_url($_POST['website']) : '';
        $phone_number = isset($_POST['phone_number']) ? sanitize_text_field($_POST['phone_number']) : '';
        $address = isset($_POST['address']) ? sanitize_text_field($_POST['address']) : '';
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        $state = isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '';
        $postcode = isset($_POST['postcode']) ? sanitize_text_field($_POST['postcode']) : '';
        $country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';
        $company = isset($_POST['company']) ? sanitize_text_field($_POST['company']) : '';

        // Update user meta data
        update_user_meta($user_id, 'display_name', $display_name);
        update_user_meta($user_id, 'description', $description);
        update_user_meta($user_id, 'logo', $logo);
        update_user_meta($user_id, 'phone_number', $phone_number);
        update_user_meta($user_id, 'billing_address_1', $address);
        update_user_meta($user_id, 'billing_city', $city);
        update_user_meta($user_id, 'billing_state', $state);
        update_user_meta($user_id, 'billing_postcode', $postcode);
        update_user_meta($user_id, 'billing_country', $country);
        update_user_meta($user_id, 'ai-company', $company);
    
        // Update user data
        $user_data->user_email = $email;
        $user_data->user_url = $website;
        wp_update_user($user_data);

        // Handle profile photo upload
        if (!empty($_FILES['profile_photo']['tmp_name'])) {

            $upload_dir = wp_upload_dir();
            $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
            $fileName = $_FILES['profile_photo']['name'];
            $fileSize = $_FILES['profile_photo']['size'];
            $fileType = $_FILES['profile_photo']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Generate a unique file name
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Directory in which the uploaded file will be moved
            $dest_path = $upload_dir['path'] . '/' . $newFileName;
            $dest_url = $upload_dir['url'] . '/' . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Create the attachment data
                $attachment = array(
                    'guid'           => $dest_url,
                    'post_mime_type' => $fileType,
                    'post_title'     => sanitize_file_name($newFileName),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );

                // Insert the attachment into the media library
                $attachment_id = wp_insert_attachment($attachment, $dest_path);

                // Generate the attachment metadata
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $dest_path);

                // Update the attachment metadata
                wp_update_attachment_metadata($attachment_id, $attachment_data);

                // Save the attachment ID to user meta
                update_user_meta(get_current_user_id(), 'profile_photo_id', $attachment_id);
            }
        }


        // Handle profile photo upload
        if (!empty($_FILES['cover_photo']['tmp_name'])) {
            $upload_dir = wp_upload_dir();
            $fileTmpPath = $_FILES['cover_photo']['tmp_name'];
            $fileName = $_FILES['cover_photo']['name'];
            $fileSize = $_FILES['cover_photo']['size'];
            $fileType = $_FILES['cover_photo']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Generate a unique file name
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Directory in which the uploaded file will be moved
            $dest_path = $upload_dir['path'] . '/' . $newFileName;
            $dest_url = $upload_dir['url'] . '/' . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Create the attachment data
                $attachment = array(
                    'guid'           => $dest_url,
                    'post_mime_type' => $fileType,
                    'post_title'     => sanitize_file_name($newFileName),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );

                // Insert the attachment into the media library
                $attachment_id = wp_insert_attachment($attachment, $dest_path);

                // Generate the attachment metadata
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $dest_path);

                // Update the attachment metadata
                wp_update_attachment_metadata($attachment_id, $attachment_data);

                // Save the attachment ID to user meta
                update_user_meta(get_current_user_id(), 'cover_photo_id', $attachment_id);
            }
        }

        // Redirect to the same page to prevent form resubmission
        wp_redirect($_SERVER['REQUEST_URI']);
        exit;
    }

    
    $address = isset($user_meta['billing_address_1'][0]) ? $user_meta['billing_address_1'][0] : '';
    $city = isset($user_meta['billing_city'][0]) ? $user_meta['billing_city'][0] : '';
    $state = isset($user_meta['billing_state'][0]) ? $user_meta['billing_state'][0] : '';
    $postcode = isset($user_meta['billing_postcode'][0]) ? $user_meta['billing_postcode'][0] : '';
    $country = isset($user_meta['billing_country'][0]) ? $user_meta['billing_country'][0] : '';
    $phone_number = isset($user_meta['billing_phone'][0]) ? $user_meta['billing_phone'][0] : '';

    $description = isset($user_meta['description'][0]) ? $user_meta['description'][0] : '';

    $cover_photo = wp_upload_dir()['baseurl'] . '/aicco-members/' . $user_id . '/cover_photo.jpg';
    $profile_photo = wp_upload_dir()['baseurl'] . '/aicco-members/' . $user_id . '/profile_photo.jpg';

    
    $email = $user_data->user_email;
    $website = $user_data->user_url;

    $output = '<div id="listing-management" class="aiccok-listing-management">';
    $output .= '<h2>Directory Listing</h2>';
    $output .= '<form method="post" action="" enctype="multipart/form-data">';
    $output .= '<label for="display_name"><span>Display Name:</span> <input type="text" name="display_name" value="' . $user_data->display_name . '"></label><br>';

    $company = isset($user_meta['ai-company'][0]) ? $user_meta['ai-company'][0] : '';
    
    $output .= '<label for="company"><span>Company/Organization:</span> <input type="text" name="company" value="' . $company . '"></label><br>';
    $output .= '<label for="description"><span>Description:</span> <textarea name="description">' . $description . '</textarea></label><br>';
    $output .= '<label for="email"><span>Email:</span> <input type="email" name="email" value="' . $email . '"></label><br>';
    $output .= '<label for="website"><span>Website:</span> <input type="url" name="website" value="' . $website . '"></label><br>';
    $output .= '<label for="phone_number"><span>Phone Number:</span> <input type="tel" name="phone_number" value="' . $phone_number . '"></label><br>';
    $output .= '<label for="address"><span>Address:</span> <input type="text" name="address" value="' . $address . '"></label><br>';
    $output .= '<label for="city"><span>City:</span> <input type="text" name="city" value="' . $city . '"></label><br>';
    $output .= '<label for="state"><span>State:</span> <input type="text" name="state" value="' . $state . '"></label><br>';
    $output .= '<label for="postcode"><span>Postcode:</span> <input type="text" name="postcode" value="' . $postcode . '"></label><br>';
    $output .= '<label for="country"><span>Country:</span> <input type="text" name="country" value="' . $country . '"></label><br>';
    
    // Check if profile photo exists and display it
    // if ($profile_photo) {
    //     $output .= '<p>Profile Photo:</p>';
    //     $output .= '<img src="' . $profile_photo . '" alt="Profile Photo">';
    // }
    // Add JavaScript to handle the media library
    ob_start();
    ?>
    <!-- <label for="profile_photo"><span>Current Profile Photo:</span>
        <?php echo wp_get_attachment_image(get_user_meta($user_id, 'profile_photo_id', true), 'thumbnail'); ?>
        <br>
        <input type="file" name="profile_photo" class="upload-profile-photo" value="Upload Profile Photo" accept=".jpg, .jpeg, .png">
    </label><br> -->

    <?php

    // Check if cover photo exists and display it
    if ($saved_cover_photo) { ?>
        <p>Current Cover Photo:</p>
        <?php echo $saved_cover_photo;
    } ?>

    <label for="cover_photo"><span>Cover Photo:</span> <input type="file" name="cover_photo" accept=".jpg, .jpeg, .png" value="Upload New"></label><br>
    <input type="submit" value="Save">
    </form>
    </div>
    <?php
    $output .= ob_get_clean();

    return $output;
}
add_shortcode('aiccok-listing-management', 'aiccok_listing_management_shortcode');