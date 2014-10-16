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
 * @deprecated, might re-use for multiple select packages
 */
function resetPackage(pageUid, lang, cObj, configurationPackage) {
	var request = {
		actionName: 'resetPackage',
		arguments: {
			cObj: cObj,
			package: configurationPackage
		}
	};

	ajaxCaller('', '#ecom-configurator-ajax-loader', pageUid, lang, request, function(result) {
		removeAjaxLoader('#ecom-configurator-ajax-loader');
		onAjaxSuccessGeneric(result);
		onAjaxSuccessUpdateIndex(result);
		ajaxCaller('#ecom-configurator-package-select-option-index:content', '#ecom-configurator-ajax-loader', result['pid'], result['L'], {
			actionName: 'selectPackageOptions',
			arguments: {
				configurationPackage: result['package'],
				cObj: result['cObj']
			}
		}, null, 1);
	});
}

/**************************************
 *                                    *
 * AJAX request functions (re-worked) *
 *                                    *
 *************************************/

/**
 * Set option function
 */
function txEcompcSetOption() {
	$('.ecom-configurator-select-package-option-wrap').on('click', function (e) {
		e.preventDefault();
		addAjaxLoader('#ecom-configurator-ajax-loader');
		genericAjaxRequest($(this).attr('data-t3pid'), $(this).attr('data-t3lang'), 1407764086, 'setOption', {
			option: $(this).attr('data-option'),
			unset: $(this).attr('data-option-state'),
			cObj: $(this).attr('data-t3cobj')
		}, function (result) {
			removeAjaxLoader('#ecom-configurator-ajax-loader');
			updateProcessIndicators(result.process);
			$('#ecom-configurator-optionSelection-package-info').html(
				'<h2>' + result.currentPackage.frontendLabel + '</h2>' +
				'<p>' + result.currentPackage.hintText + '</p>'
			);
			updatePackageNavigation(result.packages);
			buildSelector(result);
		});
	});
}

/**
 * Update index view only (switch between packages)
 */
function txEcompcIndex() {
	$('.ecom-configurator-package-select').on('click', function (e) {
		// Prevent default anchor action
		e.preventDefault();
		if ($(this).hasClass('ecom-configurator-package-state-0') || $(this).hasClass('current'))
			return false;
		addAjaxLoader('#ecom-configurator-ajax-loader');
		genericAjaxRequest($(this).attr('data-t3pid'), $(this).attr('data-t3lang'), 1407764086, 'index', {
			package: $(this).attr('data-package'),
			cObj: $(this).attr('data-t3cobj')
		}, function (result) {
			removeAjaxLoader('#ecom-configurator-ajax-loader');
			$('#ecom-configurator-optionSelection-package-info').html(
				'<h2>' + result.currentPackage.frontendLabel + '</h2>' +
				'<p>' + result.currentPackage.hintText + '</p>'
			);
			updatePackageNavigation(result.packages);
			buildSelector(result);
		});
	});
}

/**
 * get hints
 * @param option
 * @param pid
 * @param lang
 * @param cObj
 * @returns {boolean}
 */
function getOptionHint(option, pid, lang, cObj) {
	$('#dialog').remove();
	genericAjaxRequest(pid, lang, 1407764086, 'getOptionHint', {
			option: option,
			cObj: cObj
		}, function(result) {
			content = '<div id="dialog"><p>' + result['hint'] + '</p></div>';
			$('#ecom-configurator-package-select-option-index').append(content);
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
				controllerName: 'DynamicConfiguratorAjaxRequest',
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

/**********************************
 * Various build helper functions *
 *********************************/

/**
 * Update process indicators including process bar and 'percent done' display
 * @param process
 */
function updateProcessIndicators(process) {
	// Update/animate process bar
	$('#ecom-configurator-process-value').animate({value: process});
	// Update/animate number display(s)
	$('.ecom-configurator-process-value-print').each(function(index, element) {
		$({countNum: $(element).text()}).animate({countNum: Math.floor(process * 100)}, {
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
}

/**
 * Update package navigation meaning icons and link states on top representing packages
 * @param thePackages
 */
function updatePackageNavigation(thePackages) {
	/**
	 * Update package states/links
	 */
	for (var index in thePackages) {
		if (thePackages.hasOwnProperty(index)) {
			var newState = thePackages[index].active ? 1 : 0,
				oldState = thePackages[index].active ? 0 : 1,
				optionActiveState = thePackages[index].anyOptionActive,
				faIcon = $('#ecom-configurator-package-' + thePackages[index]['uid'] + '-link i'),
				link = $('#ecom-configurator-package-' + thePackages[index]['uid'] + '-link'),
				icon = $('#ecom-configurator-package-' + thePackages[index]['uid'] + '-icon');
			faIcon.addClass('icon-check' + (optionActiveState ? '' : '-empty')).removeClass('icon-check' + (optionActiveState ? '-empty' : ''));
			link.addClass('ecom-configurator-package-state-' + newState).removeClass('ecom-configurator-package-state-' + oldState);
			icon.addClass('icon-state-' + newState).removeClass('icon-state-' + oldState);
			if (thePackages[index].current) {
				link.addClass('current');
				icon.addClass('current');
			} else {
				link.removeClass('current');
				icon.removeClass('current');
			}
		}
	}
}

/**
 * build option selector HTML
 * @param result
 */
function buildSelector(result) {
	var options = result.options;
	if (options.length) {
		var html = [],
			prop,
			tabIndex = 1;
		for (prop in options) {
			if (options.hasOwnProperty(prop)) {
				var content = "<a data-t3pid=\"" + result.pid + "\" data-t3lang=\"" + result.lang + "\" data-t3cobj=\"" + result.cObj + "\" data-option=\"" + options[prop].uid + "\" data-option-state=\"" + (options[prop].active ? 1 : 0) + "\" class=\"ecom-configurator-select-package-option-wrap\" tabindex=\"" + tabIndex + "\">";
				if (result.showPriceLabels) {
					content += "<span class=\"ecom-configurator-select-package-option-price\">" + options[prop].price + "</span>";
				}
				if (options[prop].hint) {
					content += "<div class=\"ecom-configurator-select-package-option-info-wrapper\"><span class=\"ecom-configurator-select-package-option-info\">More Info</span></div>";
				}
				content += "<div class=\"ecom-configurator-checkbox " + (options[prop].active ? '' : 'un') + "checked\"><span class=\"ecom-configurator-option-checkbox-image\"></span></div>";
				content += "<span class=\"ecom-configurator-select-package-option option\">" + options[prop].title + "</span></a>";
				if (options[prop].hint) {
					content += "<span class=\"ecom-configurator-select-package-option-info-hint-box\"><span class=\"close-popover-x\" title=\"Close Info Box\">×</span>###CONTENT###</span>";
				}
				content += "<div class=\"clearfix\"></div>";
				html.push(content);
				tabIndex++;
			}
		}
		console.log(options);
		$('#ecom-configurator-select-options-ajax-update').html(html.join(''));
		txEcompcSetOption();
	}
}


/**********************************************
 * Initialize Event Listeners once DOM loaded *
 *********************************************/
(function() {
	txEcompcSetOption();
	txEcompcIndex();
})(jQuery);

