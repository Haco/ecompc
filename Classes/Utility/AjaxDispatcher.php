<?php

	namespace S3b0\Ecompc\Utility;

	/***************************************************************
	 * Copyright notice
	 *
	 * 2010 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktusteam.de>
	 * 2014 Sebastian Iffland <Sebastian.Iffland@ecom-ex.com>, ecom instruments GmbH (6.2 LTS Update)
	 * All rights reserved
	 *
	 *
	 * This script is part of the TYPO3 project. The TYPO3 project is
	 * free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * The GNU General Public License can be found at
	 * http://www.gnu.org/copyleft/gpl.html.
	 *
	 * This script is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * This copyright notice MUST APPEAR in all copies of the script!
	 ***************************************************************/
	use TYPO3\CMS\Core\Utility as CoreUtility;

	/**
	 * Utility to include defined frontend libraries as jQuery and related CSS
	 *
	 * @package Utility
	 * @author Daniel Lienert <daniel@lienert.cc>
	 */
	class AjaxDispatcher extends \Ecom\EcomToolbox\Utility\AjaxDispatcher {

		protected $defaultVendorName = 'S3b0';
		protected $defaultExtensionName = 'Ecompc';
		protected $defaultPluginName = 'configurator_dynamic';
		protected $defaultControllerName = 'DynamicConfiguratorAjaxRequest';
		protected $defaultActionName = 'index';
		protected $pageType = 1407764086;

	}

	global $TYPO3_CONF_VARS;

	/** !!! IMPORTANT TO MAKE JSON WORK !!! */
	$TYPO3_CONF_VARS['FE']['debug'] = '0';

	/** @var \S3b0\Ecompc\Utility\AjaxDispatcher $dispatcher */
	$dispatcher = CoreUtility\GeneralUtility::makeInstance('S3b0\\Ecompc\\Utility\\AjaxDispatcher');

	// ATTENTION! Dispatcher first needs to be initialized here!!!
	echo $dispatcher
		->init($TYPO3_CONF_VARS)
		->dispatch();

?>