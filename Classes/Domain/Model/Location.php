<?php
namespace Mia3\Mia3Location\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\Category;
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
 * Location
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Location extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * description
	 *
	 * @var \string
	 */
	protected $additional = NULL;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Mia3\Mia3Location\Domain\Model\Category>
	 * @lazy
	 */
	protected $categories = NULL;

	/**
	 * name
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * description
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * description
	 *
	 * @var string
	 */
	protected $contact = NULL;

	/**
	 * street
	 *
	 * @var string
	 */
	protected $street = '';

	/**
	 * zip
	 *
	 * @var string
	 */
	protected $zip = '';

	/**
	 * city
	 *
	 * @var string
	 */
	protected $city = '';

	/**
	 * country
	 *
	 * @var string
	 */
	protected $country = NULL;

	/**
	 * phone
	 *
	 * @var string
	 */
	protected $phone = '';

	/**
	 * fax
	 *
	 * @var string
	 */
	protected $fax = '';

	/**
	 * url
	 *
	 * @var string
	 */
	protected $url = '';

	/**
	 * email
	 *
	 * @var string
	 */
	protected $email = '';

	/**
	 * latitude
	 *
	 * @var string
	 */
	protected $latitude = '';

	/**
	 * longitude
	 *
	 * @var string
	 */
	protected $longitude = '';

	/**
	 * Returns the name
	 *
	 * @return \string $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the name
	 *
	 * @param \string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param string $additional
	 */
	public function setAdditional($additional) {
		$this->additional = $additional;
	}

	/**
	 * @return string
	 */
	public function getAdditional() {
		return $this->additional;
	}

	/**
	 * Returns the description
	 *
	 * @return \string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param \string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Returns the contact
	 *
	 * @return \string $contact
	 */
	public function getContact() {
		return $this->contact;
	}

	/**
	 * Sets the contact
	 *
	 * @param \string $contact
	 * @return void
	 */
	public function setContact($contact) {
		$this->contact = $contact;
	}

	/**
	 * Returns the street
	 *
	 * @return \string $street
	 */
	public function getStreet() {
		return $this->street;
	}

	/**
	 * Sets the street
	 *
	 * @param \string $street
	 * @return void
	 */
	public function setStreet($street) {
		$this->street = $street;
	}

	/**
	 * Returns the zip
	 *
	 * @return \string $zip
	 */
	public function getZip() {
		return $this->zip;
	}

	/**
	 * Sets the zip
	 *
	 * @param \string $zip
	 * @return void
	 */
	public function setZip($zip) {
		$this->zip = $zip;
	}

	/**
	 * Returns the city
	 *
	 * @return \string $city
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * Sets the city
	 *
	 * @param \string $city
	 * @return void
	 */
	public function setCity($city) {
		$this->city = $city;
	}

	/**
	 * Returns the country
	 *
	 * @return \string $country
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * Sets the country
	 *
	 * @param \string $country
	 * @return void
	 */
	public function setCountry($country) {
		$this->country = $country;
	}

	/**
	 * Returns the phone
	 *
	 * @return \string $phone
	 */
	public function getPhone() {
		return $this->phone;
	}

	/**
	 * Sets the phone
	 *
	 * @param \string $phone
	 * @return void
	 */
	public function setPhone($phone) {
		$this->phone = $phone;
	}

	/**
	 * Returns the fax
	 *
	 * @return \string $fax
	 */
	public function getFax() {
		return $this->fax;
	}

	/**
	 * Sets the fax
	 *
	 * @param \string $fax
	 * @return void
	 */
	public function setFax($fax) {
		$this->fax = $fax;
	}

	/**
	 * Returns the url
	 *
	 * @return \string $url
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Sets the url
	 *
	 * @param \string $url
	 * @return void
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * Returns the email
	 *
	 * @return \string $email
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Sets the email
	 *
	 * @param \string $email
	 * @return void
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Returns the latitude
	 *
	 * @return \string $latitude
	 */
	public function getLatitude() {
		return $this->latitude;
	}

	/**
	 * Sets the latitude
	 *
	 * @param \string $latitude
	 * @return void
	 */
	public function setLatitude($latitude) {
		$this->latitude = $latitude;
	}

	/**
	 * Returns the longitude
	 *
	 * @return \string $longitude
	 */
	public function getLongitude() {
		return $this->longitude;
	}

	/**
	 * Sets the longitude
	 *
	 * @param \string $longitude
	 * @return void
	 */
	public function setLongitude($longitude) {
		$this->longitude = $longitude;
	}

	/**
	 * Get categories
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Mia3\Mia3Location\Domain\Model\Category>
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * Get first category
	 *
	 * @return Category
	 */
	public function getFirstCategory() {
		$categories = $this->getCategories();
		if (!is_null($categories)) {
			$categories->rewind();
			return $categories->current();
		} else {
			return NULL;
		}
	}

	/**
	 * Set categories
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories
	 * @return void
	 */
	public function setCategories($categories) {
		$this->categories = $categories;
	}

	/**
	 * Adds a category to this categories.
	 *
	 * @param \Mia3\Mia3Location\Domain\Model\Category $category
	 * @return void
	 */
	public function addCategory(Category $category) {
		$this->getCategories()->attach($category);
	}

	public function getMarkerImage() {
		foreach ($this->categories as $category) {
			return $category->getLocationMarker()->getOriginalResource()->getPublicUrl();
		}
	}

}
