<?php
/**
* @package  WooSocialshop
*/
namespace Inc\Api;
use Inc\Api\BaseApi;

class TaxonomyApi extends BaseApi{

    public function register(){
        add_action( 'rest_api_init', array( $this , 'registerEndpoints' ));
    }

    public function registerEndpoints(){
        $this->registerEndpoint( '/product-cat', 'GET', 'getProductCat' );
        $this->registerEndpoint( '/product-tag/products', 'GET', 'getProductsByTags' );
        $this->registerEndpoint( '/product-cat/products', 'GET', 'getProducts' );
    }

    /**
    * @description Get list products
    * @param id is product category id ( demo = 16 )
    */
    public function getProductCat(){
        if( !isset($_GET['id']) )
            return wp_send_json_error(['message' => 'Product Category Ids is required']);

        $product_cat_id = $_GET['id'];
        $product_cat = get_term( $product_cat_id, 'product_cat' );
        return wp_send_json_success($product_cat);
    }

    /**
    * @description Get list products by product categories id
    * @param id is product category id ( demo = 16 )
    */
    public function getProducts(){
        if( !isset($_GET['category']) )
            return wp_send_json_error(['message' => 'Product Tag Ids is required']);

        $params  = $this->parseParams($_GET);
        $cat_ids = explode(',', $_GET['category'] );
        

        $terms = get_terms(array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'include'    => $cat_ids
        ));

        if( empty($terms) ){
            return wp_send_json_success([]);
        }

        $args['category'] = [];
        foreach ($terms as $key => $term) {
            $args['category'] = $term->slug;
        }

        $wc_products = wc_get_products($args);
        foreach ($wc_products as $product) {
            $products[] = $product->get_data();
        }
        return wp_send_json_success($products);
    }

    /**
    * @description Get list products by product tags id
    * @param id is product category id ( demo = 16 )
    */
    public function getProductsByTags(){
        if( !isset($_GET['tag']) )
            return wp_send_json_error(['message' => 'Product Category Id is required']);

        $params  = $this->parseParams($_GET);
        $cat_ids = explode(',', $_GET['tag'] );
        

        $terms = get_terms(array(
            'taxonomy'   => 'product_tag',
            'hide_empty' => false,
            'include'    => $cat_ids
        ));

        if( empty($terms) ){
            return wp_send_json_success([]);
        }

        $args['tag'] = [];
        foreach ($terms as $key => $term) {
            $args['tag'] = $term->slug;
        }

        $wc_products = wc_get_products($args);
        foreach ($wc_products as $product) {
            $products[] = $product->get_data();
        }
        return wp_send_json_success($products);
    }
}

