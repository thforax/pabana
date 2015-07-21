<?php
/**
* Pabana Framework : Core Module (http://pabana.co)
*
* Licensed under new BSD License
* For full copyright and license information, please see the LICENSE.txt
*
* @link      	http://github.com/thforax/pabana for the canonical source repository
* @copyright 	Copyright (c) 2014-2015 FuturaSoft (http://www.futurasoft.net)
* @license   	http://pabana.co/about/license New BSD License
* @version		1.0.0.0
*/

/**
 * Pabana_Core : Initialize Pabana Framework
 *
 * Core class of Pabana
 * This class start Pabana Framework
 *
 * @link http://pabana.co/documentation/class/name/core
 */
class Pabana_Core {
	// Declaration of Pabana_Debug Object
	private $_oPabanaDebug;
	
	public function __construct() {
		// Include Pabana_Core constant
		include('pabana/core/constant.core.php');
		// Include Pabana_Debug class
		include('pabana/debug/class.debug.php');
		// Initialise Pabana_Debug class
		$this->_oPabanaDebug = new Pabana_Debug(PE_ALL);
		// Start local storage
		$this->setLocalStorage();
		// Check if this version of PHP can use Pabana
		$this->checkPhpVersion();
		// Load class autoloader function
		$this->autoLoader();
    }
	
	private function setLocalStorage() {
		// Init pabana default configuration
		$GLOBALS['pabanaConfigStorage'] = array(
			'debug' => array(
				'show_level' => PE_ALL
			),
			'bootstrap' => array(
				'bootstrap_enable' => false
			),
			'mvc' => array(
				'mvc_enable' => false
			)
		);
		// Init pabana internal variable storage
		$GLOBALS['pabanaInternalStorage'] = array(
			'database' => array(),
			'debug' => null,
			'layoutBridge' => array(),
			'pabana' => array(
				'startTime' => $_SERVER['REQUEST_TIME_FLOAT'],
				'fatalException' => 0
			),
			'router' => array(),
			'viewBridge' => array()
		);
		// Init pabana user variable storage
		$GLOBALS['pabanaUserStorage'] = array();
	}
	
	private function checkPhpVersion() {
		// Compare current PHP version with min require version of PHP for Pabana
		if(version_compare(PHP_VERSION, PC_PHP_MIN_VERSION, '<')) {
			// If current PHP version is less than require version, show error
			$sErrorMessage = 'Your PHP version "' . PHP_VERSION . '" is less than require version of PHP "' . PC_PHP_MIN_VERSION . '" to use Pabana';
			$this->_oPabanaDebug->exception(PE_CRITICAL, 'PHP_VERSION', $sErrorMessage);
		}
	}
	
	private function autoLoader() {
		// Declare new autoload function
		spl_autoload_register(function($sAutoLoadClass) {
			// Check if autoload function is ask by Pabana
			if(stripos($sAutoLoadClass, 'Pabana') !== false) {
				// Explode class name in array
				$arsAutoLoadClass = explode('_', $sAutoLoadClass);
				// Generate directory path for class
				$sGeneralPath = strtolower($arsAutoLoadClass[0]) . '/' . strtolower($arsAutoLoadClass[1]) . '/';
				if(count($arsAutoLoadClass) == 2) {
					// Autoloader class name eq Pabana_Module
					$sClassPath = $sGeneralPath . 'class.' . strtolower($arsAutoLoadClass[1]) . '.php';
					$sConstantPath = $sGeneralPath . 'constant.' . strtolower($arsAutoLoadClass[1]) . '.php';
				} elseif(count($arsAutoLoadClass) == 3) {
					// Autoloader class name eq Pabana_Module_Class
					$sClassPath = $sGeneralPath . 'class.' . strtolower($arsAutoLoadClass[2]) . '.php';
					$sConstantPath = $sGeneralPath . 'constant.' . strtolower($arsAutoLoadClass[1]) . '.php';
				}
				// If exists include class file
				if(stream_resolve_include_path($sClassPath)) {
					include($sClassPath);
				} else {
					// Show error message coz class file don't exists
					$sErrorMessage = 'Autoloading of "' . $sAutoLoadClass . '" abort, cause "' . $sClassPath . '" file can\'t be read';
					$this->_oPabanaDebug->exception(PE_ERROR, 'CLASS_AUTOLOAD', $sErrorMessage);
				}
			} else {
				// Try to call user defined autoloader
				if(function_exists('customAutoLoader')) {
					// If exists call it
					customAutoLoader($sAutoLoadClass);
				}
			}
		});
	}
	
	public function getConfigByFile($sConfigPath) {
		// Initialize Pabana_File class for config file
		$oConfigFile = new Pabana_File($sConfigPath);
		// Check if this file exists
		if(!$oConfigFile->exists()) {
			$this->_oPabanaDebug->exception(PE_WARNING, 'CORE_CONFIG_FILE', 'File ' . $oConfigFile . ' isn\'t found');
		}
		// Get extension of this file
		$sConfigFileExtension = $oConfigFile->getExtension();
		if($sConfigFileExtension == 'json') {
			$oConfigFileParse = new Pabana_Parse_Json($oConfigFile);
		} elseif($sConfigFileExtension == 'xml') {
			$oConfigFileParse = new Pabana_Parse_Xml($oConfigFile);
		} elseif($sConfigFileExtension == 'yaml') {
			$oConfigFileParse = new Pabana_Parse_Yaml($oConfigFile);
		} else {
			$sErrorMessage = '*.' . $sConfigFileExtension . ' file isn\'t accepted by <strong>Pabana_Debug->getConfigByFile()</strong>';
			$this->_oPabanaDebug->exception(PE_WARNING, 'CORE_CONFIG_FILETYPE', $sErrorMessage);
		}
		// Put parse content on array
		$armConfig = $oConfigFileParse->toArray();
		// Merge config
		$this->getConfigByArray($armConfig);
	}
	
	public function getConfigByArray($armConfig) {
		// Merge config on array with default configuration
		$GLOBALS['pabanaConfigStorage'] = $armConfig['global'] + $GLOBALS['pabanaConfigStorage'];
		// Check if config file have a config dependant of environnement
		if(isset($armConfig[APPLICATION_ENV])) {
			// Merge environnement config on array with default configuration
			$GLOBALS['pabanaConfigStorage'] = $armConfig[APPLICATION_ENV] + $GLOBALS['pabanaConfigStorage'];
		}
		// Init debug with config param
		$this->initDebug();
	}
	
	public function initDebug() {
		$this->_oPabanaDebug = null;
		$nDebugShow = 0;
		if(isset($GLOBALS['pabanaConfigStorage']['debug']['show_level'])) {
			$nDebugShow = $GLOBALS['pabanaConfigStorage']['debug']['show_level'];
		}
		$nDebugFile = 0;
		if(isset($GLOBALS['pabanaConfigStorage']['debug']['file_level'])) {
			$nDebugFile = $GLOBALS['pabanaConfigStorage']['debug']['file_level'];
		}
		$nDebugDatabase = 0;
		if(isset($GLOBALS['pabanaConfigStorage']['debug']['database_level'])) {
			$nDebugDatabase = $GLOBALS['pabanaConfigStorage']['debug']['database_level'];
		}
		$bDebugEnvironment = true;
		if(isset($GLOBALS['pabanaConfigStorage']['debug']['environment'])) {
			$bDebugEnvironment = $GLOBALS['pabanaConfigStorage']['debug']['environment'];
		}
		$bDebugBacktrace = true;
		if(isset($GLOBALS['pabanaConfigStorage']['debug']['backtrace'])) {
			$bDebugBacktrace = $GLOBALS['pabanaConfigStorage']['debug']['backtrace'];
		}
		$bDebugLink = true;
		if(isset($GLOBALS['pabanaConfigStorage']['debug']['link'])) {
			$bDebugLink = $GLOBALS['pabanaConfigStorage']['debug']['link'];
		}
		$this->_oPabanaDebug = new Pabana_Debug($nDebugShow, $nDebugFile, $nDebugDatabase, $bDebugEnvironment, $bDebugBacktrace, $bDebugLink);
		$GLOBALS['pabanaInternalStorage']['debug'] = $this->_oPabanaDebug;
	}
	
	public function run() {
		// Check if a bootstrap file must be load
		if($GLOBALS['pabanaConfigStorage']['bootstrap']['enable'] == true) {
			$this->runBootstrap();
		}
		// Check if Pabana is launch on MVC mode
		if($GLOBALS['pabanaConfigStorage']['mvc']['enable'] == true) {
			$oPabanaCoreRouter = new Pabana_Core_Router('mvc');
			$this->runMvc($GLOBALS['pabanaInternalStorage']['router']['module'], $GLOBALS['pabanaInternalStorage']['router']['controller']);
		} else {
			//Check if Pabana is launch on router mode
			if($GLOBALS['pabanaConfigStorage']['bootstrap']['router'] == true) {
				$sUriFile = $GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . $GLOBALS['pabanaInternalStorage']['router']['uri'];
				$oUriFile = new Pabana_File($sUriFile);
				if($oUriFile->exists()) {
					$oUriFile->import();
				} else {
					header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found', true, 404); 
					$sErrorMessage = 'Loading of "URI_FILE" abort, cause "' . $oUriFile . '" file can\'t be read';
					$this->_oPabanaDebug->exception(PE_WARNING, 'URI_FILE_LOAD', $sErrorMessage);
					exit();
				}
			}
		}
	}
	
	private function runBootstrap() {
		// Include Pabana Bootstrap
		$sBootstrapPath = $GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . $GLOBALS['pabanaConfigStorage']['bootstrap']['path'];
		$oBootstrapFile = new Pabana_File($sBootstrapPath);
		if($oBootstrapFile->exists()) {
			$oBootstrapFile->import();
		} else {
			$sErrorMessage = 'Loading of "Bootstrap" abort, cause "' . $oBootstrapFile . '" file can\'t be read';
			$this->_oPabanaDebug->exception(PE_ERROR, 'BOOTSTRAP_LOAD', $sErrorMessage);
		}
	}
	
	private function runMvc($sModule, $sController) {
		$sControllerPath = $GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . $GLOBALS['pabanaConfigStorage']['mvc']['module_path'] . '/' . $sModule . '/controller.' . $sModule . '.php';
		$oControllerFile = new Pabana_File($sControllerPath);
		if($oControllerFile->exists()) {
			$oControllerFile->import();
			$sControllerClassName = $sModule . 'Module';
			$oController = new $sControllerClassName();
			if(method_exists($sControllerClassName, 'initController')) {
				$oController->initController();
			}
			$sControllerName = $sController . "Controller";
			if(!method_exists($sControllerClassName, $sControllerName)) {
				$sErrorMessage = 'Loading of "' . $sControllerClassName . '" abort, cause "' . $sControllerName . '" isn\'t defined';
				$this->_oPabanaDebug->exception(PE_ERROR, 'CONTROLLER_LOAD', $sErrorMessage);
			}
			$oController->{$sControllerName}();
			$oController = null;
		} else {
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found', true, 404); 
			$sErrorMessage = 'Loading of "controller.' . $sController . '.php" abort, cause "' . $oControllerFile . '" file can\'t be read';
			$this->_oPabanaDebug->exception(PE_WARNING, 'CONTROLLER_LOAD', $sErrorMessage);
			exit();
		}
	}
}
?>