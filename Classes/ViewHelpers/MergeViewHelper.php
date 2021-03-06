<?php
namespace SAV\SavLibraryPlus\ViewHelpers;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

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

/**
 * Merges two arrays
 *
 * @package SavLibraryMvc
 * @version $Id:
 */
class MergeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

  /**
   * @param array $argument1 Argument
   * @param array $argument2 Argument
   */
  public function render($array1 = array(), $array2 = array()) {

    return array_merge($array1, $array2);
  }
	
}
?>
