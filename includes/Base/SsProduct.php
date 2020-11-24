<?php
/**
 * @package  WooSocialshop
 */

defined( 'ABSPATH' ) || exit;

namespace Inc\Base;
use Inc\Base\SsTaxonomy;
use Inc\Base\SsConfig;
use Inc\Helpers\Common;

class SsProduct{

	protected $fields = [];
	protected $ssConfig, $ssTaxonomy;
	protected $date_format;
	protected $field_removed;

	public function __construct(){
		$this->ssTaxonomy = new SsTaxonomy();
		$this->ssConfig   = new SsConfig();
		$this->date_format   = $this->ssConfig->readKey('Global','date_format');
		$this->field_removed = $this->ssConfig->readKey('Product','removed');


		// $arr  = [];
		// $product_key  = 'attributes.material.a.b';
		// $this->setArray($arr,$product_key,[]);
		// echo '<pre>';
		// print_r( $arr );
		// echo '</pre>';
		// die();

	}


	function setArray(&$array, $keys, $value) {
		$keys = explode(".", $keys);
		$current = &$array;
		foreach($keys as $key) {
			$current = &$current[$key];
		}
		$current = $value;
	}
	
	function getImageSrc( $id, $size = 'large' ){
		$img = wc_placeholder_img_src($size);
		$thumbnail_id = get_post_thumbnail_id( $id );
		if( !empty( $thumbnail_id ) ){
			$img = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), $size );
	    	$img = $img[0];
		}
		return $img;
	}

	function getThumbnailSrc( $id = null , $size = 'large'){
	    $img = wp_get_attachment_image_src( $id, $size );
	    if( !empty($img[0]) ){
	    	return $img[0];
	    }
	    $placeholderId = $this->getPlaceholderImgId();
	    return $this->getThumbnailSrc( $placeholderId );
	}


	function getThumbnailsSrc( array $ids, $size = 'large'){
		if( empty($ids))
			return [];
		$thumbnails = [];
		foreach ($ids as $key => $id) {
			$thumbnails[$key]['id']  = $id;
			$thumbnails[$key]['url'] = $this->getThumbnailSrc( $id );
		}
	    return $thumbnails;
	}

	function getPlaceholderImgId(){
		return get_option( 'woocommerce_placeholder_image', 0 );
	}

	/**
	* Get WC Product by id
	* @since 1.0.0
	* @param $id is product id
	* @return array
	*/
	function getProduct( $id ){
		$wc_product = wc_get_product($id);
		switch ($wc_product->get_type()) {
			case 'simple':
				return $this->getProductSimple($wc_product);
				break;

			case 'variable':
				return $this->getProductSimple($wc_product);
				break;

			case 'variation':
				return $this->getProductSimple($wc_product);
				break;

			default:
				return [];
		}

	}

	/**
	* Get WC Product Simple by id
	* @since   1.0.0
	* @param   $wc_product WC Product 
	* @return  array
	*/
	function getProductSimple ( $wc_product ){

		if( empty($wc_product) )
			return [];

		return $this->convertData( $wc_product );
	}

	/**
	* Get WC Product Variation by id
	* @since   1.0.0
	* @param   $wc_product WC Product
	* @return  array
	*/
	function getProductVariation ( $wc_product ){

		echo '<pre>wc_product:';
		print_r( $wc_product->get_attributes() );
		echo '</pre>';

		if( empty($wc_product) )
			return [];

		return $this->convertData( $wc_product );
	}

	/**
	* Get and append attributes for WC Product
	* @since 1.0.0
	* @param $wc_product is WC_Product
	* @return array
	*/
	function convertData( $wc_product ){

		$mapping_config = $this->ssConfig->readKey('Product','mapping',[]);

		$product_id	  = $wc_product->get_id();
		$product_data = $wc_product->get_data();

		$product_data['date_created_with_timezone']  = Common::formatDate($wc_product->get_date_created());
		$product_data['date_modified_with_timezone'] = Common::formatDate($wc_product->get_date_modified());
		$product_data['date_on_sale_from_with_timezone'] 	= Common::formatDate($wc_product->get_date_on_sale_from());
		$product_data['date_on_sale_to_with_timezone'] 	= Common::formatDate($wc_product->get_date_on_sale_to());
		$product_data['product_type']       = $wc_product->get_type();
		$product_data['image_url']          = $wc_product->get_image();
		$product_data['gallery_images_url'] = $this->getThumbnailsSrc($product_data['gallery_image_ids']);
		$product_data['product_url'] = get_permalink($product_id);
		$wc_attributes = $wc_product->get_attributes();
		// Get list attributes protected
		if( is_array($wc_attributes) ){
			foreach ($wc_attributes as $attr_key => $attributes) {
				if( is_object($attributes)){
					$product_data['attributes'][$attr_key] = $attributes->get_data();
				}
				else{
					$product_data['attributes'][$attr_key] = $attributes;
				}
			}
		}

		$wc_downloads = $wc_product->get_downloads();
		
		// Get list attributes protected
		if( is_array($wc_downloads) ){
			foreach ($wc_downloads as $download_key => $downloads) {
				if( is_object($downloads)){
					$product_data['downloads'][$download_key] = $downloads->get_data();
				}
				else{
					$product_data['downloads'][$download_key] = $downloads;
				}
			}
		}
		
		//Mapping fields from Woo and App
		if( $product_data['product_type'] == 'variation' &&  !empty($mapping_config)){
			foreach ($mapping_config as $mapping_key => $product_key) {

				if( empty($product_key)  ){
					#unset($product_data[$product_key]);
					continue;
				}

				$nested_keys   = explode('.', $product_key );
				if( count($nested_keys) < 2  )
					continue;

				$convert_keys  = $product_data;
				$nested_length = count($nested_keys);
				for( $i = 0 ; $i < $nested_length; $i++ ){

					if( $i + 1 == $nested_length )
						continue;

					$check_key =  $nested_keys[$i];
					$next_key  =  $nested_keys[$i+1];
					$convert_keys = $this->extractData( $convert_keys[$check_key], $next_key );
				}

				$product_data[$mapping_key] = $convert_keys;
				
			}
		}

		// Unset fields not accessery
		if( !empty($this->field_removed) ){
			foreach ($this->field_removed as $field) {
				unset($product_data[$field]);
			}
		}

		// Get list variations
		$wc_children = $wc_product->get_children();
		if( is_array($wc_children) ){
			foreach ($wc_children as $children_id) {
				$children  = wc_get_product($children_id);
				$product_data['variant'][] = $this->convertData($children);
			}
		}

		return $product_data;
	}

	/**
	* Extract product data
	* @since   1.0.0
	* @param   
	* @return  
	*/
	function extractData ( $product, $key ){
		return isset($product[$key]) ? $product[$key] : null;
	}


	/**
	* Parse request data to mapping with filter from app
	* @since   1.0.0
	* @param   array $request
	* @return  array
	*/
	function parseParam ( $request ){

		$product_type_config 	= $this->ssConfig->readKey('Product','type_product',[]);		
		$include_variant_config = $this->ssConfig->readKey('Product','include_variant',[]);
		$filter['use_seo'] 			  = isset($request['use_seo']) ? $request['use_seo'] : true;
		$filter['import_unpublished'] = isset($request['import_unpublished']) ? $request['import_unpublished'] : true;
		$filter['type_get_product']   = isset($request['type_get_product']) &&  in_array( strtoupper($request['type_get_product']), $product_type_config ) 
											? strtoupper($request['type_get_product']) : $product_type_config[0];

		if( isset($request['collection_ids']) && is_arrray($request['collection_ids'])  ){
			$filter['category'] = [];
			foreach ($request['collection_ids'] as $collection_id) {
				$product_cat = get_term($collection_id,'product_cat');
				if( !term_exist( $product_cat ) )
					continue;

				#$filter['category'][] = $product_cat->slug;
				echo '<pre>product_cat:';
				print_r( $product_cat );
				echo '</pre>';
			}
		}

		$filter['include_variant']    = isset($request['include_variant']) && in_array( strtoupper($request['include_variant']), $include_variant_config ) 
											? strtoupper($request['include_variant']) : $include_variant_config[0];

		$filter['paginate'] = true;

		return $filter;

	}

}
