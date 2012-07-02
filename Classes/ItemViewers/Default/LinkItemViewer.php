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
 * Default Link item Viewer.
 * 
 * @package SavLibraryPlus
 * @version $ID:$
 */
 
class Tx_SavLibraryPlus_ItemViewers_Default_LinkItemViewer extends Tx_SavLibraryPlus_ItemViewers_Default_AbstractItemViewer {

  /**
   * Renders the item.
   *
   * @param none
   *
   * @return string
   */
  protected function renderItem() {
  
    // Gets the value
    $value = $this->getItemConfiguration('value');
    
    // Checks if the link is related to a RTF file
    if ($this->getItemConfiguration('generatertf')) {
		  if (empty($value) === false) {  	
        $path_parts = pathinfo($this->getItemConfiguration('savefilertf')); 
        $folder = $path_parts['dirname'];    
        $this->setItemConfiguration('folder', $folder);	
        $fileName = $folder . '/' . $value; 
 	
				// Checks if the file exists
				if (file_exists($fileName)) {
		  		$content .= $this->makeLink($value);
				} else {
					$content .= $value;
				}
		  } 
    } else {
      // Adds the typolink
      $content = $this->makeUrlLink($value);
    }

    return $content;
  }
}
?>