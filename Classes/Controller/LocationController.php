<?php
namespace Mia3\Mia3Location\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Marc Neuhaus <apocalip@gmail.com>, Mia3 OHG
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class LocationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \Mia3\Mia3Location\Domain\Repository\LocationRepository
	 * @inject
	 */
	protected $locationRepository;

	/**
	 * @var \SJBR\StaticInfoTables\Domain\Repository\CountryRepository
	 * @inject
	 */
	protected $countryRepository;

	/**
	 * action list
	 *
	 * @param string $address
	 * @param integer $radius
	 * @param string $country
	 * @return void
	 */
	public function listAction($address = NULL, $radius = NULL, $country = NULL) {
		if (!isset($this->settings['defaultZoom'])) {
			return '<span class="error">you need to include the mia3_location typoscript template!</span>';
		}
		if ($radius === NULL) {
			$radius = $this->settings['defaultRadius'];
		}
		if ($country === NULL) {
			if ($this->settings['resolveCountryByIp']) {
				$country = $this->getCountryFromIp();
			} else {
				$country = $this->settings['defaultCountry'];
			}
		}
		$countryInformation = $this->getCountryIsoCode($country);
		$this->view->assign('country', $country);
		$this->view->assign('address', $address);
		$locations = array();
		$this->view->assign('defaultLatitude', $this->settings['defaultMapLatitude']);
		$this->view->assign('defaultLongitude', $this->settings['defaultMapLongitude']);
		$this->view->assign('searchLatitude', 0);
		$this->view->assign('searchLongitude', 0);

		if (strlen($this->settings['categories']) > 0) {
			$categories = GeneralUtility::trimExplode(',', $this->settings['categories'], TRUE);
		} else {
			$categories = array();
		}

		if (empty($address)) {
			if ($this->settings['showAll'] == 1) {
				$locations = $this->locationRepository->findAll();
			}
		} else if (preg_match('/[0-9]+/', $address) > 0 && class_exists('\Mia3\GeoDb\GeoDb')) {
			$result = \Mia3\GeoDb\GeoDb::findByPostalCode($address, strtoupper($countryInformation['cn_iso_2']));
			if (is_array($result)) {
				$latitude = $result['latitude'];
				$longitude = $result['longitude'];
				$locations = $this->locationRepository->findNearBy($address, $latitude, $longitude, $radius, explode(',', $this->settings['searchColumns']), $categories);
				if ($latitude !== NULL) {
					$this->view->assign('searchLatitude', $latitude);
					$this->view->assign('searchLongitude', $longitude);
				}
			}
		} else {
			$coordinates = $this->findCoordinates($address, $countryInformation);

			$latitude = NULL;
			$longitude = NULL;
			if ($coordinates !== NULL) {
				$latitude = $coordinates->lat;
				$longitude = $coordinates->lng;
			}

			$locations = $this->locationRepository->findNearBy($address, $latitude, $longitude, $radius, explode(',', $this->settings['searchColumns']), $categories);
			if ($latitude !== NULL) {
				$this->view->assign('searchLatitude', $latitude);
				$this->view->assign('searchLongitude', $longitude);
			}
		}

		if ($this->settings['groupByCategory']) {
			$this->groupByCategories($locations);
		}

		$this->view->assign('locations', $locations);
		$this->view->assign('radius', $radius);

		$countries = array();
		foreach ($this->countryRepository->findAll() as $country) {
			$countries[$country->getShortNameEn()] = $country->getNameLocalized();
		}
		$this->view->assign('countries', $countries);
	}

	/**
	 * action show
	 *
	 * @param \Mia3\Mia3Location\Domain\Model\Location $location
	 * @return void
	 */
	public function mapAction() {
		if ($radius === NULL) {
			$radius = $this->settings['defaultRadius'];
		}
		$this->view->assign('radius', $radius);
		$this->view->assign('mapLatitude', $this->settings['defaultMapLatitude']);
		$this->view->assign('mapLongitude', $this->settings['defaultMapLongitude']);
	}

	/**
	 * action show
	 *
	 * @param \Mia3\Mia3Location\Domain\Model\Location $location
	 * @return void
	 */
	public function showAction(\Mia3\Mia3Location\Domain\Model\Location $location) {
		$this->view->assign('location', $location);
		$this->view->assign('mapLatitude', $location->getLatitude());
		$this->view->assign('mapLongitude', $location->getLongitude());
	}

	/**
	 * action show
	 *
	 * @param \Mia3\Mia3Location\Domain\Model\Location $location
	 * @return void
	 */
	public function teaserAction() {
		if ($radius === NULL) {
			$radius = $this->settings['defaultRadius'];
		}
		$this->view->assign('radius', $radius);
		$this->view->assign('mapLatitude', $this->settings['defaultMapLatitude']);
		$this->view->assign('mapLongitude', $this->settings['defaultMapLongitude']);
	}

	/**
	 * action list
	 *
	 * @param string $longitude
	 * @param string $latitude
	 * @return void
	 */
	public function ajaxSearchAction($longitude = NULL, $latitude = NULL) {
		$radius = $this->settings['defaultRadius'];

		if (strlen($this->settings['categories']) > 0) {
			$categories = array_merge(
				GeneralUtility::trimExplode(',',
					CategoryService::getChildrenCategories($this->settings['categories'], 0, '', TRUE),
				TRUE),
				GeneralUtility::trimExplode(',', $this->settings['categories'], TRUE)
			);
		} else {
			$categories = array();
		}
		$locations = $this->locationRepository->findNearBy($address, $latitude, $longitude, $radius, explode(',', $this->settings['searchColumns']), $categories);

		$this->view->assign('mapLatitude', $latitude);
		$this->view->assign('mapLongitude', $longitude);
		$this->groupByCategories($locations);
		$this->view->assign('locations', $locations);

		echo $this->view->render();
		exit();
	}

	public function getCountryFromIp() {
		$ip = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR');

		$tmpDir = PATH_site . 'typo3temp/ip2country/';
		if (!is_dir($tmpDir)) {
			mkdir($tmpDir);
		}
		$tmpName = $tmpDir . sha1($ip) . '.txt';
		if (!file_exists($tmpName)) {
			$countryCode = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl('http://ipinfo.io/' . $ip .'/country');

			foreach ($this->countryRepository->findAll() as $country) {
				if (strtolower($country->getIsoCodeA2()) == trim(strtolower($countryCode))) {
					$country = $country->getShortNameEn();
					break;
				}
			}
			file_put_contents($tmpName, $country);
		} else {
			$country = file_get_contents($tmpName);
		}

		return $country;
	}

	public function groupByCategories($locations) {
		if ($this->settings['groupByCategory'] == 1) {
			$categories = array();
			foreach ($locations as $location) {
				if ($location === NULL) {
					continue;
				}
				$category = $location->getFirstCategory();
				if ($category !== NULL) {
					if(!isset($categories[$category->getSorting()])) {
						$categories[$category->getSorting()] = $category;
					}

					$categories[$category->getSorting()]->locations[] = $location;

					unset($locations[$location->getUid()]);
				}
			}

			ksort($categories);

			$this->view->assign('categories', $categories);
		}
	}

	public function getCountryIsoCode($countryName) {
		$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'static_countries', 'cn_short_local = "' . $countryName . '" OR cn_short_en = "' . $countryName . '"');
		if (!isset($row['cn_iso_2'])) {
			return array();
		}
		return $row;
	}

	public function findCoordinates($address, $country) {
		$apiURL = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address .  ',' . $country['cn_short_en']).'&sensor=false&language=de';

		$addressData = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl($apiURL);
		$body = json_decode($addressData);
		if (!isset($this->settings['limitSearchToCountries']) || empty($this->settings['limitSearchToCountries'])) {
			return $body->results[0]->geometry->location;
		}
		if (!is_array($body->results)) {
			return;
		}
		foreach ($body->results as $result) {
			$matches = FALSE;
			foreach ($result->address_components as $address_component) {
				if (!isset($address_component->types[0]) || $address_component->types[0] !== 'country') {
					continue;
				}

				if ($address_component->short_name == $country['cn_iso_2']) {
					$matches = TRUE;
					break;
				}
			}
			if ($matches === TRUE) {
				return $result->geometry->location;
			}
		}
	}
}
?>
