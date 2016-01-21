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
	 * @param integer $category
	 * @param boolean $truncate
	 */
	public function importCommand($file, $pid, $category = NULL, $truncate = FALSE) {
		$importService = new ImportService();
		$importService->import($file, $pid, $category, $truncate);
	}

}
