<?php
class Pabana_Http {
	public function setLocation($sUrl, $bExit = true) {
		header("Location: " . $sUrl);
		if($bExit === true) {
			exit();
		}
		return $this;
	}
}
?>