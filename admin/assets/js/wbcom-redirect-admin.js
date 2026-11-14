/**
 * Wbcom Redirect Admin JS
 * Handles: toggle visibility, radio field switching, accordion, AJAX save.
 */
(function ($) {
	'use strict';

	$(document).ready(function () {

		// ——— Toggle enable/disable ———.
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

		// ——— Radio type switching: show/hide relevant fields ———.
		$(document).on('change', '.wbcom-redirect-type-radio', function () {
			var $config = $(this).closest('.wbcom-redirect-config');
			var type    = $(this).val();

			$config.find('.wbcom-redirect-field').hide();
			$config.find('.wbcom-redirect-field-' + type).show();
		});

		// ——— Page selects: fill from the shared page list on first use ———.
		$(document).on('mousedown focus', '.wbcom-redirect-page-select:not(.is-filled)', function () {
			var $select  = $(this).addClass('is-filled');
			var current  = $select.val();
			var template = document.getElementById('wbcom-redirect-page-options');
			if (!template) {
				return;
			}
			$select.find('option[value!=""]').remove();
			$select.append(template.content.cloneNode(true));
			$select.val(current);
		});

		// ——— Accordion for role/group-type sections ———.
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

		// ——— AJAX Save ———.
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

			var showNotice = function (ok, text) {
				$notice.removeClass('updated notice-error').addClass('settings-error notice ' + (ok ? 'updated' : 'notice-error'));
				$notice.find('strong').text(text);
				// Stays until dismissed or the next save, like core's "Settings saved".
				$notice.stop(true, true).show();
			};

			$.post(wbcomRedirect.ajaxUrl, postData, function (response) {
				$spinner.hide();
				$btn.prop('disabled', false);
				if (response.success) {
					showNotice(true, wbcomRedirect.i18n.saved);
				} else {
					showNotice(false, response.data || wbcomRedirect.i18n.error);
				}
			}).fail(function () {
				$spinner.hide();
				$btn.prop('disabled', false);
				showNotice(false, wbcomRedirect.i18n.failed);
			});
		});

		// ——— Dismiss notice ———.
		$(document).on('click', '.wbcom-redirect-notice .notice-dismiss', function () {
			$(this).closest('.wbcom-redirect-notice').fadeOut();
		});

		// ——— FAQ accordion ———.
		$(document).on('click', '.bpr-accordion', function (e) {
			e.preventDefault();
			$(this).toggleClass('active');
			var panel = $(this).next('.bpr-panel');
			if (panel[0].style.maxHeight) {
				panel[0].style.maxHeight = '';
			} else {
				panel[0].style.maxHeight = panel.prop('scrollHeight') + 'px';
			}
		});

	});

})(jQuery);
