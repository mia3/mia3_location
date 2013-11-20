<?php
namespace Famelo\FameloLocation\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Marc Neuhaus <apocalip@gmail.com>, Famelo OHG
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
	 * @var \Famelo\FameloLocation\Domain\Repository\LocationRepository
	 */
	protected $locationRepository;

	/**
	 * @var \SJBR\StaticInfoTables\Domain\Repository\CountryRepository
	 */
	protected $countryRepository;

	/**
	 * @param \Famelo\FameloLocation\Domain\Repository\LocationRepository $locationRepository
	 */
	public function injectLocationRepository(\Famelo\FameloLocation\Domain\Repository\LocationRepository $locationRepository) {
		$this->locationRepository = $locationRepository;
	}

	/**
	 * @param \SJBR\StaticInfoTables\Domain\Repository\CountryRepository $countryRepository
	 */
	public function injectCountryRepository(\SJBR\StaticInfoTables\Domain\Repository\CountryRepository $countryRepository) {
		$this->countryRepository = $countryRepository;
	}

	/**
	 * action list
	 *
	 * @param string $address
	 * @param integer $radius
	 * @param string $country
	 * @return void
	 */
	public function listAction($address = NULL, $radius = NULL, $country = NULL) {
		if ($radius === NULL) {
			$radius = $this->settings['defaultRadius'];
		}
		if ($country === NULL) {
			$country = $this->getCountryFromIp();
		}
		$this->view->assign('country', $country);
		$this->view->assign('address', $address);
		$locations = array();
		$this->view->assign('mapLatitude', $this->settings['defaultMapLatitude']);
		$this->view->assign('mapLongitude', $this->settings['defaultMapLongitude']);
		if ($address === NULL) {
			if ($this->settings['showAll'] == 1) {
				$locations = $this->locationRepository->findAll();
			}
		} elseif (preg_match('/^[0-9]*$/', $address) && strlen($address) < 5) {
			$this->flashMessageContainer->add('Bitte geben sie eine Vollständige Postleitzahl ein', NULL, \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING);
		} else {
			$address .= ',' . $country;
			$apiURL = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&language=de';
			$addressData = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl($apiURL);
			$adr = json_decode($addressData);
			$coordinates = $adr->results[0]->geometry->location;
			if ($coordinates !== NULL) {
				$latitude = $coordinates->lat;
				$longitude = $coordinates->lng;

				$locations = $this->locationRepository->findNearBy($latitude, $longitude, $radius);
				$this->view->assign('mapLatitude', $latitude);
				$this->view->assign('mapLongitude', $longitude);
			}
		}
		$this->view->assign('locations', $locations);
		$this->view->assign('radius', $radius);

		$countries = array();
		foreach ($this->countryRepository->findAll() as $country) {
			$countries[$country->getShortNameEn()] = $country->getNameLocalized();
		}
		$this->view->assign('countries', $countries);

		// $this->importAction(PATH_typo3 . '../fileadmin/pikeur_locations.csv');
	}

	public function importAction($filename) {
		$contents = file_get_contents($filename);
		$rows = explode("\r", $contents);

		$GLOBALS['TYPO3_DB']->exec_DELETEquery(
   			'tx_famelolocation_domain_model_location',
   			'pid=79'
 		);

		for ($i=1; $i < count($rows); $i++) {
			$row = $rows[$i];
			$row = str_getcsv($row, ';');
			$location = new \Famelo\FameloLocation\Domain\Model\Location();
			$location->setPid(79);
			$location->setName($row[0]);
			$location->setContact($row[3]);
			$location->setStreet($row[1]);
			$location->setZip($row[5]);
			$location->setCity($row[4]);
			$location->setCountry($row[6]);
			$location->setPhone($row[7]);
			$location->setFax($row[7]);
			$location->setUrl($row[11]);

			$address = implode(',', array(
				$location->getStreet(),
				$location->getZip(),
				$location->getCity(),
				$location->getCountry()
			));
			$coordinates = $this->getCoordinates($address);
			if ($coordinates !== NULL) {
				if (!empty($fieldArray['latitude']) && $fieldArray['latitude'] !== $row['latitude']) {

				} else {
					$location->setLatitude($coordinates->lat);
				}
				if (!empty($fieldArray['longitude']) && $fieldArray['longitude'] !== $row['longitude']) {

				} else {
					$location->setLongitude($coordinates->lng);
				}
			}
			$this->locationRepository->add($location);
		}
	}

	public function getCoordinates($address) {
		$tmpDir = PATH_site . 'typo3temp/coordinates/';
		if (!is_dir($tmpDir)) {
			mkdir($tmpDir);
		}
		$tmpName = $tmpDir . sha1($address) . '.txt';
		if (!file_exists($tmpName)) {
			$apiURL = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&language=de';
			$addressData = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl($apiURL);
			file_put_contents($tmpName, $addressData);
		} else {
			$addressData = file_get_contents($tmpName);
		}
		$adr = json_decode($addressData);
		return $adr->results[0]->geometry->location;
	}

	/**
	 * action show
	 *
	 * @param \Famelo\FameloLocation\Domain\Model\Location $location
	 * @return void
	 */
	public function showAction(\Famelo\FameloLocation\Domain\Model\Location $location) {
		$this->view->assign('location', $location);
	}

	/**
	 * action search
	 *
	 * @param string $address
	 * @param integer $radius
	 * @return void
	 */
	public function searchAction($address = NULL, $radius = NULL) {
		if (isset($_REQUEST['tx_famelolocation_locations']['address'])) {
			$address = $_REQUEST['tx_famelolocation_locations']['address'];
		}
		if (isset($_REQUEST['tx_famelolocation_locations']['radius'])) {
			$radius = $_REQUEST['tx_famelolocation_locations']['radius'];
		}
		if ($radius === NULL) {
			$radius = $this->settings['defaultRadius'];
		}
		$this->view->assign('address', $address);
		$this->view->assign('radius', $radius);
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

}
?>