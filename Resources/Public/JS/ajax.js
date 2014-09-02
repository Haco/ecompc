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

function ajaxCaller(targets, loader, id, request, backToIndex) {
	initAjaxLoader(loader, 'add');console.log(targets.split(':'));
	$('#tx-ecompc-package-select-index').fadeOut();
	var key;
	var targetsAndArrayKeys = targets.split(',');
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
			if (result['dev']) { console.log(result); }
			initAjaxLoader(loader, 'remove');
			for (key in targetsAndArrayKeys) {
				var tokens = targetsAndArrayKeys[key].split(':');
				$(tokens[0]).html(result[tokens[1]]).fadeIn();
			}
			if (backToIndex) { goBackToIndex(); }
			/** Update process info */
			$('#ecom-configurator-process-value').animate({value: result['selectedCPkg'] / totalCPkg});
			$('.ecom-configurator-process-value-print').each(function(i,e) {
				$({countNum: $(e).text()}).animate({countNum: Math.floor(result['selectedCPkg'] / totalCPkg * 100)}, {
					duration: 800,
					easing:'linear',
					step: function() {
						$(e).text(Math.floor(this.countNum));
					},
					complete: function() {
						$(e).text(this.countNum);
					}
				});
			});
			/*$('.ecom-configurator-process-value-print').html(Math.floor(result['selectedCPkg'] / totalCPkg * 100));*/
			/** Toggle Header Content */
			if (request.actionName == 'index' || request.actionName == 'setOptionAction') {
				$('#tx-ecompc-ajax-header-instructions').show();
				$('#tx-ecompc-ajax-header-backlink').hide();
			} else {
				$('#tx-ecompc-ajax-header-instructions').hide();
				$('#tx-ecompc-ajax-header-backlink').css('display', 'inline-block');
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log('Request failed with ' + textStatus + ': ' + errorThrown +  '!');
		}
	});
}

function updatePackage(pid, cObj, option, redirect, unset) {
	var request = {
		actionName: 'setOption',
		arguments: {
			cObj: cObj,
			option: option,
			redirect: redirect,
			unset: unset
		}
	};

	ajaxCaller('', '#tx-ecompc-ajax-loader', pid, request, 1);
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

		ajaxCaller('#tx-ecompc-package-select-option-index:content', '#tx-ecompc-ajax-loader', pid, request);
	});

	$('#tx-ecompc-ajax-header-backlink').on('click', function() {
		goBackToIndex();
	});

})(jQuery);