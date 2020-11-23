<?php
/**
* 
* @since 1.0.0
* @param
* @return
*/

defined( 'ABSPATH' ) || exit;

namespace Inc\Helpers;
use Inc\Base\SsConfig;

/**
 * Common Class.
 */

class Common{

	/**
	* 
	* @since 1.0.0
	* @param
	* @return
	*/
	static function getDateFormat(){
		$ssConfig = new SsConfig();
		return $ssConfig->readKey('Global','date_format');
	}

	/**
	* Format date
	* @since 1.0.0
	* @param  $date 
	* @return string
	*/
	static function formatDate($date,$format = null ){
		if( empty($format) ) {
			$format = self::getDateFormat();
		}
		$strtotime = strtotime($date);
		return date($format,$strtotime);
	}
}