/**
 * ecom Configurator :: ajax.js
 */

function addAjaxLoader(element) {
	$(element).addClass('ajaxloader');
}

function remAjaxLoader(element) {
	$(element).removeClass('ajaxloader');
}

/**
 * Go to index view
 */
function goBackToIndex() {
	$('#tx-ecompc-ajax-header-instructions').show();
	$('#tx-ecompc-ajax-header-backlink').hide();
	$('#tx-ecompc-package-select-option-index').hide();
	$('#tx-ecompc-package-select-index').fadeIn();
}

function ajaxCaller(targets, loader, pageUid, request, onSuccessFunction, updateIndex) {
	addAjaxLoader(loader);
	var targetsAndArrayKeys = targets.replace(' ', '').split(',');
	$.ajax({
		async: 'true',
		url: 'index.php',
		type: 'POST',
		//contentType: 'application/json; charset=utf-8',
		dataType: 'json',
		data: {
			eID: 'EcomProductConfigurator',
			id: pageUid,
			type: 1407764086,
			request: request
		},
		success: onSuccessFunction ? onSuccessFunction : function(result) {
			//console.log(result);
			remAjaxLoader(loader);

			/** Dynamically add contents to target. Pattern: DOMTarget1:resultArrayKey1,DOMTarget2:resultArrayKey2,DOMTarget3:resultArrayKey3 ... */
			if (targetsAndArrayKeys.length) {
				for (key in targetsAndArrayKeys) {
					var tokens = targetsAndArrayKeys[key].split(':');
					$(tokens[0]).html(result[tokens[1]]).fadeIn();
				}
			}

			onAjaxSuccessGeneric(result, result['action'] == 'updatePackages');
			onAjaxSuccessUpdateIndex(result);
			onAjaxSuccessUpdatePackages(result);
			if (updateIndex) {
				ajaxCaller('', '#tx-ecompc-ajax-loader', result['pid'], {
					actionName: 'updatePackages',
					arguments: {
						cObj: result['cObj']
					}
				});
			}
			switch (result['proceed']) {
				case 'index':
					goBackToIndex();
					break;
				case 'selectPackageOptions':
					ajaxCaller('#tx-ecompc-package-select-option-index:content', '#tx-ecompc-ajax-loader', result['pid'], {
						actionName: 'selectPackageOptions',
						arguments: {
							configurationPackage: result['package'],
							cObj: result['cObj']
						}
					});
					break;
				default :
					break;
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log('Request failed with ' + textStatus + ': ' + errorThrown +  '!');
		}
	});
}

function onAjaxSuccessUpdatePackages(result) {
	//console.log({result: result, totalPackages: tcp});
	/** Add result */
	if (result['action'] == 'updatePackages' && result['cfgres']) {
		$('#ecom-configurator-result-canvas').html(result['cfgres']).fadeIn();
		(function($) {
			var $summaryTable = $('#tx-ecompc-canvas .ecom-configurator-summary-table');
			$('#tx-ecompc-canvas .ecom-configurator-result-review-config').on('click', function(e) {
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
	}
}

/** Function to call on Ajax success updating index view contents */
function onAjaxSuccessUpdateIndex(result) {
	if (result['packagesLinksInnerHTML']) {
		/** Update package links */
		for (key in result['packagesLinksInnerHTML']) {
			var packageLinkBox = $('#tx-ecompc-configure-package-' + key);
			$('#tx-ecompc-configure-package-' + key + ' > .ecom-configurator-package-select').html(result['packagesLinksInnerHTML'][key][2]);
			if (result['packagesLinksInnerHTML'][key][0]) {
				packageLinkBox.removeClass('inactive-package').addClass('active-package'); // Whole link
			} else {
				packageLinkBox.removeClass('active-package').addClass('inactive-package'); // Whole link
			}
			if (result['packagesLinksInnerHTML'][key][1]) {
				packageLinkBox.children('.ecom-configurator-package-state').removeClass('unchecked').addClass('checked'); // Checkbox
			} else {
				packageLinkBox.children('.ecom-configurator-package-state').removeClass('checked').addClass('unchecked'); // Checkbox
			}
		}
	}
	if (result['selcps'] < tcp) {
		$('#ecom-configurator-result-canvas').html('').hide();
	}
}

/** Generic function to call on Ajax success */
function onAjaxSuccessGeneric(result, indexView) {
	/** Update process information (process bar and count) */
	$('#ecom-configurator-process-value').animate({value: result['selcps'] / tcp});
	$('.ecom-configurator-process-value-print').each(function(index, element) {
		$({countNum: $(element).text()}).animate({countNum: Math.floor(result['selcps'] / tcp * 100)}, {
			duration: 800,
			easing:'linear',
			step: function() {
				$(element).text(Math.floor(this.countNum));
			},
			complete: function() {
				$(element).text(this.countNum);
			}
		});
	});
	if (result['cfgp']) { $('#tx-ecompc-config-header-cfgp').html(result['cfgp']); }
	if (!indexView) {
		/** Toggle Header Content */
		$('#tx-ecompc-ajax-header-instructions').hide();
		$('#tx-ecompc-ajax-header-backlink').css('display', 'inline-block');
	}
}

/** Updating packages function */
function updatePackage(pageUid, cObj, option, unset) {
	var request = {
		actionName: 'setOption',
		arguments: {
			cObj: cObj,
			opt: option,
			uns: unset
		}
	};

	ajaxCaller('', '#tx-ecompc-ajax-loader', pageUid, request, null, 1);
}

function resetPackage(pageUid, cObj, configurationPackage) {
	var request = {
		actionName: 'resetPackage',
		arguments: {
			cObj: cObj,
			package: configurationPackage
		}
	};

	ajaxCaller('', '#tx-ecompc-ajax-loader', pageUid, request, function(result) {
		remAjaxLoader('#tx-ecompc-ajax-loader');
		onAjaxSuccessGeneric(result);
		onAjaxSuccessUpdateIndex(result);
		ajaxCaller('#tx-ecompc-package-select-option-index:content', '#tx-ecompc-ajax-loader', result['pid'], {
			actionName: 'selectPackageOptions',
			arguments: {
				configurationPackage: result['package'],
				cObj: result['cObj']
			}
		}, null, 1);
	});
}

/**
 * Add EventListeners
 */
(function() {
	$('.ecom-configurator-package-box').on('click', function(e) {
		// Prevent default anchor action
		e.preventDefault();
		if ($(this).hasClass('inactive-package')) {
			return;
		}
		var pageUid = $(this).attr('data-page');
		var configurationPackage = $(this).attr('data-package');
		var cObj = $(this).attr('data-cObj');
		var request = {
			actionName: 'selectPackageOptions',
			arguments: {
				configurationPackage: configurationPackage,
				cObj: cObj
			}
		};

		$('#tx-ecompc-package-select-index').hide();
		ajaxCaller('#tx-ecompc-package-select-option-index:content', '#tx-ecompc-ajax-loader', pageUid, request);
	});

	$('#tx-ecompc-ajax-header-backlink').on('click', function(e) {
		// Prevent default anchor action
		e.preventDefault();
		goBackToIndex();
	});

})(jQuery);