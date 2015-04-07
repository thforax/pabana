<?php
Class Pabana_Core_Controller {
	public $oView;
	private $bViewEnable = 1;
	private $bLayoutEnable = 0;
	private $sLayout;
	
	final public function __construct() {
		$this->oView = new Pabana_Core_View();
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
				$sViewPath = $GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . '/application/module/' . $GLOBALS['pabanaInternalStorage']['router']['module'] . '/view/view.' . $GLOBALS['pabanaInternalStorage']['router']['controller'] . '.php';
				include($sViewPath);
			}
			$sControllerCode = ob_get_contents();
			ob_end_clean();
			if($this->bLayoutEnable == 1) {
				ob_start();
				include($GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . $GLOBALS['pabanaConfigStorage']['layout']['path'] . '/' . $this->sLayout . '/layout.' . $this->sLayout . '.php');
				$sLayoutCode = ob_get_contents();
				ob_end_clean();
				$sHtmlCode = str_replace('|s|CONTENT|s|', $sControllerCode, $sLayoutCode);
			} else {
				$sHtmlCode = $sControllerCode;
			}
			// Show generate HTML code
			echo $sHtmlCode;
		} else {
			ob_end_flush();
		}
	}
	
	final public function setLayout($sLayoutName) {
		$this->sLayout = $sLayoutName;
		include($GLOBALS['pabanaConfigStorage']['pabana']['application_path'] . $GLOBALS['pabanaConfigStorage']['layout']['path'] . '/' . $this->sLayout . '/layout.init.php');
	}
	
	final public function enableLayout() {
		$this->bLayoutEnable = 1;
	}
	
	final public function disableLayout() {
		$this->bLayoutEnable = 0;
	}
	
	final public function toView($sVariableName, $mVariable) {
		$GLOBALS['pabanaInternalStorage']['viewBridge'][$sVariableName] = $mVariable;
	}
}
?>