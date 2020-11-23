<?php
/**
* @package  WooSocialshop
* Plugin Name: Woo - Socialshop
* Plugin URI: https://socialhead.io
* Description: This is a plugin demo
* Version: 1.0.0
* Author: TuanDH
* Author URI: https://socialhead.io
* License: GPLv2 or later
* Text Domain: woo-socialshop
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

define('PLUGIN_ID','socialshop');
define('PLUGIN_NAME','Woo Socialshop');
define('PLUGIN_SLUG','woo-socialshop');
define('DEFAULT_PAGE','socialshop');
define('PLUGIN_PATH', plugin_dir_path(__FILE__) );
define('INCLUDE_PATH', plugin_dir_path(__FILE__).'includes/' );
define('PLUGIN_URL', plugins_url('woo-socialshop') );
define('PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

function WSActivePlugin(){
	Inc\Base\Activate::activate();
}
register_activation_hook( __FILE__, 'WSActivePlugin' );

/**
 * The code that runs during plugin deactivation
 */
function WSDeactivePlugin() {
	Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'WSDeactivePlugin' );

/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists( 'Inc\\Init' ) ) {
	add_action('plugin_loaded',function(){
		Inc\Init::registerServices();
	});
}