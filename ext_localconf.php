<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'S3b0.' . $_EXTKEY,
	'Configurator',
	array(
		'Standard' => 'index,reset,selectRegion,request',
		'AjaxRequest' => 'updatePackages,selectPackageOptions,setOption,resetPackage'
	),
	// non-cacheable actions
	array(
		'Standard' => 'index,reset,selectRegion,request',
		'AjaxRequest' => 'updatePackages,selectPackageOptions,setOption,resetPackage'
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