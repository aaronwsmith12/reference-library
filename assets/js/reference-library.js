(function ($) {
	'use strict';

	$(function () {
		var $wrap = $('.rl-wrap');
		if (!$wrap.length) {
			return;
		}

		var $form = $wrap.find('.rl-form');
		var $results = $wrap.find('.rl-results');
		var timer = null;
		var request = null;

		function fetchResults() {
			// Cancel an in-flight request so older results never overwrite newer ones.
			if (request) {
				request.abort();
			}

			$results.addClass('rl-loading');

			request = $.post(rlData.ajaxUrl, {
				action: 'rl_search',
				nonce: rlData.nonce,
				search: $form.find('.rl-search').val(),
				topic: $form.find('.rl-topic').val(),
				source: $form.find('.rl-source').val()
			})
				.done(function (response) {
					if (response && response.success) {
						$results.html(response.data.html);
					} else {
						$results.html('<p class="rl-no-results">Something went wrong. Please try again.</p>');
					}
				})
				.fail(function (jqXHR, textStatus) {
					if ('abort' !== textStatus) {
						$results.html('<p class="rl-no-results">Something went wrong. Please try again.</p>');
					}
				})
				.always(function () {
					$results.removeClass('rl-loading');
				});
		}

		// Typing: wait 300ms after the last keystroke before searching.
		$form.on('input', '.rl-search', function () {
			clearTimeout(timer);
			timer = setTimeout(fetchResults, 300);
		});

		// Dropdowns: search right away.
		$form.on('change', '.rl-topic, .rl-source', fetchResults);

		// Enter or the Search button: use AJAX instead of reloading the page.
		$form.on('submit', function (e) {
			e.preventDefault();
			clearTimeout(timer);
			fetchResults();
		});
	});
})(jQuery);