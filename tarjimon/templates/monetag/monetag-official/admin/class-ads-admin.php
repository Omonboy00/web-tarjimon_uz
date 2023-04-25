<?php

/**
 * The admin-specific functionality of the plugin.
 */
class Ads_Admin
{
	// SSP domain for getting Anti AdBlock token
	const SSP_DOMAIN = 'https://publishers.monetag.com';

	// HELP domain for knowledge base
	const HELP_DOMAIN = 'https://help.monetag.com';

	// URLs section
	const SITES_LIST = 'https://publishers.monetag.com/#/sites/list';
	const FAQ_URL = 'https://wordpress.org/plugins/monetag-official/#faq';
	const KNOWLEDGE_BASE_URL = 'https://wordpress.org/support/plugin/official-official/';
	const SUPPORT_URL = 'https://wordpress.org/support/plugin/official-official/';
	const STATISTICS_URL = 'https://publishers.monetag.com/#/statistics';
	const SIGNUP_URL = 'https://publishers.monetag.com/#/signUp';

	/**
	 * The ID of this plugin.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * The current version of this plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Settings helper instance
	 *
	 * @var Ads_Settings_Helper
	 */
	private $setting_helper;

	/**
	 * Zone helper instance
	 *
	 * @var Ads_Zone_Helper
	 */
	private $zone_helper;

	/**
	 * Adblock helper instance
	 * 
	 * @var Ads_Anti_Adblock
	 */
	private $anti_adblock;

	/**
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->setting_helper = new Ads_Settings_Helper($this->plugin_name);
		$this->zone_helper = new Ads_Zone_Helper($this->plugin_name);
		$this->anti_adblock = new Ads_Anti_Adblock($plugin_name);
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ads-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ads-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Add an settings page to the main menu
	 */
	public function add_settings_page()
	{
		// TODO: check https://developer.wordpress.org/reference/functions/add_menu_page/#notes about capabilities
		add_menu_page(
			__('Monetag', 'monetag'),
			__('Monetag', 'monetag'),
			'administrator',
			$this->plugin_name,
			array($this, 'display_options_page'),
			'none',
			76  // right after the 'Tools' submenu
		);
	}

	/**
	 * Render the options page
	 */
	public function display_options_page()
	{
		include_once 'partials/ads-admin.php';
	}

	/**
	 * Rernder admin footer menu
	 */
	public function display_admin_footer()
	{
		include_once 'partials/ads-admin-footer.php';
		
	}

	/**
	 * Rernder plugin modal 
	 */
	public function display_admin_modal()
	{
		include_once 'partials/ads-admin-modal.php';
		
	}

	/**
	 * Register all plugin settings
	 */
	public function register_settings()
	{
		$this->setting_helper->add_section(array(
			'id' => 'general',
			'title' => __('Settings', 'monetag'),
		));

		$this->setting_helper->add_field(array(
			'section' => 'general',
			'id' => 'logged_in_disabled',
			'title' => __('Membership', 'monetag'),
			'type' => Ads_Settings_Helper::FIELD_TYPE_CHECKBOX,
			'checkbox_label' => __('Do no show ads for logged users and admins', 'monetag'),
		));

		$this->setting_helper->add_field(array(
			'section' => 'general',
			'id' => 'token',
			'title' => __('Token', 'monetag'),
			'type' => Ads_Settings_Helper::FIELD_TYPE_INPUT_HIDDEN,
			'validate' => true,
			'description' => '<a href="' . $this->token_url() . '">' . __('Get or update token automatically', 'monetag') . '</a>',
		));

		$zone_list = $this->zone_helper->get_publisher_zones_group_by_direction();

		foreach ($zone_list as $direction => $zones) {
			$this->setting_helper->add_section(array(
				'id' => $direction,
				'title' => $this->zone_helper->get_direction_title($direction),
			));

			$this->setting_helper->add_field(array(
				'section' => $direction,
				'id' => 'enabled',
				'title' => __('Activation', 'monetag'),
				'type' => Ads_Settings_Helper::FIELD_TYPE_CHECKBOX,
				'checkbox_label' => __('Allow ads on all pages', 'monetag'),
			));

			$options = array();

			foreach ($zones as $zone) {
				$title = $zone['id'] . ' ';
				$title .= isset($zone['title']) ? $zone['title'] : $zone['name'];

				$options[] = array(
					'value' => $zone['id'],
					'title' => $title,
				);
			}

			$this->setting_helper->add_field(array(
				'section' => $direction,
				'id' => 'zone_id',
				'options' => $options,
				'type' => Ads_Settings_Helper::FIELD_TYPE_INPUT_HIDDEN,
			));
		}
	}

	/**
	 * Get url for getting AntiAdBlock token
	 *
	 * @return string
	 */
	public function token_url()
	{
		return self::SSP_DOMAIN . '/#/pub/sites/anti_adblock_token?return=' . base64_encode($this->plugin_url());
	}

	/**
	 * Gets plugin settings page url
	 *
	 * @return string
	 */
	public function plugin_url()
	{
		return admin_url('admin.php?page=' . $this->plugin_name);
	}

	/**
	 * Update settings page after update publisher zone list
	 * Wordpress action hook (admin_init)
	 */
	public function redirect_after_update()
	{
		if (isset($_GET['update-publisher-zones'])) {
			Ads_Messages::add_message(__('Cache was removed. Synchronization of new zones may process some time. Please, repeat this action in 10 minutes. Thank you.', 'monetag'));
			wp_redirect($this->plugin_url());
			exit();
		}

		if (isset($_GET['publisher-logout'])) {
			$this->setting_helper->delete_field('general', 'token');
			$this->setting_helper->clear_settings();
			Ads_Messages::add_message(__('Logout successful', 'monetag'));
			wp_redirect($this->plugin_url());
			exit();
		}
	}

	/**
	 * Save publisher Anti AdBlock after redirect from SSP
	 * Wordpress action hook (admin_init)
	 */
	public function auto_save_publisher_token()
	{
		if (isset($_GET['propeller-ads-aab-token'])) {
			$token = $this->setting_helper->get_anti_adblock_token();
			$value = sanitize_text_field($_GET['propeller-ads-aab-token']);

			if ($token !== $value) {
				$this->setting_helper->set_anti_adblock_token($value);
				$this->zone_helper->update_publisher_zones();
			}

			$this->auto_save_publisher_site_id();
			$this->auto_save_verification_code();

			wp_redirect($this->plugin_url());
			exit();
		}
	}

	public function ajax_action_create_zone()
	{
		$title = sanitize_text_field($_POST["title"]);
		$direction = sanitize_text_field($_POST["direction"]);

		if (empty($direction)) {
			wp_die('Bad request', '', [
				'response' => 400,
			]);
		}

		$token = $this->setting_helper->get_anti_adblock_token();
		$publisherSiteId = $this->setting_helper->get_publisher_site_id();

		$data = [
			"title" => $title,
			"format" => $direction,
		];

		if (array_key_exists("rate_model_id", $_POST)) {
			$data["rate_model_id"] = (int)$_POST["rate_model_id"];
		}

		$zone = $this->zone_helper->create_publisher_zone($token, $publisherSiteId, $data);

		$this->zone_helper->update_publisher_zones();
	
		if (!$zone || empty($zone['id'])) {
			wp_die('Zone creation error', '', [
				'response' => 400
			]);
		} else {
			$this->setting_helper->set_field_value($direction, 'zone_id', $zone['id']);
			$this->setting_helper->set_field_value($direction, 'enabled', true);

			wp_send_json([
				'zone'=> [
					'id' => $zone['id'],
					'title' => $zone['title'],
					'direction_name' => $zone['direction_name'],
				]
			]);
		}
	}

	public function ajax_action_update_zone_id_option()
	{
		$direction = sanitize_text_field($_POST["direction"]);
		$newValue = (int)$_POST['zone_id'];
		$oldValue = $this->setting_helper->get_field_value($direction, 'zone_id');

		if ($newValue !== $oldValue) {
			$this->setting_helper->set_field_value($direction, 'zone_id', $newValue);
		}
	}
	
	public function ajax_action_update_zone_enabled_option()
	{
		$direction = sanitize_text_field($_POST["direction"]);
		$newValue = (int)$_POST['enabled'];
		$oldValue = $this->setting_helper->get_field_value($direction, 'enabled');

		if ($newValue !== $oldValue) {
			$this->setting_helper->set_field_value($direction, 'enabled', $newValue);
		}
	}

	public function ajax_action_update_logged_in_disabled()
	{
		$oldValue = $this->setting_helper->is_ads_disabled_for_authorized_users();
		$newValue = (int)$_POST['value'];

		if ($newValue !== $oldValue) {
			$this->setting_helper->set_logged_in_disabled($newValue);
		}
	}

	private function auto_save_publisher_site_id()
	{
		if (isset($_GET['propeller-ads-publisher-site-id'])) {
			$value = (int)$_GET['propeller-ads-publisher-site-id'];
			$siteId = $this->setting_helper->get_publisher_site_id();

			if ($siteId !== $value) {
				$this->setting_helper->set_publisher_site_id($value);
			}
		} else {
			Ads_Messages::add_message(__('Hostname of your website is differ. Add another website in publisher panel', 'monetag'), Ads_Messages::TYPE_ERROR);
		}
	}

	private function auto_save_verification_code()
	{
		if (isset($_GET['propeller-ads-verification-code'])) {
			$value = sanitize_text_field($_GET['propeller-ads-verification-code']);
			$code = $this->setting_helper->get_verification_code();

			if ($code !== $value) {
				$this->setting_helper->set_verification_code($value);
			}
		}
	}

	public function action_save_publisher_token()
	{
		// Clear all POST data after save publisher token
		unset($_POST);
		$this->setting_helper->clear_settings();
	}

	public function action_save_nativeads_zone_id($prev_zone_id)
	{
		$next_zone_id = $this->setting_helper->get_field_value(Ads_Zone_Helper::DIRECTION_PUSH_NOTIFICATION, 'zone_id');

		if ($prev_zone_id !== $next_zone_id) {
			$this->anti_adblock->remove_service_worker($prev_zone_id);
			$this->anti_adblock->ensure_service_worker($next_zone_id);
		}
	}

	public function action_in_plugin_update()
	{
		$wp_list_table = _get_list_table('WP_Plugins_List_Table');

		printf(
			'<tr class="plugin-update-tr"><td colspan="%s" class="plugin-update update-message notice inline notice-warning notice-alt"><div class="update-message"><h4 style="margin: 0; font-size: 14px;">%s</h4>%s</div></td></tr>',
			$wp_list_table->get_column_count(),
			__('Monetag Official Plugin Update Info', 'monetag'),
			__('WARNING! This is a brand new Monetag plugin version and its not compatible with old one. You\'ll must to relogin to Monetag SSP via plugin\'s page.', 'monetag')
		);
	}

	/**
	 * Open session if it doesn't start
	 */
	public function register_session()
	{
		if (!session_id()) {
			session_start();
		}
	}

	public function zone_update_event()
	{
		if ($this->setting_helper->get_anti_adblock_token()) {
			$this->zone_helper->update_publisher_zones();
		}
	}
}
