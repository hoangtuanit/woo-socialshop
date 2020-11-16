<?php
/**
 * @package  WooSocialshop
 */

namespace Inc\Base;

class Activate{

	public static function activate(){
		self::load_plugin_textdomain();
		self::check_require_plugins();
		self::update_option();
	}

	/**
	* @description Set textdomain for multi language
	*/
	private static function check_require_plugins(){
	    // Require parent plugin
	    if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			// Stop activation redirect and show error			
	        #add_action( 'admin_notices', array( self ,'plugin_notice') );
	        deactivate_plugins( plugin_basename( __FILE__ ) );
	    }
	}
	/*
	* @description Admin notice if unactive Woocommerce
	*/
	private static function plugin_notice(){
		echo '<div class="error my-3">
				<p>Sorry, plugin <b>'.PLUGIN_SLUG.'</b> require the woocommerce plugin to be installed and active.</p>
			</div>';
	}

	/**
	* @description Set textdomain for multi language
	*/
	private static function load_plugin_textdomain(){
		load_plugin_textdomain( PLUGIN_ID , false, 'woo-socialshop/languages' );
	}

	/**
	* @description Mock key to check active plugin in table _options
	*/
	private static function update_option(){
		update_option( 'woo_socialshop_active', true );
	}
	
}

