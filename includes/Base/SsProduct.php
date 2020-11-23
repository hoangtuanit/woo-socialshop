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
		$product_data['date_modified_with_timezone'] = $wc_product->get_date_modified()->format( $this->date_format );
		$product_data['date_on_sale_from'] 	= $wc_product->get_date_on_sale_from();
		$product_data['date_on_sale_to'] 	= $wc_product->get_date_on_sale_to();
		$product_data['product_type']       = $wc_product->get_type();
		$product_data['image_url']          = $wc_product->get_image();
		$product_data['gallery_images_url'] = $this->getThumbnailsSrc($product_data['gallery_image_ids']);
		$product_data['product_url'] = get_permalink($product_id);

		if( !empty($mapping_config)){
			foreach ($mapping_config as $mapping_key => $product_key) {

				if( empty($product_key) || !isset($product_data[$product_key]) ){
					unset($product_data[$product_key]);
					continue;
				}

				$product_data[$mapping_key] = $product_data[$product_key];
				
			}
		}

		// Unset fields not accessery
		if( !empty($this->field_removed) ){
			foreach ($this->field_removed as $field) {
				unset($product_data[$field]);
			}
		}

		return $product_data;
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
