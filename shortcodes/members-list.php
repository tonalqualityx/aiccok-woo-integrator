<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

// Register a custom shortcode to display a list of members based on role
function members_list_shortcode( $atts ) {
    $a = shortcode_atts( array(
        'role' => 'Native Student',
    ), $atts );

    $users = aicco_get_active_members_for_membership($a['role']);

    ob_start();

    echo '<div class="aiccok-members-list">';

    foreach( $users as $user ){
        $user_meta = get_user_meta( $user->user_id ); 
        $user_data = get_userdata( $user->user_id); 

        // Check if the user has a profile photo by looking in /uploads/aicco-members/user_id/profile_photo.jpg
        $profile = false;
        $profile_photo_path = ABSPATH . 'wp-content/uploads/aiccok-members/' . $user->user_id . '/cover_photo.jpg';
        $profile_photo_path_png = ABSPATH . 'wp-content/uploads/aiccok-members/' . $user->user_id . '/cover_photo.png';

        if ( file_exists( $profile_photo_path ) ) {
            $profile = '<img src="' . site_url() . '/wp-content/uploads/aiccok-members/' . $user->user_id . '/cover_photo.jpg" alt="' . $user->display_name . ' Profile Photo" />';
        } elseif ( file_exists( $profile_photo_path_png ) ) {
            $profile = '<img src="' . site_url() . '/wp-content/uploads/aiccok-members/' . $user->user_id . '/cover_photo.png" alt="' . $user->display_name . ' Profile Photo" />';
        }

        if( isset( $user_meta['logo'] )) {

            $logo_attachment_id = $user_meta['logo'][0];
        }


        // This is an attachment ID, so we need to get the attachment and we want it in medium size for the front end
        if ( isset( $logo_attachment_id ) ) {
            $logo = wp_get_attachment_image_src( $logo_attachment_id, 'medium' );
        }
    

        $display_name = '';


        if (isset($user_meta['ai-company'][0]) && !empty($user_meta['ai-company'][0])) {
            $display_name = $user_meta['ai-company'][0];
        } else {
            $display_name = $user_data->first_name . ' ' . $user_data->last_name;
        }

        ?>

        <article class="aiccok-member">
            <!-- <a href="<?php echo get_author_posts_url($user->user_id); ?>" class=""> -->
                <?php echo $profile ? $profile : '';?>
                <h2><?php echo $display_name; ?></h2>
            <!-- </a> -->
            <p class="bio"><?php echo $user_meta['description'][0]; ?></p>
            
            <?php if ( ! empty( $user_data->user_url ) ) { ?>
                <a href="<?php echo $user_data->user_url; ?>" target="_blank" class="aiccok-button">Visit Website</a>
            <?php } ?>
        </article>
    
    <?php }

    echo '</div>';
    
    return ob_get_clean();

}
add_shortcode( 'members_list', 'members_list_shortcode' );

// Register a shortcode to show a form for editing the logged in users meta and data
function edit_user_profile_shortcode() {
    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Process form data
        $user_id = get_current_user_id();

        // Update business name (display name)
        if (isset($_POST['business_name'])) {
            $nickname = sanitize_text_field($_POST['business_name']);
            wp_update_user([
            'ID' => $user_id,
            'nickname' => $nickname,
            ]);
            wp_update_user([
            'ID' => $user_id,
            'display_name' => $nickname,
            ]);
        }

        // Update email address
        if (isset($_POST['email'])) {
            wp_update_user([
                'ID' => $user_id,
                'user_email' => sanitize_email($_POST['email']),
            ]);
        }
        
        // Update description
        if (isset($_POST['description'])) {
            update_user_meta($user_id, 'description', sanitize_text_field($_POST['description']));
        }

        // Update website URL
        if (isset($_POST['website'])) {
            wp_update_user([
                'ID' => $user_id,
                'user_url' => esc_url_raw($_POST['website']),
            ]);
        }

        // Handle logo upload
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        $uploadedfile = $_FILES['logo'];
        $upload_overrides = ['test_form' => false];
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            // File is uploaded successfully. Now save the attachment ID in user meta.
            $wp_upload_dir = wp_upload_dir();
            $filename = $movefile['file'];

            // Check the type of file. We'll use this as the 'post_mime_type'.
            $filetype = wp_check_filetype(basename($filename), 
            null);

            // Prepare an array of post data for the attachment.
            $attachment = [
                'guid'           => $wp_upload_dir['url'] . '/' . basename($filename),
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ];

            // Insert the attachment.
            $attach_id = wp_insert_attachment($attachment, $filename);

            // Make sure to update user meta with new logo attachment ID
            update_user_meta($user_id, 'logo', $attach_id);
        }
    }

    // Retrieve current user data
    $user_id = get_current_user_id();
    $user_meta = get_user_meta($user_id);
    $user_data = get_userdata($user_id);

    ob_start();
    ?>

    <form id="user_update_form" action="" method="post" enctype="multipart/form-data">
        <label for="business_name">Business Name:</label>
        <input type="text" id="business_name" name="business_name" value="<?php echo esc_attr($user_data->display_name ?? ''); ?>"><br><br>

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" value="<?php echo esc_attr($user_data->user_email ?? ''); ?>"><br><br>
        
        
        <label for="description">Value Proposition:</label>
        <input type="text" id="description" name="description" value="<?php echo esc_attr($user_meta['description'][0] ?? ''); ?>"><br><br>
        
        <label for="website">Website URL:</label>
        <input type="text" id="website" name="website" value="<?php echo esc_attr($user_data->user_url ?? ''); ?>"><br><br>
        
        <?php
        $user_logo = get_user_meta($user_id, 'logo', true);
        if ($user_logo) {
            $logo_url = wp_get_attachment_image_url($user_logo, 'thumbnail');
            echo '<img id="logo-preview" src="' . $logo_url . '" alt="Logo" />';
        }
        ?>

        <label for="logo">Logo:</label>
        <input type="file" id="logo" name="logo" onchange="previewLogo(event)"><br><br>

        <script>
        function previewLogo(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function(){
                var logoPreview = document.getElementById('logo-preview');
                logoPreview.src = reader.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
        </script>

        <div></div>
        <input type="submit" value="Update Profile">
    </form>
    <?php

    return ob_get_clean();
}
// Register the member-form shortcode
add_shortcode('user_profile', 'edit_user_profile_shortcode');
