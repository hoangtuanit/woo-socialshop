<?php
/*
Plugin Name: Woo - Socialshop
Plugin URI: https://socialhead.io
Description: This is a plugin demo
Version: 1.0.0
Author: TuanDH
Author URI: https://socialhead.io
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'SS_PLUGIN_FILE' ) ) {
	define( 'SS_PLUGIN_FILE', __FILE__ );
}


if ( ! class_exists( 'FG_Socialshop' ) ) :
	/**
	 * 
	 */
	class FG_Socialshop
	{
		private $handle  = 'fb-socialshop-script';
		public $app_id 		 = 'socialshop';
		public $default_page = 'socialshop';
		public $check_require= true;
		public $support_link = 'https://help.socialhead.io/';

		public function __construct( $config = [] )
		{
			#register_activation_hook( __FILE__, array( $this, 'activate' ) );
			add_action( 'admin_init', array( $this, 'check_require_plugins' ), 0 );
			add_filter( 
				'plugin_action_links_' . plugin_basename( __FILE__ ), 
				array( $this, 'admin_plugin_settings_link' )
			);

			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			if( $this->is_page( $this->default_page ) ){
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ), 5 );
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 50 );
			}

			$this->define_constants();
			$this->init_hooks();

		}

		public function define_constants(){
			$upload_dir = wp_upload_dir( null, false );
			$this->define( 'SS_PATH', dirname( SS_PLUGIN_FILE ) . '/' );
		}

		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		public function init_hooks(){
			include_once SS_PATH . 'includes/ss-functions.php';
			include_once SS_PATH . 'includes/ss-hooks.php';
		}

		public  function admin_plugin_settings_link( $links ) {
	  		$links[] = '<a href="'.esc_url( $this->get_page() ).'">'.__('Settings', $this->app_id).'</a>';
	  		$links[] = '<a href="' . esc_url( $this->support_link ) . '">'.esc_html__( 'Support' , $this->app_id ).'</a>';
	  		return $links; 
		}

		public function plugin_notice(){
			echo '<div class="error my-3">
					<p>Sorry, plugin <b>woo-socialshop</b> require the woocommerce plugin to be installed and active.</p>
				</div>';
		}

		public function check_require_plugins(){
		    // Require parent plugin
		    if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		        // Stop activation redirect and show error
		        add_action( 'admin_notices', array($this,'plugin_notice') );
		        deactivate_plugins( plugin_basename( __FILE__ ) ); 
		    }
		}

		public function load_plugin_textdomain(){
			load_plugin_textdomain( $this->app_id , false, 'woo-socialshop/languages' );
		}

		public function is_page( $page ){
			if( $GLOBALS['pagenow'] == 'admin.php' && 
			isset($_GET['page']) && $_GET['page'] == $page  ){
				return true;
			}
			return false;
		}

		public function get_page(){
			return admin_url('admin.php?page='.$this->default_page);
		}

		public function list_products(){
			$params = wp_parse_args($_GET);
			$wc_products = wc_get_products([
				'category' 	=> @$params['product_cat'],
				's' 		=> @$params['name'],
			]);
			$terms 		 = get_terms(array(
				'hide_empty' => false,
				'taxonomy'   => 'product_cat'
			));
			?>
				<div id="woo-socialshop" class="py-5 px-2">
					<h3>List Products</h3>

					<form class="form-inline" method="GET" action="<?php echo $this->get_page() ?>">
						<input type="hidden" name='page' value='<?php echo $this->default_page ?>' />
						<div class="form-group mb-2">
							<input type="text" name="name" class="form-control" placeholder="Product name" value="<?php echo @$params['name'] ?>">
						</div>
						<div class="form-group mx-sm-3 mb-2">
							<select class="form-control" name="product_cat" id="">
								<option value=""> Choose category </option>
								<?php  
									foreach ($terms as $key => $term) {
										$is_selected = '';
										if( $params['product_cat'] == $term->slug ){
											$is_selected = 'selected';
										}
										echo '<option '.$is_selected.' value="'.$term->slug.'">'.$term->name.'</option>';
									}
								?>
							</select>
						</div>						
						<button type="submit" class="btn btn-primary mb-2">Search</button>
					</form>

					<div class="table-responsive">

						<table class="table table-hover table-light table-bordered">
							<thead>
								<tr>
									<th class="" scope="col">ID</th>
									<th scope="col">Image</th>
									<th scope="col">Name</th>
									<th scope="col">Excerpt</th>
									<th scope="col">Price</th>
									<th scope="col">Status</th>
									<th scope="col">Category</th>
									<th scope="col">Publish Date</th>
								</tr>
							</thead>
							<tbody>
								<?php if( !empty($wc_products) ): ?>
								<?php  
									foreach ($wc_products as $key => $product) {
										?>
										<tr>
											<td title="ID"> 
												<?php echo $product->get_id() ?>
											</td>
											<td title="Image">
												<?php
													$post_thumbnail_id = $product->get_image_id();
													if ( $post_thumbnail_id ) {
														$html = '<a href="'.get_edit_post_link($post_thumbnail_id).'" target="_blank">';
															$html .= '<img src="'.wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' )[0].'" class="img-fluid" alt="'.$product->get_name().'"/>';
														$html .='</a>';
													} else {
														$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
															$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
														$html .= '</div>';
													}
													echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id );
												?>
											</td>
											<td title="Name"> 
												<a href="<?php echo get_edit_post_link($product->get_id()) ?>" target="_blank">
													<?php echo $product->get_name() ?>
												</a>
											</td>
											<td title="Excerpt"> <?php echo $product->get_short_description() ?> </td>
											<td title="Price">
												<?php echo $product->get_price_html() ?>
											</td>
											<td title="Status" > 
												<span class="post-status badge" data-status="<?php echo $product->get_status() ?>">
													<?php echo $product->get_status() ?>
												</span>
											</td>
											<td title="Category"> 
												<?php  
												if( !empty($product->get_category_ids())){
													foreach ($product->get_category_ids() as $key => $cat_id) {
														$term = get_term($cat_id,'product_cat');
														echo '<a target="_blank" href="'.get_edit_term_link($term,'product_cat').'" class="mr-1">'.$term->name.'</a>';
													}
												}
												?>
											</td>
											<td title="Publish Date">  <?php echo date('Y-m-d',strtotime($product->get_date_created())) ?> </td>
										</tr>
										<?php
									}
								?>
								<?php else: ?>
									<tr>
										<td colspan="8">
											<div class="alert alert-info">No rows data</div>
										</td>
									</tr>
								<?php endif; ?>
								
							</tbody>
						</table>
						<!-- end table -->
					</div>
				</div>
				<!-- end #woo-socialshop -->
			<?php
		}

		public  function init_actions() {
			
		}

		public function enqueue_styles() {
			wp_enqueue_style( 'bootstrap', 
				plugins_url('/assets/css/bootstrap.min.css',__FILE__) , 
				array(), 
				'4.0.0', 
				'all'
			);
			wp_enqueue_style( 'woo-socialshop', 
				plugins_url('/assets/css/main.css',__FILE__) , 
				array(), 
				'1.0.0', 
				'all'
			);
		}

		public function enqueue_scripts() {

			wp_enqueue_script(
				'bootstrap',
				plugins_url('/assets/js/bootstrap.min.js',__FILE__) , 
				array('jquery')
			);
		}

		public function activate_actions(){
			add_action( 'admin_init', array( $this, 'init' ) );
		}

		/**
		 * Register plugin menus
		 *
		 * @return void
		 */
		public function admin_menu() {
			// Top-level WP Migration menu
			add_menu_page(
				'Woo - Socialshop',
				'Woo - Socialshop',
				'administrator',
				'socialshop',
				array(  $this,'list_products' ),
				'dashicons-networking',
				50
			);

			add_submenu_page( 
				'admin.php?page=socialshop', 
				__( 'Products', $this->app_id ), 
				__( 'Products', $this->app_id ), 
				'administrator', 
				'list-products', 
				array( $this, 'list_products' ) );

		}

		/**
		 * Set defaults on activation.
		 */
		public static function activate() {
			register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
		}

		/**
		 * Delete the options on uninstall.
		 */
		public static function uninstall() {
			echo 'Uninstalled';
		}

	}

    define( 'SS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
    require_once (SS__PLUGIN_DIR.'includes/rest/rest-ss.php');
    add_action( 'rest_api_init', array( 'Rest_SS', 'init' ) );


	function my_plugin_activate() {

	  add_option( 'Activated_Plugin', 'Plugin-Slug' );

	  /* activation code here */
	}
	register_activation_hook( __FILE__, 'my_plugin_activate' );

	function my_plugin_deactivate() {

	  add_option( 'Deactive_Plugin', 'Plugin-Slug' );

	  /* activation code here */
	}
	register_deactivation_hook( __FILE__, 'my_plugin_deactivate' );
	

	add_action('plugins_loaded',function(){
		
		new FG_Socialshop();
		
	});

endif;