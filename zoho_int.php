<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://zoho_int.com
 * @since             1.0.0
 * @package           Zoho_int
 *
 * @wordpress-plugin
 * Plugin Name:       Honeybee Herb Wholesale Dropship
 * Plugin URI:        https://zoho_int.com
 * Description:       A plugin that does integration with Honeybee Herb Wholesale Dropship.
 * Version:           1.0.0
 * Author:            Denis
 * Author URI:        https://zoho_int.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       zoho_int
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ZOHO_INT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-zoho_int-activator.php
 */
function activate_zoho_int() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zoho_int-activator.php';
	Zoho_int_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-zoho_int-deactivator.php
 */
function deactivate_zoho_int() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zoho_int-deactivator.php';
	Zoho_int_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_zoho_int' );
register_deactivation_hook( __FILE__, 'deactivate_zoho_int' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-zoho_int.php';
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_zoho_int() {

	$plugin = new Zoho_int();
	$plugin->run();

}
run_zoho_int();

add_action( 'admin_menu', 'Api_Link' );
function Api_Link()
{
    add_menu_page(
        'Honeybee Herb Wholesale Dropship', 
        'Honeybee Herb Wholesale Dropship', 
        'manage_options', 
        'zoho_int/entrance.php', 
    );
    add_submenu_page(
        'zoho_int/entrance.php', 
        'Settings', 
        'Settings', 
        'manage_options', 
        'zoho_int/settings.php'
    );

}