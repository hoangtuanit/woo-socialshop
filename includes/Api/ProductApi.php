<?php
/**
* @package  WooSocialshop
*/
namespace Inc\Api;
use Inc\Api\BaseApi;
use Inc\Base\SsTaxonomy;
use Inc\Base\SsProduct;

class ProductApi extends BaseApi{
    protected $ssTaxonomy, $ssProduct;

    public function __construct(){
        parent::__construct(); 
        $this->ssTaxonomy  = new SsTaxonomy();
        $this->ssProduct   = new SsProduct();

        // $wc_query = new \WC_Product_Query();
        // echo '<pre>wc_query:';
        // print_r( $wc_query->get_query_vars() );
        // echo '</pre>';

    }

    public function register(){        
        add_action( 'rest_api_init', array( $this , 'registerEndpoints' ));
        add_filter( 'woocommerce_product_object_query', array($this,'filterConvertObjectToArray'), 10, 2 );
    }

    /**
    * 1, Lấy danh sách variants của từng product
    * 2, Lấy toàn bộ custom fields
    */
    public function registerEndpoints(){
        $this->registerEndpoint( '/product/(?P<id>\d+)',            'GET', 'getProduct' );
        $this->registerEndpoint( '/products',                       'GET', 'getProducts' );
        $this->registerEndpoint( '/product/tags',                   'GET', 'getProductTags' );
        $this->registerEndpoint( '/product/metas',                  'GET', 'getMetas' );
        $this->registerEndpoint( '/product/attributes',             'GET', 'getAttributes' );
        $this->registerEndpoint( '/product/categories',             'GET', 'getProductCats' );
        $this->registerEndpoint( '/product/variants',               'GET', 'getProductVariants' );
        $this->registerEndpoint( '/product/variation-attributes',   'GET', 'getVariationAttributes' );
    }
    
    /*
    * @description Get list products
    */
    public function getProducts(){
        $params   = $this->parseParams($_GET); 
        $wc_products = wc_get_products($params);
        $results  = [];
        foreach ($wc_products->products as $key => $product) {
            $results[$key] = $this->ssProduct->convertData($product);
        }
        return wp_send_json_success($results);
    }

    /*
    * @description Get list products
    */
    public function getProduct( $request ){
        $product_id = $request->get_param('id');
        if( !isset($product_id) )
            return wp_send_json_error(['Product Id is required']);

        $product = $this->ssProduct->getProduct($product_id);
        if( empty($product) ){
            return wp_send_json_error(['message' => 'Product not found']);
        }

        if( $product ){
            return wp_send_json_success( $product );
        }
        return wp_send_json_error([]);
    }
    
    /*
    * @description Get list products
    */
    public function getMetas(){
        if( !isset($_GET['id']) )
            return wp_send_json_error(['message' => 'Product Id is required']);

        $product_id = $_GET['id'];
        $wc_product = new \WC_Product( $product_id );
        return wp_send_json_success( $wc_product->get_meta_data());
    }

    /*
    * @description Get list product attributes
    */
    public function getAttributes(){
        if( !isset($_GET['id']) )
            return wp_send_json_error(['message' => 'Product Id is required']);

        $product_id = $_GET['id'];
        $wc_product = wc_get_product( $product_id );
        echo '<pre>product_id:';
        print_r( $wc_product->get_type() );
        echo '</pre>';
        // echo '<pre>get_data:';  
        // print_r( $wc_product->get_data() );
        // echo '</pre>';
        return wp_send_json_success();
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

