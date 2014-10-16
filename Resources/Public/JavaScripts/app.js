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
	var triggerHint = '#ecom-configurator-canvas .ecom-configurator-select-package-option-info',
		hintBoxSelector = '#ecom-configurator-canvas .ecom-configurator-select-package-option-info-hint-box',
		windowHeight,
		popupHeight,
		scrollPosition,
		currentHintBox;

	// Hide the Box Function
	function hideHintBox(hintBox) {
		$(hintBox).fadeOut('fast', function() {
			$(this).css({'display': 'none', 'opacity': 0, 'top': 0});
		});
	}
	// Popup on click
	$(triggerHint).on('click', function() {
		// First hide every active hint box
		hideHintBox(hintBoxSelector);

		// Setting new vars
		windowHeight = $(document).height();
		currentHintBox = $(this).parent('.ecom-configurator-select-package-option-wrap').next('.ecom-configurator-select-package-option-info-hint-box');

		// Calculate position of the hint-box
		popupHeight = $(currentHintBox).outerHeight();
		// Check if popup high exceeds window height
		if (windowHeight <= popupHeight) {
			// Then set position top 15px
			scrollPosition = 15;
		} else {
			// Else arrange it in center position
			scrollPosition = (windowHeight - popupHeight) / 2;
		}
		// Show Popup
		currentHintBox.css('display', 'block').animate({'top': scrollPosition, 'opacity': 1}, 'fast');

		// Prevent Option-a-tag from executing
		return false;
	});
	// Hide hint-box on click and ESC key
	$('#ecom-configurator-canvas .ecom-configurator-select-package-option-info-hint-box .close-popover-x, #ecom-configurator-canvas .ecom-configurator-select-package-option-info-hint-box, body').on('click keyup', function(e) {
		// If the keyup event is triggered
		if (e.type == 'keyup') {
			if (e.keyCode == 27) {
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
	var $summaryTable = $('#ecom-configurator-canvas .ecom-configurator-summary-table');
	$('#ecom-configurator-canvas .ecom-configurator-result-review-config').on('click', function(e) {
		// Prevent default anchor action
		e.preventDefault();
		$(this).stop().toggleClass('active');
		$summaryTable.stop().slideToggle('slow').toggleClass('active');

		// Scroll in position if the table is not currently hidden
		if ($summaryTable.hasClass('active')) {
			$('html, body').stop().animate({
				scrollTop: $summaryTable.offset().top
			}, 'slow');
		}
	});
})(jQuery);

(function($) {
	$('#ecom-configurator-canvas').tooltip();
})(jQuery);