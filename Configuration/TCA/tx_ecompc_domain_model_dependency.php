<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$extTranslationPath = 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:';

return array(
	'ctrl' => array(
		'title'	=> $extTranslationPath . 'tx_ecompc_domain_model_dependency',
		'label' => 'mode',
		'label_alt' => 'ref_option',
		'label_alt_force' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'hideTable' => 1,
		'delete' => 'deleted',
		'requestUpdate' => 'mode',
		'typeicon_column' => 'mode',
		'typeicon_classes' => array(
			'default' => 'extensions-ecompc-dependency-default',
			'1' => 'extensions-ecompc-dependency-allow',
			'0' => 'extensions-ecompc-dependency-deny'
		),
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'mode,options,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('ecompc') . 'Configuration/TCA/Dependency.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ecompc') . 'Resources/Public/Icons/tx_ecompc_domain_model_dependency.png'
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden, mode, options',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden;;1;;1-1-1, mode;;2, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
		'2' => array('showitem' => 'packages, options', 'canNotCollapse' => 1)
	),
	'columns' => array(

		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),

		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),

		'mode' => array(
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_dependency.mode',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array($extTranslationPath . 'select.prompt', ''),
					array($extTranslationPath . 'tx_ecompc_domain_model_dependency.mode.allow', 1),
					array($extTranslationPath . 'tx_ecompc_domain_model_dependency.mode.deny', 0)
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => 'required'
			),
		),
		'packages' => array(
			'displayCond' => 'FIELD:mode:IN:0,1',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_dependency.packages',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_ecompc_domain_model_package',
				'foreign_table_where' => 'AND NOT tx_ecompc_domain_model_package.uid=(SELECT configuration_package FROM tx_ecompc_domain_model_option WHERE uid=###REC_FIELD_ref_option###) AND tx_ecompc_domain_model_package.sorting < (SELECT sorting FROM tx_ecompc_domain_model_package WHERE uid=(SELECT configuration_package FROM tx_ecompc_domain_model_option WHERE uid=###REC_FIELD_ref_option###)) AND tx_ecompc_domain_model_package.pid=###REC_FIELD_pid### AND NOT tx_ecompc_domain_model_package.deleted AND tx_ecompc_domain_model_package.sys_language_uid IN (-1,0)',
				'MM' => 'tx_ecompc_dependency_package_mm',
				'size' => 10,
				'autoSizeMax' => 30,
				'minitems' => 1,
				'maxitems' => 9999,
				'multiple' => 0,
				'renderMode' => 'checkbox',
				'disableNoMatchingValueElement' => 1,
			),
		),
		'options' => array(
			'displayCond' => 'FIELD:mode:IN:0,1',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_dependency.options',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_ecompc_domain_model_option',
				'foreign_table_where' => 'AND tx_ecompc_domain_model_option.pid=###REC_FIELD_pid### AND NOT tx_ecompc_domain_model_option.deleted AND tx_ecompc_domain_model_option.sys_language_uid IN (-1,0) ORDER BY tx_ecompc_domain_model_option.configuration_package, tx_ecompc_domain_model_option.backend_label, tx_ecompc_domain_model_option.frontend_label',
				'MM' => 'tx_ecompc_dependency_option_mm',
				'itemsProcFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->itemsProcFuncTxEcompcDomainModelDependencyOptions',
				'size' => 10,
				'autoSizeMax' => 30,
				'minitems' => 1,
				'maxitems' => 9999,
				'multiple' => 0,
				'renderMode' => 'checkbox',
				'disableNoMatchingValueElement' => 1,
			),
		),
		'ref_option' => array(
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_ecompc_domain_model_option'
			)
		)

	),
);
