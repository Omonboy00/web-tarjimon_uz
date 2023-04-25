<?php

/**
 * Helper functions for registering / rendering settings
 */
class Ads_Settings_Helper
{
	// Field types
	const FIELD_TYPE_CHECKBOX = 'checkbox';
	const FIELD_TYPE_INPUT_TEXT = 'input_text';
	const FIELD_TYPE_DROPDOWN = 'dropdown';
	const FIELD_TYPE_INPUT_HIDDEN = 'hidden';

	/**
	 * @var string $settings_page The slug-name of the settings page
	 */
	private $settings_page;

	/**
	 * @var string $settings_prefix Unique options prefix for plugin
	 */
	private $settings_prefix;

	public function __construct($settings_page)
	{
		$this->settings_page = $settings_page;
		$this->settings_prefix = str_replace('-', '_', $this->settings_page);
	}

	/**
	 * Get publisher AntiAdBlock token
	 *
	 * @return string
	 */
	public function get_anti_adblock_token()
	{
		return $this->get_field_value('general', 'token');
	}

	/**
	 * Store publisher AntiAdBlock token
	 *
	 * @param string $value
	 */
	public function set_anti_adblock_token($value)
	{
		$this->set_field_value('general', 'token', $value);
	}

	/**
	 * Get publisher site id
	 *
	 * @return string
	 */
	public function get_publisher_site_id()
	{
		return $this->get_field_value('general', 'publisher_site_id');
	}

	/**
	 * Store publisher site id
	 *
	 * @param string $value
	 */
	public function set_publisher_site_id($value)
	{
		$this->set_field_value('general', 'publisher_site_id', $value);
	}

	/**
	 * Get site verification code
	 *
	 * @return string|false
	 */
	public function get_verification_code()
	{
		return $this->get_field_value('general', 'verification_code');
	}

	/**
	 * Store site verification code
	 *
	 * @param string $value
	 */
	public function set_verification_code($value)
	{
		$this->set_field_value('general', 'verification_code', $value);
	}

	public function is_ads_disabled_for_authorized_users()
	{
		return $this->get_field_value('general', 'logged_in_disabled');
	}

	/**
	 * Store publisher AntiAdBlock token
	 *
	 * @param string $value
	 */
	public function set_logged_in_disabled($value)
	{
		$this->set_field_value('general', 'logged_in_disabled', $value);
	}

	/**
	 * Get field (option) value
	 *
	 * @param int $section_id
	 * @param int $field_id
	 *
	 * @return mixed    Option value
	 */
	public function get_field_value($section_id, $field_id)
	{
		return get_option($this->get_field_id($section_id, $field_id));
	}

	/**
	 * Delete field (option)
	 *
	 * @param int $section_id
	 * @param int $field_id
	 *
	 * @return mixed    Option value
	 */
	public function delete_field($section_id, $field_id)
	{
		return delete_option($this->get_field_id($section_id, $field_id));
	}

	public function get_field_id($section_id, $field_id)
	{
		return sprintf('%s_%s_%s', $this->settings_prefix, $section_id, $field_id);
	}

	/**
	 * Add settings section to plugin settings page
	 *
	 * @param array $config Key-value config (id, title)
	 */
	public function add_section($config)
	{
		add_settings_section(
			$this->get_section_id($config['id']),
			__($config['title'], $this->settings_page),   // TODO: is it ok for i18n tools?
			array($this, 'render_section'),   // TODO: Do we need to configure callback?
			$this->settings_page
		);
	}

	private function get_section_id($id)
	{
		return sprintf('%s_%s', $this->settings_prefix, $id);
	}

	/**
	 * Register setting and setup field rendering / sanitization
	 *
	 * @param array $config Key-value config (type, id, title, section)
	 */
	public function add_field($config)
	{
		$field_id = $this->get_field_id($config['section'], $config['id']);
		$renderer_name = 'render_' . $config['type'];
		$args = array_merge($config, array(
			'id' => $field_id,
			'label_for' => $field_id,
			'value' => $this->get_field_value($config['section'], $config['id']),
		));

		add_settings_field(
			$field_id,
			__(isset($config['title']) ? $config['title'] : '', $this->settings_page),
			array($this, $renderer_name),
			$this->settings_page,
			$this->get_section_id($config['section']),
			$args
		);

		register_setting(
			$this->settings_page,
			$field_id,
			$this->get_sanitize_callback($config['type'])
		);

		if (isset($config['validate']) && $config['validate'] === true) {
			register_setting(
				$this->settings_page,
				$field_id,
				array($this, 'validate_' . $field_id)
			);
		}
	}

	private function get_sanitize_callback($type)
	{
		if ($type === self::FIELD_TYPE_CHECKBOX) {
			return 'intval';
		}

		return '';
	}

	/**
	 * Set field (option) value
	 *
	 * @param int    $section_id
	 * @param int    $field_id
	 * @param string $value
	 */
	public function set_field_value($section_id, $field_id, $value)
	{
		update_option($this->get_field_id($section_id, $field_id), $value);
	}

	/**
	 * Delete options after update token
	 */
	public function clear_settings()
	{
		$this->delete_field(Ads_Zone_Helper::DIRECTION_ONCLICK, 'enabled');
		$this->delete_field(Ads_Zone_Helper::DIRECTION_INTERSTITIAL, 'enabled');
		$this->delete_field(Ads_Zone_Helper::DIRECTION_PUSH_NOTIFICATION, 'enabled');
		$this->delete_field(Ads_Zone_Helper::DIRECTION_IN_PAGE_PUSH, 'enabled');
		$this->delete_field(Ads_Zone_Helper::DIRECTION_VIGNETTE, 'enabled');

		$this->delete_field(Ads_Zone_Helper::DIRECTION_ONCLICK, 'zone_id');
		$this->delete_field(Ads_Zone_Helper::DIRECTION_INTERSTITIAL, 'zone_id');
		$this->delete_field(Ads_Zone_Helper::DIRECTION_PUSH_NOTIFICATION, 'zone_id');
		$this->delete_field(Ads_Zone_Helper::DIRECTION_IN_PAGE_PUSH, 'zone_id');
		$this->delete_field(Ads_Zone_Helper::DIRECTION_VIGNETTE, 'zone_id');

		$this->delete_field('general', 'logged_in_disabled');
	}

	public function get_enabled_directions()
	{
		return [
			Ads_Zone_Helper::DIRECTION_ONCLICK => $this->get_field_value(Ads_Zone_Helper::DIRECTION_ONCLICK, 'enabled'),
			Ads_Zone_Helper::DIRECTION_INTERSTITIAL => $this->get_field_value(Ads_Zone_Helper::DIRECTION_INTERSTITIAL, 'enabled'),
			Ads_Zone_Helper::DIRECTION_PUSH_NOTIFICATION => $this->get_field_value(Ads_Zone_Helper::DIRECTION_PUSH_NOTIFICATION, 'enabled'),
			Ads_Zone_Helper::DIRECTION_IN_PAGE_PUSH => $this->get_field_value(Ads_Zone_Helper::DIRECTION_IN_PAGE_PUSH, 'enabled'),
			Ads_Zone_Helper::DIRECTION_VIGNETTE => $this->get_field_value(Ads_Zone_Helper::DIRECTION_VIGNETTE, 'enabled')
		];
	}

	public function get_zones_directions()
	{
		return [
			Ads_Zone_Helper::DIRECTION_ONCLICK => $this->get_field_value(Ads_Zone_Helper::DIRECTION_ONCLICK, 'zone_id'),
			Ads_Zone_Helper::DIRECTION_INTERSTITIAL => $this->get_field_value(Ads_Zone_Helper::DIRECTION_INTERSTITIAL, 'zone_id'),
			Ads_Zone_Helper::DIRECTION_PUSH_NOTIFICATION => $this->get_field_value(Ads_Zone_Helper::DIRECTION_PUSH_NOTIFICATION, 'zone_id'),
			Ads_Zone_Helper::DIRECTION_IN_PAGE_PUSH => $this->get_field_value(Ads_Zone_Helper::DIRECTION_IN_PAGE_PUSH, 'zone_id'),
			Ads_Zone_Helper::DIRECTION_VIGNETTE => $this->get_field_value(Ads_Zone_Helper::DIRECTION_VIGNETTE, 'zone_id')
		];
	}
}
