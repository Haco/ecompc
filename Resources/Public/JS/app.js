/*
* ecom Configurator :: app.js
* */


(function($) {
	function equalizeHeight() {
		$('#tx-ecompc-canvas .ecom-configurator-package-select').each(function() {
			$(this).siblings('.ecom-configurator-package-state').height($(this).outerHeight());
		});
	}equalizeHeight();
	$(window).resize(function() {
		equalizeHeight();
	});
})(jQuery);