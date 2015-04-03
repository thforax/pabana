<?php
class Pabana_Database {
	private $armDatabaseConnexion = array();
	
	public function setConnexion($armConnexion) {
		$sConnexion = $armConnexion['name'];
		$this->armDatabaseConnexion[$sConnexion] = $armConnexion;
	}
	
	public function connect($sConnexion) {
		if($this->armDatabaseConnexion[$sConnexion]['dbms'] == 'access') {
			return new Pabana_Database_Access($this->armDatabaseConnexion[$sConnexion]);
		}
	}
}
?>