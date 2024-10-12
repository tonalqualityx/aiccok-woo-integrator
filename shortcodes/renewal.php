<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

function aiccok_renewal_shortcode() {
    // Check if user is logged in
    if (is_user_logged_in()) {
        // Get the current user
        $user = wp_get_current_user();

        
        // Get the active membership for the user
        $membership = wc_memberships_get_user_memberships($user->ID, ['status' => 'active']);
    
        if( count($membership) == 0 ) {
            // Redirect the user to the membership page
            return "<p>You do not have an active membership. Please <a href='/membership'>purchase a membership</a> to access this content.</p>";
        }
        
        // Check if the user has an active membership
        if ($membership) {

            $membership_name = "Unknown Membership - Please contact support";

            // Get the membership name
            foreach( $membership as $m ){
                // Check if the membership has a plan
                if( $m->get_plan() ){
                    $membership_name = $m->get_plan()->get_name();
                }
            }
            
            // Get the membership renewal date
            $renewal_date = '2024-12-31';
            $renewal_date = $membership[0]->get_end_date();
            
            // Format the renewal date
            $renewal_date_formatted = date('F j, Y', strtotime($renewal_date));
            
            // Output the membership details
            $output = '<h2>Membership: ' . $membership_name . '</h2>';
            // Check if the renewal date is in the past
            if (strtotime($renewal_date) < time()) {
                $output .= '<p><strong>Renewal Date:</strong> ' . $renewal_date_formatted . ' (Expired)</p>';
            } else {
                $output .= '<p><strong>Renewal Date:</strong> ' . $renewal_date_formatted . '</p>';
            }
            $output .= '<a href="/membership/" class="aiccok-button">Renew Now</a>';
            
            return $output;
        } else {
            return 'No active membership found.';
        }
    } else {
        return 'Please log in to view membership details.';
    }
}
add_shortcode('aiccok_renewal', 'aiccok_renewal_shortcode');