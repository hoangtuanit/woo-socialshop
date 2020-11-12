<?php

if( !function_exists('ss_set_post_default_category')):
	function set_post_default_category( $post_id, $post, $update ) {
		delete_option('ss_save_post');
	    add_option('ss_save_post',json_encode([
	    	'post_id' => $post_id,
	    	'post'	  => $post,
	    	'update'  => $update
	    ]));

	    $url = 'https://cc8f20c327192ed882a9caa555a0c1c6.m.pipedream.net';
	    return wp_remote_post( $url,[
	    	'post_id' => $post_id,
	    	'post'	  => $post,
	    	'update'  => $update
	    ] );
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





/**
 * add_new_topic_hooks will add a new webhook topic hook. 
 * @param array $topic_hooks Esxisting topic hooks.
 */
function add_new_topic_hooks( $topic_hooks ) {

	// Array that has the topic as resource.event with arrays of actions that call that topic.
	$new_hooks = array(
		'order.custom_filter' => array(
			'custom_order_filter',
			),
		);

	return array_merge( $topic_hooks, $new_hooks );
}
add_filter( 'woocommerce_webhook_topic_hooks', 'add_new_topic_hooks' );

/**
 * add_new_topic_events will add new events for topic resources.
 * @param array $topic_events Existing valid events for resources.
 */
function add_new_topic_events( $topic_events ) {

	// New events to be used for resources.
	$new_events = array(
		'custom_filter',
	);

	return array_merge( $topic_events, $new_events );
}
add_filter( 'woocommerce_valid_webhook_events', 'add_new_topic_events' );

/**
 * add_new_webhook_topics adds the new webhook to the dropdown list on the Webhook page.
 * @param array $topics Array of topics with the i18n proper name.
 */
function add_new_webhook_topics( $topics ) {

	// New topic array to add to the list, must match hooks being created.
	$new_topics = array( 
		'order.custom_filter' => __( 'Order Custom Filter', 'woocommerce' ),
	);

	return array_merge( $topics, $new_topics );
}
add_filter( 'woocommerce_webhook_topics', 'add_new_webhook_topics' );

/**
 * my_order_item_check will check an order when it is created through the checkout form,
 * if it has product ID 10603 as one of the items, it will fire off the action `custom_order_filter`
 * 
 * @param  int    $order_id    The ID of the order that was just created.
 * @param  array  $posted_data Array of all of the data that was posted through checkout form.
 * @param  object $order       The order object.
 * @return null
 */
function my_order_item_check( $order_id, $posted_data, $order ) {
	
	do_action( 'custom_order_filter', $order_id, $posted_data, $order );
}
add_action( 'woocommerce_checkout_order_processed', 'my_order_item_check', 10, 3 );