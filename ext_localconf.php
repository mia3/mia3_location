<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT:source="FILE:EXT:mia3_location/Configuration/TsConfig/PageTsConfig.ts">');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Mia3.' . $_EXTKEY,
	'Locations',
	array(
		'Location' => 'list, show, map, teaser, ajaxSearch',

	),
	// non-cacheable actions
	array(
		'Location' => 'list, show, ajaxSearch',

	)
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:mia3_location/Classes/Hooks/TCEFetchCoordinates.php:Mia3\Mia3Location\Hooks\TCEFetchCoordinates';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Mia3\Mia3Location\Command\LocationCommandController';

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['location'] =
		'Mia3\Mia3Location\Hooks\RealUrlAutoConfiguration->addLocationConfig';
}
