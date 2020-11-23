<?php

namespace Inc\Base;

/**
 * Define and read configs from 'Configs/*'
 */
class SsConfig
{
	/**
	* Read config file
	* @since 1.0.0
	* @param @filename
	* @return array
	*/
	public function read( $filename ){
		$file = INCLUDE_PATH.'Configs/'.$filename.'.php';
		if( !file_exists($file) ){
			return null;
		}
		return require($file);
	}
}