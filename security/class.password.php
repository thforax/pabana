<?php
class Pabana_Security_Password {
	private $_sPassword;
	
	public function __construct($sPassword = '') {
		$this->_sPassword = $sPassword;
    }
	
	public function __toString() {
        return $this->get();
    }
	
	public function get() {
		return $this->_sPassword;
	}
	
	public function set($sPassword) {
		$this->_sPassword = $sPassword;
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
		$this->_sPassword = '';
		$nCharLen = strlen($sChar);
		for($i=0; $i<$nCharNbr; $i++) {
			$nCharPos = mt_rand(0,($nCharLen-1));
			$this->_sPassword .= $sChar[$nCharPos];
		}
		return $this;
	}
	
	public function passwordHash($sPassword) {
		$this->_sPassword = password_hash($sPassword, PASSWORD_DEFAULT);
		return $this;
	}
	
	public function passwordVerify($sHash) {
		return password_verify($this->_sPassword, $sHash);
	}
}
?>