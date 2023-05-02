<div class="ads__footer">
    <a href="<?php echo Ads_Admin::FAQ_URL; ?>" class="ads__footer-link">
        <?php _e('FAQ', 'monetag');?>
    </a>

    <a href="<?php echo Ads_Admin::KNOWLEDGE_BASE_URL; ?>" class="ads__footer-link">
        <?php _e('Knowledge base', 'monetag');?>
    </a>

    <a href="<?php echo Ads_Admin::SUPPORT_URL ?>" class="ads__footer-link">
        <?php _e('Support team', 'monetag');?>
    </a>

<?php if ($this->setting_helper->get_anti_adblock_token()): ?>
    <a href="<?php echo esc_html($this->plugin_url()) ?>&publisher-logout"
        class="ads__footer-link"
        onclick="return confirm('<?php esc_attr_e('Are you sure to logout? All installed tags will refused by logout.\n\nIf you are want to use another Monetag account, please logout or re-login in SSP before.', 'monetag');?>')"
    >
        <?php _e('Logout from plugin', 'monetag');?>
        <span class="ads__icon ads__icon--arrow"></span>
    </a>
<?php endif; ?>
</div>
