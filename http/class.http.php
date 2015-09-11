<?php
class Pabana_Http {
	public function isAjax() {
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		} else {
			return false;
		}
	}
	
	public function isConsole() {
		if(php_sapi_name() == 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function setLocation($sUrl, $bExit = true) {
		header("Location: " . $sUrl);
		if($bExit === true) {
			exit();
		}
		return $this;
	}
	
	public function getIpAddress() {
		$sIpAddress = $_SERVER['REMOTE_ADDR'];
		if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
			$sIpAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
		}
		return $sIpAddress;
	}
	
	public function isPublicIpAddress($sIpAddress = '') {
		if(empty($sIpAddress)) {
			$sIpAddress = $this->getIpAddress();
		}
		return filter_var($sIpAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE);
	}
}
?>