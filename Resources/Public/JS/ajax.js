/**
 * Created by sebo on 11.08.14.
 */
function initAjaxLoader(element, action) {
	if (action == 'add') {
		$(element).addClass('ajaxloader');
	} else {
		$(element).removeClass('ajaxloader');
	}
}

(function() {

	$('.ecom-configurator-package-box').on('click', function() {
		initAjaxLoader('#tx-ecompc-canvas', 'add');
		var pkg = $(this).attr('data-package');
		var cObj = $(this).attr('data-cObj');
		$('#tx-ecompc-canvas').css('cursor', 'wait');
		$.ajax({
			async: 'true',
			url: 'index.php',
			type: 'POST',
			//contentType: 'application/json; charset=utf-8',
			dataType: 'json',
			data: {
				eID: 'EcomProductConfigurator',
				id: 8,
				type: 1407764086,
				request: {
					actionName: 'selectPackageOptions',
					arguments: {
						storagePid: 4,
						configurationPackage: pkg,
						cObj: cObj
					}
				}
			},
			success: function(result) {
				if (result['success'] === true) {
					initAjaxLoader('#tx-ecompc-canvas', 'remove');
					/** Update process info */
					$('#ecom-configurator-process-value').prop('value', 0.1);
					$('.ecom-configurator-process-value-print').html(10);
					//$('#businessMgmtFormCountry').html(result['content']).removeAttr('disabled');
				} else {
					console.log('Request failed!');
				}
				$('#tx-ecompc-canvas').css('cursor', 'default');
			}
		});
	});

})(jQuery);