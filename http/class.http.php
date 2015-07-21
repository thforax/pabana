<?php
class Pabana_Http {
	public function setLocation($sUrl, $bExit = true) {
		header("Location: " . $sUrl);
		if($bExit === true) {
			exit();
		}
		return $this;
	}
	
	public function isAjax() {
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		} else {
			return false;
		}
	}
}
?>