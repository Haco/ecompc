<?php
	/**
	 * Created by PhpStorm.
	 * User: sebo
	 * Date: 25.11.14
	 * Time: 13:43
	 */

$extKey = 'ecompc';
$locallang_db = 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:';

$tempColumns = array(
	// Packages selectable
	'tx_' . $extKey . '_packages' => array(
		'l10n_mode' => 'exclude',
		'exclude' => 1,
		'label' => $locallang_db . 'tx_ecompc_domain_model_dependency.packages',
		'config' => array(
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
	// Configuration(s) available
	'tx_' . $extKey . '_configurations' => array(
		'l10n_mode' => 'exclude',
		'exclude' => 1,
		'label' => $locallang_db . 'ff_sTitle_config',
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
				'showPossibleLocalizationRecords' => 0,
				'showAllLocalizationLink' => 0,
			),
			'behaviour' => array(
				'localizationMode' => 'keep',
				'localizeChildrenAtParentLocalization' => 0,
				'disableMovingChildrenWithParent' => 0,
				'enableCascadingDelete' => 1
			),
		),
	),
	/**
	 * Base Price in Default Currency
	 * @deprecated hold for compatibility
	 */
	'tx_' . $extKey . '_base_price_default' => array(
		'l10n_mode' => 'exclude',
		'exclude' => 1,
		'label' => $locallang_db . 'tx_ecompc_domain_model_configuration.price_old_basic',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'double2',
			'readOnly' => 1
		)
	),
	// Base Price in Foreign Currencies (XML)
	'tx_' . $extKey . '_pricing' => array(
		'l10n_mode' => 'exclude',
		'exclude' => 1,
		'label' => $locallang_db . 'tx_ecompc_domain_model_configuration.pricing',
		'config' => array(
			'type' => 'flex',
			'form_type' => 'user',
			'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTtContentTxEcompcPricing',
			'ds' => array(
				'default' => 'FILE:EXT:' . $extKey . '/Configuration/FlexForms/price_list.xml'
			)
		)
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $tempColumns, 1);

$pluginSignatureDynamic = str_replace('_', '', $extKey) . '_configurator_dynamic';
$pluginSignatureSku = str_replace('_', '', $extKey) . '_configurator_sku';
$defaultTypeConfiguration = ('
	--palette--;' . $locallang_db . 'tx_ecompc_domain_model_configuration.available_packages;tx_ecompc_palettes_1,
	tx_' . $extKey . '_configurations,
	--div--;' . $locallang_db . 'tabs.pricing,
	tx_' . $extKey . '_pricing;;tx_ecompc_palettes_2,
	--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended,
	bodytext;' . $locallang_db . 'bodytext_formlabel;;richtext:rte_transform[flag=rte_enabled|mode=ts_css],
	rte_enabled
');
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignatureDynamic] = $defaultTypeConfiguration;
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignatureSku] = $defaultTypeConfiguration;

$GLOBALS['TCA']['tt_content']['palettes']['tx_ecompc_palettes_1'] = array('showitem' => 'tx_' . $extKey . '_packages');
$GLOBALS['TCA']['tt_content']['palettes']['tx_ecompc_palettes_2'] = array('showitem' => 'tx_' . $extKey . '_base_price_default');