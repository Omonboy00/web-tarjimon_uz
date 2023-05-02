<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 */

if (isset($_GET['settings-updated'])) {
	Ads_Messages::add_message(__('Settings Updated', 'monetag'));
}

if ($this->setting_helper->get_anti_adblock_token() && $this->setting_helper->get_publisher_site_id()) {
    include_once 'ads-admin-formats.php';
    include_once 'ads-admin-script.php';
} else if (!$this->setting_helper->get_verification_code()) {
    include_once 'ads-admin-verify.php';
} else {
    include_once 'ads-admin-connect.php';
}

?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
