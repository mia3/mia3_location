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
	 * @param \Famelo\FameloLocation\Domain\Repository\LocationRepository $locationRepository
	 */
	public function injectPageService(\Famelo\FameloLocation\Domain\Repository\LocationRepository $locationRepository) {
		$this->locationRepository = $locationRepository;
	}

	/**
	 * action list
	 *
	 * @param string $address
	 * @param integer $radius
	 * @return void
	 */
	public function listAction($address = NULL, $radius = NULL) {
		if ($radius === NULL) {
			$radius = $this->settings['defaultRadius'];
		}
		$this->view->assign('address', $address);
		if ($address === NULL) {
			$locations = $this->locationRepository->findAll();
		} else {
			if (!empty($this->settings['defaultCountry'])) {
				 $address .= ',Deutschland';
			}
			$locations = $this->locationRepository->findNearBy($address, $radius);
		}
		$this->view->assign('locations', $locations);
		$this->view->assign('radius', $radius);
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

}
?>