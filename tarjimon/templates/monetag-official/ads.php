<?php

/**
 * Plugin Name:       Monetag Official
 * Plugin URI:        https://wordpress.org/plugins/monetag-official/
 * Description:       This plugin helps to integrate and manage Monetag ad codes to increase revenue from websites.
 * Version:           1.0.5
 * Author:            Monetag
 * Author URI:        https://monetag.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ads
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

require plugin_dir_path(__FILE__) . 'includes/class-ads.php';

function run_ads()
{
	$plugin = new Ads();
	$plugin->run();
}

run_ads();
