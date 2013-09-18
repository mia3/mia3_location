<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Famelo.' . $_EXTKEY,
	'Locations',
	array(
		'Location' => 'list, show',

	),
	// non-cacheable actions
	array(
		'Location' => 'list, show',

	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Famelo.' . $_EXTKEY,
	'Locationsearch',
	array(
		'Location' => 'search',

	),
	// non-cacheable actions
	array(
		'Location' => 'search',

	)
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:famelo_location/Classes/Hooks/TCEFetchCoordinates.php:Famelo\FameloLocation\Hooks\TCEFetchCoordinates';
?>