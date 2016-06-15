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

class ImportService
{

    /**
     * @param string $file
     * @param integer $pid
     * @param integer $category
     * @param boolean $truncate
     */
    public function import($file, $pid, $category, $truncate = false)
    {
        ini_set("auto_detect_line_endings", true);
        ini_set('max_execution_time', '360');
        $encoding = $this->determineEncoding($file);
        if (($handle = fopen($file, 'r')) !== false) {
            $rows = array();
            $headers = null;
            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                ;
                if ($headers === null) {
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

        if ($truncate === true) {
            // $GLOBALS['TYPO3_DB']->exec_DELETEquery('sys_category_record_mm', 'tablenames = "tx_mia3location_domain_model_location" AND ');
            $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_mia3location_domain_model_location', 'pid = ' . $pid);
        }

        $allowedColumns = array(
            'name' => null,
            'contact' => null,
            'street' => null,
            'zip' => 'postal_code',
            'city' => 'locality',
            'country' => 'country',
            'region' => 'administrative_area_level_1',
            'phone' => null,
            'fax' => null,
            'url' => null,
            'latitude' => 'latitude',
            'longitude' => 'longitude',
        );
        $searchColumns = array(
            'street',
            'zip',
            'city',
            'country',
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
            echo 'address: ' . $address . '<br />';

            $insertData = array(
                'pid' => $pid,
            );
            foreach ($allowedColumns as $allowedColumn => $fallbackColumn) {
                if (isset($row[$allowedColumn]) && !empty($row[$allowedColumn])) {
                    $insertData[$allowedColumn] = $row[$allowedColumn];
                } else {
                    if (isset($googleResult[$fallbackColumn])) {
                        $insertData[$allowedColumn] = $googleResult[$fallbackColumn];
                    }
                }
            }
            $rows[$key] = $insertData;
            $GLOBALS['TYPO3_DB']->exec_INSERTquery(
                'tx_mia3location_domain_model_location',
                $insertData
            );
            if ($category !== null) {
                $GLOBALS['TYPO3_DB']->exec_INSERTquery(
                    'sys_category_record_mm',
                    array(
                        'uid_local' => $category,
                        'uid_foreign' => $key,
                        'tablenames' => 'tx_mia3location_domain_model_location',
                        'fieldname' => 'categories',
                    )
                );
            }
        }

        return $rows;
    }

    public function getCoordinates($address)
    {
        $tmpDir = PATH_site . 'typo3temp/coordinates/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $tmpName = $tmpDir . sha1($address) . '.txt';
        if (file_exists($tmpName)) {
            $addressData = file_get_contents($tmpName);
        }
        $adr = json_decode($addressData, true);

        if (!isset($adr['results'][0])) {
            $apiURL = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false&language=de';
            $addressData = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl($apiURL);
            file_put_contents($tmpName, $addressData);
            $adr = json_decode($addressData, true);

            if (isset($adr['error_message'])) {
                throw new \Exception('API Error: ' . $adr['error_message']);
            }
        }

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

    function determineEncoding($file)
    {
        $string = file_get_contents($file);
        $encodingCandidates = array(
            'UTF-8',
            'ASCII',
            'macintosh',
            'ISO-8859-1',
            'ISO-8859-2',
            'ISO-8859-3',
            'ISO-8859-4',
            'ISO-8859-5',
            'ISO-8859-6',
            'ISO-8859-7',
            'ISO-8859-8',
            'ISO-8859-9',
            'ISO-8859-10',
            'ISO-8859-13',
            'ISO-8859-14',
            'ISO-8859-15',
            'ISO-8859-16',
            'Windows-1251',
            'Windows-1252',
            'Windows-1254',
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
