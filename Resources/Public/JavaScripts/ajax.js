/**
 * ecom Configurator :: ajax.js
 */

function addAjaxLoader(element) {
	$(element).addClass('ajaxloader');
}

function removeAjaxLoader(element) {
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

function ajaxCaller(targets, loader, pageUid, lang, request, onSuccessFunction, updateIndex) {
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
			L: lang,
			type: 1407764086,
			request: request
		},
		success: typeof onSuccessFunction == 'function' ? onSuccessFunction : function(result) {
			//console.log(result);
			removeAjaxLoader(loader);

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
				ajaxCaller('', '#tx-ecompc-ajax-loader', result['pid'], result['L'], {
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
					ajaxCaller('#tx-ecompc-package-select-option-index:content', '#tx-ecompc-ajax-loader', result['pid'], result['L'], {
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
			if (result['action'] == 'selectPackageOptions') {
				initTriggerHint();
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
function updatePackage(pageUid, lang, cObj, option, unset) {
	var request = {
		actionName: 'setOption',
		arguments: {
			cObj: cObj,
			opt: option,
			uns: unset
		}
	};

	ajaxCaller('', '#tx-ecompc-ajax-loader', pageUid, lang, request, null, 1);
}

function resetPackage(pageUid, lang, cObj, configurationPackage) {
	var request = {
		actionName: 'resetPackage',
		arguments: {
			cObj: cObj,
			package: configurationPackage
		}
	};

	ajaxCaller('', '#tx-ecompc-ajax-loader', pageUid, lang, request, function(result) {
		removeAjaxLoader('#tx-ecompc-ajax-loader');
		onAjaxSuccessGeneric(result);
		onAjaxSuccessUpdateIndex(result);
		ajaxCaller('#tx-ecompc-package-select-option-index:content', '#tx-ecompc-ajax-loader', result['pid'], result['L'], {
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
			return false;
		}

		$('#tx-ecompc-package-select-index').hide();
		addAjaxLoader('#tx-ecompc-ajax-loader');
		genericAjaxRequest($(this).attr('data-page'), $(this).attr('data-lang'), 1407764086, 'selectPackageOptions', {
			configurationPackage: $(this).attr('data-package'),
			cObj: $(this).attr('data-cObj')
		}, function(result) {
			//console.log(result);
			removeAjaxLoader('#tx-ecompc-ajax-loader');
			$('#tx-ecompc-package-select-option-index').html(result['content']).fadeIn();

			buildOptionSelector(result);
		});
	});

	$('#tx-ecompc-ajax-header-backlink').on('click', function(e) {
		// Prevent default anchor action
		e.preventDefault();
		goBackToIndex();
	});

})(jQuery);

function buildOptionSelector(result) {
	content = '';
	for (var i = 0; i < result['options'].length; i++) {
		content += '<a href="javascript:void(0)" onclick="updatePackage(' + result['pid'] + ',' + result['L'] + ',' + result['cObj'] + ',' + result['options'][i]['uid'] + ',' + result['options'][i]['state'] + ')" class="ecom-configurator-select-package-option-wrap" tabindex="' + i + '">';
		// Add price labels, if enabled
		if (result['showPriceLabels']) {
			content += '<span class="ecom-configurator-select-package-option-price">' + result['options'][i]['price'] + '</span>';
		}
		// Add link to hint, if any
		if (result['options'][i]['hint']) {
			content += '<div class="ecom-configurator-select-package-option-info-wrapper"><span onclick="getOptionHint(' + result['options'][i]['uid'] + ',' + result['pid'] + ',' + result['L'] + ',' + result['cObj'] + ')" class="ecom-configurator-select-package-option-info">More Info</span></div>';
		}
		content += '<div class="ecom-configurator-checkbox "' + (result['options'][i]['active'] ? '' : 'un') + 'checked"><span class="ecom-configurator-option-checkbox-image"></span></div><span class="ecom-configurator-select-package-option option">' + result['options'][i]['title'] + '</span></a>';
	}
	$('#tx-ecompc-package-select-option-index').html(content).show();
}

function getOptionHint(option, pid, lang, cObj) {
	$('#dialog').remove();
	genericAjaxRequest(pid, lang, 1407764086, 'getOptionHint', {
			option: option,
			cObj: cObj
		}, function(result) {
			content = '<div id="dialog"><p>' + result['hint'] + '</p></div>';
			$('#tx-ecompc-package-select-option-index').append(content);
			$(function() {
				$('#dialog').dialog({
					width: '90%',
					position: { my: 'center top-25%', of: 'body' }
				});
			});
		}
	);
	return false;
}

function genericAjaxRequest(pid, language, type, action, arguments, onSuccess) {
	$.ajax({
		async: 'true',
		url: 'index.php',
		type: 'POST',
		//contentType: 'application/json; charset=utf-8',
		dataType: 'json',
		data: {
			eID: 'EcomProductConfigurator',
			id: parseInt(pid),
			L: parseInt(language),
			type: parseInt(type),
			request: {
				actionName: action,
				arguments: arguments
			}
		},
		success: onSuccess,
		error: function(jqXHR, textStatus, errorThrown) {
			console.log('Request failed with ' + textStatus + ': ' + errorThrown +  '!');
		}
	});
}