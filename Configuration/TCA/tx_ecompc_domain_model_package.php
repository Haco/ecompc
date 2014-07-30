<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$extTranslationPath = 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:';

return array(
	'ctrl' => array(
		'title'	=> $extTranslationPath . 'tx_ecompc_domain_model_package',
		'label' => 'backend_label',
		'label_alt' => 'frontend_label',
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
		'requestUpdate' => 'frontend_label, visible_in_frontend',
		'typeicon_column' => 'visible_in_frontend',
		'typeicon_classes' => array(
			'default' => 'extensions-ecompc-package-default',
			'0' => 'extensions-ecompc-package-not-visible-fe',
			'1' => 'extensions-ecompc-package-default',
		),
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'backend_label,frontend_label,prompt,hint_text,image,visible_in_frontend,multiple_select,default_option,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('ecompc') . 'Configuration/TCA/Package.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ecompc') . 'Resources/Public/Icons/tx_ecompc_domain_model_package.png'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, backend_label, frontend_label, prompt, hint_text, image, visible_in_frontend, multiple_select, default_option',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, frontend_label;;3, prompt, hint_text;;;richtext:rte_transform[mode=ts_links], --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.appearance, image, --palette--;;2, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
		'2' => array('showitem' => 'default_option, visible_in_frontend, multiple_select, percent_pricing', 'canNotCollapse' => 1),
		'3' => array('showitem' => 'backend_label')
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
				'foreign_table' => 'tx_ecompc_domain_model_package',
				'foreign_table_where' => 'AND tx_ecompc_domain_model_package.pid=###CURRENT_PID### AND tx_ecompc_domain_model_package.sys_language_uid IN (-1,0)',
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
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_package.backend_label',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim',
				'placeholder' => '__row|frontend_label',
				'mode' => 'useOrOverridePlaceholder'
			),
		),
		'frontend_label' => array(
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_package.frontend_label',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'prompt' => array(
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_package.prompt',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'hint_text' => array(
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_package.hint_text',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'softref' => 'rtehtmlarea_images,typolink_tag,email[subst],url',
				'wizards' => array(
					'RTE' => array(
						'icon' => 'wizard_rte2.gif',
						'notNewRecords'=> 1,
						'RTEonly' => 1,
						'module' => array(
							'name' => 'wizard_rte'
						),
						'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
						'type' => 'script'
					)
				)
			),
		),
		'image' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_package.image',
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
							'localizeChildrenAtParentLocalization' => 0
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
		'visible_in_frontend' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_package.visible_in_frontend',
			'config' => array(
				'type' => 'check',
				'default' => 1
			)
		),
		'multiple_select' => array(
			'displayCond' => 'FIELD:percent_pricing:REQ:false',
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_package.multiple_select',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'percent_pricing' => array(
			'displayCond' => 'FIELD:multiple_select:REQ:false',
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_package.percent_pricing',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'default_option' => array(
			'displayCond' => 'FIELD:visible_in_frontend:REQ:false',
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_package.default_option',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_ecompc_domain_model_option',
				'foreign_table_where' => 'AND tx_ecompc_domain_model_option.pid=###CURRENT_PID### AND NOT tx_ecompc_domain_model_option.deleted AND tx_ecompc_domain_model_option.sys_language_uid IN (-1,0) AND tx_ecompc_domain_model_option.configuration_package=###THIS_UID### ORDER BY tx_ecompc_domain_model_option.frontend_label, tx_ecompc_domain_model_option.backend_label',
				'items' => array(
					array($extTranslationPath . 'select.prompt', '')
				),
				'minitems' => 1,
				'maxitems' => 1,
			),
		),

	),
);
