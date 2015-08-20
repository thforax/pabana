<?php
class Pabana_String {
	private $_sVariable;
	
	public function __construct($sVariable = '') {
		$this->_sVariable = $sVariable;
    }
	
	public function __toString() {
        return $this->get();
    }
	
	public function get() {
		return $this->_sVariable;
	}
	
	public function set($sVariable) {
		$this->_sVariable = $sVariable;
    }
	
	public function concat($sConcat) {
		$this->_sVariable .= $sConcat;
		return $this;
    }
	
	public function contains($sSearch) {
		if(strpos($this->_sVariable, $sSearch) === false) {
			return false;
		} else {
			return true;
		}
    }
	
	public function escape() {
		$this->_sVariable = htmlentities($this->_sVariable);
		return $this;
    }
	
	public function fluf() {
		$arsReturnText = array(
			'Invite your Grandpa to code with Pabana, you will get a GrandPabana',
			'&lt;?php _pe("Pabana rocks"); ?&gt;',
			'Without Herobrine',
			'(PHP + Pabana) == <3'
		);
		shuffle($arsReturnText);
		$this->_sVariable = $arsReturnText[0];
		return $this;
	}
	
	public function isEmpty() {
		return empty($this->_sVariable);
    }
	
	public function length() {
		return strlen($this->_sVariable);
    }
	
	public function matches($sRegex) {
		return preg_match($sRegex, $this->_sVariable);
	}
	
	public function replace($sTarget, $sReplacement) {
		$this->_sVariable = str_replace($sTarget, $sReplacement, $this->_sVariable);
		return $this;
	}
	
	public function substring($nStart, $nLength = false) {
		if($nLength === false) {
			return substr($this->_sVariable, $nStart);
		} else {
			return substr($this->_sVariable, $nStart, $nLength);
		}
	}
	
	public function toArray($sDelimiter) {
		return explode($sDelimiter, $this->_sVariable);
    }
	
	public function toLowerCase() {
		$this->_sVariable = strtolower($this->_sVariable);
		return $this;
    }
	
	public function toUpperCase() {
		$this->_sVariable = strtoupper($this->_sVariable);
		return $this;
    }
	
	public function trim() {
		return trim($this->_sVariable);
	}
}
?>