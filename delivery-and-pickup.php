<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.arnedebelser.be
 * @since             1.0.0
 * @package           Delivery_And_Pickup
 *
 * @wordpress-plugin
 * Plugin Name:       Delivery and Pickup
 * Plugin URI:        https://www.arnedebelser.be
 * Description:       No description
 * Version:           1.0.0
 * Author:            De Belser Arne
 * Author URI:        https://www.arnedebelser.be
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       delivery-and-pickup
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('DELIVERY_AND_PICKUP_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-delivery-and-pickup-activator.php
 */
function activate_delivery_and_pickup()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-delivery-and-pickup-activator.php';
	Delivery_And_Pickup_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-delivery-and-pickup-deactivator.php
 */
function deactivate_delivery_and_pickup()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-delivery-and-pickup-deactivator.php';
	Delivery_And_Pickup_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_delivery_and_pickup');
register_deactivation_hook(__FILE__, 'deactivate_delivery_and_pickup');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-delivery-and-pickup.php';

if (!function_exists('dd')) {
	function dd($message)
	{
		echo '<pre>';
		var_dump($message);
		echo '</pre>';
		die();
	}
}

define('DAP_PLUGIN_LOG',	plugin_dir_path(__FILE__) . 'debug.log');

if (!function_exists('dbdp_log')) {
	function dbdp_log($message)
	{
		$logLine = '[' . date('d F Y, h:i:s') . '] ' . print_r($message, true) . PHP_EOL;
		error_log($logLine, 3, DAP_PLUGIN_LOG);
	}
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_delivery_and_pickup()
{
	$plugin = new Delivery_And_Pickup();
	$plugin->run();
}

run_delivery_and_pickup();
