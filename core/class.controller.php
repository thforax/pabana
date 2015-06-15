<?php
Class Pabana_Core_Controller {
	public $oView;
	private $bViewEnable = 1;
	private $bLayoutEnable = 0;
	private $sLayout;
	private $sView;
	private $sControllerContent = '';
	private $sHtmlContent = '';
	
	final public function __construct() {
		$this->oView = new Pabana_Dom();
		$this->sView = $GLOBALS['pabanaInternalStorage']['router']['controller'];
		if($GLOBALS['pabanaConfigStorage']['layout']['enable'] == "true") {
			$this->bLayoutEnable = 1;
			$this->sLayout = $GLOBALS['pabanaConfigStorage']['layout']['default'];
		}
		if($this->bLayoutEnable == 1) {
			include($GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . $GLOBALS['pabanaConfigStorage']['layout']['path'] . '/' . $this->sLayout . '/layout.init.php');
		}
		ob_start();
	}
	
	final public function __destruct() {
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
				foreach($GLOBALS['pabanaInternalStorage']['layoutBridge'] as $sVariableName=>$mVariable) {
					${$sVariableName} = $mVariable;
				}
				ob_start();
				$sLayoutPath = $GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . $GLOBALS['pabanaConfigStorage']['layout']['path'] . '/';
				$sLayoutPath .= $this->sLayout . '/layout.' . $this->sLayout . '.php';
				include($sLayoutPath);
				$this->sHtmlContent = ob_get_contents();
				ob_end_clean();
			} else {
				$this->sHtmlContent = $this->sControllerContent;
			}
			// Show generate HTML code
			echo $this->sHtmlContent;
		} else {
			ob_end_flush();
		}
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
	
	final public function getContent() {
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