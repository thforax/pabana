<?php
class Pabana_Variable {
	private $oPabanaDebug;
	private $mVariable;
	
	public function __construct($mVariable) {
		$this->mVariable = $mVariable;
    }
	
	public function __toString()
    {
        return $this->get();
    }
	
	public function set($mVariable) {
		$this->mVariable = $mVariable;
    }
	
	public function get() {
		return $this->mVariable;
	}
	
	public function isArray() {
		return is_array($this->mVariable);
    }
	
	public function isBool() {
		return is_bool($this->mVariable);
    }
	
	public function isEmpty() {
		return empty($this->mVariable);
    }
	
	public function isFloat() {
		return is_float($this->mVariable);
    }
	
	public function isInt() {
		return is_integer($this->mVariable);
    }
	
	public function isNull() {
		return is_null($this->mVariable);
    }
	
	/*public function isSet() {
		return isset($this->mVariable);
    }*/
	
	public function isString() {
		return is_string($this->mVariable);
    }
	
	public function parseBool() {
		return boolval($this->mVariable);
    }
	
	public function parseFloat() {
		return floatval($this->mVariable);
    }
	
	public function parseInt() {
		return intval($this->mVariable);
    }
	
	public function parseString() {
		return strval($this->mVariable);
    }
}
?>