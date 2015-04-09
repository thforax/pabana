<?php
class Pabana_Database {
	private $armDatabaseConnexion = array();
	public $cnxPdo;
	
	public function setConnexion($armConnexion) {
		$sConnexion = $armConnexion['name'];
		$sDatabaseUser = '';
		if(isset($armConnexion['user'])) {
			$sDatabaseUser = $armConnexion['user'];
		}
		if(isset($armConnexion['password'])) {
			$sDatabasePassword = $armConnexion['password'];
		}
		if($armConnexion['dbms'] == 'access') {
			$sDsn = 'odbc:Uid=' . $sDatabaseUser . ';Pwd=' . $sDatabasePassword . ';';
			if(isset($armConnexion['driver'])) {
				$sDsn .= 'Driver={' . $armConnexion['driver'] . '};';
			}
			if(isset($armConnexion['database'])) {
				$sDsn .= 'Dbq=' . $armConnexion['database'] . ';';
			}
			if(isset($armConnexion['system_database'])) {
				$sDsn .= 'SystemDB=' . $armConnexion['system_database'] . ';';
			}
		}
		$GLOBALS['pabanaInternalStorage']['database'][$sConnexion] = array(
			'dsn' => $sDsn,
			'user' => $sDatabaseUser,
			'password' => $sDatabasePassword
		);
		return $this;
	}
	
	public function connect($sConnexion) {
		$sDsn = $GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['dsn'];
		$sDatabaseUser = $GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['user'];
		$sDatabasePassword = $GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['password'];
		try {
			$this->cnxPdo = new PDO($sDsn, $sDatabaseUser, $sDatabasePassword);
			$this->cnxPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e) {
			echo 'Connexion Access échouée  : ' . $e->getMessage();
			exit();
		}
		return $this->cnxPdo;
	}
}
?>