<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

function aiccok_single_profile_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => ''
    ), $atts);

    $user_id = isset($_GET['id']) ? $_GET['id'] : $atts['id'];
    $user_data = get_userdata($user_id);
    $user_meta = get_user_meta($user_id);

    $display_name = aiccok_get_display_name($user_meta);
    $profile = aiccok_get_profile($user_data);
    
    $address_1 = !empty($user_meta['billing_address_1'][0]) ? $user_meta['billing_address_1'][0] . "<br>" : '';
    $address_2 = !empty($user_meta['billing_address_2'][0]) ? $user_meta['billing_address_2'][0] . "<br>" : '';
    $city = !empty($user_meta['billing_city'][0]) ? $user_meta['billing_city'][0] . ", " : '';
    $state = !empty($user_meta['billing_state'][0]) ? $user_meta['billing_state'][0] . " " : '';
    $postcode = !empty($user_meta['billing_postcode'][0]) ? $user_meta['billing_postcode'][0] : '';

    $phone_number = !empty($user_meta['phone_number'][0]) ? $user_meta['phone_number'][0] : '';

    $address = $address_1 . $address_2 . $city . $state . $postcode;

    $logo = aiccok_get_profile($user_data, 'profile');

    ob_start();
    ?>
    <article class="aiccok-member single">
        <?php echo $profile ? $profile : ''; ?>
        <div class="flex">
            <?php if($logo) { ?>
                <?php echo $logo; ?>
            <?php } ?>
            <h2><?php echo $display_name; ?></h2>
        </div>
        <div class="">
        <p class="bio"><?php echo $user_meta['description'][0]; ?></p>
        

        <div class="address">
            <?php if( !empty($address) ) { ?>
                <p><?php echo $address; ?></p>
            <?php } ?>
        </div>

        <?php if (!empty($phone_number)) { ?>
            <p class="phone"><?php echo $phone_number; ?></p>
        <?php } ?>

        <?php if (!empty($user_data->user_url)) { ?>
            <a href="<?php echo $user_data->user_url; ?>" target="_blank" class="aiccok-button">Visit Website</a>
        <?php } ?>

    </article>
    <?php
    return ob_get_clean();
}
add_shortcode('aiccok_single_profile', 'aiccok_single_profile_shortcode');