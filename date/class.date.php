<?php
/**
* Pabana : Date Class (http://pabana.co)
*
* Licensed under new BSD License
* For full copyright and license information, please see the LICENSE.txt
*
* @link      	http://github.com/thforax/pabana for the canonical source repository
* @copyright 	Copyright (c) 2014-2015 FuturaSoft (http://www.futurasoft.net)
* @license   	http://pabana.co/about/license New BSD License
* @version		1.0.0.0
*/
class Pabana_Date {
	private $_sYear;
	private $_sMonth;
	private $_sDay;
	private $_sHour;
	private $_sMinute;
	private $_sSecond;
	
	public function __construct($sYear, $sMonth, $sDay, $sHour = 0, $sMinute = 0, $sSecond = 0) {
		$this->_sYear = $sYear;
		$this->_sMonth = $sMonth;
		$this->_sDay = $sDay;
		$this->_sHour = $sHour;
		$this->_sMinute = $sMinute;
		$this->_sSecond = $sSecond;
    }
	
	public function __toString() {
		return $this->sFilePath;
    }
	
	public function now() {
		$this->_sYear = date('Y');
		$this->_sMonth = date('m');
		$this->_sDay = date('d');
		$this->_sHour = date('H');
		$this->_sMinute = date('i');
		$this->_sSecond = date('s');
		return $this;
	}
	
	public function toSql() {
		return $this->_sYear . '-' . $this->_sMonth . '-' . $this->_sDay . ' ' . $this->_sHour . ':' . $this->_sMinute . ':' . $this->_sSecond;
	}
	
	public function toTimestamp() {
		return mktime($this->_sHour, $this->_sMinute, $this->_sSecond, $this->_sMonth, $this->_sDay, $this->_sYear);
	}
}
?>