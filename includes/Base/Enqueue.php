<?php
/**
 * @package  WooSocialshop
 */

defined( 'ABSPATH' ) || exit;

namespace Inc\Base;

class Enqueue{

    public function register(){
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /*
    * @description Register styles into admin
    */
    public function enqueue_styles() {
        wp_enqueue_style( 'bootstrap', 
            PLUGIN_URL.'/assets/css/bootstrap.min.css', 
            array(), 
            '4.0.0', 
            'all'
        );

        wp_enqueue_style( 'woo-socialshop', 
            PLUGIN_URL.'/assets/css/main.css', 
            array(), 
            '1.0.0', 
            'all'
        );

    }

    /*
    * @description Register script into admin
    */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'bootstrap',
            PLUGIN_URL.'/assets/js/bootstrap.min.js', 
            array('jquery')
        );
    }
}
