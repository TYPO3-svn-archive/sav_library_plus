<?php/****************************************************************  Copyright notice**  (c) 2011 Laurent Foulloy <yolf.typo3@orange.fr>*  All rights reserved**  This script is part of the TYPO3 project. The TYPO3 project is*  free software; you can redistribute it and/or modify*  it under the terms of the GNU General Public License as published by*  the Free Software Foundation; either version 2 of the License, or*  (at your option) any later version.**  The GNU General Public License can be found at*  http://www.gnu.org/copyleft/gpl.html.**  This script is distributed in the hope that it will be useful,*  but WITHOUT ANY WARRANTY; without even the implied warranty of*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the*  GNU General Public License for more details.**  This copyright notice MUST APPEAR in all copies of the script!***************************************************************//** * Query configuration manager * * @package SavLibraryPlus * @version $ID:$ */class Tx_SavLibraryPlus_Managers_QueryConfigurationManager {  /**   * The query configuration   *   * @var array   */  protected $queryConfiguration;         /**   * Injects the query configuration   *   * @param array $queryConfiguration   *   * @return none   */  public function injectQueryConfiguration($queryConfiguration) {    $this->queryConfiguration = $queryConfiguration;  }    /**   * Gets the main table.   *   * @param none   *   * @return string   */  public function getMainTable() {    return $this->queryConfiguration['mainTable'];  }  /**   * Gets the foreign tables.   *   * @param none   *   * @return string   */  public function getForeignTables() {    if (empty($this->queryConfiguration['foreignTables'])) {      return '';    } else {      return $this->queryConfiguration['foreignTables'];    }  }  /**   * Gets the SELECT clause.   *   * @param none   *   * @return string   */  public function getSelectClause() {    if (empty($this->queryConfiguration['selectClause'])) {      return '*';    } else {      return $this->queryConfiguration['selectClause'];    }  }      /**   * Gets the aliases.   *   * @param none   *   * @return string   */  public function getAliases() {    if (empty($this->queryConfiguration['aliases'])) {      return '';    } else {      return $this->queryConfiguration['aliases'];    }  }  /**   * Gets the WHERE Clause.   *   * @param none   *   * @return string   */  public function getWhereClause() {      // If a WhereTag is used, its WHERE Clause overrides the configuration one    $whereTagKey = Tx_SavLibraryPlus_Managers_UriManager::getWhereTagKey();    if (empty($whereTagKey) === false) {      $whereTag = $this->getWhereTag($whereTagKey);      if (isset($whereTag['whereClause'])) {        return $whereTag['whereClause'];      }    }        // Returns the configuration WHERE clause    if (empty($this->queryConfiguration['whereClause'])) {      return '1';    } else {      return $this->queryConfiguration['whereClause'];    }  }  /**   * Gets the GROUP BY Clause.   *   * @param none   *   * @return string   */  public function getGroupByClause() {    if (empty($this->queryConfiguration['groupByClause'])) {      return '';    } else {      return $this->queryConfiguration['groupByClause'];    }  }    /**   * Gets the ORDER BY Clause.   *   * @param none   *   * @return string   */  public function getOrderByClause() {    // If a WhereTag is used, its ORDER BY Clause overrides the configuration one    $whereTagKey = Tx_SavLibraryPlus_Managers_UriManager::getWhereTagKey();    if (empty($whereTagKey) === false) {      $whereTag = $this->getWhereTag($whereTagKey);      if (isset($whereTag['orderByClause'])) {        return $whereTag['orderByClause'];      }    }    // Returns the configuration ORDER BY clause if any otherwise the ORDER BY clause from the TCA    if (empty($this->queryConfiguration['orderByClause'])) {    	return Tx_SavLibraryPlus_Managers_TcaConfigurationManager::getTcaOrderByClause($this->getMainTable());    } else {      return $this->queryConfiguration['orderByClause'];    }  }    /**   * Gets the LIMIT BY Clause.   *   * @param none   *   * @return string   */  public function getLimitClause() {    if (empty($this->queryConfiguration['limitClause'])) {      return '';    } else {      return $this->queryConfiguration['limitClause'];    }  }  /**   * Gets the WHERE Tag    *   * @param $whereTagKey string The WHERE Tag key   *   * @return array or NULL   */  public function getWhereTag($whereTagKey) {    if (empty($this->queryConfiguration['whereTags'][$whereTagKey])) {      return NULL;    } else {      return $this->queryConfiguration['whereTags'][$whereTagKey];    }  }  /**   * Gets the uid part to the WHERE clause    *   * @return string   */  public function getUidPartToWhereClause() {    $uidForWhereClause = intval(Tx_SavLibraryPlus_Managers_UriManager::getUid());    $whereClausePart = ' AND ' . $this->getMainTable(). '.uid = ' . $uidForWhereClause;    	    return $whereClausePart;  }         /**   * Sets an additionalpart to the WHERE clause    *   * @param string $whereClausePart The part to add   *   * @return none   */  public function setAdditionalPartToWhereClause($whereClausePart) {   if (empty($this->queryConfiguration['additionalWhereClause'])) {  		$this->queryConfiguration['additionalWhereClause'] = $whereClausePart;  	} else {    	$this->queryConfiguration['additionalWhereClause'] .= $whereClausePart;  	}  }    /**   * Gets the additional part to the WHERE clause    *   * @return string   */  public function getAdditionalPartToWhereClause() {  	if (empty($this->queryConfiguration['additionalWhereClause'])) {  		return '';  	} else {    	return $this->queryConfiguration['additionalWhereClause'];  	}  }      }?>