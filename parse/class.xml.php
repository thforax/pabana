<?php
class Pabana_Parse_Xml {
	private $armConfig;
	
	function __construct($sFilePath) {
		$oConfig = simplexml_load_file($sFilePath);
		$jsonConfig = json_encode($oConfig);
		$this->armConfig = json_decode($jsonConfig,TRUE);
    }
	
	function toArray() {
		return $this->armConfig;
	}
}
?>