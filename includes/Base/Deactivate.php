<?php
/**
 * @package  WooSocialshop
 */

namespace Inc\Base;

class Deactivate{
	public static function deactivate(){
		self::update_option();
	}

	/**
	* @description Mock key to check active plugin in table _options
	*/
	private static function update_option(){
		update_option( 'woo_socialshop_active', false );
	}

}