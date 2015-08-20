<?php
	/**
	 * Created by PhpStorm.
	 * User: sebo
	 * Date: 25.11.14
	 * Time: 13:43
	 */

$extKey = 'ecompc';
$translate = 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:';

$tempColumns = [
	// Packages selectable
	'tx_' . $extKey . '_packages' => [
		'l10n_mode' => 'exclude',
		'exclude' => 1,
		'label' => $translate . 'tx_ecompc_domain_model_dependency.packages',
		'config' => [
			'type' => 'select',
			'form_type' => 'user',
			'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTtContentTxEcompcPackages',
			'foreign_table' => 'tx_ecompc_domain_model_package',
			'foreign_table_where' => 'AND NOT tx_ecompc_domain_model_package.deleted AND tx_ecompc_domain_model_package.sys_language_uid IN (-1,0) ORDER BY tx_ecompc_domain_model_package.pid ASC, tx_ecompc_domain_model_package.sorting ASC',
			'size' => 10,
			'autoSizeMax' => 30,
			'minitems' => 1,
			'maxitems' => 100000,
			'multiple' => 0,
			'wizards' => [
				'_PADDING' => 10,
				'_VALIGN' => 'middle',
				'suggest' => [
					'type' => 'suggest',
					'default' => [
						'searchWholePhrase' => TRUE
					]
				]
			]
		]
	],
	// Configuration(s) available
	'tx_' . $extKey . '_configurations' => [
		'l10n_mode' => 'exclude',
		'exclude' => 1,
		'label' => $translate . 'ff_sTitle_config',
		'config' => [
			'type' => 'inline',
			'form_type' => 'user',
			'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTtContentTxEcompcConfigurations',
			'foreign_table' => 'tx_ecompc_domain_model_configuration',
			'foreign_field' => 'tt_content_uid',
			'appearance' => [
				'collapseAll' => 1,
				'expandSingle' => 1,
				'newRecordLinkAddTitle' => 1,
				'levelLinksPosition' => 'bottom',
				'showPossibleLocalizationRecords' => 0,
				'showAllLocalizationLink' => 0
			],
			'behaviour' => [
				'localizationMode' => 'keep',
				'localizeChildrenAtParentLocalization' => 0,
				'disableMovingChildrenWithParent' => 0,
				'enableCascadingDelete' => 1
			]
		]
	],
	'tx_' . $extKey . '_pricing' => [
		'l10n_mode' => 'exclude',
		'exclude' => 1,
		'label' => $translate . 'tx_ecompc_domain_model_configuration.pricing',
		'config' => [
			'type' => 'flex',
			'form_type' => 'user',
			'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTtContentTxEcompcPricing',
			'ds' => [
				'default' => 'FILE:EXT:' . $extKey . '/Configuration/FlexForms/price_list.xml'
			]
		]
	]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $tempColumns, 1);

$defaultTypeConfiguration = ('
	--palette--;' . $translate . 'tx_ecompc_domain_model_configuration.available_packages;tx_ecompc_palettes_1, tx_' . $extKey . '_configurations,
	--div--;' . $translate . 'tabs.pricing, tx_' . $extKey . '_pricing,
	--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended, bodytext;' . $translate . 'bodytext_formlabel;;richtext:rte_transform[flag=rte_enabled|mode=ts_css], rte_enabled
');
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][\S3b0\Ecompc\Setup::CONFIGURATOR_DYN_SIGNATURE] = $defaultTypeConfiguration;
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][\S3b0\Ecompc\Setup::CONFIGURATOR_SKU_SIGNATURE] = $defaultTypeConfiguration;

$GLOBALS['TCA']['tt_content']['palettes']['tx_ecompc_palettes_1'] = [ 'showitem' => 'tx_' . $extKey . '_packages' ];