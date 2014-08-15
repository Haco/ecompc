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

function goBackToIndex() {
	$('#tx-ecompc-ajax-header-instructions').show();
	$('#tx-ecompc-ajax-header-backlink').hide();
	$('#tx-ecompc-package-select-option-index').fadeOut();
	$('#tx-ecompc-package-select-index').fadeIn();
}

function ajaxCaller(target, loader, id, request) {
	initAjaxLoader(loader, 'add');
	$('#tx-ecompc-package-select-index').fadeOut();
	$.ajax({
		async: 'true',
		url: 'index.php',
		type: 'POST',
		//contentType: 'application/json; charset=utf-8',
		dataType: 'json',
		data: {
			eID: 'EcomProductConfigurator',
			id: id,
			type: 1407764086,
			request: request
		},
		success: function(result) {
			initAjaxLoader(loader, 'remove');
			if (target.length) {
				$(target).html(result['content']).fadeIn();
			} else {
				goBackToIndex();
			}
			/** Update process info */
			$('#ecom-configurator-process-value').prop('value', result['selectedCPkg'] / totalCPkg);
			$('.ecom-configurator-process-value-print').html(Math.floor(result['selectedCPkg'] / totalCPkg * 100));
			/** Toggle Header Content */
			if (request.actionName == 'index') {
				$('#tx-ecompc-ajax-header-instructions').show();
				$('#tx-ecompc-ajax-header-backlink').hide();
			} else {
				$('#tx-ecompc-ajax-header-instructions').hide();
				$('#tx-ecompc-ajax-header-backlink').css('display', 'inline-block');
			}
		},
		error: function(error) {
			console.log('Request failed with ' + error + '!');
		}
	});
}

function updatePackage(pid, cObj, option, redirectAction, unset) {
	var request = {
		actionName: 'setOptionAction',
		arguments: {
			cObj: cObj,
			option: option,
			redirectAction: redirectAction,
			unset: unset
		}
	};

	ajaxCaller('', '#tx-ecompc-ajax-loader', pid, request);
/*
	$.ajax({
		async: 'true',
		url: 'index.php',
		type: 'POST',
		//contentType: 'application/json; charset=utf-8',
		dataType: 'json',
		data: {
			eID: 'EcomProductConfigurator',
			id: pid,
			type: 1407764086,
			request: request
		},
		success: function(result) {
			initAjaxLoader(loader, 'remove');
			$(target).html(result['content']).fadeIn();
			*/
/** Update process info *//*

			$('#ecom-configurator-process-value').prop('value', result['selectedCPkg'] / totalCPkg);
			$('.ecom-configurator-process-value-print').html(Math.floor(result['selectedCPkg'] / totalCPkg * 100));
			*/
/** Toggle Header Content *//*

			if (request.actionName == 'index') {
				$('#tx-ecompc-ajax-header-instructions').show();
				$('#tx-ecompc-ajax-header-backlink').hide();
			} else {
				$('#tx-ecompc-ajax-header-instructions').hide();
				$('#tx-ecompc-ajax-header-backlink').css('display', 'inline-block');
			}
		},
		error: function(error) {
			console.log('Request failed with ' + error + '!');
		}
	});
*/
}

(function() {
	$('.ecom-configurator-package-box').on('click', function() {
		var pid = $(this).attr('data-page');
		var pkg = $(this).attr('data-package');
		var cObj = $(this).attr('data-cObj');
		var request = {
			actionName: 'selectPackageOptions',
			arguments: {
				configurationPackage: pkg,
				cObj: cObj
			}
		};

		ajaxCaller('#tx-ecompc-package-select-option-index', '#tx-ecompc-ajax-loader', pid, request);
	});

	$('#tx-ecompc-ajax-header-backlink').on('click', function() {
		goBackToIndex();
	});

})(jQuery);