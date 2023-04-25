<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class Ads
{
	/**
	 * @var Ads_Loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * @var string The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * @var string The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 */
	public function __construct()
	{
		$this->plugin_name = 'monetag';
		$this->version = '1.0.0';

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
	 * - Ads_Loader. Orchestrates the hooks of the plugin.
	 * - Ads_i18n. Defines internationalization functionality.
	 * - Ads_Admin. Defines all hooks for the admin area.
	 * - Ads_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 */
	private function load_dependencies()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ads-loader.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ads-i18n.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ads-settings-helper.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ads-anti-adblock.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ads-anti-adblock-client.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ads-zone-helper.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ads-messages.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ads-admin.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ads-public.php';

		$this->loader = new Ads_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale()
	{
		$plugin_i18n = new Ads_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Ads_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_init', $plugin_admin, 'register_session');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_settings_page');
		$this->loader->add_action('admin_init', $plugin_admin, 'zone_update_event');
		$this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
		$this->loader->add_action('admin_init', $plugin_admin, 'redirect_after_update');
		$this->loader->add_action('admin_init', $plugin_admin, 'auto_save_publisher_token');
		$this->loader->add_action('admin_head', $plugin_admin, 'display_admin_modal');
		$this->loader->add_action('admin_footer', $plugin_admin, 'display_admin_footer');

		$this->loader->add_action('wp_ajax_update_logged_in_disabled', $plugin_admin, 'ajax_action_update_logged_in_disabled');
		$this->loader->add_action('wp_ajax_update_zone_id_option', $plugin_admin, 'ajax_action_update_zone_id_option');
		$this->loader->add_action('wp_ajax_update_zone_enabled_option', $plugin_admin, 'ajax_action_update_zone_enabled_option');
		$this->loader->add_action('wp_ajax_create_zone', $plugin_admin, 'ajax_action_create_zone');

		$this->loader->add_action('add_option_Ads_nativeads_zone_id', $plugin_admin, 'action_save_nativeads_zone_id');
		$this->loader->add_action('update_option_Ads_general_token', $plugin_admin, 'action_save_publisher_token');
		$this->loader->add_action('update_option_Ads_nativeads_zone_id', $plugin_admin, 'action_save_nativeads_zone_id');
		$this->loader->add_action('in_plugin_update_message-ads/ads.php', $plugin_admin,'action_in_plugin_update');
		
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Ads_Public($this->get_plugin_name());

		$this->loader->add_filter('wp_head', $plugin_public, 'insert_verification_code');
		$this->loader->add_filter('wp_footer', $plugin_public, 'publish_tags');

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'publish_aab_tags');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return Ads_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}
}
