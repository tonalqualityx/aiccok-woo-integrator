<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

function indpl_enqueue(){
    wp_enqueue_style( 'indpl-style', INDPL_ROOT_URL . 'css/main.min.css', array(), INDPL_VERSION );
    wp_register_script( 'indpl-js', INDPL_ROOT_URL . 'js/app.min.js', array( 'jquery' ), INDPL_VERSION );
    wp_localize_script( 'indpl-js', 'indpl_ajax',
       array(
          'ajaxurl' => admin_url( 'admin-ajax.php' ),
          'nonce' => wp_create_nonce( 'INDPL_nonce', 'security' ),
       )
    );
    wp_enqueue_script( 'indpl-js' );

    // If the page is /account enqueue the media uploader
      if( is_page('account') ){
         wp_enqueue_media();


      }

  }
  add_action( 'wp_enqueue_scripts', 'indpl_enqueue' );

// Enqueue admin.js for the admin page
function indpl_admin_enqueue(){
   wp_enqueue_script( 'indpl-admin-js', INDPL_ROOT_URL . 'js/admin.min.js', array( 'jquery' ), INDPL_VERSION );
   wp_localize_script( 'indpl-admin-js', 'indpl_ajax',
         array(
         'ajaxurl' => admin_url( 'admin-ajax.php' ),
         'nonce' => wp_create_nonce( 'INDPL_nonce', 'security' ),
         )
   );
}

add_action( 'admin_enqueue_scripts', 'indpl_admin_enqueue' );