<?php
Class Pabana_Core_Global {
	public function getInternalStorage($sKey) {
		return $GLOBALS['pabanaInternalStorage']['pabana'][$sKey];
	}
	
	public function setInternalStorage($sKey, $sValue) {
		$GLOBALS['pabanaInternalStorage']['pabana'][$sKey] = $sValue;
	}
}
?>