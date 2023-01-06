<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://zoho_int.com
 * @since      1.0.0
 *
 * @package    Zoho_int
 * @subpackage Zoho_int/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Zoho_int
 * @subpackage Zoho_int/admin
 * @author     Denis <yankinndenis@gmail.com>
 */
class Zoho_int_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zoho_int_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zoho_int_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'theme-blue', plugin_dir_url( __FILE__ ) . 'css/theme.blue.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/zoho_int-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zoho_int_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zoho_int_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$screen = get_current_screen();

		if ( in_array( $screen->id, array( 'zoho_int/entrance' ) ) ){
		    wp_enqueue_script( 'tablesorter', plugin_dir_url( __FILE__ ) . 'js/jquery.tablesorter.min.js', array( 'jquery-core' ), $this->version, false );
			wp_enqueue_script( 'tablesorter-widgets', plugin_dir_url( __FILE__ ) . 'js/jquery.tablesorter.widgets.min.js', array( 'jquery-core' ), $this->version, false );
			wp_enqueue_script( 'tablesorter-pager', plugin_dir_url( __FILE__ ) . 'js/jquery.tablesorter.pager.js', array( 'jquery-core' ), $this->version, false );
			wp_enqueue_script( 'tablesorter-script', plugin_dir_url( __FILE__ ) . 'js/tablesorter.js', array( 'jquery-core' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/zoho_int-admin.js', array( 'jquery-core' ), $this->version, false );
		}	

	}

	

}
