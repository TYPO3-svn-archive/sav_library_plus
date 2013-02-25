<?php/****************************************************************  Copyright notice**  (c) 2011 Laurent Foulloy <yolf.typo3@orange.fr>*  All rights reserved**  This script is part of the TYPO3 project. The TYPO3 project is*  free software; you can redistribute it and/or modify*  it under the terms of the GNU General Public License as published by*  the Free Software Foundation; either version 2 of the License, or*  (at your option) any later version.**  The GNU General Public License can be found at*  http://www.gnu.org/copyleft/gpl.html.**  This script is distributed in the hope that it will be useful,*  but WITHOUT ANY WARRANTY; without even the implied warranty of*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the*  GNU General Public License for more details.**  This copyright notice MUST APPEAR in all copies of the script!***************************************************************//** * Abstract controller. * * @package SavLibraryPlus * @version $ID:$ */abstract class Tx_SavLibraryPlus_Controller_AbstractController {  // Constants  const LIBRARY_NAME = 'sav_library_plus';    // Debug constants  const DEBUG_NONE = 0;  const DEBUG_QUERY = 1;  const DEBUG_PERFORMANCE = 2;    /**   * Variable to encode/decode form parameters   *   * @var array   */  private static $formParameters = array (    'folderKey',   	'formAction',    'formName',                'page',    'pageInSubform',      'subformFieldKey',      'subformUidForeign',    'subformUidLocal',    'uid',      'viewId',      'whereTagKey',  );  /**   * Variable to encode/decode form actions   *   * @var array   */  private static $formActions = array (    'changeFolderTab',     'changePageInSubform',    'changePageInSubformInEditMode',       'close',    'closeInEditMode',    'delete',       'deleteInSubform',      'downInSubform',       'edit',    	'export',  	'exportExecute',    	'exportLoadConfiguration',  	'exportSaveConfiguration',  	'exportDeleteConfiguration', 		'exportSubmit',  	'exportToggleDisplay',    'firstPage',    'firstPageInEditMode',      'firstPageInSubform',    'firstPageInSubformInEditMode',    	'formAdmin',      'new',    'newInSubform',      'nextPage',    'nextPageInEditMode',    'nextPageInSubform',    'nextPageInSubformInEditMode',    	'noDisplay',      'lastPage',    'lastPageInEditMode',      'lastPageInSubform',    'lastPageInSubformInEditMode',      'list',    'listInEditMode',    'previousPage',    'previousPageInEditMode',    'previousPageInSubform',    'previousPageInSubformInEditMode',    'save',  	'saveForm',    'saveFormAdmin',      'single',      'upInSubform',  );  /**   * Variable to provide alternative form action when the user is not authenticated   *   * @var array   */  private static $formActionsWhenUserIsNotAuthenticated = array (    'edit' => 'single',    'listInEditMode' => 'list',    'new' => 'list',    'newInSubform' => 'single',    'upInSubform' => 'single',    'downInSubform' => 'single',    'deleteInSubform' => 'single',    'changePageInSubformInEditMode' => 'single',    'firstPageInSubformInEditMode' => 'single',    'previousPageInSubformInEditMode' => 'single',    'nextPageInSubformInEditMode' => 'single',    'lastPageInSubformInEditMode' => 'single',  );  /**   * Variable for the comptability with the SAV Library   *   * @var array   */  private static $formActionsCompatibility = array (  	'updateFormAction' => 'formAction', 	); 	  /**   * The library configuration manager   *   * @var Tx_SavLibraryPlus_Managers_LibraryConfigurationManager   */  private $libraryConfigurationManager;  /**   * The extension configuration manager   *   * @var Tx_SavLibraryPlus_Managers_ExtensionConfigurationManager   */  private $extensionConfigurationManager;  /**   * The uri manager   *   * @var Tx_SavLibraryPlus_Managers_UriManager   */  private $uriManager;  /**   * The user manager   *   * @var Tx_SavLibraryPlus_Managers_UserManager   */  private $userManager;  /**   * The session manager   *   * @var Tx_SavLibraryPlus_Managers_SessionManager   */  private $sessionManager;  /**   * The page TypoScript manager   *   * @var Tx_SavLibraryPlus_Managers_PageTypoScriptConfigurationManager   */  private $pageTypoScriptConfigurationManager;      /**   * The querier   *   * @var Tx_SavLibraryPlus_Queriers_AbstractQuerier   */  protected $querier = NULL;  /**   * The viewer   *   * @var Tx_SavLibraryPlus_Queriers_AbstractViewer   */  protected $viewer = NULL;  /**   * Debug flag   *   * @var boolean   */  private $debug;  /**   * The form name   *   * @var string   */  private static $formName;  /**   * The short form name (without the content id)   *   * @var string   */  private static $shortFormName;        /**   * Constructor   *   * @param none   *   * @return none   */  public function __construct() {    // Creates the library configuration manager    $this->libraryConfigurationManager = t3lib_div::makeInstance('Tx_SavLibraryPlus_Managers_LibraryConfigurationManager');    $this->libraryConfigurationManager->injectController($this);    // Creates the extension configuration manager    $this->extensionConfigurationManager = t3lib_div::makeInstance('Tx_SavLibraryPlus_Managers_ExtensionConfigurationManager');    $this->extensionConfigurationManager->injectController($this);    // Creates the URI manager    $this->uriManager = t3lib_div::makeInstance('Tx_SavLibraryPlus_Managers_UriManager');    $this->uriManager->injectController($this);    // Creates the user manager    $this->userManager = t3lib_div::makeInstance('Tx_SavLibraryPlus_Managers_UserManager');    $this->userManager->injectController($this);    // Creates the session manager    $this->sessionManager = t3lib_div::makeInstance('Tx_SavLibraryPlus_Managers_SessionManager');    $this->sessionManager->injectController($this);        // Creates the page TypoScript manager    $this->pageTypoScriptConfigurationManager = t3lib_div::makeInstance('Tx_SavLibraryPlus_Managers_PageTypoScriptConfigurationManager');    $this->pageTypoScriptConfigurationManager->injectController($this);    }	/**	 * Renders the controller action	 *	 * @param none	 *	 * @return string (the whole content result, wraped as plugin)	 */	public function render() {  	// Checks if TYPO3 is greater or equal to 4.5		if (t3lib_div::compat_version('4.5') === false) {      return Tx_SavLibraryPlus_Controller_FlashMessages::translate('fatal.incorrectTypo3Version');					}		// Sets the plugin type		if ($this->setPluginType() === false)			return;					// Initializes the controller		if ($this->initialize() === false) {			$viewerClassName = 'Tx_SavLibraryPlus_Viewers_ErrorViewer';			$this->viewer = t3lib_div::makeInstance($viewerClassName);    	$this->viewer->injectController($this);    	$content = $this->viewer->render();			return $content;		}			    // Loads the sessions    $this->getSessionManager()->loadSession();    // Gets the action name.    $actionName = $this->getActionName();    // Executes the action    $content = $this->$actionName();        // Saves the sessions    $this->getSessionManager()->saveSession();        // Adds the javaScript header if required    Tx_SavLibraryPlus_Managers_AdditionalHeaderManager::addAdditionalJavaScriptHeader();        return $content;  }     	/**	 * Sets the plugin type.	 *	 * @param none	 *	 * @return boolean	 */ 	protected function setPluginType() {		// Gets the extension		$extension = $this->getExtensionConfigurationManager()->getExtension();				// Gets the content object		$contentObject = Tx_SavLibraryPlus_Managers_ExtensionConfigurationManager::getExtensionContentObject();		// Gets the user plugin flag		$userPluginFlag = Tx_SavLibraryPlus_Managers_FormConfigurationManager::getUserPluginFlag();		if (empty($userPluginFlag) || Tx_SavLibraryPlus_Managers_UriManager::hasNoCacheParameter() === true) {			// Converts the plugin to the USER_INT type			if ($contentObject->getUserObjectType() == tslib_cObj::OBJECTTYPE_USER){				$contentObject->convertToUserIntObject();							return false;			}      $extension->pi_checkCHash = false;		  $extension->pi_USER_INT_obj = 1;					} else {      // USER plugin      $extension->pi_checkCHash = true;		  $extension->pi_USER_INT_obj = 0;					}		return true;  }   	/**	 * Initializes the controller   *   * @param none   *	 * @return boolean (true if no error occurs)	 */  public function initialize() {		    // Sets debug    if ($this->debug & self::DEBUG_QUERY) {      $GLOBALS['TYPO3_DB']->debugOutput = true;    }    // Initializes the library configuration manager    if ($this->getLibraryConfigurationManager()->initialize() === false) {      return Tx_SavLibraryPlus_Controller_FlashMessages::addError('fatal.incorrectConfiguration');    }    // Sets the form name    $this->setFormName();  }  /**   * Sets the debug variable   *   * @param integer $debug   *   * @return none   */  public function setDebug($debug) {    $this->debug = $debug;  }  /**   * Gets the debug variable   *   * @param none   *   * @return integer   */  public function getDebug() {    return $this->debug;  }      /**   *  Gets the form name   *   * @param none   *   * @return string   */  public static function getFormName() {    return self::$formName;  }  /**   *  Gets the short form name   *   * @param none   *   * @return string   */  public static function getShortFormName() {    return self::$shortFormName;  }       /**   *  Sets the form name   *   * @param none   *   * @return string   */  public function setFormName() {    $formTitle = Tx_SavLibraryPlus_Managers_FormConfigurationManager::getFormTitle();    self::$shortFormName = $this->getExtensionConfigurationManager()->getExtensionKey() . '_' . strtr(strtolower($formTitle), ' -', '__');    self::$formName = self::$shortFormName . '_' .  $this->getExtensionConfigurationManager()->getContentIdentifier();  }  /**   * Gets the Library Configuration manager   *   * @param none   *   * @return Tx_SavLibraryPlus_Managers_LibraryConfigurationManager   */  public function getLibraryConfigurationManager() {    return $this->libraryConfigurationManager;  }  /**   * Gets the extension configuration manager.   *   * @param none   *   * @return Tx_SavLibraryPlus_Managers_ExtensionConfigurationManager   */  public function getExtensionConfigurationManager() {    return $this->extensionConfigurationManager;  }  /**   * Gets the uri manager.   *   * @param none   *   * @return Tx_SavLibraryPlus_Managers_UriManager   */  public function getUriManager() {    return $this->uriManager;  }  /**   * Gets the user manager.   *   * @param none   *   * @return Tx_SavLibraryPlus_Managers_UserManager   */  public function getUserManager() {    return $this->userManager;  }  /**   * Gets the session manager.   *   * @param none   *   * @return Tx_SavLibraryPlus_Managers_SessionManager   */  public function getSessionManager() {    return $this->sessionManager;  }  /**   * Gets the page TypoScript configuration manager.   *   * @param none   *   * @return Tx_SavLibraryPlus_Managers_PageTypoScriptConfigurationManager   */  public function getPageTypoScriptConfigurationManager() {    return $this->pageTypoScriptConfigurationManager;  }    /**   * Injects the querier   *   * @param Tx_SavLibraryPlus_Queriers_AbstractQuerier $querier   *   * @return none   */  public function injectQuerier($querier) {    $this->querier = $querier;  }  /**   * Gets the querier   *   * @param none   *   * @return Tx_SavLibraryPlus_Queriers_AbstractQuerier   */  public function getQuerier() {    return $this->querier;  }  /**   * Injects the viewer   *   * @param Tx_SavLibraryPlus_Queriers_AbstractViewer $viewer   *   * @return none   */  public function injectViewer($viewer) {    $this->viewer = $viewer;  }  /**   * Gets the viewer   *   * @param none   *   * @return Tx_SavLibraryPlus_Queriers_AbstractViewier   */  public function getViewer() {    return $this->viewer;  }  /**   * Gets the action name   *   * @param none   *   * @return string   */  public function getActionName() {    // Default action name.    $actionName = 'listAction';        // Gets the action from the filter if any    $selectedFilterKey = Tx_SavLibraryPlus_Managers_SessionManager::getSelectedFilterKey();    if(empty($selectedFilterKey) === false) {    	$filterActionName = Tx_SavLibraryPlus_Managers_SessionManager::getFilterField($selectedFilterKey, 'formAction');    	if (empty($filterActionName) === false) {    		$actionName = $filterActionName . 'Action';    	}    }    // Gets the action    if (Tx_SavLibraryPlus_Managers_UriManager::hasLibraryParameter()) {      // Sets the GET variables      Tx_SavLibraryPlus_Managers_UriManager::setGetVariables();      // Retrieves the action from the URI if it is the active form      if (Tx_SavLibraryPlus_Managers_UriManager::isActiveForm() === true) {        $actionName = Tx_SavLibraryPlus_Managers_UriManager::getFormAction() . 'Action';      } else {        // Retreieves the action from the        $compressedParameters = Tx_SavLibraryPlus_Managers_SessionManager::getFieldFromSession('compressedParameters');        if(!empty($compressedParameters)) {          Tx_SavLibraryPlus_Managers_UriManager::setCompressedParameters($compressedParameters);          if (Tx_SavLibraryPlus_Managers_UriManager::isActiveForm() === true) {            $actionName = Tx_SavLibraryPlus_Managers_UriManager::getFormAction() . 'Action';          }        }      }    }    // If needed, the action name is changed (compatibility with SAV Library)    if (array_key_exists($actionName, self::$formActionsCompatibility)) {    	$actionName = self::$formActionsCompatibility[$actionName];    }           return $actionName;  }  	/**	 * Builds a string to compress parameters which will be used with the	 * extension. Mainly, the method replaces the form parameter by	 * an integer. Same process occurs for form actions	 *	 * @param $parameters array (parameter array)	 *	 * @return string (compressed parameter string)	 */  public static function compressParameters($parameters) {    $out = '';    foreach($parameters as $parameterKey => $parameter) {      $key = array_search($parameterKey, self::$formParameters);      if ($key === false) {        Tx_SavLibraryPlus_Controller_FlashMessages::addError('error.unknownFormParam', array($parameterKey));        return '';      } else {        $out .= dechex($key);      }      switch ($parameterKey) {        case 'formAction':          $key = array_search($parameter, self::$formActions);          if ($key === false) {            Tx_SavLibraryPlus_Controller_FlashMessages::addError('error.unknownFormAction', array($parameter));            return '';          } else {            $out .= sprintf('%02x%s',strlen($key), $key);          }          break;        case 'formName':        	if(empty($parameter)) {        		$parameter = self::getFormName();        	}          $parameter = hash(Tx_SavLibraryPlus_Managers_ExtensionConfigurationManager::getFormNameHashAlgorithm(), $parameter);          $out .= sprintf('%02x%s',strlen($parameter), $parameter);          break;        default:          $out .= sprintf('%02x%s',strlen($parameter), $parameter);          break;      }    }    return $out;  }	/**	 * Builds an array from a compressed string	 * Mainly, the method splits the string to recover the parameter and its value	 *	 * @param $compressedString string (compressed string)	 *	 * @return array (parameter array)	 */  public static function uncompressParameters($compressedString) {    // Checks if there is a fragment in the link    $fragmentPosition = strpos($compressedString, '#');    if ($fragmentPosition !== false) {      $compressedString = substr($compressedString, 0, $fragmentPosition);    }    $out = array();    while ($compressedString) {      // Reads the form param index      list($parameter) = sscanf($compressedString, '%1x');      $formParameter = self::$formParameters[$parameter];      if (empty($formParameter)) {        Tx_SavLibraryPlus_Controller_FlashMessages::addError('error.unknownFormParam', array($parameter));      }      $compressedString = substr($compressedString, 1);      // Reads the length      list($length) = sscanf($compressedString, '%2x');      $compressedString = substr($compressedString, 2);      // Reads the value      list($value) = sscanf($compressedString, '%' . $length . 's');      $compressedString = substr($compressedString, $length);      switch($formParameter) {        case 'formAction':          $out[$formParameter] = self::$formActions[$value];          if (empty($out[$formParameter])) {            Tx_SavLibraryPlus_Controller_FlashMessages::addError('error.unknownFormAction', array($value));          }          break;        case 'formName':          $formName = self::getFormName();          if($value != hash((Tx_SavLibraryPlus_Managers_ExtensionConfigurationManager::getFormNameHashAlgorithm()), $formName)) {            return NULL;          }          $out[$formParameter] = $formName;          break;        default:          $out[$formParameter] = $value;          break;      }    }    return $out;  }	/**	 * Changes a parameter in the compressed parameters string	 *	 * @param string $compressedParameters The compressed parameters string	 * @param string $key The key of the parameter to change	 * @paraam mixed $value The value of the parameter to change	 *	 * @return string The modified compressed parameter string	 */  public static function changeCompressedParameters($compressedParameters, $key, $value) {    $uncompressParameters = self::uncompressParameters($compressedParameters);    $uncompressParameters[$key] = $value;    return self::compressParameters($uncompressParameters);  }	/**	 * Builds a link to the current page.	 *	 * @param $str string (string associated with the link)	 * @param array $formParameters (form parameters)	 * @param $cache integer (set to 1 if the page should be cached)	 * @param $addpHash boolean (if true, phash is added to the form parameters)	 *	 * @return string (link)	 */  public function buildLinkToPage($str, $formParameters, $cache = 0, $additionalParameters = array()) {  	// Gets the page id  	$pageId = $formParameters['pageId'];    	if(!empty($pageId)) {    	unset($formParameters['pageId']);   		  	}   	 	  	// Gets the form name  	$formName = ($formParameters['formName'] ? $formParameters['formName']: self::getFormName());  	  	// Builds the form parameters    $formParameters = array_merge(      array(        'formName' => $formName,      ),      $formParameters    );        // Builds the parameter array    $parameters = array(      'sav_library_plus' => self::compressParameters($formParameters),    );    $parameters = array_merge($parameters, $additionalParameters);    // Creates the link    if (empty($pageId)) {    	$out = $this->getExtensionConfigurationManager()->getExtension()->pi_linkTP($str, $parameters, $cache);    } else {    	$out = $this->getExtensionConfigurationManager()->getExtension()->pi_linkToPage($str, $pageId, $formParameters['target'], $parameters);    }    return $out;  }	/**	 * Gets the form action code.	 *	 * @param string $formAction The form action	 *	 * @return integer The form action code	 */  public static function getFormActionCode($formAction) {    return array_search($formAction, self::$formActions);  }	/**	 * Gets the form action when the user is not authenticated.	 *	 * @param string $formAction The form action	 *	 * @return string	 */  public static function getFormActionWhenUserIsNotAuthenticated($formAction) {    if (isset(self::$formActionsWhenUserIsNotAuthenticated[$formAction])) {      return self::$formActionsWhenUserIsNotAuthenticated[$formAction];    } else {      return $formAction;    }  }	/**	 * Crypts a tag.	 *	 * @param string $tag The tag	 *	 * @return string The crypted tag	 */  public static function cryptTag($tag) {    return 'a' . t3lib_div::md5int($tag);  } 	/**	 * Generates the form	 *	 * @param $formAction string (The form action)	 *	 * @return string (the whole content result, wraped as plugin)	 */	public function renderForm($formAction){		  // Checks if the user is authenticated    if($this->getUserManager()->userIsAuthenticated() === false) {      $formAction = self::getFormActionWhenUserIsNotAuthenticated($formAction);    }        // Checks if an update query was performed    $updateQuerier = ($this->querier instanceof Tx_SavLibraryPlus_Queriers_UpdateQuerier ? $this->querier : NULL);    // Calls the querier	  $querierClassName = 'Tx_SavLibraryPlus_Queriers_' . ucfirst($formAction) . 'SelectQuerier';	  $this->querier = t3lib_div::makeInstance($querierClassName);	  $this->querier->injectController($this);	  $this->querier->injectQueryConfiguration();	  $this->querier->injectUpdateQuerier($updateQuerier);	  	  $queryResult = $this->querier->processQuery();        // Calls the viewer    if ($queryResult === false) {    	$viewerClassName = 'Tx_SavLibraryPlus_Viewers_ErrorViewer';    } else {     	$viewerClassName = 'Tx_SavLibraryPlus_Viewers_' . ucfirst($formAction) . 'Viewer';    }        $this->viewer = t3lib_div::makeInstance($viewerClassName);    $this->viewer->injectController($this);    $content = $this->viewer->render();    return $content;  }  /**   * Gets the default date format:   * - From the extension TypoScript configuration if any,   * - Else from the library TypoScript configuration if any,   * - Else '%d/%m/%Y'   *   * @param none   *   * @return string   */  public function getDefaultDateFormat() {  	// Gets the default formats  	$extensionDefaultDateFormat = Tx_SavLibraryPlus_Managers_ExtensionConfigurationManager::getDefaultDateFormat();  	$libraryDefaultDateFormat = Tx_SavLibraryPlus_Managers_LibraryConfigurationManager::getDefaultDateFormat();  	// Defines which format to return  	if ($extensionDefaultDateFormat !== NULL) {  		$defaultDateFormat = $extensionDefaultDateFormat;  	} elseif ($libraryDefaultDateFormat !== NULL) {  		$defaultDateFormat = $libraryDefaultDateFormat;  		  	} else {  		$defaultDateFormat = '%d/%m/%Y';  	}  	return $defaultDateFormat;  }   /**   * Gets the dateTime format:   * - From the extension TypoScript configuration if any,   * - Else from the library TypoScript configuration if any,   * - Else '%d/%m/%Y %H:%M'   *   * @param none   *   * @return string   */  public function getDefaultDateTimeFormat() {  	// Gets the default formats  	$extensionDefaultDateTimeFormat = Tx_SavLibraryPlus_Managers_ExtensionConfigurationManager::getDefaultDateTimeFormat();  	$libraryDefaultDateTimeFormat = Tx_SavLibraryPlus_Managers_LibraryConfigurationManager::getDefaultDateTimeFormat();  	// Defines which format to return  	if ($extensionDefaultDateTimeFormat !== NULL) {  		$defaultDateTimeFormat = $extensionDefaultDateTimeFormat;  	} elseif ($libraryDefaultDateTimeFormat !== NULL) {  		$defaultDateTimeFormat = $libraryDefaultDateTimeFormat;  		  	} else {  		$defaultDateTimeFormat = '%d/%m/%Y %H:%M';  	}  	return $defaultDateTimeFormat;  } 	  }?>