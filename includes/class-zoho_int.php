<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://zoho_int.com
 * @since      1.0.0
 *
 * @package    Zoho_int
 * @subpackage Zoho_int/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Zoho_int
 * @subpackage Zoho_int/includes
 * @author     Denis <yankinndenis@gmail.com>
 */
class Zoho_int {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Zoho_int_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ZOHO_INT_VERSION' ) ) {
			$this->version = ZOHO_INT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'zoho_int';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Zoho_int_Loader. Orchestrates the hooks of the plugin.
	 * - Zoho_int_i18n. Defines internationalization functionality.
	 * - Zoho_int_Admin. Defines all hooks for the admin area.
	 * - Zoho_int_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-zoho_int-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-zoho_int-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-zoho_int-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-zoho_int-public.php';

		$this->loader = new Zoho_int_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Zoho_int_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Zoho_int_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Zoho_int_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Zoho_int_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Zoho_int_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	static function get_items($url = '', $token = '', $organization = ''){
		$client_id = array("customer_id"=>$organization);
		$fields = json_encode($client_id);
	  	$ch = curl_init();

	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

	    $headers = array();
	    $headers[] = 'Content-Type: application/json;charset=UTF-8';
	    $headers[] = 'Authorization: bearer '.$token;
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	    $result = curl_exec($ch);
	    if (curl_errno($ch)) {
	        echo 'Error:' . curl_error($ch);
	    }
	    curl_close($ch);
	    return json_decode($result, true);
	}

	static function add_products($products, $url = '', $token = '', $organization = ''){
	    
	    $ch = curl_init();

	    curl_setopt($ch, CURLOPT_URL, get_option('zoho_url'));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

	    curl_setopt($ch, CURLOPT_POSTFIELDS, $products);

	    $headers = array();
	    $headers[] = 'Content-Type: application/json;charset=UTF-8';
	    $headers[] = 'Organization: '.$organization;
	    $headers[] = 'Authorization: bearer '.get_option('zoho_authorization');
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	    $result = curl_exec($ch);
	    if (curl_errno($ch)) {
	        echo 'Error:' . curl_error($ch);
	    }
	    curl_close($ch);
	    return json_decode($result, true);
	}

	static function check($sku){
		
		global $woocommerce;
		$stock = 0;
	    $args = array(
	         'post_type'  => 'product',
	         'meta_query' => array(
	             array(
	                 'key'   => '_sku',
	                 'value' => $sku,
	             )
	         )
	    );
	    $posts = get_posts( $args );

	    if(sizeof($posts)){
	        
	        $product_id = $posts[0]->ID;
	        $product_id = intval($product_id);
	        $product = wc_get_product( $product_id );
	        $stock = $product->get_stock_quantity();
	    }else{

	        $args = array(
	            'post_type'  => 'product_variation',
	            'meta_query' => array(
	                array(
	                    'key'   => '_sku',
	                    'value' => $sku,
	                )
	            )
	        );
	        $posts = get_posts( $args );
	        
	        $product_id = $posts[0]->post_parent;
	        $product_id = intval($product_id);
	        $stock = $product->get_stock_quantity();

	    }
	    return $stock;
	}

	static function get_products_sku($url, $token) {
		$items_zoho = Zoho_int::get_items($url, $token);

		$i = 0;
		$items = [];
		foreach($items_zoho as $item){
			$items[$i]['item_id'] = $item['Item_ID'];
			$items[$i]['sku'] = $item['SKU'];
			$i++;
		}	

		$product_items = [];
		$i=0;
		foreach($items as $key => $item){
			$quantity = check($item['sku']);
			
			if($quantity != 0){
				$product_items[$i]['item_id'] = $item['item_id'];
				$product_items[$i]['quantity'] = $quantity;
			}
			
			$i++;
		}

		return $product_items; 
	}

	static function send_to_zoho() {
		

		return $product_items; 
	}

}
