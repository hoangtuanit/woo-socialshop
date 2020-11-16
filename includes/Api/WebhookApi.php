<?php
/**
* @package  WooSocialshop
*/
namespace Inc\Api;
use Inc\Api\BaseApi;

class WebhookApi extends BaseApi{
	public function register(){
        add_action( 'rest_api_init', array( $this , 'registerEndpoints' ));
    }

    public function registerEndpoints(){
        $this->registerEndpoint( '/webhook', 'GET', 'getWebhook' );
        $this->registerEndpoint( '/webhook', 'POST', 'createWebhook' );
    }


    public function createWebhook(){
    	$webhook = [];
    	$webhook = new \WC_Webhook();
    	$webhook->set_user_id( 1 ); // User ID used while generating the webhook payload.
    	$webhook->set_name( 'Test Create' ); // User ID used while generating the webhook payload.
    	$webhook->set_topic( 'order.created' ); // Event used to trigger a webhook.
    	$webhook->set_secret( 'secret' ); // Secret to validate webhook when received.
    	$webhook->set_delivery_url( 'https://webhook-handler.com' ); // URL where webhook should be sent.
    	$webhook->set_status( 'active' ); // Webhook status.
    	$saved = $webhook->save();

    	if( $saved ){
    		return wp_send_json_success('Webhook created successfull!');
    	}
    	return wp_send_json_error('Can\'t create webhook!');
    }

    /**
    * @defined wc-webhook-functions.php
    */

    public function getWebhook(){
    	if( !isset($_GET['id']) ){
            return wp_send_json_error(['message' => 'Webhook Id is required']);
    	}

    	$webhook_id = $_GET['id'];
    	$webhook = wc_get_webhook($webhook_id);
    	return wp_send_json_success($webhook->get_data());
    }
}
