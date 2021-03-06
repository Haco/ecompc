/**
 * ecom Configurator :: ajax.js
 */

function addAjaxLoader(element) {
	$('#' + element).addClass('ajaxloader');
}

function removeAjaxLoader(element) {
	$('#' + element).removeClass('ajaxloader');
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
		if ( $(this).hasClass('disabled') ) {
			$(this).blur();
			return void(0);
		}
		addAjaxLoader('ecom-configurator-ajax-loader');
		genericAjaxRequest(t3pid, t3lang, 1407764086, 'setOption', {
			option: $(this).attr('data-option'),
			unset: $(this).attr('data-option-state'),
			cObj: t3cobj
		}, function (result) {
			var pkgInfoDiv = $('#ecom-configurator-optionSelection-package-info'),
				resultDiv = $('#ecom-configurator-result-canvas');
			removeAjaxLoader('ecom-configurator-ajax-loader');
			updateProgressIndicators(result.progress);
			if ( result.currentPackage instanceof Object ) {
				/* Add dependency notes */
				var addDN = result.currentPackage['dependencyNotesFluidParsedMessage'].length ? '<p>' + result.currentPackage.dependencyNotesFluidParsedMessage + '</p>' : '';
				pkgInfoDiv.html(
					'<h2>' + result.currentPackage.frontendLabel + '</h2>' +
					'<p>' + result.currentPackage.hintText + '</p>' + addDN
				).show();
			}
			if ( result.showResult ) {
				pkgInfoDiv.hide();
				$('#ecom-configurator-request').attr('href', result.requestLink);
				$('#ecom-configurator-result-canvas .ecom-configurator-result h3.ecom-configurator-result-label').first().html(result.configurationData[0]);
				$('#ecom-configurator-result-canvas .ecom-configurator-result small.ecom-configurator-result-code').first().html(getConfigurationCode(result.configurationData[1]));
				$('#ecom-configurator-summary-table').html(getConfigurationSummary(result.configurationData[2], result.pricingEnabled));
				$('.ecompc-syntax-help').tooltip({
					tooltipClass: "ecompc-custom-tooltip-styling",
					track: true
				});
				resultDiv.show();
				$('#ecom-configurator-show-result-button').hide();
				txEcompcIndex(); // Re-assign Click-function()
			} else {
				pkgInfoDiv.show();
				resultDiv.hide();
				if ( result.progress === 1 ) {
					$('#ecom-configurator-show-result-button').show();
				} else {
					$('#ecom-configurator-show-result-button').hide();
				}
			}
			updatePackageNavigation(result.packages);
			buildSelector(result);
			if ( result.pricingEnabled && result.pricing ) {
				$('#ecom-configurator-config-header-config-price').html(result.pricing);
			}
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
		if ( $(this).hasClass('ecom-configurator-package-state-0') || $(this).hasClass('current') )
			return false;
		addAjaxLoader('ecom-configurator-ajax-loader');
		genericAjaxRequest(t3pid, t3lang, 1407764086, 'index', {
			package: $(this).attr('data-package'),
			cObj: t3cobj
		}, function (result) {
			var pkgInfoDiv = $('#ecom-configurator-optionSelection-package-info'),
				resultDiv = $('#ecom-configurator-result-canvas');
			removeAjaxLoader('ecom-configurator-ajax-loader');
			if ( result.currentPackage instanceof Object ) {
				/* Add dependency notes */
				var addDN = result.currentPackage['dependencyNotesFluidParsedMessage'].length ? '<p>' + result.currentPackage.dependencyNotesFluidParsedMessage + '</p>' : '';
				pkgInfoDiv.html(
					'<h2>' + result.currentPackage.frontendLabel + '</h2>' +
					'<p>' + result.currentPackage.hintText + '</p>' + addDN
				).show();
			}
			if ( result.showResult ) {
				pkgInfoDiv.hide();
				resultDiv.show();
				$('#ecom-configurator-show-result-button').hide();
			} else {
				pkgInfoDiv.show();
				resultDiv.hide();
				if ( result.progress === 1 ) {
					$('#ecom-configurator-show-result-button').show();
				} else {
					$('#ecom-configurator-show-result-button').hide();
				}
			}
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
	var hintBox = $('#ecom-configurator-select-package-option-info-hint-box'),
		hintBoxContent = $('#ecom-configurator-select-package-option-info-hint-box > div');
	hintBox.addClass('ajaxloader');
	hintBoxContent.html('');
	genericAjaxRequest(pid, lang, 1407764086, 'getOptionHint', {
			option: option,
			cObj: cObj
		}, function(result) {
			hintBoxContent.html(result.hint);
			hintBox.removeClass('ajaxloader');
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
 * Update progress indicators including progress bar and 'percent done' display
 * @param progress
 */
function updateProgressIndicators(progress) {
	// Update/animate progress bar
	$('#ecom-configurator-progress-value').animate({value: progress});
	// Update/animate number display(s)
	$('.ecom-configurator-progress-value-print').each(function(index, element) {
		$({countNum: $(element).text()}).animate({countNum: Math.floor(progress * 100)}, {
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
	for ( var index in thePackages ) {
		if ( thePackages.hasOwnProperty(index) ) {
			if ( !thePackages[index].visibleInFrontend )
				continue;
			var newState = thePackages[index].active ? 1 : 0,
				oldState = thePackages[index].active ? 0 : 1,
				optionActiveState = thePackages[index].anyOptionActive,
				faIcon = $('#ecom-configurator-package-' + thePackages[index].uid + '-link i'),
				link = $('#ecom-configurator-package-' + thePackages[index].uid + '-link'),
				icon = $('#ecom-configurator-package-' + thePackages[index].uid + '-icon');
			faIcon.addClass('fa-' + (optionActiveState ? 'check-' : '') + 'square-o').removeClass('fa-' + (optionActiveState ? '' : 'check-') + 'square-o');
			link.addClass('ecom-configurator-package-state-' + newState).removeClass('ecom-configurator-package-state-' + oldState);
			icon.addClass('icon-state-' + newState).removeClass('icon-state-' + oldState);
			if ( thePackages[index].current ) {
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
	var html = [],
		prop,
		tabIndex = 1;
	if ( options !== null && options.length ) {
		for ( prop in options ) {
			if ( options.hasOwnProperty(prop) ) {
				var content = "<a data-t3pid=\"" + result.pid + "\" data-t3lang=\"" + result.lang + "\" data-t3cobj=\"" + result.cObj + "\" data-option=\"" + options[prop].uid + "\" data-option-state=\"" + (options[prop].active ? 1 : 0) + "\" class=\"ecom-configurator-select-package-option-wrap " + (options[prop].disabled ? "disabled" : "enabled") + "\" tabindex=\"" + tabIndex + "\">";
				if ( result.pricingEnabled ) {
					content += "<span class=\"ecom-configurator-select-package-option-price\">" + options[prop].price + "</span>";
				}
				if ( options[prop].hint ) {
					content += "<div class=\"ecom-configurator-select-package-option-info-wrapper\"><span class=\"ecom-configurator-select-package-option-info\">" + moreInfoLinkTitle + "</span></div>";
				}
				content += "<div class=\"ecom-configurator-checkbox " + (options[prop].active ? '' : 'un') + "checked\"><span class=\"ecom-configurator-option-checkbox-image\"></span></div>";
				content += "<span class=\"ecom-configurator-select-package-option option\">" + options[prop].title + "</span></a>";
				/*if ( options[prop].hint ) {
				 content += "<span class=\"ecom-configurator-select-package-option-info-hint-box\"><span class=\"close-popover-x\" title=\"Close Info Box\">×</span>###CONTENT###</span>";
				 }*/
				content += "<div class=\"clearfix\"></div>";
				html.push(content);
				tabIndex++;
			}
		}
		$('#ecom-configurator-select-options-ajax-update').html(html.join('')).show();
		$('#ecom-configurator-reset-configuration-button').show();
		txEcompcSetOption(); // Re-assign Click-function()
		addInfoTrigger();
	} else if ( result.showResult ) {
		$('#ecom-configurator-select-options-ajax-update').html('').hide();
		$('#ecom-configurator-reset-configuration-button').hide();
	}
}

function getConfigurationCode(config) {
	var spans = '';
	for ( var i=0; i<config.length; i++ ) {
		var addClass = ' class="ecompc-syntax-help"',
			addTitle = ' title="' + config[i].pkg + '"',
			prependInnerHTML = '';
		if ( !config[i].pkg ) {
			addClass = '';
			addTitle = '';
		}
		/** Variant with lock icon for fixed packages (!visibleInFrontend) */
		/*
		 if ( config[i][2] ) {
		 prependInnerHTML = '<i class="fa fa-lock"></i>';
		 }
		 */
		spans += '<span' + addClass + addTitle + '>' + prependInnerHTML + config[i][1] + '</span>';
	}

	return spans;
}

function getConfigurationSummary(config, pricingEnabled) {
	var table = "<table>",
		iterator = 0;
	for ( var key in config ) {
		iterator++;
		if ( !config[key].pkg ) {
			continue;
		}
		table += "<tr><td>" + iterator + "</td><td>" + config[key].pkg + "</td><td>" + config[key][0] + (config[key][1] != "" ? " [" + config[key][1] + "]" : "") + "</td><td>" + (config[key].pkgUid ? "<a data-package=\"" + config[key].pkgUid + "\" class=\"ecom-configurator-package-select\"><i class=\"fa fa-edit\"></i></a>" : "") + "</td>";
		if ( pricingEnabled ) {
			table += "<td class=\"align-right\">" + (config[key].pricing ? config[key].pricing : "") + "</td>";
		}
		table += "</tr>";
	}
	table += "</table>";
	return table;
}

// Popup on click
function addInfoTrigger() {
	var triggerHint = '#ecom-configurator-canvas .ecom-configurator-select-package-option-info',
		hintBoxSelector = '#ecom-configurator-canvas #ecom-configurator-select-package-option-info-hint-box',
		windowHeight,
		popupHeight,
		scrollPosition,
		currentHintBox;

	$(triggerHint).on('click', function(e) {
		/**
		 * First hide every active hint box @deprecated since single box is used - switching contents via AJAX
		 */
			//hideHintBox(hintBoxSelector);

			// Prevent default anchor action
		e.preventDefault();

		// AJAX request
		getOptionHint($(this).parents('a').first().attr('data-option'), t3pid, t3lang, t3cobj);

		// Setting new vars
		windowHeight = $(document).height();
		currentHintBox = $(hintBoxSelector);

		// Calculate position of the hint-box
		popupHeight = $(currentHintBox).outerHeight();
		// Check if popup high exceeds window height
		// Then set position top 15px
		scrollPosition = -5;
		// Show Popup
		//currentHintBox.css('display', 'block').animate({'top': scrollPosition, 'opacity': 1}, 'fast');
		currentHintBox.slideDown();

		// Prevent Option-a-tag from executing
		return false;
	})
}


/**********************************************
 * Initialize Event Listeners once DOM loaded *
 *********************************************/
(function() {
	txEcompcSetOption();
	txEcompcIndex();
	addInfoTrigger();
})(jQuery);