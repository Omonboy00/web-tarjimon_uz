<?php

/**
 * Flash messages for plugin
 */
class Ads_Messages
{
	const MESSAGES = 'messages';

	const TYPE_SUCCESS = 'updated';
	const TYPE_ERROR = 'error';

	/**
	 * Add a new message
	 *
	 * @param string $text
	 * @param string $type
	 */
	public static function add_message($text, $type = self::TYPE_SUCCESS)
	{
		if (!isset($_SESSION[self::MESSAGES])) {
			$_SESSION[self::MESSAGES] = array();
		}

		$_SESSION[self::MESSAGES][] = array(
			'content' => $text,
			'type' => $type,
		);
	}

	/**
	 * Show messages and remove it after show
	 */
	public static function show_messages()
	{
		if (isset($_SESSION[self::MESSAGES])) {
			foreach ($_SESSION[self::MESSAGES] as $message) {
				add_settings_error(
					'Ads_messages',
					'Ads_message',
					sanitize_text_field( $message['content'] ),
					sanitize_text_field( $message['type'] )
				);
			}

			$_SESSION[self::MESSAGES] = array();
		}

		settings_errors('Ads_messages');
	}
}
