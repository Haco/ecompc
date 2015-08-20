<?php
if ( !defined ('TYPO3_MODE') ) {
	die ('Access denied.');
}

$translate = 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:';

return [
	'ctrl' => [
		'title'	=> $translate . 'tx_ecompc_domain_model_package',
		'label' => 'backend_label',
		'label_alt' => 'frontend_label',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'sortby' => 'sorting',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'requestUpdate' => 'frontend_label, visible_in_frontend',
		'type' => 'visible_in_frontend',
		'typeicon_column' => 'visible_in_frontend',
		'typeicon_classes' => [
			'default' => 'extensions-ecompc-package-default',
			'0' => 'extensions-ecompc-package-not-visible-fe',
			'1' => 'extensions-ecompc-package-default'
		],
		'enablecolumns' => [
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group'
		],
		'searchFields' => 'backend_label,frontend_label,prompt,hint_text,icon,visible_in_frontend,visibility,multiple_select,default_option,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ecompc') . 'Resources/Public/Icons/tx_ecompc_domain_model_package.png'
	],
	'interface' => [
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, backend_label, frontend_label, prompt, hint_text, icon, sorting_in_code, visible_in_frontend, visibility, multiple_select, default_option'
	],
	'types' => [
		'0' => [ 'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, frontend_label;;4, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.appearance, --palette--;;3, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime, --linebreak--, fe_group' ],
		'1' => [ 'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, frontend_label;;4, prompt, hint_text;;;wizards[t3editorHtml], --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.appearance, icon, --palette--;;2, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime, --linebreak--, fe_group' ]
	],
	'palettes' => [
		'1' => [ 'showitem' => '' ],
		'2' => [ 'showitem' => 'sorting_in_code, --linebreak--, visible_in_frontend, visibility, multiple_select, percent_pricing', 'canNotCollapse' => 1 ],
		'3' => [ 'showitem' => 'default_option, sorting_in_code, --linebreak--, visible_in_frontend, visibility, percent_pricing', 'canNotCollapse' => 1 ],
		'4' => [ 'showitem' => 'backend_label' ]
	],
	'columns' => [

		'sys_language_uid' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => [
					[ 'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1 ],
					[ 'LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0 ]
				]
			]
		],
		'l10n_parent' => [
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => [
				'type' => 'select',
				'items' => [
					[ '', 0 ],
				],
				'foreign_table' => 'tx_ecompc_domain_model_package',
				'foreign_table_where' => 'AND tx_ecompc_domain_model_package.pid=###CURRENT_PID### AND tx_ecompc_domain_model_package.sys_language_uid IN (-1,0)'
			]
		],
		'l10n_diffsource' => [
			'config' => [
				'type' => 'passthrough'
			]
		],
		'sorting' => [
			'l10n_mode' => 'exclude',
			'config' => [
				'type' => 'passthrough'
			]
		],

		't3ver_label' => [
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'max' => 255
			]
		],

		'hidden' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check'
			]
		],
		'starttime' => [
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => [
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => [
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				]
			]
		],
		'endtime' => [
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => [
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				]
			]
		],
		'fe_group' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.fe_group',
			'config' => [
				'type' => 'select',
				'size' => 7,
				'maxitems' => 20,
				'items' => [
					[
						'LLL:EXT:lang/locallang_general.xlf:LGL.hide_at_login',
						-1
					],
					[
						'LLL:EXT:lang/locallang_general.xlf:LGL.any_login',
						-2
					],
					[
						'LLL:EXT:lang/locallang_general.xlf:LGL.usergroups',
						'--div--'
					]
				],
				'exclusiveKeys' => '-1,-2',
				'foreign_table' => 'fe_groups',
				'foreign_table_where' => 'ORDER BY fe_groups.title'
			]
		],

		'backend_label' => [
			'l10n_mode' => 'exclude',
			'displayCond' => 'FIELD:frontend_label:REQ:true',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_package.backend_label',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim',
				'placeholder' => '__row|frontend_label',
				'mode' => 'useOrOverridePlaceholder'
			]
		],
		'frontend_label' => [
			'l10n_mode' => 'prefixLangTitle',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_package.frontend_label',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			]
		],
		'prompt' => [
			'l10n_mode' => 'prefixLangTitle',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_package.prompt',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			]
		],
		'hint_text' => [
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_package.hint_text',
			'config' => [
				'type' => 'text',
				'cols' => 100,
				'rows' => 10,
				'eval' => 'trim',
				'wizards' => [
					't3editorHtml' => [
						'enableByTypeConfig' => 1,
						'type' => 'userFunc',
						'userFunc' => 'TYPO3\\CMS\\T3editor\\FormWizard->main',
						'params' => [
							'format' => 'html'
						]
					]
				]
			]
		],
		'icon' => [
			'displayCond' => 'FIELD:visible_in_frontend:REQ:TRUE',
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_package.icon',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
					'icon',
					[
						'maxitems' => 1,
						'appearance' => [
							'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
							'enabledControls' => [
								'localize' => 0
							]
						],
						'behaviour' => [
							'localizeChildrenAtParentLocalization' => 0
						],
						'foreign_types' => [
							\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
								'showitem' => '--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,--palette--;;filePalette'
							],
						],
						'filter' => [
							'0' => [
								'parameters' => [
									'allowedFileExtensions' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
								]
							]
						]
					],
					$GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
				)
		],
		'sorting_in_code' => [
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_package.sorting_in_code',
			'config' => [
				'type' => 'input',
				'size' => 5,
				'eval' => 'trim,int',
				'range' => [
					'lower' => 0,
					'upper' => 999
				],
				'default' => 0,
				'wizards' => [
					'angle' => [
						'type' => 'slider',
						'step' => 1,
						'width' => 999
					]
				]
			]
		],
		'visible_in_frontend' => [
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_package.visible_in_frontend',
			'config' => [
				'type' => 'check',
				'default' => 1
			]
		],
		'visibility' => [
			'displayCond' => 'FIELD:visible_in_frontend:REQ:false',
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_package.visibility',
			'config' => [
				'type' => 'check',
				'items' => [
					[ $translate . 'tx_ecompc_domain_model_package.visible_in_summary' ],
					[ $translate . 'tx_ecompc_domain_model_package.visible_in_navigation' ]
				],
				'default' => 3
			]
		],
		'multiple_select' => [
			'l10n_mode' => 'exclude',
			'displayCond' => 'FIELD:percent_pricing:REQ:false',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_package.multiple_select',
			'config' => [
				'type' => 'check',
				'default' => 0
			]
		],
		'percent_pricing' => [
			'displayCond' => 'FIELD:multiple_select:REQ:false',
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_package.percent_pricing',
			'config' => [
				'type' => 'check',
				'default' => 0
			]
		],
		'default_option' => [
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_package.default_option',
			'config' => [
				'type' => 'select',
				'foreign_table' => 'tx_ecompc_domain_model_option',
				'foreign_table_where' => 'AND tx_ecompc_domain_model_option.pid=###CURRENT_PID### AND NOT tx_ecompc_domain_model_option.deleted AND tx_ecompc_domain_model_option.sys_language_uid IN (-1,0) AND tx_ecompc_domain_model_option.configuration_package IN (###THIS_UID###,###REC_FIELD_l10n_parent###) ORDER BY tx_ecompc_domain_model_option.frontend_label, tx_ecompc_domain_model_option.backend_label',
				'items' => [
					[ $translate . 'select.prompt', '' ]
				],
				'minitems' => 1,
				'maxitems' => 1
			]
		]
	]
];
