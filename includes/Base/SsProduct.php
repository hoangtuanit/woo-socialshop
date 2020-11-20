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

	function getThumbnailSrc($id, $size = 'large'){
		$img = wc_placeholder_img_src($size);
	    $img = wp_get_attachment_image_src( $id, $size );
	    $img = !empty($img[0]) ? $img[0] : $img_default;
		return $img;
	}

}
