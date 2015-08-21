<?php
if ( !defined ('TYPO3_MODE') ) {
	die ('Access denied.');
}

$translate = 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:';

return [
	'ctrl' => [
		'title'	=> $translate . 'tx_ecompc_domain_model_dependency_note',
		'label' => 'note',
		'label_alt' => 'ref_package',
		/*'label_alt_force' => TRUE,*/
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'hideTable' => 1,
		'delete' => 'deleted',
		'enablecolumns' => [
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime'
		],
		'searchFields' => 'mode,options,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ecompc') . 'Resources/Public/Icons/tx_ecompc_domain_model_dependency_note.png'
	],
	'interface' => [
		'showRecordFieldList' => 'hidden, mode, options'
	],
	'types' => [
		'1' => [ 'showitem' => 'hidden;;1;;1-1-1, note;;2, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime' ]
	],
	'palettes' => [
		'1' => [ 'showitem' => '' ],
		'2' => [ 'showitem' => 'dependent_options', 'canNotCollapse' => 1 ]
	],
	'columns' => [

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

		'note' => [
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_dependency_note.note',
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
		'dependent_options' => [
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => $translate . 'tx_ecompc_domain_model_dependency_note.dependent_options',
			'config' => [
				'type' => 'select',
				'foreign_table' => 'tx_ecompc_domain_model_option',
				'foreign_table_where' => ' AND tx_ecompc_domain_model_option.pid=###REC_FIELD_pid### AND NOT tx_ecompc_domain_model_option.deleted AND tx_ecompc_domain_model_option.sys_language_uid IN (-1,0) ORDER BY tx_ecompc_domain_model_option.configuration_package, tx_ecompc_domain_model_option.backend_label, tx_ecompc_domain_model_option.frontend_label',
				'MM' => 'tx_ecompc_dependencynote_package_mm',
				'itemsProcFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->itemsProcFuncTxEcompcDomainModelDependencyNoteDependentOptions',
				'size' => 10,
				'autoSizeMax' => 30,
				'minitems' => 1,
				'maxitems' => 9999,
				'multiple' => 0,
				'renderMode' => 'checkbox',
				'disableNoMatchingValueElement' => 1
			]
		],
		'ref_option' => [
			'config' => [
				'type' => 'select',
				'foreign_table' => 'tx_ecompc_domain_model_option'
			]
		]
	]
];
