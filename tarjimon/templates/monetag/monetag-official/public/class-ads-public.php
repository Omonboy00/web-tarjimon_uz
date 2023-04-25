<?php

/**
 * The public-facing functionality of the plugin.
 */
class Ads_Public
{
	/**
	 * Settings helper instance
	 *
	 * @var Ads_Settings_Helper
	 */
	private $setting_helper;

	/**
	 * Ad-Block file helper instance
	 *
	 * @var Ads_Anti_Adblock
	 */
	private $anti_adblock;

	/**
	 * Zones helper instance
	 *
	 * @var Ads_Zone_Helper
	 */
	private $zone_helper;

	/**
	 * @param string $plugin_name The name of the plugin.
	 */
	public function __construct($plugin_name)
	{
		$this->setting_helper = new Ads_Settings_Helper($plugin_name);
		$this->anti_adblock = new Ads_Anti_Adblock($plugin_name);
		$this->zone_helper = new Ads_Zone_Helper($plugin_name);
	}

	/**
	 * Publish tags for AntiAdBlock zones
	 */
	public function publish_aab_tags()
	{
		// do not publish tags if setting is activated and user is logged in
		if ( $this->setting_helper->is_ads_disabled_for_authorized_users() && is_user_logged_in() ) {
			return;
		}

		foreach ( $this->zone_helper->get_allowed_directions() as $direction ) {
			// ignore not activated directions
			if (!$this->setting_helper->get_field_value( $direction, 'enabled') ) {
				continue;
			}
			$zone_id = $this->setting_helper->get_field_value( $direction, 'zone_id' );
			// process AAB zones only
			if (!$this->zone_helper->is_anti_adblock_zone( $zone_id )) {
				continue;
			}

			if ($direction === Ads_Zone_Helper::DIRECTION_PUSH_NOTIFICATION) {
				$this->anti_adblock->ensure_service_worker( $zone_id );
			}

			// output AAB tag as published inline script to prevent quotes and html tags encoding
			$this->create_inline_script(
				$direction,
				$this->anti_adblock->get( $zone_id )
			);
		}
	}

	/**
	 * Publish tags for ordinary zones
	 */
	public function publish_tags()
	{
		// do not publish tags if setting is activated and user is logged in
		if ( $this->setting_helper->is_ads_disabled_for_authorized_users() && is_user_logged_in() ) {
			return;
		}

		$allowed_html = array(
			'script' => array(
				'type' => array(),
				'src' => array(),
				'async' => array(),
				'data-cfasync' => array(),
			),
		);

		foreach ( $this->zone_helper->get_allowed_directions() as $direction ) {
			// ignore not activated directions
			if (!$this->setting_helper->get_field_value( $direction, 'enabled') ) {
				continue;
			}
			$zone_id = $this->setting_helper->get_field_value( $direction, 'zone_id' );
			// process non AAB zones only
			if ($this->zone_helper->is_anti_adblock_zone( $zone_id )) {
				continue;
			}

			if ($direction === Ads_Zone_Helper::DIRECTION_PUSH_NOTIFICATION) {
				$this->anti_adblock->ensure_service_worker( $zone_id );
			}

			// output sanitized tag `as is`
			echo wp_kses( $this->anti_adblock->get( $zone_id ), $allowed_html ) . PHP_EOL;
		}
	}

	/**
	 * Register and enqueue custom inline javascript tag
	 *
	 * @param string $handler Script handler name
	 * @param string $content Script content
	 */
	private function create_inline_script($handler, $content)
	{
		if (empty($content)) {
			return;
		}
		/**
		 * $handler - script handler name
		 * $source - register without source path
		 * $dependencies - no script dependencies required
		 * $version - no version
		 * $in_footer - register script placeholder in page footer
		 */
		wp_register_script( $handler, '', array(), '', true );
		wp_enqueue_script( $handler );
		wp_add_inline_script( $handler, $content );
	}

	/**
	 * Insert meta tag with verification code
	 */
	public function insert_verification_code()
	{
		$verification_code = $this->setting_helper->get_verification_code();
		if ($verification_code !== false) {
			?>
			<meta name="propeller" content="<?php echo esc_attr( $verification_code ); ?>" />
			<?php
		}
	}
}
