<?php
class Pabana_Core {
	public $oPabanaDebug;
	private $armConfigStorage;
	
	public function __construct() {
		// Include Pabana_Core constant
		include('pabana/core/constant.core.php');
		// Include Pabana_Core function
		include('pabana/core/function.core.php');
		// Include Pabana_Debug class
		include('pabana/debug/class.debug.php');
		// Initialise Pabana_Debug class
		$this->oPabanaDebug = new Pabana_Debug(PE_ALL);
		// Start local storage
		$this->setLocalStorage();
		// Check if this version of PHP can use Pabana
		$this->checkPhpVersion();
		// Load class autoloader function
		$this->autoLoader();
		// Get default configuration
		$this->getDefaultConfiguration();
    }
	
	private function setLocalStorage() {
		$GLOBALS['pabanaInternalStorage']['PABANA_START_TIME'] = microtime(true);
		$GLOBALS['pabanaInternalStorage']['fatalException'] = 0;
		$GLOBALS['pabanaInternalStorage']['viewBridge'] = array();
		$GLOBALS['pabanaInternalStorage']['database'] = array();
		$GLOBALS['pabanaConfigStorage'] = array();
		$GLOBALS['pabanaUserStorage'] = array();
	}
	
	private function checkPhpVersion() {
		// Compare current PHP version with min require version of PHP for Pabana
		if(version_compare(PHP_VERSION, PC_PHP_MIN_VERSION, '<')) {
			// If current PHP version is less than require version, show error
			$sErrorMessage = 'Your PHP version "' . PHP_VERSION . '" is less than require version of PHP "' . PC_PHP_MIN_VERSION . '" to use Pabana';
			$this->oPabanaDebug->exception(PE_CRITICAL, 'PHP_VERSION', $sErrorMessage);
		}
	}
	
	private function autoLoader() {
		// Declare new autoload function
		spl_autoload_register(function($sAutoLoadClass) {
			if(stripos($sAutoLoadClass, 'Pabana') !== false) {
				$arsAutoLoadClass = explode('_', $sAutoLoadClass);
				$sGeneralPath = strtolower($arsAutoLoadClass[0]) . '/' . strtolower($arsAutoLoadClass[1]) . '/';
				if(count($arsAutoLoadClass) == 2) {
					$sClassPath = $sGeneralPath . 'class.' . strtolower($arsAutoLoadClass[1]) . '.php';
				} elseif(count($arsAutoLoadClass) == 3) {
					$sClassPath = $sGeneralPath . 'class.' . strtolower($arsAutoLoadClass[2]) . '.php';
				}
				if(stream_resolve_include_path($sClassPath)) {
					include($sClassPath);
				} else {
					$sErrorMessage = 'Autoloading of "' . $sAutoLoadClass . '" abort, cause "' . $sClassPath . '" file can\'t be read';
					$this->oPabanaDebug->exception(PE_ERROR, 'CLASS_AUTOLOAD', $sErrorMessage);
				}
			}
		});
	}
	
	private function getDefaultConfiguration() {
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
	}
	
	public function getConfigByFile($sConfigPath) {
		$oConfigFile = new Pabana_File($sConfigPath);
		if(!$oConfigFile->exists()) {
			$this->oPabanaDebug->exception(PE_WARNING, 'CORE_CONFIG_FILE', 'File ' . $oConfigFile . ' isn\'t found');
		}
		$sConfigFileExtension = $oConfigFile->getExtension();
		if($sConfigFileExtension == 'json') {
			$oConfigFileParse = new Pabana_Parse_Json($oConfigFile);
		} elseif($sConfigFileExtension == 'xml') {
			$oConfigFileParse = new Pabana_Parse_Xml($oConfigFile);
		} elseif($sConfigFileExtension == 'yaml') {
			$oConfigFileParse = new Pabana_Parse_Yaml($oConfigFile);
		} else {
			$sErrorMessage = '*.' . $sConfigFileExtension . ' file isn\'t accepted by <strong>Pabana_Debug->getConfigByFile()</strong>';
			$this->oPabanaDebug->exception(PE_WARNING, 'CORE_CONFIG_FILETYPE', $sErrorMessage);
		}
		$armConfig = $oConfigFileParse->toArray();
		$this->getConfigByArray($armConfig);
	}
	
	public function getConfigByArray($armConfig) {
		$GLOBALS['pabanaConfigStorage'] = $armConfig['global'] + $GLOBALS['pabanaConfigStorage'];
		if(isset($armConfig[APPLICATION_ENV])) {
			$GLOBALS['pabanaConfigStorage'] = $armConfig[APPLICATION_ENV] + $GLOBALS['pabanaConfigStorage'];
		}
	}
	
	public function run() {
		// Load php configuration form paban config file
		$this->armConfigStorage = _configStorage();
		// Include Pabana Bootstrap file
		if($this->armConfigStorage['bootstrap']['enable'] == true) {
			// Include Pabana Bootstrap
			$sBootstrapPath = $this->armConfigStorage['pabana']['application_path'] . $this->armConfigStorage['bootstrap']['path'];
			$oBootstrap = new Pabana_File($sBootstrapPath);
			if($oBootstrap->exists()) {
				$oBootstrap->import();
			} else {
				$sErrorMessage = 'Loading of "Bootstrap" abort, cause "' . $oBootstrap . '" file can\'t be read';
				$this->oPabanaDebug->exception(PE_ERROR, 'BOOTSTRAP_LOAD', $sErrorMessage);
			}
		}
		if($this->armConfigStorage['mvc']['enable'] == true) {
			// Load Pabana Router
			$oPabanaCoreRouter = new Pabana_Core_Router();
			$sControllerPath = $this->armConfigStorage['pabana']['application_path'] . $this->armConfigStorage['mvc']['module_path'] . '/' . $oPabanaCoreRouter->getModule() . '/controller.' . $oPabanaCoreRouter->getModule() . '.php';
			$oControllerFile = new Pabana_File($sControllerPath);
			if($oControllerFile->exists()) {
				$oControllerFile->import();
				$sControllerClassName = $oPabanaCoreRouter->getModule() . 'Module';
				$oController = new $sControllerClassName();
				if(method_exists($sControllerClassName, 'initController')) {
					$oController->initController();
				}
				$sControllerName = $oPabanaCoreRouter->getController() . "Controller";
				if(!method_exists($sControllerClassName, $sControllerName)) {
					$sErrorMessage = 'Loading of "' . $sControllerClassName . '" abort, cause "' . $sControllerName . '" isn\'t defined';
					$this->oPabanaDebug->exception(PE_ERROR, 'CONTROLLER_LOAD', $sErrorMessage);
				}
				$oController->{$sControllerName}();
				$oController = null;
			} else {
				header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found', true, 404); 
				$sErrorMessage = 'Loading of "controller.' . $oPabanaCoreRouter->getController() . '.php" abort, cause "' . $oControllerFile . '" file can\'t be read';
				$this->oPabanaDebug->exception(PE_ERROR, 'CONTROLLER_LOAD', $sErrorMessage);
				exit();
			}
		}
	}
}
?>