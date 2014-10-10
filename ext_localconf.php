<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'S3b0.' . $_EXTKEY,
	'configurator_dynamic',
	array(
		'DynamicConfigurator' => 'index,reset,selectRegion,request',
		'DynamicConfiguratorAjaxRequest' => 'updatePackages,selectPackageOptions,setOption,resetPackage'
	),
	// non-cacheable actions
	array(
		'DynamicConfigurator' => 'index,reset,selectRegion,request',
		'DynamicConfiguratorAjaxRequest' => 'updatePackages,selectPackageOptions,setOption,resetPackage'
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'S3b0.' . $_EXTKEY,
	'configurator_sku',
	array(
		'SkuConfigurator' => 'index,reset,selectRegion,request',
		'SkuConfiguratorAjaxRequest' => 'updatePackages,selectPackageOptions,setOption,resetPackage'
	),
	// non-cacheable actions
	array(
		'SkuConfigurator' => 'index,reset,selectRegion,request',
		'SkuConfiguratorAjaxRequest' => 'updatePackages,selectPackageOptions,setOption,resetPackage'
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'S3b0.' . $_EXTKEY,
	'Resolver',
	array(
		'Resolver' => 'index'
	),
	// non-cacheable actions
	array(
		'Resolver' => 'index'
	)
);

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['EcomProductConfigurator'] = 'EXT:ecompc/Classes/Utility/AjaxDispatcher.php';