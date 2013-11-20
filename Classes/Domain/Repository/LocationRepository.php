<?php
namespace Famelo\FameloLocation\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Felix Kopp <felix-source@phorax.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Repository for \TYPO3\CMS\Beuser\Domain\Model\BackendUser
 *
 */
class LocationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
	public function findNearBy($latitude, $longitude, $distance=30) {
        $pi = M_PI;
        $query = 'SELECT *, (
        	((acos(
				sin((' . ($latitude * $pi / 180) . ')) * sin((latitude * ' . $pi . ' / 180))
				+
				cos((' . ($latitude * $pi / 180) . ')) *  cos((latitude * ' . $pi . ' / 180))
				*
				cos(((' . $longitude . ' - longitude) * ' . $pi . ' / 180))
			)) * 180 / ' . $pi . ') * 60 * 1.423
		) as distance
		FROM tx_famelolocation_domain_model_location
		HAVING distance <= ' . intval($distance) . '
		AND deleted = "0"
		AND hidden = "0"
		ORDER BY distance ASC';

        $result = $GLOBALS['TYPO3_DB']->sql_query($query);

        $locations = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$locations[] = $this->findByUid($row['uid']);
		}
		return $locations;
	}
}