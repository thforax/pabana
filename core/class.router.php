<?php
Class Pabana_Core_Router {
	private $sModule;
	private $sController;
	private $arGetVariable = array();
	
	public function __construct() {
		$this->sModule = 'index';
		$this->sController = 'index';
		$arsUri = explode('/', $_SERVER['REQUEST_URI']);
		$nSplitUri = count($arsUri);
		if($nSplitUri >= 2 && !empty($arsUri[1])) {
			$this->sModule = $arsUri[1];
		}
		if($nSplitUri >= 3 && !empty($arsUri[2])) {
			$this->sController = $arsUri[2];
		}
		$sKeyGetVariable = '';
		for($i=3; $i<$nSplitUri; $i++) {
			if(empty($arsUri[$i])) {
				break;
			}
			if(($i+1)%2 == 0) {
				$sKeyGetVariable = urldecode($arsUri[$i]);
				$this->arGetVariable[$sKeyGetVariable] = null;
			} else {
				$this->arGetVariable[$sKeyGetVariable] = urldecode($arsUri[$i]);
			}
		}
		$_GET = $this->arGetVariable;
		$GLOBALS['pabanaInternalStorage']['router']['module'] = $this->sModule;
		$GLOBALS['pabanaInternalStorage']['router']['controller'] = $this->sController;
	}
	
	public function getModule() {
		return $this->sModule;
	}
	
	public function getController() {
		return $this->sController;
	}
}
?>