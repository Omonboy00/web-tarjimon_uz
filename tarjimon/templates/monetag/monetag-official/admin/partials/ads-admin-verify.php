<div class="ads">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

	<div class="ads__messages">
        <?php Ads_Messages::show_messages(); ?>
	</div>

	<div class="ads__container">
        <div class="ads__box">
            <div class="ads__box-checked">
                <span class="ads__icon ads__icon--checked"></span>
            </div>
            <div class="ads__box-title">
                <?php _e('The plugin is connected, but the site is waiting for verification', 'monetag');?>
            </div>

            <div class="ads__box-content">
                <div class="ads__text">
                    <p>
                        <?php _e('This may take some time, but usually no more than a few minutes. After that, you will have access to format management.', 'monetag');?>
                    </p>
                </div>
                <div class="ads__buttons">
                    <button class="ads__button" onclick="window.location='<?php echo esc_html($this->token_url()); ?>';">
                        <?php _e('Check verification', 'monetag');?>
                    </button>
                </div> 
                <div class="ads__text">
                    <p>
                        <a href="<?php echo Ads_Admin::SITES_LIST; ?>">
                            <?php _e('Open my sites in Monetag', 'monetag');?>
                            <span class="ads__icon ads__icon--arrow"></span>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>