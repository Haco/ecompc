<?php
if ( !defined ('TYPO3_MODE') ) {
	die ('Access denied.');
}

$extTranslationPath = 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:';

return array(
	'ctrl' => array(
		'title'	=> $extTranslationPath . 'tx_ecompc_domain_model_currency',
		'label' => 'label',
		'label_alt' => 'iso_4217',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'default_sortby' => 'ORDER BY label',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group'
		),
		'searchFields' => 'label, iso_4217, symbol, region, local_lang, exchange, flag,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('static_info_tables') . 'Resources/Public/Images/Icons/icon_static_currencies.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden, label, iso_4217, symbol, settings, region, local_lang, exchange, flag',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden;;1, label;;4, --palette--;' . $extTranslationPath . 'tx_ecompc_domain_model_currency.palettes.basic;2, --palette--;' . $extTranslationPath . 'tx_ecompc_domain_model_currency.palettes.region;3, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime, --linebreak--, fe_group'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
		'2' => array('showitem' => 'iso_4217, exchange, symbol', 'canNotCollapse' => 1),
		'3' => array('showitem' => 'region, local_lang, --linebreak--, flag', 'canNotCollapse' => 1),
		'4' => array('showitem' => 'settings')
	),
	'columns' => array(

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

		'label' => array(
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_currency.label',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim, required',
			),
		),
		'iso_4217' => array(
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_currency.iso_4217',
			'config' => array(
				'type' => 'input',
				'size' => 5,
				'max' => 3,
				'eval' => 'trim,nospace,upper,unique,alpha,required'
			),
		),
		'symbol' => array(
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_currency.symbol',
			'config' => array(
				'type' => 'input',
				'size' => 7,
				'eval' => 'trim,nospace'
			),
		),
		'region' => array(
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_currency.region',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'local_lang' => array(
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_currency.local_lang',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,nospace'
			)
		),
		'exchange' => array(
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_currency.exchange',
			'config' => array(
				'type' => 'input',
				'size' => 5,
				'eval' => 'double2',
				'default' => '0.00'
			)
		),
		'flag' => array(
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_currency.flag',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
					'flag',
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
		'settings' => array(
			'exclude' => 1,
			'label' => $extTranslationPath . 'tx_ecompc_domain_model_currency.settings',
			'config' => array(
				'type' => 'check',
				'form_type' => 'user',
				'userFunc' => 'S3b0\\Ecompc\\User\\TCAMod\\ModifyTCA->userFuncTxEcompcCurrencySettings',
				'items' => array(
					array($extTranslationPath . 'tx_ecompc_domain_model_currency.currency_default'),
					array($extTranslationPath . 'tx_ecompc_domain_model_currency.currency_prepend_symbol'),
					array($extTranslationPath . 'tx_ecompc_domain_model_currency.currency_separate'),
					array($extTranslationPath . 'tx_ecompc_domain_model_currency.currency_format_us')
				),
				'default' => 4
			),
		)

	),
);
