<?php
/**
 * @package  WooSocialshop
 */

defined( 'ABSPATH' ) || exit;

namespace Inc\Base;
use Inc\Base\SsTaxonomy;
use Inc\Base\SsConfig;

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
			$thumbnails[$key]['src'] = $this->getThumbnailSrc( $id );
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
		$product_id	  = $wc_product->get_id();
		$product_data = $wc_product->get_data();
		$product_data['date_created_with_timezone']  = $wc_product->get_date_created()->format( $this->date_format );
		$product_data['date_modified_with_timezone'] = $wc_product->get_date_modified()->format( $this->date_format );
		$product_data['product_type']       = $wc_product->get_type();
		$product_data['image_src']          = $wc_product->get_image();
		$product_data['gallery_images_src'] = $this->getThumbnailsSrc($product_data['gallery_image_ids']);
		$product_data['tags']       = $this->ssTaxonomy->getTags($product_id);
		$product_data['categories'] = $this->ssTaxonomy->getProductCats($product_id);
		// Unset fields not accessery
		if( !empty($this->field_removed) ){
			foreach ($this->field_removed as $field) {
				unset($product_data[$field]);
			}
		}

		return $product_data;
	}
}
