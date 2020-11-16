<?php
/**
* @package  WooSocialshop
*/
namespace Inc\Api;

class BaseApi{
    
    public $namespace = 'ss-integrate';

    public function __construct(){
    }

    public function registerEndpoint( $endpoint, $method, $callback ){
        register_rest_route( $this->namespace, $endpoint , array(
            array(
                'methods' => $method,
                'permission_callback' => array( $this , 'privilegedPermissionCallback' ),
                'callback' => array( $this, $callback ),
            )
        ) );
    }

    public function parseParams( $request ){
        $result = ['paged'=>1,'limit'=>10,'paginate'=>true];
        $param  =  wp_parse_args($request);
        if( isset($param['page'])){
            $result['page'] = $param['page'];
        }
        if( isset($param['limit'])){
            $result['page'] = $param['limit'];
        }
        return $result;
    }

    public function privilegedPermissionCallback(){
        $param =  wp_parse_args($_GET);
        return true;
    }


    public function get( $endpoint, $callback ){
        $this->register( $endpoint ,'GET',$callback);
    }

    public function post( $endpoint, $callback ){
        $this->register( $endpoint ,'POST',$callback);
    }

    public function put( $endpoint, $callback ){
        $this->register( $endpoint ,'PUT',$callback);
    }

    public function delete( $endpoint, $callback ){
        $this->register( $endpoint ,'DELETE',$callback);
    }

}