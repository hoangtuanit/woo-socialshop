<?php
/**
 * @package  WooSocialshop
 */

defined( 'ABSPATH' ) || exit;

namespace Inc\Base;

class SsTaxonomy{

	public function __construct(){

	}
	
	/**
	 * Retrieve a post's taxonomy as a list with specified format.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $id       Post ID.
	 * @param string $taxonomy Taxonomy name.
	 * @return string|false|WP_Error A list of terms on success, false if there are no terms, WP_Error on failure.
	 */
	public function getTaxonomies( $id, $taxonomy ){
		$terms = get_the_terms( $id, $taxonomy );

		if ( is_wp_error( $terms ) ) {
			return $terms;
		}

		if ( empty( $terms ) ) {
			return false;
		}

		$links = array();

		foreach ( $terms as $key =>  $term ) {
			$link = get_term_link( $term, $taxonomy );
			if ( is_wp_error( $link ) ) {
				return $link;
			}
			$links[$key]['id']   = $term->term_id;
			$links[$key]['name'] = $term->name;
		}

		return $links;
	}

	/**
	 * Retrieve a product's product categories.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $id       Post ID.
	 * @return string|false|WP_Error A list of terms on success, false if there are no terms, WP_Error on failure.
	 */
	public function getProductCats( $id ){
		return $this->getTaxonomies($id,'product_cat');
	}

	/**
	 * Retrieve a product's tags.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $id       Post ID.
	 * @return string|false|WP_Error A list of terms on success, false if there are no terms, WP_Error on failure.
	 */
	public function getTags( $id ){
		return $this->getTaxonomies($id,'product_tag');
	}
}
