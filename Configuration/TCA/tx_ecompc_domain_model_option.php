<?php
if ( !defined ('TYPO3_MODE') ) {
	die ('Access denied.');
}

$translate = 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:';

return [
	'ctrl' => [
		'title'	=> $translate . 'tx_ecompc_domain_model_option',
		'label' => 'backend_label',
		'label_alt' => 'configuration_code_segment,frontend_label,configuration_package',
		'label_userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->labelUserFuncTxEcompcDomainModelOption',
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
		'useColumnsForDefaultValues' => 'configuration_package',
		'enablecolumns' => [
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group'
		],
		'searchFields' => 'backend_label,frontend_label,configuration_code_segment,image,hint_text,pricing,price_percental,configuration_package,dependency,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ecompc') . 'Resources/Public/Icons/tx_ecompc_domain_model_option.png'
	],
	'interface' => [
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, backend_label, frontend_label, configuration_code_segment, image, hint_text, pricing, price_percental, configuration_package, dependency'
	],
	'types' => [
		'1' => [ 'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, frontend_label;;2, configuration_package, configuration_code_segment, --div--;' . $translate . 'tabs.referral, image, dependency, --div--;' . $translate . 'tabs.pricing, pricing, price_percental, --div--;LLL:EXT:cms/locallang_tca.xlf:pages.tabs.extended, hint_text;;;richtext:rte_transform[mode=ts_links], --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime, --linebreak--, fe_group' ]
	],
	'palettes' => [
		'1' => [ 'showitem' => '' ],
		'2' => [ 'showitem' => 'backend_label' ]
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
				'foreign_table' => 'tx_ecompc_domain_model_option',
				'foreign_table_where' => 'AND tx_ecompc_domain_model_option.pid=###CURRENT_PID### AND tx_ecompc_domain_model_option.sys_language_uid IN (-1,0)'
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
			'label' => $translate . 'tx_ecompc_domain_model_option.backend_label',
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
			'label' => $translate . 'tx_ecompc_domain_model_option.frontend_label',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			]
		],
		'configuration_code_segment' => [
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_option.configuration_code_segment',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			]
		],
		'image' => [
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_option.image',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
					'image',
					[
						'maxitems' => 1,
						'appearance' => [
							'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
							'enabledControls' => [
								'localize' => 0
							]
						],
						'behaviour' => [
							'localizeChildrenAtParentLocalization' => FALSE
						],
						'foreign_types' => [
							\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
								'showitem' => '--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,--palette--;;filePalette'
							]
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
		'hint_text' => [
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_option.hint_text',
			'config' => [
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'wizards' => [
					'RTE' => [
						'icon' => 'wizard_rte2.gif',
						'notNewRecords'=> 1,
						'RTEonly' => 1,
						'script' => 'wizard_rte.php',
						'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
						'type' => 'script'
					]
				]
			]
		],
		'price_percental' => [
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_option.price_percental',
			'config' => [
				'type' => 'input',
				'form_type' => 'user',
				'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTxEcompcDomainModelOptionPricePercental',
				'size' => 22,
				'range' => [
					'lower' => 0,
					'upper' => 100
				],
				'eval' => 'double2'
			]
		],
		'pricing' => [
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_option.pricing',
			'config' => [
				'type' => 'flex',
				'form_type' => 'user',
				'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTxEcompcOptionPricing',
				'ds' => [
					'default' => 'FILE:EXT:ecompc/Configuration/FlexForms/price_list.xml'
				]
			]
		],
		'configuration_package' => [
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_option.configuration_package',
			'config' => [
				'type' => 'select',
				'foreign_table' => 'tx_ecompc_domain_model_package',
				'foreign_table_where' => 'AND tx_ecompc_domain_model_package.pid=###CURRENT_PID### AND NOT tx_ecompc_domain_model_package.deleted AND tx_ecompc_domain_model_package.sys_language_uid IN (-1,0) ORDER BY tx_ecompc_domain_model_package.backend_label, tx_ecompc_domain_model_package.frontend_label',
				'items' => [
					[ $translate . 'select.prompt', '' ]
				],
				'minitems' => 1,
				'maxitems' => 1
			]
		],
		'dependency' => [
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_option.dependency',
			'config' => [
				'type' => 'inline',
				'foreign_table' => 'tx_ecompc_domain_model_dependency',
				'foreign_field' => 'ref_option',
				'maxitems' => 1,
				'appearance' => [
					'collapseAll' => 0,
					'newRecordLinkAddTitle' => 0,
					'newRecordLinkTitle' => $translate . 'tx_ecompc_domain_model_option.dependency.inlineElementAddTitle',
					'levelLinksPosition' => 'bottom'
				],
				'behaviour' => [
					'localizationMode' => 'keep',
					'localizeChildrenAtParentLocalization' => 0,
					'disableMovingChildrenWithParent' => 0,
					'enableCascadingDelete' => 1
				]
			]
		]
	]
];
