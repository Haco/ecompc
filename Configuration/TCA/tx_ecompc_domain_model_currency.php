<?php
if ( !defined ('TYPO3_MODE') ) {
	die ('Access denied.');
}

$translate = 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:';

return [
	'ctrl' => [
		'title'	=> $translate . 'tx_ecompc_domain_model_currency',
		'label' => 'label',
		'label_alt' => 'iso_4217',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'enablecolumns' => [
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group'
		],
		'searchFields' => 'label, iso_4217, symbol, region, local_lang, exchange, flag,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('static_info_tables') ? \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('static_info_tables') . 'Resources/Public/Images/Icons/icon_static_currencies.gif' : ''
	],
	'interface' => [
		'showRecordFieldList' => 'hidden, label, iso_4217, symbol, settings, region, local_lang, exchange, flag'
	],
	'types' => [
		'1' => [ 'showitem' => 'hidden;;1, label;;4, --palette--;' . $translate . 'tx_ecompc_domain_model_currency.palettes.basic;2, --palette--;' . $translate . 'tx_ecompc_domain_model_currency.palettes.region;3, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime, --linebreak--, fe_group' ]
	],
	'palettes' => [
		'1' => [ 'showitem' => '' ],
		'2' => [ 'showitem' => 'iso_4217, exchange, symbol', 'canNotCollapse' => 1 ],
		'3' => [ 'showitem' => 'region, local_lang, --linebreak--, flag', 'canNotCollapse' => 1 ],
		'4' => [ 'showitem' => 'settings' ]
	],
	'columns' => [

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

		'label' => [
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_currency.label',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim, required'
			]
		],
		'iso_4217' => [
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_currency.iso_4217',
			'config' => [
				'type' => 'input',
				'size' => 5,
				'max' => 3,
				'eval' => 'trim,nospace,upper,unique,alpha,required'
			]
		],
		'symbol' => [
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_currency.symbol',
			'config' => [
				'type' => 'input',
				'size' => 7,
				'eval' => 'trim,nospace'
			]
		],
		'region' => [
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_currency.region',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			]
		],
		'local_lang' => [
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_currency.local_lang',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,nospace'
			]
		],
		'exchange' => [
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_currency.exchange',
			'config' => [
				'type' => 'input',
				'size' => 5,
				'eval' => 'double2',
				'default' => '0.00'
			]
		],
		'flag' => [
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_currency.flag',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
					'flag',
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
		'settings' => [
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_currency.settings',
			'config' => [
				'type' => 'check',
				'form_type' => 'user',
				'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTxEcompcCurrencySettings',
				'items' => [
					[ $translate . 'tx_ecompc_domain_model_currency.currency_default' ],
					[ $translate . 'tx_ecompc_domain_model_currency.currency_prepend_symbol' ],
					[ $translate . 'tx_ecompc_domain_model_currency.currency_separate' ],
					[ $translate . 'tx_ecompc_domain_model_currency.currency_format_us' ]
				],
				'default' => 4
			]
		]
	]
];
