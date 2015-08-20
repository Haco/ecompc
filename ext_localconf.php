<?php
if ( !defined('TYPO3_MODE') ) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'S3b0.' . $_EXTKEY,
	'configurator_dynamic',
	[
		'DynamicConfigurator' => 'index, reset, selectRegion, request',
		'DynamicConfiguratorAjaxRequest' => 'index, setOption',
		'Standard' => 'request'
	],
	// non-cacheable actions
	[
		'DynamicConfigurator' => 'index, reset, selectRegion, request',
		'DynamicConfiguratorAjaxRequest' => 'index, setOption',
		'Standard' => 'request'
	]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'S3b0.' . $_EXTKEY,
	'configurator_sku',
	[
		'SkuConfigurator' => 'index, reset, selectRegion, request',
		'SkuConfiguratorAjaxRequest' => 'index, setOption',
		'Standard' => 'request'
	],
	// non-cacheable actions
	[
		'SkuConfigurator' => 'index, reset, selectRegion, request',
		'SkuConfiguratorAjaxRequest' => 'index, setOption',
		'Standard' => 'request'
	]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'S3b0.' . $_EXTKEY,
	'resolver',
	[
		'Resolver' => 'show, list, showUserInformation'
	],
	// non-cacheable actions
	[
		'Resolver' => 'show, list, showUserInformation'
	]
);

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['EcomProductConfigurator'] = 'EXT:ecompc/Classes/Utility/AjaxDispatcher.php';