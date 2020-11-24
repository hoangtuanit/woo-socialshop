<?php
/**
* @package  WooSocialshop
*/
namespace Inc\Api;
use WP_REST_SERVER;
use WP_REST_Request;
use Inc\Base\SsConfig;


class BaseApi{
    
    public $namespace = 'ss-integrate';
    protected $ssConfig;

    public function __construct(){
        $this->ssConfig = new SsConfig();
    }

    public function registerEndpoint( $endpoint, $method, $callback ){

        register_rest_route( $this->namespace, $endpoint , array(
            array(
                'methods' => 'GET',
                #'methods' => array( $this, 'getMethods'),
                'args' => array(),
                'permission_callback' => array( $this , 'privilegedPermissionCallback' ),
                'callback' => array( $this, $callback ),
            )
        ) );
    }

    public function getMethods( WP_REST_Request $request){
        echo '<pre>get_json_params:';
        print_r( $request->get_json_params() );
        echo '</pre>';
        return WP_REST_Server::READABLE;
        /*switch (strtoupper($method)  {
            case 'GET':
                return WP_REST_Server::READABLE;
            case 'POST':
                return WP_REST_Server::CREATABLE;
            case 'PUT':
                return WP_REST_Server::EDITABLE;
            case 'PATCH':
                return WP_REST_Server::EDITABLE;
            default:
                return WP_REST_Server::READABLE;
                break;
        }*/
    }

    public function parseParams( $request ){
        $limit    = $this->ssConfig->readKey('Product','limit');
        $paginate = $this->ssConfig->readKey('Product','paginate');
        $result = ['paged'=>1, 'limit'=>$limit, 'paginate' => $paginate];
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