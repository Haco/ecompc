<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$extKey = 'ecompc';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$extKey,
	'Configurator',
	'Product Configurator'
);

$extendTca = array(
	'tx_' . $extKey . '_type' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:tx_ecompc_domain_model_configuration.type',
		'config' => array(
			'type' => 'select',
			'form_type' => 'user',
			'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTtContentTxEcompcType',
			'items' => array(
				array('-- please choose --', -1),
				array('Static [SKU]', 0),
				array('Dynamic [Config Code]', 1)
			),
			'size' => 1,
			'minitems' => 1,
			'eval' => '',
			'default' => -1
		),
	),
	'tx_' . $extKey . '_pckg' => array(
		'displayCond' => 'FIELD:tx_ecompc_type:>:-1',
		'exclude' => 1,
		'label' => 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:tx_ecompc_domain_model_configuration.available_packages',
		'config' => array(
			'type' => 'select',
			'form_type' => 'user',
			'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTtContentTxEcompcPackages',
			'foreign_table' => 'tx_ecompc_domain_model_package',
			'foreign_table_where' => 'AND NOT tx_ecompc_domain_model_package.deleted AND tx_ecompc_domain_model_package.sys_language_uid IN (-1,0)',
			'size' => 10,
			'autoSizeMax' => 30,
			'minitems' => 1,
			'maxitems' => 100000,
			'multiple' => 0,
			'wizards' => array(
				'_PADDING' => 10,
				'_VALIGN' => 'middle',
				'suggest' => array(
					'type' => 'suggest',
					'default' => array(
						'searchWholePhrase' => TRUE
					)
				)
			)
		),
	),
	'tx_' . $extKey . '_conf' => array(
		'displayCond' => array(
			'AND' => array(
				'FIELD:tx_ecompc_type:>:-1',
				'FIELD:tx_ecompc_packages:REQ:true'
			)
		),
		'exclude' => 1,
		'label' => 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:ff_sTitle_config',
		'config' => array(
			'type' => 'inline',
			'form_type' => 'user',
			'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTtContentTxEcompcConfigurations',
			'foreign_table' => 'tx_ecompc_domain_model_configuration',
			'foreign_field' => 'tt_content_uid',
			'appearance' => array(
				'collapseAll' => 1,
				'expandSingle' => 1,
				'newRecordLinkAddTitle' => 1,
				'levelLinksPosition' => 'bottom',
				'showPossibleLocalizationRecords' => 1,
				'showAllLocalizationLink' => 1,
			),
			'behaviour' => array(
				'localizationMode' => 'select',
				'localizeChildrenAtParentLocalization' => 0,
				'disableMovingChildrenWithParent' => 0,
				'enableCascadingDelete' => 1
			),
		),
	),
	'tx_' . $extKey . '_bpdc' => array(
		'l10n_mode' => 'exclude',
		'exclude' => 1,
		'label' => 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:tx_ecompc_domain_model_configuration.price',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'double2'
		)
	),
	'tx_' . $extKey . '_bpfc' => array(
		'l10n_mode' => 'exclude',
		'exclude' => 1,
		'label' => '',
		'config' => array(
			'type' => 'flex',
			'ds' => array(
				'default' => 'FILE:EXT:ecompc/Configuration/FlexForms/price_list.xml'
			)
		)
	)
);

if (TYPO3_MODE === 'BE') {
	// Add plugin to new element wizard
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['S3b0\\Ecompc\\Wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey) . 'Resources/Private/PHP/Wizicon.php';
}

$GLOBALS['TCA']['tt_content']['ctrl']['requestUpdate'] .= ', tx_' . $extKey . '_type';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $extendTca, 1);
$pluginSignature = str_replace('_','',$extKey) . '_configurator';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'tx_' . $extKey . '_type;;tx_ecompc, tx_' . $extKey . '_conf, --div--;LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:tabs.pricing, tx_' . $extKey . '_bpdc;;tx_ecompc_pricing, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended, bodytext;LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:bodytext_formlabel;;richtext:rte_transform[flag=rte_enabled|mode=ts_css], rte_enabled';
$GLOBALS['TCA']['tt_content']['palettes']['tx_ecompc'] = array('showitem' => 'tx_' . $extKey . '_pckg');
$GLOBALS['TCA']['tt_content']['palettes']['tx_ecompc_pricing'] = array('showitem' => 'tx_' . $extKey . '_bpfc');
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $extKey . '/Configuration/FlexForms/flexform_configurator.xml');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($extKey, 'Configuration/TypoScript', 'Product Configurator');

// Tables allowed on regular TYPO3 pages
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ecompc_domain_model_configuration');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ecompc_domain_model_dependency');

// Add context sensitive help
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_ecompc_domain_model_configuration', 'EXT:ecompc/Resources/Private/Language/locallang_csh_tx_ecompc_domain_model_configuration.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_ecompc_domain_model_option', 'EXT:ecompc/Resources/Private/Language/locallang_csh_tx_ecompc_domain_model_option.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_ecompc_domain_model_package', 'EXT:ecompc/Resources/Private/Language/locallang_csh_tx_ecompc_domain_model_package.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_ecompc_domain_model_dependency', 'EXT:ecompc/Resources/Private/Language/locallang_csh_tx_ecompc_domain_model_dependency.xlf');

// Add Sprite Icons for different record types (visual distinction)
\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons(
	array(
		'dependency-default' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($extKey) . 'Resources/Public/Icons/tx_ecompc_domain_model_dependency.png',
		'dependency-allow' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('t3skin') . 'images/icons/status/status-permission-granted.png',
		'dependency-deny' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('t3skin') . 'images/icons/status/status-permission-denied.png',
		'package-default' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($extKey) . 'Resources/Public/Icons/tx_ecompc_domain_model_package.png',
		'package-not-visible-fe' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($extKey) . 'Resources/Public/Icons/tx_ecompc_domain_model_package_not_visible_fe.png'
	),
	$extKey
);