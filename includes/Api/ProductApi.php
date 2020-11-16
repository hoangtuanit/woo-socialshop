<?php
/**
* @package  WooSocialshop
*/
namespace Inc\Api;
use Inc\Api\BaseApi;

class ProductApi extends BaseApi{
    public function register(){
        parent::__construct();
        add_action( 'rest_api_init', array( $this , 'register' ));
    }

    public function register(){
        add_action( 'rest_api_init', array( $this , 'register' ));
        $this->register( '/product', 'GET', 'getProducts' );
    }

    public function getProducts(){
        $request     = $this->parseParams($_GET);
        $wc_products = wc_get_products($request);
        $result = [];
        foreach ($wc_products->products as $key => $wc_product) {
            $item = ['id' => $wc_product->id];
            foreach (self::$listField  as $field ) {
                $value = $wc_product-> {$field};
                if(iso8601_to_datetime($value) !== false) {
                    $item[$field] =  iso8601_to_datetime($value);
                } else {
                    $item[$field] =  $value;
                }

            }
        }
        return $result;
    }
}

