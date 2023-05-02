<div class="ads">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

	<div class="ads__messages">
        <?php Ads_Messages::show_messages(); ?>
	</div>

	<div class="ads__container">
        <div class="ads__box">
            <div class="ads__box-title">
                <?php _e('Connect the plugin to Monetag', 'monetag');?>
            </div>

            <div class="ads__box-content">
                <div class="ads__text">
                    <p>
                        <?php _e('You will be redirected to the Monetag Cabinet', 'monetag');?>
                    </p>
                    <p>
                        <?php _e('If you are already logged in, the plugin will automatically connect, otherwise you will need to log in to your account', 'monetag');?>
                    </p>
                </div>
                <div class="ads__buttons">
                    <button class="ads__button" onclick="window.location='<?php echo esc_html($this->token_url()); ?>';">
                        <?php _e('Connect', 'monetag');?>
                    </button>
                </div>  
            </div>
        </div>
        <div class="ads__text">
            <p>
                <span class="ads__icon ads__icon--profile"></span>
                <?php _e('If you don’t have an account', 'monetag');?>,
                <a href="<?php echo Ads_Admin::SIGNUP_URL; ?>"><?php _e('create one', 'monetag');?></a>
            </p>
        </div>

    </div>
</div>
