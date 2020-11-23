<?php

defined( 'ABSPATH' ) || exit;

namespace Inc\Base;

/**
 * Define and read configs from 'Configs/*'
 */
class SsConfig
{

	protected $config_path;
	/**
	* Define config path
	* @param
	* @return
	*/
	function __construct (){
		$this->config_path = INCLUDE_PATH.'Configs/';
	}

	/**
	* Get config path
	* @param $filename without extension
	* @return string
	*/
	function getPath ( $filename ){
		$file = $this->config_path.$filename.'.php';
		if( !file_exists($file) ){
			return null;
		}
		return $file;
	}

	/**
	* Read config file by key
	* @since 1.0.0
	* @param string $filename File name.
	* @param string $key 	  Key in data return.
	* @return array|string
	*/
	public function readKey( $filename, $key ){
		$data = $this->read($filename);
		if( isset($data[ $key ] ) ){
			return $data[ $key];
		}
		return null;
	}

	/**
	* Read config file
	* @since 1.0.0
	* @param string $filename File name.
	* @return array|string
	*/
	public function read( $filename ){
		$filePath = $this->getPath($filename);
		if( !file_exists($filePath) ){
			return null;
		}
		return require($filePath);
	}

}