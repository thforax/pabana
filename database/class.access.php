<?php
class Pabana_Database_Access {
	private $oPdo;
	
	public function __construct($armConnexion) {
		$sDsn = 'odbc:Driver={' . $armConnexion['driver'] . '};Dbq=' . $armConnexion['database'] . ';SystemDB=' . $armConnexion['system_database'] . ';Uid=ER;Pwd=DUKE;';
		try {
			$this->oPdo = new PDO($sDsn);
			$this->oPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e) {
			echo 'Connexion Access échouée  : ' . $e->getMessage();
			exit();
		}
	}
}
?>