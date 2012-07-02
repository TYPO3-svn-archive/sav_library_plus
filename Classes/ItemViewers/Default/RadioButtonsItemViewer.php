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
 * Default Radio buttons item Viewer.
 * 
 * @package SavLibraryPlus
 * @version $ID:$
 */
 
class Tx_SavLibraryPlus_ItemViewers_Default_RadioButtonsItemViewer extends Tx_SavLibraryPlus_ItemViewers_Default_AbstractItemViewer {

  /**
   * Renders the item.
   *
   * @param none
   *
   * @return string
   */
  protected function renderItem() {
  
    $htmlArray = array();

    $columnsCount = ($this->getItemConfiguration('cols') ? $this->getItemConfiguration('cols') : 1);
    $counter = 0;
    // Gets the value
    $value = $this->getItemConfiguration('value');

    // Adds the option elements
    $items = $this->getItemConfiguration('items');
    foreach ($items as $itemKey => $item) {

      // Sets the class for the item
      $class = 'radioButton item' . $itemKey;
        
      // Checks if the columns count is reached

      if ($counter == $columnsCount) {
        // Additional class
        $class .= ' clearLeft';
        // Resets the counter
        $counter = 0;
      }
      $counter++;


      // Builds the message
      $message = Tx_SavLibraryPlus_Utility_HtmlElements::htmlSpanElement(
        array(
          Tx_SavLibraryPlus_Utility_HtmlElements::htmlAddAttribute('class', 'radioButtonMessage'),
        ),
        stripslashes(Tx_Extbase_Utility_Localization::translate($item[0]))
      );
        
      // Adds the Div element
      if ($this->itemConfigurationNotSet('displayasimage') || $this->getItemConfiguration('displayasimage')) {
        if ($item[1] == $value) {
          $htmlArray[] = Tx_SavLibraryPlus_Utility_HtmlElements::htmlDivElement(
            array(
              Tx_SavLibraryPlus_Utility_HtmlElements::htmlAddAttribute('class', $class),
            ),
            $this->renderSelectedAsImage() . $message
          );
        } else {
          $htmlArray[] = Tx_SavLibraryPlus_Utility_HtmlElements::htmlDivElement(
            array(
              Tx_SavLibraryPlus_Utility_HtmlElements::htmlAddAttribute('class', $class),
            ),
            $this->renderNotSelectedAsImage() . $message
          );
        }
      } elseif ($item[1] == $value) {
        $htmlArray[] = Tx_SavLibraryPlus_Utility_HtmlElements::htmlDivElement(
          array(
            Tx_SavLibraryPlus_Utility_HtmlElements::htmlAddAttribute('class', $class),
          ),
          $message
        );
      }
    }

    return $this->arrayToHTML($htmlArray);
  }

  /**
   * Renders a checked checkbox as an image.
   *
   * @param none
   *
   * @return string
   */
  protected function renderSelectedAsImage() {
    // Gets the image file name
  	$imageFileName = $this->getItemConfiguration('radiobuttonselectedimage');
  	if (empty($imageFileName)) {
  		$imageFileName = 'radioButtonSelected.gif';
  	}  	
    $iconsDirectory = $this->getController()->getLibraryConfigurationManager()->getIconsDirectory($imageFileName);
    $content = Tx_SavLibraryPlus_Utility_HtmlElements::htmlImgElement(
      array(
        Tx_SavLibraryPlus_Utility_HtmlElements::htmlAddAttribute('class', 'radioButtonSelected'),
        Tx_SavLibraryPlus_Utility_HtmlElements::htmlAddAttribute('src', $iconsDirectory . $imageFileName),
        Tx_SavLibraryPlus_Utility_HtmlElements::htmlAddAttribute('title', Tx_Extbase_Utility_Localization::translate('itemviewer.radioButtonSelected', 'sav_library_plus')),
        Tx_SavLibraryPlus_Utility_HtmlElements::htmlAddAttribute('alt', Tx_Extbase_Utility_Localization::translate('itemviewer.radioButtonSelected', 'sav_library_plus')),
      )
    );

    return $content;
  }

  /**
   * Renders a unchecked checkbox as an image.
   *
   * @param none
   *
   * @return string
   */
  protected function renderNotSelectedAsImage() {
   	// Gets the image file name
  	$imageFileName = $this->getItemConfiguration('radiobuttonnotselectedimage');
  	if (empty($imageFileName)) {
  		$imageFileName = 'radioButtonNotSelected.gif';
  	}  	
    $iconsDirectory = $this->getController()->getLibraryConfigurationManager()->getIconsDirectory($imageFileName);
    $content = Tx_SavLibraryPlus_Utility_HtmlElements::htmlImgElement(
      array(
        Tx_SavLibraryPlus_Utility_HtmlElements::htmlAddAttribute('class', 'radioButtonNotSelected'),
        Tx_SavLibraryPlus_Utility_HtmlElements::htmlAddAttribute('src', $iconsDirectory . $imageFileName),
        Tx_SavLibraryPlus_Utility_HtmlElements::htmlAddAttribute('title', Tx_Extbase_Utility_Localization::translate('itemviewer.radioButtonNotSelected', 'sav_library_plus')),
        Tx_SavLibraryPlus_Utility_HtmlElements::htmlAddAttribute('alt', Tx_Extbase_Utility_Localization::translate('itemviewer.radioButtonNotSelected', 'sav_library_plus')),
      )
    );

    return $content;
  }


}
?>