<?php
class Pabana_Math {
	private $mNumber;
	
	public function __construct($mNumber) {
		$this->mNumber = $mNumber;
	}
	
	public function factorial() {
		$nResult = $this->mNumber;
		for($i=($this->mNumber - 1); $i>0; $i--) {
			$nResult *= $i;
		}
		$this->mNumber = $nResult;
		return $this;
	}
}
?>