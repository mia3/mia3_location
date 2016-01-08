<?php
namespace Mia3\Mia3Location\Command;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

class LocationCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController {

	/**
	 * @param string $file
	 * @param integer $pid
	 * @param string $defaultCountry
	 * @param integer $category
	 */
	public function importCommand($file, $pid, $defaultCountry, $category) {
		ini_set("auto_detect_line_endings", true);
		if (($handle = fopen($file, 'r')) !== FALSE) {
			$rows = array();
			$headers = NULL;
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {;
				if ($headers === NULL) {
					$headers = $data;
					continue;
				}
				$row = array();
				foreach ($headers as $key => $column) {
					$row[strtolower(trim($column))] = $data[$key];
				}
				$rows[] = $row;
			}
			fclose($handle);
		}

		$GLOBALS['TYPO3_DB']->exec_DELETEquery('sys_category_record_mm', 'tablenames = "tx_mia3location_domain_model_location"');
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_mia3location_domain_model_location', '1=1');

		$allowedColumns = array(
			'name' => NULL,
			'contact' => NULL,
			'street' => NULL,
			'zip' => 'postal_code',
			'city' => 'locality',
			'country' => 'country',
			'region' => 'administrative_area_level_1',
			'phone' => NULL,
			'fax' => NULL,
			'url' => NULL,
			'latitude' => 'latitude',
			'longitude' => 'longitude'
		);
		$searchColumns = array(
			'street',
			'zip',
			'city',
			'country'
		);
		foreach ($rows as $key => $row) {
			$searchParts = array();
			foreach ($searchColumns as $searchColumn) {
				if (!isset($row[$searchColumn])) {
					continue;
				}
				$searchParts[$searchColumn] = $row[$searchColumn];
			}
			if (!isset($row['country'])) {
				$searchParts[] = $defaultCountry;
			}

			$address = implode(',', $searchParts);
			$googleResult = $this->getCoordinates($address);

			$insertData = array(
				'pid' => $pid,
				'uid' => $key
			);
			foreach ($allowedColumns as $allowedColumn => $fallbackColumn) {
				if (isset($row[$allowedColumn])) {
					$insertData[$allowedColumn] = $row[$allowedColumn];
				} else if (isset($googleResult[$fallbackColumn])) {
					$insertData[$allowedColumn] = $googleResult[$fallbackColumn];
				}
			}
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'tx_mia3location_domain_model_location',
				$insertData
			);
			if ($category !== NULL) {
				$GLOBALS['TYPO3_DB']->exec_INSERTquery(
					'sys_category_record_mm',
					array(
						'uid_local' => $category,
						'uid_foreign' => $key,
						'tablenames' => 'tx_mia3location_domain_model_location',
						'fieldname' => 'categories'
					)
				);
			}
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
		$adr = json_decode($addressData, TRUE);
		if (!isset($adr['results'][0])) {
			return;
		}
		$rawResult = $adr['results'][0];
		$result = array();

		if (isset($rawResult['address_components'])) {
			foreach ($rawResult['address_components'] as $addressComponent) {
				$result[$addressComponent['types'][0]] = $addressComponent['long_name'];
			}
		}
		$result['latitude'] = $rawResult['geometry']['location']['lat'];
		$result['longitude'] = $rawResult['geometry']['location']['lng'];

		return $result;
	}

}
