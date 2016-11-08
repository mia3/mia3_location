<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}


if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Mia3.' . $_EXTKEY,
        'tools',          // Main area
        'txmia3locationsmod1',         // Name of the module
        '',             // Position of the module
        array(          // Allowed controller action combinations
            'Backend' => 'index,import'
        ),
        array(          // Additional configuration
            'access'    => 'admin',
            'icon'      => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/marker.svg',
            'labels'    => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xml',
        )
    );
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Locations',
	'MIA3 - Location'
);

$pluginName = 'mia3location_locations';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginName] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginName, 'FILE:EXT:mia3_location/Configuration/FlexForms/Locations.xml');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'MIA3 Locations');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_mia3location_domain_model_location', 'EXT:mia3_location/Resources/Private/Language/locallang_csh_tx_mia3location_domain_model_location.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mia3location_domain_model_location');

$TCA['tx_mia3location_domain_model_location'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:mia3_location/Resources/Private/Language/locallang_db.xlf:tx_mia3location_domain_model_location',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
        'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',

		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'name,description,street,zip,city,phone,fax,url,email,latitude,longitude,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Location.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/marker-stroked.png'
	),
);
