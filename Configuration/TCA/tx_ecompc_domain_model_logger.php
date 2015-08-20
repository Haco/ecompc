<?php
if ( !defined ('TYPO3_MODE') ) {
	die ('Access denied.');
}

$translate = 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:';

return [
	'ctrl' => [
		'title'	=> 'ecom Configurator Log',
		'label' => 'tstamp',
		'label_alt' => 'configuration_code',
		'label_alt_force' => TRUE,
		'default_sortby' => 'ORDER BY tstamp',
		'tstamp' => 'tstamp',
		'dividers2tabs' => TRUE,
		'rootLevel' => 1,
		'readOnly' => TRUE,
		//'hideTable' => 1,
		'searchFields' => 'ses_id, tstamp, configuration_code, selected_configuration, configuration, currency, price, ip_address, fe_user',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('belog') . 'ext_icon.gif'
	],
	'interface' => [
		'showRecordFieldList' => 'ses_id, tstamp, configuration_code, selected_configuration, configuration, currency, price, ip_address, fe_user'
	],
	'types' => [
		'1' => [ 'showitem' => 'ses_id, tstamp, configuration_code, selected_configuration, configuration, currency, price, ip_address, fe_user' ]
	],
	'palettes' => [
		'1' => [ 'showitem' => '' ]
	],
	'columns' => [
		'ses_id' => [
			'exclude' => 1,
			'label' => 'Session-ID',
			'config' => [
				'type' => 'input',
				'size' => 41,
				'eval' => 'trim,nospace,required'
			]
		],
		'tstamp' => [
			'exclude' => 1,
			'label' => 'DateTime',
			'config' => [
				'type' => 'input',
				'size' => 10,
				'eval' => 'trim,datetime',
				'default' => time()
			]
		],
		'configuration_code' => [
			'exclude' => 1,
			'label' => 'Configuration Code // SKU',
			'config' => [
				'type' => 'input',
				'size' => 41,
				'eval' => 'trim,nospace,required'
			]
		],
		'selected_configuration' => [
			'exclude' => 1,
			'label' => 'Selected Configuration (serialized array)',
			'config' => [
				'type' => 'text'
			]
		],
		'configuration' => [
			'exclude' => 1,
			'label' => 'Configuration',
			'config' => [
				'type' => 'select',
				'foreign_table' => 'tx_ecompc_domain_model_configuration',
				'foreign_table_where' => 'AND NOT tx_ecompc_domain_model_configuration.deleted AND tx_ecompc_domain_model_configuration.sys_language_uid IN (-1,0) ORDER BY tx_ecompc_domain_model_configuration.frontend_label'
			]
		],
		'currency' => [
			'exclude' => 1,
			'label' => 'Currency',
			'config' => [
				'type' => 'select',
				'foreign_table' => 'tx_ecompc_domain_model_currency',
				'foreign_table_where' => ' ORDER BY tx_ecompc_domain_model_currency.label'
			]
		],
		'price' => [
			'exclude' => 1,
			'label' => 'Price',
			'config' => [
				'type' => 'input',
				'eval' => 'trim,double2'
			]
		],
		'ip_address' => [
			'exclude' => 1,
			'label' => 'IP Address',
			'config' => [
				'type' => 'input',
				'max' => 15,
				'eval' => 'trim,nospace,is_in',
				'is_in' => '0123456789.*'
			]
		],
		'fe_user' => [
			'exclude' => 1,
			'label' => 'Frontend User',
			'config' => [
				'type' => 'select',
				'foreign_table' => 'fe_users',
				'foreign_table_where' => 'AND NOT fe_users.disable ORDER BY fe_users.username',
				'items' => [
					[ '', 0 ]
				]
			]
		]
	]
];
