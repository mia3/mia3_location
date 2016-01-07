<?php
namespace Mia3\Mia3Location\ViewHelpers;

/*                                                                        *
 * This script is backported from the FLOW3 package "TYPO3.Fluid".        *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/*
 */
class ArraySplitViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
	/**
	 * @param array $items
	 * @param integer $max
	 * @param string $as
	 * @return string
	 */
	public function render($items, $max, $as = 'groups') {
		$groups = array();
		$group = array();
		foreach ($items as $key => $value) {
			if (count($group) >= $max) {
				$groups[] = $group;
				$group = array();
			}
			$group[] = $value;
		}
		$groups[] = $group;
		$this->templateVariableContainer->add($as, $groups);
		$output = $this->renderChildren();
		$this->templateVariableContainer->remove($as);
		return $output;
	}
}


?>
