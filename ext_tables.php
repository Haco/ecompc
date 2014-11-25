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

	if ( TYPO3_MODE === 'BE' ) {
		// Add plugin to new element wizard
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['S3b0\\Ecompc\\Wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey) . 'Resources/Private/PHP/Wizicon.php';
	}

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