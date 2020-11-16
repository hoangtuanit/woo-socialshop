<?php
/**
* @package  WooSocialshop
*/
namespace Inc\Api;
use Inc\Api\BaseApi;

class ProductApi extends BaseApi{

    public function register(){
        add_action( 'rest_api_init', array( $this , 'registerEndpoints' ));
        add_filter( 'woocommerce_product_object_query', array($this,'filterConvertObjectToArray'), 10, 2 );
    }

    public function registerEndpoints(){
        $this->registerEndpoint( '/products',       'GET', 'getProducts' );
        $this->registerEndpoint( '/product/tags',   'GET', 'getProductTags' );
        $this->registerEndpoint( '/product/categories', 'GET', 'getProductCats' );
        $this->registerEndpoint( '/product/variants',   'GET', 'getProductVariants' );
        $this->registerEndpoint( '/product/variation-attributes', 'GET', 'getVariationAttributes' );
    }

    /*
    * @description Get list products
    */
    public function getProducts(){
        $params   = $this->parseParams($_GET); 
        $wc_products = wc_get_products($params);
        $results  = [];
        foreach ($wc_products->products as $key => $product) {
            $results[] = $product->get_data();
        }
        echo '<pre>wc_products:';
        print_r( $wc_products );
        echo '</pre>';
        return wp_send_json_success($results);
    }

    /**
    * @description Get list product variations by product id
    * @param id is product id
    * @since 1.0.0
    */
    public function getProductVariants(){

        if( !isset($_GET['id']) )
            return wp_send_json_error(['message' => 'Product Id is required']);

        $product_id = $_GET['id'];
        $wc_product = new \WC_Product_Variable( $product_id );
        $product_variations = $wc_product->get_available_variations();
        return wp_send_json_success( $product_variations );
    }

    /**
    * @description Get list product variations by product id
    * @param id is product id
    * @since 1.0.0
    */
    public function getVariationAttributes(){

        if( !isset($_GET['id']) )
            return wp_send_json_error(['message' => 'Product Id is required']);

        $product_id = $_GET['id'];
        $wc_product = new \WC_Product_Variable( $product_id );
        $product_variations = $wc_product->get_variation_attributes();
        return wp_send_json_success( $product_variations );
    }

    /**
    * @description Get list product tags by product id
    * @param id is product id
    * @since 1.0.0
    */
    public function getProductTags(){

        if( !isset($_GET['id']) )
            return wp_send_json_error(['message' => 'Product Id is required']);

        $product_id = $_GET['id'];
        $tags = wp_get_post_terms( $product_id, 'product_tag' );
        return wp_send_json_success( $tags );
    }


    /**
    * @description Get list product tags by product id
    * @param id is product id
    * @since 1.0.0
    */
    public function getProductCats(){

        if( !isset($_GET['id']) )
            return wp_send_json_error(['message' => 'Product Id is required']);

        $product_id = $_GET['id'];
        $tags = wp_get_post_terms( $product_id, 'product_cat' );
        return wp_send_json_success( $tags );
    }



    function filterConvertObjectToArray( $results, $args ){
        return $results;
    }

}

