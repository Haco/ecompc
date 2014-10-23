<?php
	if ( !defined('TYPO3_MODE') ) {
		die('Access denied.');
	}

	global $TYPO3_CONF_VARS;

	$extKey = 'ecompc';

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		$extKey, 'configurator_dynamic', 'Configurator: Dynamic Configuration'
	);

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		$extKey, 'configurator_sku', 'Configurator: Static Configuration [SKU]'
	);

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		$extKey, 'resolver', 'Configuration Resolver'
	);

	$extendTtContentFields = array(
		// Packages selectable
		'tx_' . $extKey . '_packages' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:tx_ecompc_domain_model_dependency.packages',
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
		// Configuration(s) available
		'tx_' . $extKey . '_configurations' => array(
			'l10n_mode' => 'exclude',
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
			'label' => 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:tx_ecompc_domain_model_configuration.price_old_basic',
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
			'label' => 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:tx_ecompc_domain_model_configuration.pricing',
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

	if ( TYPO3_MODE === 'BE' ) {
		// Add plugin to new element wizard
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['S3b0\\Ecompc\\Wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey) . 'Resources/Private/PHP/Wizicon.php';
	}

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $extendTtContentFields, 1);

	$pluginSignatureDynamic = str_replace('_','',$extKey) . '_configurator_dynamic';
	$pluginSignatureSku = str_replace('_','',$extKey) . '_configurator_sku';
	$defaultTypeConfiguration = ('
		--palette--;LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:tx_ecompc_domain_model_configuration.available_packages;tx_ecompc_palettes_1,
		tx_' . $extKey . '_configurations,
		--div--;LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:tabs.pricing,
		tx_' . $extKey . '_pricing;;tx_ecompc_palettes_2,
		--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended,
		bodytext;LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:bodytext_formlabel;;richtext:rte_transform[flag=rte_enabled|mode=ts_css],
		rte_enabled
	');
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignatureDynamic] = $defaultTypeConfiguration;
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignatureSku] = $defaultTypeConfiguration;

	$GLOBALS['TCA']['tt_content']['palettes']['tx_ecompc_palettes_1'] = array('showitem' => 'tx_' . $extKey . '_packages');
	$GLOBALS['TCA']['tt_content']['palettes']['tx_ecompc_palettes_2'] = array('showitem' => 'tx_' . $extKey . '_base_price_default');


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