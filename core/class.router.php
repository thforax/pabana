<?php
Class Pabana_Core_Router {
	private $sModule;
	private $sController;
	private $arGetVariable = array();
	private $sUri;
	
	public function __construct($sRoutingType = 'default') {
		$this->sUri = $_SERVER['REQUEST_URI'];
		if($sRoutingType == 'default') {
			$this->defaultRouting();
		} else {
			$this->prepareUriToMvc();
			$this->mvcRouting();
		}
	}
	
	private function defaultRouting() {
		$GLOBALS['pabanaInternalStorage']['router']['uri'] = $this->sUri;
	}
	
	private function prepareUriToMvc() {
		$arsReplaceUri = array('?', '=', '&');
		$this->sUri = str_replace($arsReplaceUri, '/', $this->sUri);
	}
	
	private function mvcRouting() {
		$sModule = 'index';
		$sController = 'index';
		$arsUri = explode('/', $this->sUri);
		$nSplitUri = count($arsUri);
		if($nSplitUri >= 2 && !empty($arsUri[1])) {
			$sModule = $arsUri[1];
		}
		if($nSplitUri >= 3 && !empty($arsUri[2])) {
			$sController = $arsUri[2];
		}
		$sKeyGetVariable = '';
		for($i=3; $i<$nSplitUri; $i++) {
			if(($i+1)%2 == 0) {
				$sKeyGetVariable = urldecode($arsUri[$i]);
				$this->arGetVariable[$sKeyGetVariable] = null;
			} else {
				$this->arGetVariable[$sKeyGetVariable] = urldecode($arsUri[$i]);
			}
		}
		$_GET = $this->arGetVariable;
		$GLOBALS['pabanaInternalStorage']['router']['module'] = $sModule;
		$GLOBALS['pabanaInternalStorage']['router']['controller'] = $sController;
	}
	
	
}
?>