<?php

defined( 'ABSPATH' ) || exit;

namespace Inc\Woocommerce;

class WsProductData extends \WC_Product_Data_Store_CPT{

	/**
	* Get list woocommerce custom fields.
	* @since   1.0.0
	* @param   
	* @return  array
	*/
	function getInternalMetaFields (){
		return $this->internal_meta_keys;
	}

}
