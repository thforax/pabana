<?php
class Pabana_Core {
	// Public object for debug class
	public $oPabanaDebug;
	public $oPabanaGlobal;
	
	public function __construct() {
		// Include Pabana_Core constant
		include('pabana/core/constant.core.php');
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
		// Load Global method of Pabana_Core
		$this->oPabanaGlobal = new Pabana_Core_Global();
    }
	
	private function setLocalStorage() {
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
		$GLOBALS['pabanaInternalStorage'] = array(
			'database' => array(),
			'layoutBridge' => array(),
			'pabana' => array(
				'startTime' => $_SERVER['REQUEST_TIME_FLOAT'],
				'fatalException' => 0
			),
			'router' => array(),
			'viewBridge' => array()
		);
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
			// Check if autoload function is ask by Pabana
			if(stripos($sAutoLoadClass, 'Pabana') !== false) {
				// Explode class name in array
				$arsAutoLoadClass = explode('_', $sAutoLoadClass);
				// Generate directory path for class
				$sGeneralPath = strtolower($arsAutoLoadClass[0]) . '/' . strtolower($arsAutoLoadClass[1]) . '/';
				if(count($arsAutoLoadClass) == 2) {
					$sClassPath = $sGeneralPath . 'class.' . strtolower($arsAutoLoadClass[1]) . '.php';
					$sConstantPath = $sGeneralPath . 'constant.' . strtolower($arsAutoLoadClass[1]) . '.php';
				} elseif(count($arsAutoLoadClass) == 3) {
					$sClassPath = $sGeneralPath . 'class.' . strtolower($arsAutoLoadClass[2]) . '.php';
					$sConstantPath = $sGeneralPath . 'constant.' . strtolower($arsAutoLoadClass[1]) . '.php';
				}
				// If exists include class file
				if(stream_resolve_include_path($sClassPath)) {
					include($sClassPath);
				} else {
					// Show error message coz class file don't exists
					$sErrorMessage = 'Autoloading of "' . $sAutoLoadClass . '" abort, cause "' . $sClassPath . '" file can\'t be read';
					$this->oPabanaDebug->exception(PE_ERROR, 'CLASS_AUTOLOAD', $sErrorMessage);
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
					$this->oPabanaDebug->exception(PE_WARNING, 'URI_FILE_LOAD', $sErrorMessage);
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
			$this->oPabanaDebug->exception(PE_ERROR, 'BOOTSTRAP_LOAD', $sErrorMessage);
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
				$this->oPabanaDebug->exception(PE_ERROR, 'CONTROLLER_LOAD', $sErrorMessage);
			}
			$oController->{$sControllerName}();
			$oController = null;
		} else {
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found', true, 404); 
			$sErrorMessage = 'Loading of "controller.' . $sController . '.php" abort, cause "' . $oControllerFile . '" file can\'t be read';
			$this->oPabanaDebug->exception(PE_WARNING, 'CONTROLLER_LOAD', $sErrorMessage);
			exit();
		}
	}
}
?>