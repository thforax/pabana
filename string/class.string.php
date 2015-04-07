<?php
class Pabana_String {
	private $sVariable;
	
	public function __construct($sVariable) {
		$this->sVariable = $sVariable;
    }
	
	public function __toString() {
        return $this->get();
    }
	
	public function set($sVariable) {
		$this->sVariable = $sVariable;
    }
	
	public function get() {
		return $this->sVariable;
	}
	
	public function concat($sConcat) {
		$this->sVariable .= $sConcat;
    }
	
	public function contains($sSearch) {
		if(strpos($this->sVariable, $sSearch) === false) {
			return false;
		} else {
			return true;
		}
    }
	
	public function isEmpty() {
		return empty($this->sVariable);
    }
	
	public function length() {
		return strlen($this->sVariable);
    }
	
	public function escape() {
		return htmlentities($this->sVariable);
    }
	
	public function toArray($sDelimiter) {
		return explode($sDelimiter, $this->sVariable);
    }
	
	public function toLowerCase($sDelimiter) {
		return strtolower($this->sVariable);
    }
	
	public function toUpperCase($sDelimiter) {
		return strtoupper($this->sVariable);
    }
}
?>