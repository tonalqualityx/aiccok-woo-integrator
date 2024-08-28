<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

/* Use this class as a sample to get you started.
 * 
 * Please remove it when you no longer need it!
*/

/* 
 * This code registers a new REST API endpoint with the namespace "$projectName/v1" and route "/grail-posts". 
 * The endpoint is registered to accept only GET requests. When a GET request is made to this endpoint, 
 * the function "get_grail_posts" will be called to handle the request. This code is hooked into the 
 * "rest_api_init" action, which fires when the REST API is initialized.
 */

add_action( 'rest_api_init', function () {
    register_rest_route( 'aiccok/v1', '/test', array(
        'methods' => 'GET',
        'callback' => 'run_the_test',
    ) );
} );

function run_the_test() {

    move_um_images();    

    // transfer_memberships_wc();

}

function move_um_images() {
    
    // foreach user check in the uploads/ultimatemember folder for a folder with their user id
    // if it exists, move the contents to uploads/aiccok-members/user_id
    $users = get_users();
    foreach($users as $user) {
        $user_id = $user->ID;
        $um_dir = wp_upload_dir()['basedir'] . '/ultimatemember/' . $user_id;
        $aiccok_dir = wp_upload_dir()['basedir'] . '/aiccok-members/' . $user_id;
        if(file_exists($um_dir)) {
            if(!file_exists($aiccok_dir)) {
                mkdir($aiccok_dir, 0755, true);
            }
            $files = scandir($um_dir);
            foreach($files as $file) {
                if($file != '.' && $file != '..') {
                    rename($um_dir . '/' . $file, $aiccok_dir . '/' . $file);
                }
            }
            rmdir($um_dir);
        }
    }
}

function transfer_memberships_wc() {
    add_member_renewal_dates();
    
    $users = get_users();
    $grouped_users = array();
    $expired_members = array();
    $csv_data = array();


    foreach ($users as $user) {
        $roles = $user->roles;
        $renewal_date = get_user_meta($user->ID, 'wmd_renewal', true);
        $renewal_date_passed = (strtotime($renewal_date) < time());

        $product_slug = '';
        
        foreach ($roles as $role) {

            switch ($role) {
                case 'um_tribal':
                    $product_slug = 'tribal';
                    break;
                case 'um_non-native-individual':
                    $product_slug = 'non-native-individual';
                    break;
                case 'um_native-student':
                    $product_slug = 'native-student';
                    break;
                case 'um_native-individual':
                    $product_slug = 'native-individual';
                    break;
                case 'um_native-business':
                    $product_slug = 'native-business';
                    break;
                case 'um_custom_role_1':
                    $product_slug = 'associate-non-native';
                    break;
            }

        }

        if ( $product_slug != '') {

            $membership_plan = wc_memberships_get_membership_plan($product_slug);
            
            if ($membership_plan->id && $membership_plan->id > 0) {
                $membership = wc_memberships_create_user_membership(['user_id' => $user->id, 'plan_id' => $membership_plan->id]);
                $membership->set_end_date($renewal_date);

                if (strtotime($renewal_date) < strtotime('yesterday')) {
                    $membership->update_status('expired');
                }
            }

        }

        if (!empty($product_slug)) {
            $grouped_users[$product_slug][] = array(
            'user' => $user,
            'renewal_date_passed' => $renewal_date_passed,
            'renewal_date' => $renewal_date // Add renewal date to the array
            );
        }

        if ($renewal_date_passed) {
            $csv_data[] = array($user->ID, $user->display_name, $user->user_email, $product_slug, $renewal_date, implode(' ', $user->roles));
        }
    }

    // Export expired members to CSV
    if (!empty($csv_data)) {

        $csv_content = '';
        foreach ($csv_data as $row) {
            $csv_content .= implode(',', $row) . "\n";
        }
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="proposed-expired.csv"');
        echo $csv_content;
        exit;
    }
}

function add_member_renewal_dates() {
    $args = array(
		'role__in' => array( 'um_tribal', 'um_non-native-individual', 'um_native-student', 'um_native-individual', 'um_native-business', 'um_custom_role_1', 'um_additional-cdib-voting' )
	);
	$users = get_users($args);
	//$users = array();
	$i=1;
	$csv = array();
	$csv[0] = array('ID', 'First Name', 'Last Name', 'Email Address', 'Work Phone', 'Cell Phone', 'Billing Address', 'City', 'State', 'Zip', 'Chapter', 'Company', 'Membership', 'Date Paid', 'Next Payment Due');
	foreach($users as $user) {
		$user_address = get_user_meta($user->ID, 'ai-bill-address', true);
		$city = get_user_meta($user->ID, 'ai-bill-city', true);
		$state = get_user_meta($user->ID, 'ai-bill-state', true);
		$zip = get_user_meta($user->ID, 'ai-bill-zip', true);
		$chapter = get_user_meta($user->ID, 'ai-chapter', true);
		$company = get_user_meta($user->ID, 'ai-company', true);
		$work_phone = get_user_meta($user->ID, 'ai-work-phone', true);
		$cell_phone = get_user_meta($user->ID, 'ai-cell-phone', true);
		$date_paid = "";
		$date_next_due = "1/1/2021";
		
		
		//echo $i . ": " . $user->ID . " " . $user->first_name . " " . $user->last_name . " " . $user->user_email . " ";
		$order_args = array(
			'customer_id' => $user->ID,
			'orderby' => 'date',
			'order' => 'DESC',
			'post_status' => array('wc-processing','wc-completed')
		);
		$orders = wc_get_orders($order_args);
		if($orders) {
			//echo count($orders) . " orders / ";
			//echo date('n/j/Y', strtotime($orders[0]->get_date_paid())) . " ";
			
			$product_ids = array();
			foreach($orders as $order) {
				$line_items = $order->get_items();

                $product_slugs = array(
                    'tribal',
                    'non-native-individual',
                    'native-student',
                    'native-individual',
                    'native-business',
                    'associate-non-native',
                );

                
				foreach ( $line_items as $item ) {
                    $membership_products = array(5399, 5400, 5401, 5402, 5403, 5404, 5405);
					$product = $order->get_product_from_item( $item );
					$product_id = $product->get_id();
                    // get user for the product and add to order for lookup later
					$product_obj = get_post($product_id);	
					
					if(in_array($product_id, $membership_products) && !in_array($product_id, $product_ids)) {
                        $date_paid = date('n/j/Y', strtotime($order->get_date_paid()));
                        $date_next_due = date("n/j/Y", strtotime(date("Y-m-d", strtotime($order->get_date_paid())) . " + 1 year"));
                        update_user_meta($user->ID, 'wmd_renewal', $date_next_due);
						$product_ids[] = $product_id;
						$product_title = $product_obj->post_title;
						$csv[] = array($user->ID, $user->first_name, $user->last_name, $user->user_email, $user_address, $work_phone, $cell_phone, $city, $state, $zip, $chapter, $company, $product_title, $date_paid, $date_next_due);
					}
				}
			}
		}
    }
}