<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Laurent Foulloy (yolf.typo3@orange.fr)
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Abstract class Viewer.
 * 
 * @package SavLibraryPlus
 * @version $ID:$
 */
abstract class Tx_SavLibraryPlus_Viewers_AbstractDefaultRootPath {

  // Default item viewvier directory
  const DEFAULT_ITEM_VIEWERS_DIRECTORY = 'General';  

  /**
   * The partial root directory
   *
   * @var string
   */
  protected $partialRootPath = 'EXT:sav_library_plus/Resources/Private/Partials/TYPO4x';

  /**
   * The layout root directory
   *
   * @var string
   */
  protected $layoutRootPath = 'EXT:sav_library_plus/Resources/Private/Layouts/TYPO4x';
  
  /**
   * The default template root directory
   *
   * @var string
   */
  protected $defaultTemplateRootPath = 'EXT:sav_library_plus/Resources/Private/Templates/Default/TYPO4x';  
  
}
?>
