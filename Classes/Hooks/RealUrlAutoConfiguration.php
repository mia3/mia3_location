<?php
namespace Mia3\Mia3Location\Hooks;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class RealUrlAutoConfiguration {

	/**
	 * Generates additional RealURL configuration and merges it with provided configuration
	 *
	 * @param       array $params Default configuration
	 * @return      array Updated configuration
	 */
	public function addLocationConfig($params) {
		return array_merge_recursive($params['config'], array(
				'postVarSets' => array(
					'_DEFAULT' => array(
						'location' => array(
							array(
								'GETvar' => 'tx_mia3location_locations[controller]',
								'noMatch' => 'bypass'
							),
							array(
								'GETvar' => 'tx_mia3location_locations[action]',
								'noMatch' => 'bypass',
								'valueMap' => array(
								  'show' => 'show'
								)
							),
							array(
								'GETvar' => 'tx_mia3location_locations[location]',
								'lookUpTable' => array(
									'table' => 'tx_mia3location_domain_model_location',
									'id_field' => 'uid',
									'alias_field' => 'name',
									'useUniqueCache' => 1,
									'useUniqueCache_conf' => array(
										'strtolower' => 1,
										'spaceCharacter' => '-'
									)
								)
							)
						)
					)
				)
			)
		);
	}
}
