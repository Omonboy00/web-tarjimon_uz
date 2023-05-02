<div class="ads__modal">
	<div class="ads__modal-container">
		<h1 class="ads__modal-title" data-modal-title></h1>

		<div class="ads__modal-label">
			<?php _e('Zone', 'monetag');?>
		</div>

		<div class="ads__modal-tabs">
			<div class="ads__modal-tab" data-tab="existing" >
				<?php _e('Existing', 'monetag');?>
			</div>
			<div class="ads__modal-tab" data-tab="new">
				<?php _e('Create new', 'monetag');?>
			</div>
		</div>

		<div class="ads__content">

			<div class="ads__modal-content-tab" data-content-tab="new">
				<form action="options.php" method="post">
					<div>
						<input name="title" type="text" class="ads__modal-input" data-field-title  placeholder="<?php echo _e('Enter zone name', 'monetag'); ?>" />
					</div>

					<div class="ads__modal-options hidden" data-only-for="nativeads">
						<div class="ads__modal-label">
							<?php _e('Pricing model', 'monetag'); ?>
						</div>

						<div class="ads__modal-option">
							<input data-field-rate-model-id type="radio" id="rate-model-1" name="rate_model_id" value="1">
							<label for="rate-model-1">CPM - <?php _e('Get paid for each notification shown', 'monetag');?></label>
						</div>
						
						<div class="ads__modal-option">
							<input data-field-rate-model-id type="radio" id="rate-model-7" name="rate_model_id" value="7">
							<label for="rate-model-7">CPS - <?php _e('Get paid for each user subscribing on your website', 'monetag');?></label>
						</div>
					</div>

					<div class="ads__modal-buttons">
						<button class="ads__button" type="submit">
							<?php _e('Create', 'monetag');?>
						</button>
						<button class="ads__button ads__button--secondary" type="reset">
							<?php _e('Cancel', 'monetag');?>
						</button>
					</div>
				</form>
			</div>

			<div class="ads__modal-content-tab" data-content-tab="existing">
				<form action="options.php" method="post">
					<div>
						<select class="ads__modal-select" data-field-zone-id></select>
					</div>

					<div class="ads__modal-buttons">
						<button class="ads__button" type="submit">
							<?php _e('Save', 'monetag');?>
						</button>
						<button class="ads__button ads__button--secondary" type="reset">
							<?php _e('Cancel', 'monetag');?>
						</button>
					</div>
				</form>
			</div>
		</div>

	</div>

	<div class="ads__modal-overlay"></div>
</div>
