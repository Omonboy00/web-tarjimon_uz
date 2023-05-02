<?php

$directions = $this->zone_helper->get_allowed_directions();
$zone_list = $this->zone_helper->get_publisher_zone_list();
$enabled_directions = $this->setting_helper->get_enabled_directions();
$zones_directions = $this->setting_helper->get_zones_directions();
$logged_in_disabled = $this->setting_helper->is_ads_disabled_for_authorized_users();

?>
<div class="ads">

	<h2><?php echo esc_html(get_admin_page_title()); ?></h2>

	<div class="ads__messages">
		<?php Ads_Messages::show_messages(); ?>
	</div>

	<div class="ads__container">

		<h2 class="ads__title">
			<?php echo _e('Ad Formats', 'monetag');?>
		</h2>

		<div class="ads__description">
			<?php echo _e('Enable the preferred advertising formats for this site', 'monetag'); ?>
		</div>

		<div class="ads__cards">
			<?php foreach ($directions as $direction):?>
				<div class="ads__card" data-card-direction="<?php echo esc_html($direction); ?>">
					<div class="ads__card-image ads__card-image--<?php echo esc_html($direction); ?>"></div>

					<div class="ads__card-title">
						<?php echo esc_html($this->zone_helper->get_direction_title($direction)); ?>
					</div>

					<div class="ads__card-description" data-description data-direction="<?php echo esc_html($direction); ?>">
						<?php if (!empty($zones_directions[$direction]) && !empty($zone_list[$zones_directions[$direction]])) : ?>
							<?php echo esc_html($zone_list[$zones_directions[$direction]]['title']); ?>
						<?php else: ?>
							<?php echo esc_html($this->zone_helper->get_direction_description($direction)); ?>
						<?php endif; ?>
					</div>

					<div class="ads__group-actions">
						<?php
							$action = 'create';
							if ($this->zone_helper->direction_has_zones($direction)) {
								if (!empty($zones_directions[$direction])) {
									$action = 'edit';
								} else {
									$action = 'select';
								}
							}
						?>

						<div class="ads__group-action <?php echo $action === "edit" ? "ads__group-action--active" : "" ?>" data-group-action="edit">
							<label class="ads__switch">
								<input type="checkbox" data-action="toggle-enable" data-direction="<?php echo esc_html($direction); ?>" <?php echo $enabled_directions[$direction] ? ' checked="checked"' : '';  ?>>
								<span class="ads__switch-slider"></span>
							</label>
							<div class="ads__action" data-action="edit" data-direction="<?php echo esc_html($direction); ?>">
								<span class="ads__icon ads__icon--pencil"></span>
								<?php _e('Edit', 'monetag'); ?>
							</div>
						</div>

						<div class="ads__group-action <?php echo $action === "select" ? "ads__group-action--active" : "" ?>" data-group-action="choose">
							<div class="ads__action" data-action="select" data-direction="<?php echo esc_html($direction); ?>">
								<span class="ads__icon ads__icon--select"></span>
								<?php _e('Choose a zone', 'monetag'); ?>
							</div>
						</div>

						<div class="ads__group-action <?php echo $action === "create" ? "ads__group-action--active" : "" ?>" data-group-action="create">
							<div class="ads__action" data-action="create" data-direction="<?php echo esc_html($direction); ?>">
								<span class="ads__icon ads__icon--plus"></span>
								<?php _e('Add format', 'monetag'); ?>
							</div>
						</div>
					</div>

				</div>
			<?php endforeach; ?>
		</div>

		<div class="ads__option-logged_in_disabled">
			<input type="checkbox" id="logged_in_disabled" data-action="toggle-logged_in_disabled" <?php echo $logged_in_disabled ? ' checked="checked"' : '';  ?>>
			<label for="logged_in_disabled">
				<?php _e('Do not show ads for logged users and admins', 'monetag'); ?>
			</label>
		</div>

		<div class="ads__action">
			<a href="<?php echo Ads_Admin::STATISTICS_URL; ?>">
				<?php _e('Go to statistics', 'monetag'); ?>
				<span class="ads__icon ads__icon--arrow"></span>
			</a>
		</div>

	</div>
</div>
