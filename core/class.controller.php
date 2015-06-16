<?php
Class Pabana_Core_Controller {
	public $oView;
	private $oLayout;
	private $bViewEnable = 1;
	private $bLayoutEnable = 0;
	private $sLayout;
	private $sView;
	private $sControllerContent = '';
	private $sHtmlContent = '';
	
	final public function __construct() {
		// Initialize Dom class
		$this->oView = new Pabana_Dom();
		$this->sView = $GLOBALS['pabanaInternalStorage']['router']['controller'];
		// Check if layout are enable
		if($GLOBALS['pabanaConfigStorage']['layout']['enable'] == "true") {
			$this->oLayout = new Pabana_Dom();
			$this->bLayoutEnable = 1;
			$this->sLayout = $GLOBALS['pabanaConfigStorage']['layout']['default'];
		}
		ob_start();
	}
	
	final public function __destruct() {
		// Check if a fatal exception was catched by Pabana_Debug
		if($GLOBALS['pabanaInternalStorage']['pabana']['fatalException'] != 1 ) {
			if($this->bViewEnable == 1) {
				foreach($GLOBALS['pabanaInternalStorage']['viewBridge'] as $sVariableName=>$mVariable) {
					${$sVariableName} = $mVariable;
				}
				$sViewPath = $GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . '/application/module/' . $GLOBALS['pabanaInternalStorage']['router']['module'] . '/view/view.' . $this->sView . '.php';
				include($sViewPath);
			}
			$this->sControllerContent = ob_get_contents();
			ob_end_clean();
			if($this->bLayoutEnable == 1) {
				$this->getLayout();
			} else {
				$this->sHtmlContent = $this->sControllerContent;
			}
			// Show generate HTML code
			echo $this->sHtmlContent;
		} else {
			ob_end_flush();
		}
	}
	
	private function getLayout() {
		foreach($GLOBALS['pabanaInternalStorage']['layoutBridge'] as $sVariableName=>$mVariable) {
			${$sVariableName} = $mVariable;
		}
		ob_start();
		$sLayoutPath = $GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . $GLOBALS['pabanaConfigStorage']['layout']['path'] . '/';
		$sLayoutInit = $sLayoutPath . $this->sLayout . '/layout.init.php';
		$sLayoutFile = $sLayoutPath . $this->sLayout . '/layout.' . $this->sLayout . '.php';
		include($sLayoutInit);
		$this->oLayout->mergeDom($this->oView);
		include($sLayoutFile);
		$this->sHtmlContent = ob_get_contents();
		ob_end_clean();
	}
	
	final public function disableLayout() {
		$this->bLayoutEnable = 0;
	}
	
	final public function disableView() {
		$this->bViewEnable = 0;
	}
	
	final public function enableLayout() {
		$this->bLayoutEnable = 1;
	}
	
	final public function enableView() {
		$this->bViewEnable = 1;
	}
	
	final public function getControllerContent() {
		return $this->sControllerContent;
	}
	
	final public function getModel($sModelName) {
		$sModelClassName = $sModelName . 'Model';
		if(!class_exists($sModelClassName)) {
			include($GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . $GLOBALS['pabanaConfigStorage']['mvc']['model_path'] . '/model.' . $sModelName . '.php');
		}
		return new $sModelClassName();
	}
	
	final public function getLayoutPart($sPartName) {
		include($GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . $GLOBALS['pabanaConfigStorage']['layout']['path'] . '/' . $this->sLayout . '/layout.' . $sPartName . '.php');
	}
	
	final public function setLayout($sLayoutName) {
		$this->oView = new Pabana_Dom();
		$this->sLayout = $sLayoutName;
		include($GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . $GLOBALS['pabanaConfigStorage']['layout']['path'] . '/' . $this->sLayout . '/layout.init.php');
	}
	
	final public function setView($sViewName) {
		$this->sView = $sViewName;
	}
	
	final public function toLayout($sVariableName, $mVariable) {
		$GLOBALS['pabanaInternalStorage']['layoutBridge'][$sVariableName] = $mVariable;
	}
	
	final public function toView($sVariableName, $mVariable) {
		$GLOBALS['pabanaInternalStorage']['viewBridge'][$sVariableName] = $mVariable;
	}
}
?>