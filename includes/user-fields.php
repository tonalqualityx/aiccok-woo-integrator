<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

// Using standard WordPress, register a custom "logo" field for users
function indpl_user_fields( $user ) {
    // Retrieve the current logo value
    $logo = get_the_author_meta( 'logo', $user->ID );
    ?>
    <h3><?php _e('Extra Profile Information', 'indpl'); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="logo"><?php _e('Logo', 'indpl'); ?></label></th>
            <td>
                <?php
                $image_url = wp_get_attachment_url( $logo );
                ?>
                <input type="hidden" name="logo" id="logo" value="<?php echo esc_attr( $logo ); ?>" />
                <input type="button" id="upload_logo_button" class="button" value="<?php _e('Upload Logo', 'indpl'); ?>" />
                <br />
                <span class="description"><?php _e('Please upload your logo.', 'indpl'); ?></span>
                <br />
                <img id="logo_preview" src="<?php echo esc_url( $image_url ); ?>" style="max-width: 200px; height: auto;" />
            </td>
        </tr>
    </table>
    <script>
        jQuery(document).ready(function($) {
            // Media upload button click event
            $('#upload_logo_button').click(function(e) {
                e.preventDefault();
                var customUploader = wp.media({
                    title: '<?php _e('Upload Logo', 'indpl'); ?>',
                    button: {
                        text: '<?php _e('Select Logo', 'indpl'); ?>'
                    },
                    multiple: false
                });
                customUploader.on('select', function() {
                    var attachment = customUploader.state().get('selection').first().toJSON();
                    $('#logo').val(attachment.id);
                    $('#logo_preview').attr('src', attachment.url);
                });
                customUploader.open();
            });
        });
    </script>
    <?php
}

// Hook into the user profile display and edit screens
add_action( 'show_user_profile', 'indpl_user_fields' );
add_action( 'edit_user_profile', 'indpl_user_fields' );

// Save the custom "logo" field when the user saves
function indpl_save_user_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return;
    }

    if ( isset( $_POST['logo'] ) ) {
        // Sanitize and save the logo value
        $logo = sanitize_text_field( $_POST['logo'] );
        update_user_meta( $user_id, 'logo', $logo );
    }
}
add_action( 'personal_options_update', 'indpl_save_user_fields' );
add_action( 'edit_user_profile_update', 'indpl_save_user_fields' );