<?php
class Pabana_String {
	private $sVariable;
	
	public function __construct($sVariable = '') {
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
		return $this;
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
		$this->sVariable = htmlentities($this->sVariable);
		return $this;
    }
	
	public function toArray($sDelimiter) {
		return explode($sDelimiter, $this->sVariable);
    }
	
	public function toLowerCase() {
		$this->sVariable = strtolower($this->sVariable);
		return $this;
    }
	
	public function toUpperCase() {
		$this->sVariable = strtoupper($this->sVariable);
		return $this;
    }
	
	public function createPassword($nCharNbr, $nCharAllow = 15, $sPersonnalChar = '') {
		$sChar = '';
		if(!empty($sPersonnalChar)) {
			$sChar = $sPersonnalChar;
		} else {
			if($nCharAllow >=8) {
				$sChar .= '~!@#$%^&*()-_=+[]{};:,.<>/?';
				$nCharAllow -= 8;
			}
			if($nCharAllow >=4) {
				$sChar .= implode('', range(0, 9));
				$nCharAllow -= 4;
			}
			if($nCharAllow >=2) {
				$sChar .= implode('', range('A', 'Z'));
				$nCharAllow -= 2;
			}
			if($nCharAllow >=1) {
				$sChar .= implode('', range('a', 'z'));
				$nCharAllow -= 1;
			}
		}
		$sPassword = '';
		$nCharLen = strlen($sChar);
		for($i=0; $i<$nCharNbr; $i++) {
			$nCharPos = mt_rand(0,($nCharLen-1));
			$sPassword .= $sChar[$nCharPos];
		}
		$this->sVariable = $sPassword;
		return $this;
	}
	
	public function passwordHash($sPassword) {
		$this->sVariable = password_hash($sPassword, PASSWORD_DEFAULT);
		return $this;
	}
	
	public function passwordVerify($sPassword, $sHash) {
		return password_verify($sPassword, $sHash);
	}
	
	public function fluf() {
		$arsReturnText = array(
			'Invite your Grandpa to code with Pabana, you will get a GrandPabana',
			'&lt;?php _pe("Pabana rocks"); ?&gt;',
			'Without Herobrine',
			'(PHP + Pabana) == <3'
		);
		shuffle($arsReturnText);
		$this->sVariable = $arsReturnText[0];
		return $this;
	}
	
	public function toNonAccented($sCurrentCharset = 'utf-8') {
		$this->sVariable = iconv($sCurrentCharset, 'ASCII//TRANSLIT//IGNORE', $this->sVariable);
		return $this;
	}
}
?>