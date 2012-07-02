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
 * Default Form Select Querier.
 *
 * @package SavLibraryPlus
 * @version $ID:$
 */
 
class Tx_SavLibraryPlus_Queriers_FormSelectQuerier extends Tx_SavLibraryPlus_Queriers_AbstractQuerier {

	/**
   * The saved row
   *
   * @var array
   */
	protected $savedRow;	
	
  /**
   * Executes the query
   *
   * @param none
   *
   * @return none
   */
  protected function executeQuery() {

    // Select the items
		$this->resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			/* SELECT   */	$this->buildSelectClause(),
			/* FROM     */	$this->buildFromClause(),
 			/* WHERE    */	$this->buildWhereClause(),
			/* GROUP BY */	$this->buildGroupByClause()
		);		
		
    // Sets the rows from the query
    $this->setRows();

    // Saves the current row
    $this->savedRow = $this->rows[$this->currentRowId];
    
    // Gets the submitted data and unserializes them
    $submittedData = $this->getFieldValueFromCurrentRow($this->buildFullFieldName('_submitted_data_'));
    $unserializedData = unserialize($submittedData);
    
    // Gets the temporary data associated with the form if any
    $shortFormName = Tx_SavLibraryPlus_Controller_AbstractController::getShortFormName();
    
    if (empty($unserializedData[$shortFormName]) === false) {
    	$formUnserializedSata = $unserializedData[$shortFormName];
    	if(empty($formUnserializedSata['temporary']) === false) {
    		$this->rows[$this->currentRowId] = array_merge($this->rows[$this->currentRowId], $formUnserializedSata['temporary']);
    	}
    }
  }

  /**
   * Builds the WHERE clause
   *
   * @param none
   *
   * @return string The WHERE clause
   */
  protected function buildWhereClause() {
  	
    // Builds the where clause
    $whereClause = '1';
    
    // Adds the WHERE clause coming from the selected filter if any
		$selectedFilterKey = Tx_SavLibraryPlus_Managers_SessionManager::getSelectedFilterKey();
		if (empty($selectedFilterKey) === false) {
			// Gets the addWhere
		  $additionalWhereClause = Tx_SavLibraryPlus_Managers_SessionManager::getFilterField($selectedFilterKey, 'addWhere');
			$whereClause .= ' AND ' . (empty($additionalWhereClause) ? '0' : $additionalWhereClause);  
			
			// Gets the uid and modifies the compressed parameters
			$uid = Tx_SavLibraryPlus_Managers_SessionManager::getFilterField($selectedFilterKey, 'uid');
			$compressedParameters = Tx_SavLibraryPlus_Managers_UriManager::getCompressedParameters();
			$compressedParameters = Tx_SavLibraryPlus_Controller_AbstractController::changeCompressedParameters($compressedParameters, 'uid', $uid);
			Tx_SavLibraryPlus_Managers_UriManager::setCompressedParameters($compressedParameters);
		}
				
    return $whereClause;
  }

  /**
   * Gets a saved row field
   *
   * @param $fullFieldName
   *
   * @return mixed
   */
  public function getFieldValueFromSavedRow($fullFieldName) {
  	return $this->savedRow[$fullFieldName];
  }
  
}
?>