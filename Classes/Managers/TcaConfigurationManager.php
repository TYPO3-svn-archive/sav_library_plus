<?php/****************************************************************  Copyright notice**  (c) 2011 Laurent Foulloy <yolf.typo3@orange.fr>*  All rights reserved**  This script is part of the TYPO3 project. The TYPO3 project is*  free software; you can redistribute it and/or modify*  it under the terms of the GNU General Public License as published by*  the Free Software Foundation; either version 2 of the License, or*  (at your option) any later version.**  The GNU General Public License can be found at*  http://www.gnu.org/copyleft/gpl.html.**  This script is distributed in the hope that it will be useful,*  but WITHOUT ANY WARRANTY; without even the implied warranty of*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the*  GNU General Public License for more details.**  This copyright notice MUST APPEAR in all copies of the script!***************************************************************//** * TCA configuration manager *  * @package SavLibraryPlus * @version $ID:$ */class Tx_SavLibraryPlus_Managers_TcaConfigurationManager {	/**	 * Gets the TCA field label.	 *   * @param string $tableName   * @param string $fieldName	 *	 * @return string	 */	public static function getTcaFieldLabel($tableName, $fieldName) {		t3lib_div::loadTCA($tableName);		return($GLOBALS['TSFE']->sL($GLOBALS['TCA'][$tableName]['columns'][$fieldName]['label']));	}	  /**   * Gets the TCA ctrl for a given field in a table.   *   * @param string $tableName   * @param string $fieldName   *   * @return string   */  public static function getTcaCtrlField($tableName, $fieldName) {    t3lib_div::loadTCA($tableName);    return $GLOBALS['TCA'][$tableName]['ctrl'][$fieldName];  }  /**   * Gets the TCA columns a given table.   *   * @param string $tableName   *   * @return string   */  public static function getTcaColumns($tableName) {    t3lib_div::loadTCA($tableName);    return $GLOBALS['TCA'][$tableName]['columns'];  }  /**   * Gets the TCA config for a given field in a table.   *   * @param string $tableName   * @param string $fieldName   *   * @return array   */  public static function getTcaConfigField($tableName, $fieldName) {    t3lib_div::loadTCA($tableName);    $config = $GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config'];    return (is_array($config) ? $config : array());  }  /**   * Gets the TCA config for a full field name.   *   * @param string $fullFieldName   *   * @return array   */  public static function getTcaConfigFieldFromFullFieldName($fullFieldName) {  	$fieldNameParts = explode('.', $fullFieldName);  	return self::getTcaConfigField($fieldNameParts[0], $fieldNameParts[1]);  }    /**   * Gets the TCA ORDER BY clause for the table. It iseither the TCA default_sortby or sortby control field.   *   * @param string $tableName   *   * @return array   */  public static function getTcaOrderByClause($tableName) {    $defaultSortBy = Tx_SavLibraryPlus_Managers_TcaConfigurationManager::getTcaCtrlField($tableName, 'default_sortby');    if (empty($defaultSortBy) === false) {    		// Removes the ORDER BY part to get only the fields    		$defaultSortBy = str_replace('ORDER BY ', '', $defaultSortBy);      	return $defaultSortBy;    } else {    	$sortBy = Tx_SavLibraryPlus_Managers_TcaConfigurationManager::getTcaCtrlField($tableName, 'sortby');    	if (empty($sortBy) === false) {      	return $sortBy;     	} else {    		return ''; 		    	}  	}    }  	/**	 * Builds a basic configuration from the TCA.	 *	 * @param array $fullFieldName The full field name	 *	 * return array The basic field configuration	 */  public static function buildBasicConfigurationFromTCA($fullFieldName) {  	$fullFieldNameParts = explode('.', $fullFieldName);  	if (count($fullFieldNameParts) == 1) {  		return array();  	}  	// Gets the field configuration from the TCA    $fieldConfiguration = self::getTcaConfigFieldFromFullFieldName($fullFieldName);        // Builds the type    switch($fieldConfiguration['type']) {      case 'input':        $fieldType = 'String';        break;      case 'check':        $fieldType = 'Checkbox';        break;      case 'check_4':      case 'check_10':        $fieldType = 'Checkboxes';        break;      case 'date':        $fieldType = 'Date';        break;      case 'datetime':        $fieldType = 'DateTime';        break;      case 'files':        $fieldType = 'Files';        break;      case 'integer':        $fieldType = 'Integer';        break;      case 'graph':        $fieldType = 'Graph';        break;      case 'link':        $fieldType = 'Link';        break;      case 'radio':        $fieldType = 'RadioButtons';        break;      case 'rel':        if ($fieldConfiguration['conf_rel_type'] == 'group') {          $fieldType = 'RelationManyToManyAsSubform';        } elseif ($fieldConfiguration['conf_relations'] > 1) {          $fieldType = 'RelationManyToManyAsDoubleSelectorbox';        } else {          $fieldType = 'RelationOneToManyAsSelectorbox';        }        break;      case 'select':      	if (!empty($fieldConfiguration['foreign_table'])) {          $fieldType = 'RelationOneToManyAsSelectorbox';      		      	} else {        	$fieldType = 'Selectorbox';      	}        break;      case 'ShowOnly':        $fieldType = 'ShowOnly';        break;      case 'textarea':        $fieldType = 'Text';        break;      case 'textarea_rte':        $fieldType = 'RichTextEditor';        break;      default:        $fieldType = 'Unknown';        break;    }      $fieldConfiguration = array_merge($fieldConfiguration, array(    	'tableName' => $fullFieldNameParts[0],    	'fieldName' => $fullFieldNameParts[1],      	'fieldType' => $fieldType,           	)    );      return $fieldConfiguration;  }       }?>