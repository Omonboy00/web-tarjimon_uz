<?php

$zone_list = $this->zone_helper->get_publisher_zones_group_by_direction();
$zone_names = $this->zone_helper->get_direction_titles();
$zones_directions = $this->setting_helper->get_zones_directions();

?>

<script type="text/javascript">
	var $ = jQuery;

	var isModalOpended = false;
	var zoneNames = <?php echo json_encode($zone_names); ?>;
	var zoneList = <?php echo json_encode($zone_list); ?>;
	var zonesDirection = <?php echo json_encode($zones_directions); ?>;

	var errorContainer = $(".ads__messages");
	var defaultTitles = [
		"The best tag",
		"Cheerful tag",
		"Glad tag",
		"Profitable tag",
	];

	function generateRandomTitle() {
    	var randomIndex = Math.floor(Math.random() * (defaultTitles.length + 1));

		return defaultTitles[randomIndex];
	}

	function showModal() {
		isModalOpended = true;
		$(".ads__modal").css('display', 'flex');
	}	

	function hideModal() {
		isModalOpended = false;
		$(".ads__modal").css('display', 'none');
	}

	function redirect() {
		window.location = window.location;
	}

	function showError(text) {
		$("<div>", { 
			id: "setting-error-Ads_message",
			class: "notice notice-error settings-error is-dismissible",
			html: "<p><strong>"+ text + "</strong></p><button type=\"button\" class=\"notice-dismiss\"><span class=\"screen-reader-text\">Dismiss this notice.</span></button>"
		}).appendTo(errorContainer);
	}

	function ajaxAction(data, onSuccess, onError) {
		$.ajax({
			url: ajaxurl,
			method: 'POST',
			data: data,
			success: onSuccess,
			error: onError
		});
	}

	// error behavior
	$(document).ready(function() {
		errorContainer.on('click', 'button.notice-dismiss', function(e) {
			$(e.target).parents('.notice').remove();
		})
	});

	// modal behavior
	$(document).ready(function() {
		var $modal = $(".ads__modal");
		var $overlay = $modal.find(".ads__modal-overlay");
		var $form = $modal.find("form");

		$overlay.on('click', function() {
			hideModal();
		});

		$form.on('reset', function(e) {
			e.preventDefault();
			hideModal();
		});

		$(document).keyup(function(e) {
			if (e.key === "Escape") {
				hideModal();
			}
		});
	});

    // edit zone behavior
	$(document).ready(function() {
		var currentFormat = null;
		var isModalOpenedFromSelect = false;

		var $modal = $(".ads__modal");
		var $modalTitle = $modal.find("[data-modal-title]");

		function setGroupAction(direction, group) {
			var $card = $('[data-card-direction="' +  direction + '"]');

			$card.find('[data-group-action]').removeClass('ads__group-action--active');
			$card.find('[data-group-action="' + group + '"]').addClass('ads__group-action--active');
		}

		function setDirectionEnabled(direction) {
			$('[data-action="toggle-enable"][data-direction="' +  direction + '"]').prop('checked', 'checked')
		}

		function setDirectionTitle(direction, title) {
			$('[data-description][data-direction="' + direction + '"]').text(title)
		}

		function setActiveTab(tabName) {
			$modal.find('[data-tab]').removeClass('ads__modal-tab--active');
			$modal.find('[data-tab="' + tabName + '"]').addClass('ads__modal-tab--active');
			$modal.find('[data-content-tab]').removeClass('ads__modal-content-tab--active');

			var $contentTab = $modal.find('[data-content-tab="' + tabName + '"]');

			$contentTab.addClass('ads__modal-content-tab--active');
			$contentTab.find('input:text, input:radio, select').focus();
			$contentTab.find('[data-only-for]').addClass('hidden');
			$contentTab.find('[data-only-for="' + currentFormat + '"]').removeClass('hidden');

			if (!$contentTab.find('[data-only-for] input:radio:checked').length) {
				$contentTab.find('[data-only-for] input:radio:first').prop('checked', true);
			}
		}

		function setupExistingSaveButtonText() {
			var $button = $modal.find('[data-content-tab="existing"] button[type="submit"]')

			if (isModalOpenedFromSelect) {
				$button.text('<?php _e('Save and enabled' , 'monetag'); ?>');
			} else {
				$button.text('<?php _e('Save', 'monetag'); ?>');
			}
		}

		function resetTitleInput() {
			$('input[data-field-title]').val(
				generateRandomTitle()
			);
		}

		function fillZoneOptions() {
			var $select = $modal.find("[data-field-zone-id]");

			if (zoneList[currentFormat]) {
				$select.find("option").remove();
				$modalTitle.text(zoneNames[currentFormat]);
				$.each(zoneList[currentFormat], function (i, item) {
					$select.append($('<option>', { 
						value: item.id,
						text : item.title
					}));
				});

				var selected = zonesDirection[currentFormat] || $select.find("option:first").val()

				$select.val(selected);
			}
		}

		// process modify existing format form
		$modal.find('[data-content-tab="existing"] form').on('submit', function(e) {
			e.preventDefault();

			var zoneId = parseInt($modal.find("[data-field-zone-id]").val(), 10);
			var zoneTitle;

			$.each(zoneList[currentFormat], function (i, item) {
				if (item.id === zoneId) {
					zoneTitle = item.title;
				}
			});

			ajaxAction({
				action: 'update_zone_id_option',
				direction: currentFormat,
				zone_id: zoneId,
			}, function() {
				zonesDirection[currentFormat] = zoneId;
				setDirectionTitle(currentFormat, zoneTitle);
			}, function() { 
				showError("<?php _e('Can not change zone' , 'monetag'); ?>");
			});

			if (isModalOpenedFromSelect) {
				ajaxAction({
					action: 'update_zone_enabled_option',
					direction: currentFormat,
					enabled: 1,
				}, function() {
					$('[data-action="toggle-enable"][data-direction="' + currentFormat + '"]').prop('checked', 'checked');
					setGroupAction(currentFormat, 'edit');
					setDirectionEnabled(currentFormat);
				}, function () {
					showError("<?php _e('Can not enabled direction' , 'monetag'); ?>");
				});
			}

			hideModal();
		});

		// process create format zone form
		$modal.find('[data-content-tab="new"] form').on('submit', function(e) {
			e.preventDefault();

			var valueTitle = $('input[data-field-title]').val();
			var valueRateModelId = $('[data-field-rate-model-id]:checked').val();

			ajaxAction({
				action: 'create_zone',
				direction: currentFormat,
				title: (valueTitle || '').trim() || generateRandomTitle(),
				rate_model_id: valueRateModelId,
			}, function(data) {
				setGroupAction(currentFormat, 'edit');
				setDirectionEnabled(currentFormat);
				setDirectionTitle(currentFormat, data.zone &&  data.zone.title ? data.zone.title : valueTitle);
				if (!zoneList[currentFormat]) {
					zoneList[currentFormat] = [];
				}
				if (data.zone) {
					zoneList[currentFormat].push(data.zone);
					zonesDirection[currentFormat] = data.zone.id;
				}
			}, function() {
				showError("<?php _e('Can not create zone' , 'monetag'); ?>");
			});

			hideModal();
		});

		$('[data-tab]').on('click',function(e) {
			setActiveTab(e.target.dataset.tab);
		});

		// actions

		$('[data-action="select"]').on('click', function(e) {
			currentFormat = e.target.dataset.direction;
			isModalOpenedFromSelect = true;

			$modal.find('[data-tab]').css('display', 'block');

			resetTitleInput();
			fillZoneOptions();
			setupExistingSaveButtonText();
			setActiveTab('existing');
			showModal();
		});

		$('[data-action="create"]').on('click', function(e) {
			currentFormat = e.target.dataset.direction;
			isModalOpenedFromSelect = false;

			$modal.find('[data-tab]').not('[data-tab="new"]').css('display', 'none');

			resetTitleInput();
			fillZoneOptions();
			setupExistingSaveButtonText();
			setActiveTab('new');
			showModal();
		});

		$('[data-action="edit"]').on('click', function(e) {
			currentFormat = e.target.dataset.direction;
			isModalOpenedFromSelect = false;

			$modal.find('[data-tab]').css('display', 'block');

			resetTitleInput();
			fillZoneOptions();
			setupExistingSaveButtonText();
			setActiveTab('existing');
			showModal();
		});
	});

    // options
    $(document).ready(function() {
		$('[data-action="toggle-enable"]').on('change', function(e) {
			ajaxAction({
				action: 'update_zone_enabled_option',
				direction: e.target.dataset.direction,
				enabled: e.target.checked ? 1 : 0,
			}, null, function() {
				showError("<?php _e('Can not toggle direction' , 'monetag'); ?>");
			});
		});

		// option: logged_in_disabled
        $('[data-action="toggle-logged_in_disabled"]').on('change', function(e) {
			ajaxAction({
				action: 'update_logged_in_disabled',
				value: e.target.checked ? 1 : 0,
			}, null, function() {
				showError("<?php _e('Can not change `disabled` option' , 'monetag'); ?>");
			});
        });
    });
</script>
