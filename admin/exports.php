<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

// When the form is submitted, export the members as a CSV file. Be sure to sanitize the data before outputting it. And be sure to follow wordpress best practices


// Create an admin page called exports
function indpl_exports_page(){
    
    ?>
    <div class="wrap">
        <h1>Exports</h1>
        <p>Export data from the AICCOK website</p>
        <form id="export-voting-members" action="" method="post">
            <input type="submit" name="export" value="Export Members for Voting">
        </form>

        <?php 

        var_dump( wmd_get_all_active_memberships() );        
        
        ?>
    </div>
    <?php

}

// Add the admin page to the admin menu
function indpl_add_exports_page(){
    add_menu_page( 'Exports', 'Exports', 'manage_options', 'indpl-exports', 'indpl_exports_page', 'dashicons-download', 6 );
}
add_action( 'admin_menu', 'indpl_add_exports_page' );


add_action( 'admin_init', 'export_members_csv' );
function export_members_csv() {

    // Check if the form was submitted
    if( !isset($_POST['export']) ){
        return;
    }

    $memberships = wmd_get_all_members(); 
       
        // Format and print the membership array as csv data in a string
        $csv = '';

        // Add headers to the CSV
        $csv .= 'First Name,Last Name,Company Name,Membership Type,Membership Status,Membership Expiration Date,Chapter Designation,Additional Voting Membership' . "\n";

        foreach( $memberships as $membership ){
            $csv .= '"' . $membership['First Name'] . '",';
            $csv .= '"' . $membership['Last Name'] . '",';
            $csv .= '"' . $membership['Company Name'] . '",';
            $csv .= '"' . $membership['Membership Type'] . '",';
            $csv .= '"' . $membership['Membership Status'] . '",';
            $csv .= '"' . $membership['Membership Expiration Date'] . '",';
            $csv .= '"' . $membership['Chapter Designation'] . '",';
            // $csv .= '"' . $membership['Additional Voting Membership'] . '",';
            $csv .= "\n";
        }

        // echo $csv;

        //download the CSV file
        if( isset($_POST['export']) ){
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="voting-members.csv"');
            echo $csv;
            exit();
        }
}