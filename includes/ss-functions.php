<?php

if( !function_exists('ss_set_post_default_category')):
	function set_post_default_category( $post_id, $post, $update ) {
		delete_option('ss_save_post');
	    add_option('ss_save_post',json_encode([
	    	'post_id' => $post_id,
	    	'post'	  => $post,
	    	'update'  => $update
	    ]));
	}	

endif;

add_action( 'transition_post_status', 'trigger_post_status', 10,3 );

if( !function_exists('trigger_post_status')):

	function trigger_post_status( $new_status, $old_status, $post ) {
		delete_option('ss_transition_post_status');
	    add_option('ss_transition_post_status',json_encode([
	    	'new_status' 	=> $new_status,
	    	'old_status'	=> $old_status,
	    	'post'  		=> $post
	    ]));
	}	

endif;


add_action( 'save_post_product', 'wpdocs_notify_subscribers', 10, 3 );
 
function wpdocs_notify_subscribers( $post_id, $post, $update ) {
 	delete_option('ss_save_post_product');
    add_option('ss_save_post_product',json_encode([
    	'post_id' 	=> $post_id,
    	'post'	=> $post,
    	'update'  		=> $update
    ]));
}

add_action( 'woocommerce_update_product', 'mp_sync_on_product_save', 10, 1 );
function mp_sync_on_product_save( $product_id) {
 	delete_option('ss_woocommerce_update_product');
    add_option('ss_woocommerce_update_product',json_encode([
    	'product_id' 	=> $product_id,
    ]));
}

add_action( 'delete_post', 'ss_delete_post', 10, 2 );
function ss_delete_post( $postid, $post ) {
 	delete_option('ss_delete_post');
    add_option('ss_delete_post',json_encode([
    	'postid' 	=> $postid ,
    	'post' 	=> $post ,
    ]));
}

// define the woocommerce_new_order callback 
function action_woocommerce_new_order( $order_id, $data ) { 
	delete_option('ss_woocommerce_new_order');
	   add_option('ss_woocommerce_new_order',json_encode([
	   	'order_id' 	=> $order_id ,
	   	'data' 	=> $data ,
	   ]));
}; 
         
// add the action 
add_action( 'woocommerce_new_order', 'action_woocommerce_new_order', 10, 3 ); 