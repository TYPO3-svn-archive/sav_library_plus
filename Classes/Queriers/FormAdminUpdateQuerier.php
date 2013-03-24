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
 * Default update Querier.
 * 
 * @package SavLibraryPlus
 * @version $ID:$
 */
 
class Tx_SavLibraryPlus_Queriers_FormAdminUpdateQuerier extends Tx_SavLibraryPlus_Queriers_UpdateQuerier {

	/**
   * The validation array
   *
   * @var array
   */
	protected $validation;	
	
		
  /**
   * Executes the query
   *
   * @param none
   *
   * @return none
   */
  protected function executeQuery() {

    // Gets the library configuration manager
    $libraryConfigurationManager = $this->getController()->getLibraryConfigurationManager();

    // Gets the view configuration
    $viewIdentifier =  $libraryConfigurationManager->getViewIdentifier('formView');    
    $viewConfiguration = $libraryConfigurationManager->getViewConfiguration($viewIdentifier);

    // Gets the active folder key
    $activeFolderKey = $this->getController()->getUriManager()->getFolderKey();
    if($activeFolderKey === NULL) {
      reset($viewConfiguration);
      $activeFolderKey = key($viewConfiguration);
    }

    // Sets the active folder
    $activeFolder = $viewConfiguration[$activeFolderKey];

    // Creates the field configuration manager
    $fieldConfigurationManager = t3lib_div::makeInstance('Tx_SavLibraryPlus_Managers_FieldConfigurationManager');
    $fieldConfigurationManager->injectController($this->getController());
    
    // Gets the fields configuration for the folder
    $folderFieldsConfiguration = $fieldConfigurationManager->getFolderFieldsConfiguration($activeFolder, true);

    // Gets the POST variables
    $postVariables = $this->getController()->getUriManager()->getPostVariables();
    unset($postVariables['formAction']);
       
    $this->validation = $postVariables['validation'];
    unset($postVariables['validation']);
   
    // Gets the main table
    $mainTable = $this->getQueryConfigurationManager()->getMainTable();
		$mainTableUid = Tx_SavLibraryPlus_Managers_UriManager::getUid();

		// Initializes special marker array
		$markerItemsManual = array();
		$markerItemsAuto = array();
		
		// Processes the regular fields. Explode the key to get the table and field names
		$variablesToUpdate = array();
		foreach ($this->validation as $fieldKey => $validated) {
			if ($validated) {
        // Sets the field configuration
        $this->fieldConfiguration = $this->searchConfiguration($folderFieldsConfiguration, $fieldKey);

        $tableName = $this->fieldConfiguration['tableName'];
        $fieldName = $this->fieldConfiguration['fieldName'];
        $fieldType = $this->fieldConfiguration['fieldType'];
        $fullFieldName = $tableName . '.' . $fieldName;
        
        // Adds the cryted full field name
        $this->fieldConfiguration['cryptedFullFieldName'] = $fieldKey;        
        
        // Gets the field value and uid
        $uid = key($postVariables[$fieldKey]);        
				$value = current($postVariables[$fieldKey]);
				        
        // Adds the uid to the configuration
        $this->fieldConfiguration['uid'] = $uid;  
             
        // Makes pre-processings.
        self::$doNotAddValueToUpdateOrInsert = false;
        $value = $this->preProcessor($value);  

				// Gets the rendered value 
				$fieldConfiguration = $this->fieldConfiguration;
				$fieldConfiguration['value'] = $value;
      	$className = 'Tx_SavLibraryPlus_ItemViewers_Default_' . $fieldConfiguration['fieldType'] . 'ItemViewer';
      	$itemViewer = t3lib_div::makeInstance($className);
      	$itemViewer->injectController($this->getController());
      	$itemViewer->injectItemConfiguration($fieldConfiguration);
      	$renderedValue = $itemViewer->render();
      	if ($renderedValue == $value) {
      		$markerValue = $renderedValue;
      	}	else {
      		$markerValue = $renderedValue . ' (' . $value .')';
      	}
      				
				// Sets the items markers
				if ($uid === 0) {
					$markerItemsManual = array_merge($markerItemsManual, array($fullFieldName => $markerValue));
				} elseif ($uid > 0) {
					$markerItemsAuto = array_merge($markerItemsAuto, array($fullFieldName => $markerValue));					
				} else {
					self::$doNotAddValueToUpdateOrInsert = true;
				}    
        
        // Adds the variables
        if (self::$doNotAddValueToUpdateOrInsert === false) {
		      $variablesToUpdateOrInsert[$tableName][$uid][$fullFieldName] = $value;
        }       		
			}
		}
		
		// Injects the markers
		$markerContent = '';
		
		foreach($markerItemsAuto as $markerKey => $marker) {
			$markerContent .= $markerKey . ' : ' . $marker . chr(10);
		}
		$this->getController()->getQuerier()->injectAdditionalMarkers(array('###ITEMS_AUTO###' => $markerContent));
  	$markerContent = '';
		foreach($markerItemsManual as $markerKey => $marker) {
			$markerContent .= $markerKey . ' : ' . $marker . chr(10);
		}		
		$this->getController()->getQuerier()->injectAdditionalMarkers(array('###ITEMS_MANUAL###' => $markerContent));	
		
		// Updates the fields if any
		if (empty($variablesToUpdateOrInsert) === false) {
    	$variableToSerialize = array();		
	
  		foreach ($variablesToUpdateOrInsert as $tableName => $variableToUpdateOrInsert) {
        if (empty($tableName) === false) {
        	$variableToSerialize = $variableToSerialize + $variableToUpdateOrInsert; 	
      	
    			// Updates the data
    			$key =  key($variableToUpdateOrInsert);
    			$fields = current($variableToUpdateOrInsert);

    			if ($key > 0) { 				
				    $this->updateFields($tableName, $fields, $key);  
    			} 
        }
      }
    
      // Updates the _submitted_data_ field
      $shortFormName = Tx_SavLibraryPlus_Controller_AbstractController::getShortFormName();
			$variableToSerialize = $variableToSerialize + array('validation' => $this->validation);
			$serializedVariable = serialize(array($shortFormName => array('temporary' => $variableToSerialize)));  
      $this->updateFields($mainTable, array('_submitted_data_' => $serializedVariable,'_validated_' => 1), $mainTableUid);   
			Tx_SavLibraryPlus_Controller_FlashMessages::addMessage('message.dataSaved'); 			    	      
    }

    if (empty($this->postProcessingList) === false) {
      foreach($this->postProcessingList as $postProcessingItem) {
        $this->fieldConfiguration = $postProcessingItem['fieldConfiguration'];
        $method = $postProcessingItem['method'];
        $value = $postProcessingItem['value'];
        $this->$method($value);
      }
    }  
  }

	/**
	 * Pre-processor which calls the method according to the type
	 *
	 * @param mixed $value Value to be pre-processed
	 *
	 * @return mixed
	 */
  protected function preProcessor($value) {
  
    // Builds the field type
    $fieldType = $this->getFieldConfigurationAttribute('fieldType');
    if ($fieldType == 'ShowOnly') {
    	$renderType = $this->getFieldConfigurationAttribute('renderType');
    	$fieldType = (empty($renderType) ? 'String' : $renderType);
    }
    
    $fieldType = $this->getFieldConfigurationAttribute('fieldType');

    // Calls the verification method for the type if it exists
    $verifierMethod = 'verifierFor' . $fieldType;
    if (method_exists($this,$verifierMethod) && $this->$verifierMethod($value) !== true) {
     	self::$doNotAddValueToUpdateOrInsert = true;
    	self::$doNotUpdateOrInsert = true;
      return $value;
    }
  	
  	// Builds the method name
    $preProcessorMethod = 'preProcessorFor' . $fieldType;
    
    // Gets the crypted full field name
    $cryptedFullFieldName = $this->fieldConfiguration['cryptedFullFieldName'];

    if (empty($this->validation[$cryptedFullFieldName])) {
    	self::$doNotAddValueToUpdateOrInsert = true;
    }
 
    // Calls the methods if it exists
    if (method_exists($this,$preProcessorMethod)) {
      $newValue =  $this->$preProcessorMethod($value);
    } else {
      $newValue = $value;
    }   
 
		// Checks if a required field is not empty
		if ($this->isRequired() && empty($newValue)) {
			self::$doNotUpdateOrInsert = true;
			Tx_SavLibraryPlus_Controller_FlashMessages::addError('error.fieldRequired', array($this->fieldConfiguration['label']));		
		}    
		
    // Sets a post-processor for the email if any 
    if ($this->getFieldConfigurationAttribute('mail')) {
    	// Sets a post processor
    	$this->postProcessingList[] = array(
      	'method' => 'postProcessorToSendEmail',
      	'value' => $value,
      	'fieldConfiguration' => $this->fieldConfiguration
    	);
    	
    	// Gets the row before processing
    	$this->rows['before'] = $this->getCurrentRowInEditView();
    } 
    
    // Calls the verifier if it exists
    $verifierMethod = $this->getFieldConfigurationAttribute('verifier');
    if (!empty($verifierMethod)) {
    	if(!method_exists($this,$verifierMethod)) {
    		self::$doNotAddValueToUpdateOrInsert = true;
    		self::$doNotUpdateOrInsert = true;
    		Tx_SavLibraryPlus_Controller_FlashMessages::addError('error.verifierUnknown');
    	} elseif ($this->$verifierMethod($newValue) !== true) {
    		self::$doNotAddValueToUpdateOrInsert = true;
    		self::$doNotUpdateOrInsert = true;    		
    	}
    }
    
    return $newValue;
  }
  
  
}
?>
