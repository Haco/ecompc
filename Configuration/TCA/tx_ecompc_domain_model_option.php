<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$extTranslationPath = 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:';

return array(
	'ctrl' => array(
		'title'	=> $extTranslationPath . 'tx_ecompc_domain_model_option',
		'label' => 'backend_label',
		'label_alt' => 'configuration_code_segment,frontend_label,configuration_package',
		'label_userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->labelUserFuncTxEcompcDomainModelOption',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'default_sortby' => 'ORDER BY backend_label,frontend_label',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'sortby' => 'sorting',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'useColumnsForDefaultValues' => 'configuration_package',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'backend_label,frontend_label,configuration_code_segment,image,hint_text,price,price_percental,configuration_package,dependency,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('ecompc') . 'Configuration/TCA/Option.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ecompc') . 'Resources/Public/Icons/tx_ecompc_domain_model_option.png'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, backend_label, frontend_label, configuration_code_segment, image, hint_text, price, price_percental, configuration_package, dependency',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, frontend_label;;2, configuration_package, configuration_code_segment, --div--;' . $extTranslationPath . 'tabs.referral, image, dependency, --div--;LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:tabs.pricing, --palette--;;3, price_list, --div--;LLL:EXT:cms/locallang_tca.xlf:pages.tabs.extended, hint_text;;;richtext:rte_transform[mode=ts_links], --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
		'2' => array('showitem' => 'backend_label'),
		'3' => array('showitem' => 'price, price_percental', 'canNotCollapse' => 1)
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
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_ecompc_domain_model_option',
				'foreign_table_where' => 'AND tx_ecompc_domain_model_option.pid=###CURRENT_PID### AND tx_ecompc_domain_model_option.sys_language_uid IN (-1,0)',
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

		'backend_label' => array(
			'l10n_mode' => 'exclude',
			'displayCond' => 'FIELD:frontend_label:REQ:true',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_option.backend_label',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim',
				'placeholder' => '__row|frontend_label',
				'mode' => 'useOrOverridePlaceholder'
			),
		),
		'frontend_label' => array(
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_option.frontend_label',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'configuration_code_segment' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_option.configuration_code_segment',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'image' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_option.image',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
					'image',
					array(
						'maxitems' => 1,
						'appearance' => array(
							'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
							'enabledControls' => array(
								'localize' => 0
							)
						),
						'behaviour' => array(
							'localizeChildrenAtParentLocalization' => FALSE
						),
						'foreign_types' => array(
							\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
								'showitem' => '--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,--palette--;;filePalette'
							),
						),
						'filter' => array(
							'0' => array(
								'parameters' => array(
									'allowedFileExtensions' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
								)
							)
						)
					),
					$GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
			),
		),
		'hint_text' => array(
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_option.hint_text',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'wizards' => array(
					'RTE' => array(
						'icon' => 'wizard_rte2.gif',
						'notNewRecords'=> 1,
						'RTEonly' => 1,
						'script' => 'wizard_rte.php',
						'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
						'type' => 'script'
					)
				)
			),
		),
		'price' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_option.price',
			'config' => array(
				'type' => 'input',
				'size' => 22,
				'eval' => 'double2'
			)
		),
		'price_percental' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_option.price_percental',
			'config' => array(
				'type' => 'input',
				'size' => 22,
				'range' => array(
					'lower' => 0,
					'upper' => 100
				),
				'eval' => 'double2'
			)
		),
		'price_list' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => '',
			'config' => array(
				'type' => 'flex',
				'ds' => array(
					'default' => 'FILE:EXT:ecompc/Configuration/FlexForms/price_list.xml'
				)
			)
		),
		'configuration_package' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_option.configuration_package',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_ecompc_domain_model_package',
				'foreign_table_where' => 'AND tx_ecompc_domain_model_package.pid=###CURRENT_PID### AND NOT tx_ecompc_domain_model_package.deleted AND tx_ecompc_domain_model_package.sys_language_uid IN (-1,0) ORDER BY tx_ecompc_domain_model_package.backend_label, tx_ecompc_domain_model_package.frontend_label',
				'items' => array(
					array($extTranslationPath . 'select.prompt', '')
				),
				'minitems' => 1,
				'maxitems' => 1,
			),
		),
		'dependency' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:tx_ecompc_domain_model_option.dependency',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_ecompc_domain_model_dependency',
				'foreign_field' => 'ref_option',
				'maxitems' => 1,
				'appearance' => array(
					'collapseAll' => 0,
					'newRecordLinkAddTitle' => 0,
					'newRecordLinkTitle' => 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:tx_ecompc_domain_model_option.dependency.inlineElementAddTitle',
					'levelLinksPosition' => 'bottom'
				),
				'behaviour' => array(
					'localizationMode' => 'select',
					'localizeChildrenAtParentLocalization' => 0,
					'disableMovingChildrenWithParent' => 0,
					'enableCascadingDelete' => 1
				)
			),

		),
	),
);
