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
 * Default Export Viewer.
 *
 * @package SavLibraryPlus
 * @version $ID:$
 */
 
class Tx_SavLibraryPlus_Viewers_ExportViewer extends Tx_SavLibraryPlus_Viewers_AbstractViewer {

  /**
   * The template file
   *
   * @var string
   */
  protected $templateFile = 'EXT:sav_library_plus/Resources/Private/Templates/Default/Export.html';
	
  /**
   * Renders the view
   *
   * @param none
   *
   * @return string The rendered view
   */
  public function render() {
  	
  	// Builds the item configuration
  	$itemConfiguration = array(
  		'foreign_table' => Tx_SavLibraryPlus_Queriers_ExportSelectQuerier::$exportTableName,
  		'whereselect' => 'cid=' . intval($this->getController()->getExtensionConfigurationManager()->getExtensionContentObject()->data['uid']),
  		'orderselect' => 'name',
  		'overridestartingpoint' => 1,
  	);
  	
    // Builds the querier
    $querierClassName = 'Tx_SavLibraryPlus_Queriers_ForeignTableSelectQuerier';
    $querier = t3lib_div::makeInstance($querierClassName);
    $querier->injectController($this->getController());
    $querier->buildQueryConfigurationForForeignTable($itemConfiguration);
    $querier->injectQueryConfiguration();
    $querier->processQuery();
    
    // Gets the rows
    $rows = $querier->getRows(); 
    
    // Builds the option for the configuration selector
    $optionsConfiguration = array(0 => '');
    foreach ($rows as $row) {
    	$optionsConfiguration[$row['uid']] = $row[Tx_SavLibraryPlus_Queriers_ExportSelectQuerier::$exportTableName . '.name'];
    }
   
    // Adds the options for the configuration to the view configuration
    $this->addToViewConfiguration('optionsConfiguration', $optionsConfiguration);
    
    // Builds the groups for the user
    $optionsGroup = array(0 => '');  
  	foreach($GLOBALS['TSFE']->fe_user->groupData['title'] as $groupKey => $group) {
  		$optionsGroup[$groupKey] = $group;
    }

    // Adds the options for the group to the view configuration
    $this->addToViewConfiguration('optionsGroup', $optionsGroup);

		// Adds the export configuration to the view
    $this->addToViewConfiguration('exportConfiguration', $this->getController()->getQuerier()->getExportConfiguration());
    
    // Adds information to the view configuration
		$this->addToViewConfiguration('general',   
      array(
        'extensionKey' => $this->getController()->getExtensionConfigurationManager()->getExtensionKey(),
        'formName' => Tx_SavLibraryPlus_Controller_AbstractController::getFormName(), 
        'userIsAllowedToExportData' => $this->getController()->getUserManager()->userIsAllowedToExportData(), 
      	'execIsAllowed' => $this->getController()->getExtensionConfigurationManager()->getAllowExec(),      
      )
    );

    // Renders the view
    return $this->renderView();  	
  }	
}
?>