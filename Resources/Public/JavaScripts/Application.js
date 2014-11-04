/**
 * ecom Configurator :: app.js
 */

// Equalize height of checkbox and package selection on resize.
(function($) {
	function equalizeHeight() {
		$('#ecom-configurator-canvas .ecom-configurator-package-select').each(function() {
			$(this).siblings('.ecom-configurator-package-state').height($(this).outerHeight());
		});
	}equalizeHeight();
	$(window).resize(function() {
		equalizeHeight();
	});
})(jQuery);

// Package-OptionList => Popover for hint text
(function($) {
	var hintBoxSelector = '#ecom-configurator-canvas #ecom-configurator-select-package-option-info-hint-box';

	// Hide the Box Function
	function hideHintBox(hintBox) {
		$(hintBox).slideUp();
	}
	// Hide hint-box on click and ESC key
	$('#ecom-configurator-canvas #ecom-configurator-select-package-option-info-hint-box .close-popover-x, #ecom-configurator-canvas #ecom-configurator-select-package-option-info-hint-box, body').on('click keyup', function(e) {
		// If the keyup event is triggered
		if ( e.type == 'keyup' ) {
			if ( e.keyCode == 27 ) {
				hideHintBox(hintBoxSelector);
			}
		// If click event is triggered
		} else {
			hideHintBox(hintBoxSelector);
		}
	});
})(jQuery);

// Review/Summary Configuration Button
(function($) {
	var $summaryTable = $('#ecom-configurator-canvas #ecom-configurator-summary-table');
	$('#ecom-configurator-canvas .ecom-configurator-result-review-config').on('click', function(e) {
		// Prevent default anchor action
		e.preventDefault();
		$(this).stop().toggleClass('active');
		$summaryTable.stop().slideToggle('slow').toggleClass('active');

		// Scroll in position if the table is not currently hidden
		if ( $summaryTable.hasClass('active') ) {
			$('html, body').stop().animate({
				scrollTop: $summaryTable.offset().top
			}, 'slow');
		}
	});
})(jQuery);

(function($) {
	$('#ecom-configurator-package-select-index').tooltip({
		tooltipClass: "ecompc-custom-tooltip-styling",
		track: true
	});
	$('.ecompc-syntax-help').tooltip({
		tooltipClass: "ecompc-custom-tooltip-styling",
		track: true
	});
	if ( showResult === null ) {
		$('#ecom-configurator-result-canvas').hide();
		$('#ecom-configurator-select-options-ajax-update').show();
		$('#ecom-configurator-reset-configuration-button').show();
	} else {
		$('#ecom-configurator-result-canvas').show();
		$('#ecom-configurator-select-options-ajax-update').hide();
		$('#ecom-configurator-reset-configuration-button').hide();
	}
})(jQuery);