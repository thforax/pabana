<?php
class Pabana_Parse_Ini extends Pabana_Parse {
	
	private $_filename;
	protected $_object_parse;
	
	function __construct($filename) {
		$this->_filename = $filename;
		$this->_object_parse = (object) parse_ini_file($filename, true);
    }
}
?>