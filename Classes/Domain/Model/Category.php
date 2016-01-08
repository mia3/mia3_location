<?php
namespace Mia3\Mia3Location\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Marc Neuhaus <apocalip@gmail.com>, Famelo OHG
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
 * Category Model
 *
 * @package TYPO3
 * @subpackage famelo_location
 */
class Category extends \TYPO3\CMS\Extbase\Domain\Model\Category {

	/**
	 * @var string
	 */
	protected $title = NULL;

	/**
	 * @var integer
	 */
	protected $sorting = NULL;

	/**
	 * temporary property to store location results in categories
	 *
	 * @var array
	 */
	public $locations = array();

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	protected $locationMarker = NULL;

	public function __toString() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the locationMarker
	 *
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $locationMarker
	 */
	public function setLocationMarker($locationMarker) {
		$this->locationMarker = $locationMarker;
	}

	/**
	 * Returns the locationMarker
	 *
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	public function getLocationMarker() {
		return $this->locationMarker;
	}

	/**
	 * @param integer $sorting
	 */
	public function setSorting($sorting) {
		$this->sorting = $sorting;
	}

	/**
	 * @return integer
	 */
	public function getSorting() {
		return $this->sorting;
	}

}
