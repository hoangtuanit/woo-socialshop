<?php
/**
 * @package  WooSocialshop
 */

namespace Inc\Base;

class SsProduct{

	public function __construct(){

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
	    $this->getThumbnailSrc( $placeholderId );
	}

	function getPlaceholderImgId(){
		return get_option( 'woocommerce_placeholder_image', 0 );
	}


}
