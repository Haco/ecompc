<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'S3b0.' . $_EXTKEY,
	'Configurator',
	array(
		'Standard' => 'index,selectPackageOptions,setOption,reset,selectRegion',
	),
	// non-cacheable actions
	array(
		'Standard' => 'index,selectPackageOptions,setOption,reset',
	)
);