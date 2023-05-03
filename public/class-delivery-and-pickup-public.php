<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.arnedebelser.be
 * @since      1.0.0
 *
 * @package    Delivery_And_Pickup
 * @subpackage Delivery_And_Pickup/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Delivery_And_Pickup
 * @subpackage Delivery_And_Pickup/public
 * @author     De Belser Arne <arne@arnedebelser.be>
 */
class Delivery_And_Pickup_Public
{

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->run_views();
	}

	public function run_views()
	{
		require_once plugin_dir_path(__FILE__) . 'views/class-delivery-and-pickup-checkout-view.php';

		new Delivery_And_Pickup_Checkout_View();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/delivery-and-pickup-public.css', array(), $this->version, 'all');

		wp_enqueue_style('jquery-ui-datepicker-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script('jquery-ui-datepicker');

		wp_enqueue_script('dap-checkout', plugin_dir_url(__FILE__) .  'js/delivery-and-pickup-selector.js', ['jquery'], $this->version, true);
		wp_enqueue_script('dap-datepicker', plugin_dir_url(__FILE__) .  'js/delivery-and-pickup-datepicker.js', ['jquery'], $this->version, true);
	}
}
