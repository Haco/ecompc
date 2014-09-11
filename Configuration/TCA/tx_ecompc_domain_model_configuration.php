<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$extTranslationPath = 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:';

return array(
	'ctrl' => array(
		'title'	=> $extTranslationPath . 'tx_ecompc_domain_model_configuration',
		'label' => 'frontend_label',
		'label_alt' => 'sku,configuration_code_prefix',
		'label_alt_force' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'default_sortby' => 'ORDER BY sku,frontend_label',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'hideTable' => 1,
		'requestUpdate' => 'frontend_label',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group'
		),
		'searchFields' => 'frontend_label,sku,configuration_code_suffix,configuration_code_prefix,options,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ecompc') . 'Resources/Public/Icons/tx_ecompc_domain_model_configuration.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, frontend_label, sku, configuration_code_suffix, configuration_code_prefix, options',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, type, frontend_label;;2, --div--;' . $extTranslationPath . 'tabs.referral, options, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime, --linebreak--, fe_group'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
		'2' => array('showitem' => 'sku, configuration_code_prefix, configuration_code_suffix', 'canNotCollapse' => 1)
	),
	'columns' => array(

		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => array(
				'AND' => array(
					'FIELD:sys_language_uid:>:0',
					'HIDE_FOR_NON_ADMINS'
				)
			),
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_ecompc_domain_model_configuration',
				'foreign_table_where' => 'AND tx_ecompc_domain_model_configuration.pid=###CURRENT_PID### AND tx_ecompc_domain_model_configuration.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

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
		'fe_group' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.fe_group',
			'config' => array(
				'type' => 'select',
				'size' => 7,
				'maxitems' => 20,
				'items' => array(
					array(
						'LLL:EXT:lang/locallang_general.xlf:LGL.hide_at_login',
						-1
					),
					array(
						'LLL:EXT:lang/locallang_general.xlf:LGL.any_login',
						-2
					),
					array(
						'LLL:EXT:lang/locallang_general.xlf:LGL.usergroups',
						'--div--'
					)
				),
				'exclusiveKeys' => '-1,-2',
				'foreign_table' => 'fe_groups',
				'foreign_table_where' => 'ORDER BY fe_groups.title'
			)
		),

		'frontend_label' => array(
			'l10n_mode' => 'prefixLangTitle',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_configuration.frontend_label',
			'config' => array(
				'type' => 'input',
				'size' => 41,
				'eval' => 'trim,required'
			),
		),
		'sku' => array(
			'l10n_mode' => 'mergeIfNotBlank',
			'l10n_display' => 'defaultAsReadonly',
			'displayCond' => 'REC:NEW:false',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_configuration.sku',
			'config' => array(
				'type' => 'input',
				'form_type' => 'user',
				'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTxEcompcDomainModelConfigurationSku',
				'size' => 10,
				'eval' => 'trim'
			),
		),
		'configuration_code_suffix' => array(
			'l10n_mode' => 'mergeIfNotBlank',
			'l10n_display' => 'defaultAsReadonly',
			'displayCond' => 'REC:NEW:false',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_configuration.configuration_code_suffix',
			'config' => array(
				'type' => 'input',
				'form_type' => 'user',
				'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTxEcompcDomainModelConfigurationConfigurationCodeSuffix',
				'size' => 10,
				'eval' => 'trim'
			),
		),
		'configuration_code_prefix' => array(
			'l10n_mode' => 'mergeIfNotBlank',
			'l10n_display' => 'defaultAsReadonly',
			'displayCond' => 'REC:NEW:false',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_configuration.configuration_code_prefix',
			'config' => array(
				'type' => 'input',
				'form_type' => 'user',
				'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTxEcompcDomainModelConfigurationConfigurationCodePrefix',
				'size' => 10,
				'eval' => 'trim'
			),
		),
		'options' => array(
			'l10n_mode' => 'mergeIfNotBlank',
			'l10n_display' => 'defaultAsReadonly',
			'displayCond' => 'REC:NEW:false',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_configuration.options',
			'config' => array(
				'type' => 'select',
				'form_type' => 'user',
				'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTxEcompcDomainModelConfigurationOptions',
				'foreign_table' => 'tx_ecompc_domain_model_option',
				'foreign_table_where' => 'AND NOT tx_ecompc_domain_model_option.deleted AND tx_ecompc_domain_model_option.sys_language_uid IN (-1,0) ORDER BY tx_ecompc_domain_model_option.configuration_package, tx_ecompc_domain_model_option.frontend_label, tx_ecompc_domain_model_option.backend_label',
				'MM' => 'tx_ecompc_configuration_option_mm',
				'size' => 10,
				'autoSizeMax' => 30,
				'maxitems' => 9999,
				'multiple' => 0,
				'renderMode' => 'checkbox',
				'disableNoMatchingValueElement' => 1
			),
		),
		'tt_content_uid' => array(
			'config' => array(
				'type' => 'passthrough'
			)
		)

	),
);
