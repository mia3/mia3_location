<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_mia3location_domain_model_location'] = array(
	'ctrl' => $TCA['tx_mia3location_domain_model_location']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, additional, description, contact, street, zip, city, country, phone, fax, url, email, categories, images, latitude, longitude,external_id,sorting',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, name, additional, description, contact, street, zip, city, country, phone, fax, url, email, categories, images, latitude, longitude,external_id,--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,starttime, endtime, sorting'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(

		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
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
				'foreign_table' => 'tx_mia3location_domain_model_location',
				'foreign_table_where' => 'AND tx_mia3location_domain_model_location.pid=###CURRENT_PID### AND tx_mia3location_domain_model_location.sys_language_uid IN (-1,0)',
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
		'name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'additional' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.additional',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			),
			'defaultExtras' => 'richtext:rte_transform[flag=rte_enabled|mode=ts]',
		),
		'contact' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.contact',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'street' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.street',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'zip' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.zip',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'city' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.city',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'region' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.region',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'country' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.country',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'phone' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.phone',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'fax' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.fax',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'url' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.url',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'email' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.email',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'latitude' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.latitude',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'longitude' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.longitude',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'categories' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.categories',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectTree',
				'MM' => 'sys_category_record_mm',
				'MM_match_fields' => array(
					'fieldname' => 'categories',
					'tablenames' => 'tx_mia3location_domain_model_location',
				),
                'treeConfig' => array(
					'appearance' => array(
						'expandAll' => 1,
						'showHeader' => 1
					),
					'parentField' => 'parent'
				),
				'MM_opposite_field' => 'items',
				'foreign_table' => 'sys_category',
				'foreign_table_where' => ' AND (sys_category.sys_language_uid = 0 OR sys_category.l10n_parent = 0) ORDER BY sys_category.sorting',
				'size' => 10,
				'autoSizeMax' => 20,
				'minitems' => 0,
				'maxitems' => 20,
			)
		),
		'images' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.images',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
				'images',
				array(
					'maxitems' => 10
				),
				$GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
			),
		),
		'external_id' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location.external_id',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'sorting' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
	),
);

?>
