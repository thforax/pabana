<?php
class Pabana_Localization {
	public function changeCharset($mValue, $sInCharset, $sOutCharset) {
		$mReturn = $mValue;
		if(is_string($mValue)) {
			$mReturn = iconv($sInCharset, $sOutCharset . "//TRANSLIT//IGNORE", $mValue);
		} else if(is_array($mValue)) {
			$mReturn = array();
			foreach($mValue as $mArrayKey=>$mArrayValue) {
				$mArrayKey = $this->changeCharset($mArrayKey, $sInCharset, $sOutCharset);
				$mArrayValue = $this->changeCharset($mArrayValue, $sInCharset, $sOutCharset);
				$mReturn[$mArrayKey] = $mArrayValue;
			}
		}
		return $mReturn;
	}
}
?>