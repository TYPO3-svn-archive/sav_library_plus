<?php
namespace SAV\SavLibraryPlus\ItemViewers\Edit;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * Edit DateTime item Viewer.
 * 
 * @package SavLibraryPlus
 * @version $ID:$
 */
 
class DateTimeItemViewer extends AbstractItemViewer {

  /**
   * Renders the item.
   *
   * @param none
   *
   * @return string
   */
  protected function renderItem() {

    $htmlArray = array();

    // Sets the format
    $format = ($this->getItemConfiguration('format') ? $this->getItemConfiguration('format') : $this->getController()->getDefaultDateTimeFormat());

    // Sets the value
    $value = (
      $this->getItemConfiguration('value') ?
      strftime($format, $this->getItemConfiguration('value')) :
      ($this->getItemConfiguration('nodefault') ? '' : strftime($format))
    );

    $htmlArray[] = \SAV\SavLibraryPlus\Utility\HtmlElements::htmlInputTextElement(
      array(
        \SAV\SavLibraryPlus\Utility\HtmlElements::htmlAddAttribute('name', $this->getItemConfiguration('itemName')),
        \SAV\SavLibraryPlus\Utility\HtmlElements::htmlAddAttribute('id', 'input_' . strtr($this->getItemConfiguration('itemName'), '[]', '__')),
        \SAV\SavLibraryPlus\Utility\HtmlElements::htmlAddAttributeIfNotNull('class', $this->getItemConfiguration('classhtmltag')),
        \SAV\SavLibraryPlus\Utility\HtmlElements::htmlAddAttributeIfNotNull('style', $this->getItemConfiguration('stylehtmltag')),
        \SAV\SavLibraryPlus\Utility\HtmlElements::htmlAddAttribute('value', $value),
        \SAV\SavLibraryPlus\Utility\HtmlElements::htmlAddAttribute('onchange', 'document.changed=1;'),
      )
    );

    // Creates the date picker
    $datePicker = GeneralUtility::makeInstance('SAV\\SavLibraryPlus\\DatePicker\\DatePicker');
  
    // Renders the date picker
    $htmlArray[] = $datePicker->render(
      array(
        'id' => strtr($this->getItemConfiguration('itemName'), '[]', '__'),
        'format' => $format,
        'showsTime' => TRUE,
        'iconPath' => \SAV\SavLibraryPlus\Managers\LibraryConfigurationManager::getIconPath('calendar'),
      )
    );

    return $this->arrayToHTML($htmlArray);
  }

}
?>
