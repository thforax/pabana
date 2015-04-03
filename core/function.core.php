<?php
function _e() {
	$nArgs = func_num_args();
    $mArgs = func_get_args();
    for ($i = 0; $i < $nArgs; $i++) {
		print_r($mArgs[$i]);
    }	
}

function _h() {
	$nArgs = func_num_args();
    $mArgs = func_get_args();
    for ($i = 0; $i < $nArgs; $i++) {
		$sValue = print_r($mArgs[$i], true);
		echo htmlentities($sValue, ENT_QUOTES);
    }	
}

function _storage($sType, $sIndex = null) {
	if(empty($sIndex)) {
		return $GLOBALS[$sType];
	} else {
		return $GLOBALS[$sType][$sIndex];
	}
}

function _internalStorage($sIndex = null) {
	return _storage('pabanaInternalStorage', $sIndex);
}

function _configStorage($sIndex = null) {
	return _storage('pabanaConfigStorage', $sIndex);
}

function _userStorage($sIndex = null) {
	return _storage('pabanaUserStorage', $sIndex);
}
?>