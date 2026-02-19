/**
 * Wbcom Redirect Admin JS
 * Handles: toggle visibility, radio field switching, accordion, AJAX save.
 */
(function ($) {
	'use strict';

	$(document).ready(function () {

		// ——— Toggle enable/disable ———
		$(document).on('click', '.wbcom-redirect-toggle', function () {
			var $toggle = $(this);
			var target  = $toggle.data('target');
			var $hidden = $toggle.siblings('input[name="enabled"]');

			if ($toggle.prop('checked')) {
				$(target).slideDown(300);
				$hidden.val('yes');
			} else {
				$(target).slideUp(300);
				$hidden.val('no');
			}
		});

		// ——— Radio type switching: show/hide relevant fields ———
		$(document).on('change', '.wbcom-redirect-type-radio', function () {
			var $config = $(this).closest('.wbcom-redirect-config');
			var type    = $(this).val();

			$config.find('.wbcom-redirect-field').hide();
			$config.find('.wbcom-redirect-field-' + type).show();
		});

		// ——— Accordion for role/group-type sections ———
		$('[id$="-accordion"]').each(function () {
			$(this).accordion({
				heightStyle: 'content',
				collapsible: true,
				icons: {
					header: 'ui-icon-plus',
					activeHeader: 'ui-icon-minus'
				},
				header: '> .group > h3'
			}).sortable({
				axis: 'y',
				handle: 'h3',
				stop: function (event, ui) {
					ui.item.children('h3').triggerHandler('focusout');
					$(this).accordion('refresh');
				}
			});
		});

		// ——— AJAX Save ———
		$(document).on('click', '.wbcom-redirect-save', function () {
			var $btn     = $(this);
			var $form    = $btn.closest('.wbcom-redirect-form');
			var $spinner = $form.find('.wbcom-redirect-spinner');
			var $notice  = $form.find('.wbcom-redirect-notice');
			var scope    = $btn.data('scope');

			// Build settings as a query string so PHP parse_str() creates proper nested arrays.
			var parts = [];

			// Enabled toggle.
			var enabled = $form.find('input[name="enabled"]').val() || 'no';
			parts.push('enabled=' + encodeURIComponent(enabled));

			// Collect all input/select fields within the form section.
			$form.find('input[type="radio"]:checked, input[type="url"], select').each(function () {
				var name = $(this).attr('name');
				if (name) {
					parts.push(encodeURIComponent(name) + '=' + encodeURIComponent($(this).val()));
				}
			});

			var postData = {
				action:      'wbcom_redirect_save_settings',
				nonce:       wbcomRedirect.nonce,
				scope:       scope,
				settings:    parts.join('&')
			};

			// For integration tabs, include the integration slug.
			if (scope === 'integration') {
				postData.integration = $btn.data('integration') || $form.data('integration');
			}

			$spinner.show();
			$btn.prop('disabled', true);

			$.post(wbcomRedirect.ajaxUrl, postData, function (response) {
				$spinner.hide();
				$btn.prop('disabled', false);

				if (response.success) {
					$notice.addClass('updated settings-error notice').show();
					setTimeout(function () {
						$notice.fadeOut();
					}, 3000);
				} else {
					alert(response.data || 'Error saving settings.');
				}
			}).fail(function () {
				$spinner.hide();
				$btn.prop('disabled', false);
				alert('Request failed. Please try again.');
			});
		});

		// ——— Dismiss notice ———
		$(document).on('click', '.wbcom-redirect-notice .notice-dismiss', function () {
			$(this).closest('.wbcom-redirect-notice').fadeOut();
		});

		// ——— FAQ accordion ———
		$(document).on('click', '.bpr-accordion', function (e) {
			e.preventDefault();
			$(this).toggleClass('active');
			var panel = $(this).next('.bpr-panel');
			if (panel.css('max-height') && panel.css('max-height') !== 'none') {
				panel.css('max-height', '');
			} else {
				panel.css('max-height', panel.prop('scrollHeight') + 'px');
			}
		});

	});

})(jQuery);
