<?php

class Rest_SS {
    private static $listField = [
        'name','slug','date_created','date_modified','status','description','sku','price','sale_price','date_on_sale_from','date_on_sale_to','total_sales'
    ];

    public  static function  init(){
        register_rest_route( 'ss-integrate', '/product', array(
            array(
                'methods' => 'get',
                'permission_callback' => array( 'Rest_SS', 'privileged_permission_callback' ),
                'callback' => array( 'Rest_SS', 'get_products' ),
            )
        ) );
    }

    public static function privileged_permission_callback() {
        $param =  wp_parse_args($_GET);
        return true;
    }

    public static function get_products() {
        $param =  wp_parse_args($_GET);
        $page  = @$param['page'];
        if(empty($page)) {
            $page = 1;
        }
        $limit  = @$param['limit'];
        if(empty($page)) {
            $limit = 1;
        }
        $args = ['limit' => $limit, 'paginate' => true, 'paged' => $page];
        $wc_products = wc_get_products($args);
        $result = [];
        foreach ($wc_products->products as $wc_product) {
            $item = ['id' => $wc_product->id];
            foreach (self::$listField  as $field ) {
                $value = $wc_product-> {$field};
                if(iso8601_to_datetime($value) !== false) {
                    $item[$field] =  iso8601_to_datetime($value);
                } else {
                    $item[$field] =  $value;
                }

            }
            $result[] = $item;
        }
        return ["status" => true,'data' => $result];
    }

}