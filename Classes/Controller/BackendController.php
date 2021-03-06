<?php
namespace Mia3\Mia3Location\Controller;

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Mia3\Mia3Location\Service\ImportService;

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
 *
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class BackendController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * action list
     *
     * @return void
     */
    public function indexAction()
    {
        $db = $GLOBALS['TYPO3_DB'];
        /** @var DatabaseConnection $db */
        $sysFolders = $db->exec_SELECTgetRows('*', 'pages', 'doktype = 254');
        $location = $db->exec_SELECTgetSingleRow('*', 'tx_mia3location_domain_model_location', 'deleted=0');
        $storagePids = array();
        foreach($sysFolders as $sysFolder) {
            $storagePids[$sysFolder['uid']] = $sysFolder['title'] . ' (' . $sysFolder['uid'] . ')';
        }
        if (is_array($location)) {
            $this->view->assign('storagePid', $location['pid']);
        }
        $this->view->assign('storagePids', $storagePids);

        $categories = array();
        foreach($db->exec_SELECTgetRows('*', 'sys_category', 'deleted=0', '', 'title') as $category) {
            $categories[$category['uid']] = $category['title'] . ' (' . $category['uid'] . ')';
        }
        $this->view->assign('categories', $categories);
    }

    /**
     * @param integer $storagePid
     * @param integer $categoryUid
     * @param boolean $truncate
     * @param array $file
     * @param string $imageFolder
     */
    public function importAction($storagePid, $categoryUid, $truncate, $file, $imageFolder = null)
    {
        $importService = new ImportService();
        $rows = $importService->import($file['tmp_name'], $storagePid, $categoryUid, $truncate, $imageFolder);

        return 'imported ' . count($rows) . ' locations';
    }
}
