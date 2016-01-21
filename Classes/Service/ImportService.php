<?php
namespace Mia3\Mia3Location\Service;

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

class ImportService {

	/**
	 * @param string $file
	 * @param integer $pid
	 * @param string $defaultCountry
	 * @param integer $category
	 */
	public function import($file, $pid, $defaultCountry, $category, $truncate = FALSE) {
		ini_set("auto_detect_line_endings", true);
		ini_set('max_execution_time', '360');
		$encoding = $this->determineEncoding($file);
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
					$row[strtolower(trim($column))] = iconv($encoding, 'UTF-8', $data[$key]);;
				}
				$rows[] = $row;
			}
			fclose($handle);
		}

		if ($truncate === TRUE) {
			// $GLOBALS['TYPO3_DB']->exec_DELETEquery('sys_category_record_mm', 'tablenames = "tx_mia3location_domain_model_location" AND ');
			$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_mia3location_domain_model_location', 'pid = ' . $pid);
		}

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
		$apiURL = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&language=de';
		$tmpName = $tmpDir . sha1($apiURL) . '.txt';
		if (!file_exists($tmpName)) {
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

	function determineEncoding($file) {
		$string = file_get_contents($file);
		$encodingCandidates = array(
			'UTF-8', 'ASCII', 'macintosh',
			'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
			'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
			'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
			'Windows-1251', 'Windows-1252', 'Windows-1254',
		);

		$result = false;
		foreach ($encodingCandidates as $item) {
			$sample = iconv($item, $item, $string);
			if (md5($sample) == md5($string)) {
				$result = $item;
				break;
			}
		}

		return $result;
	}

}
