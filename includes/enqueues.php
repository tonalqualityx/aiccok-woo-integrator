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
  }
  add_action( 'wp_enqueue_scripts', 'indpl_enqueue' );