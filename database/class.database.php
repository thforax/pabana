<?php
class Pabana_Database {
	private $armDatabaseConnexion = array();
	private $_oPabanaDebug;
	private $_oPdo;
	private $_sCurrentConnexion = null;
	private $_armInternalDatabase;
	
	public function __construct() {
	}
	
	public function setConnexion($armConnexion) {
		$sConnexion = $armConnexion['name'];
		$sDatabaseUser = '';
		if(isset($armConnexion['user'])) {
			$sDatabaseUser = $armConnexion['user'];
		}
		$sDatabasePassword = '';
		if(isset($armConnexion['password'])) {
			$sDatabasePassword = $armConnexion['password'];
		}
		if($armConnexion['dbms'] == 'access') {
			$sDsn = $this->_getAccessDsn($armConnexion);
		} elseif($armConnexion['dbms'] == 'mysql') {
			$sDsn = $this->_getMysqlDsn($armConnexion);
		}
		$GLOBALS['pabanaInternalStorage']['database'][$sConnexion] = array(
			'dsn' => $sDsn,
			'user' => $sDatabaseUser,
			'password' => $sDatabasePassword
		);
		return $this;
	}
	
	private function _getAccessDsn($armConnexion) {
		$sDsn = 'odbc:Uid=' . $armConnexion['user'] . ';Pwd=' .$armConnexion['password'] . ';';
		if(isset($armConnexion['driver'])) {
			$sDsn .= 'Driver={' . $armConnexion['driver'] . '};';
		}
		if(isset($armConnexion['database'])) {
			$sDsn .= 'Dbq=' . $armConnexion['database'] . ';';
		}
		if(isset($armConnexion['system_database'])) {
			$sDsn .= 'SystemDB=' . $armConnexion['system_database'] . ';';
		}
		return $sDsn;
	}
	
	private function _getMysqlDsn($armConnexion) {
		$sDsn = 'mysql:';
		if(isset($armConnexion['host'])) {
			$sDsn .= 'host=' . $armConnexion['host'] . ';';
		}
		if(isset($armConnexion['port'])) {
			$sDsn .= 'port=' . $armConnexion['port'] . ';';
		}
		if(isset($armConnexion['database'])) {
			$sDsn .= 'dbname=' . $armConnexion['database'] . ';';
		}
		if(isset($armConnexion['charset'])) {
			$sDsn .= 'charset=' . $armConnexion['charset'] . ';';
		}
		return $sDsn;
	}
	
	public function connect($sConnexion) {
		$sDsn = $GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['dsn'];
		$sDatabaseUser = $GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['user'];
		$sDatabasePassword = $GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['password'];
		try {
			$armPdoOption = array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
			);
			$cnxPdo = new PDO($sDsn, $sDatabaseUser, $sDatabasePassword, $armPdoOption);
			$this->_oCurrentPdo = $cnxPdo;
			$GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['pdo'] = $cnxPdo;
			$this->_sCurrentConnexion = $sConnexion;
		}
		catch (PDOException $oException) {
			echo $oException->getMessage();
		}
		return $this;
	}
	
	public function close($sConnexion) {
		if(isset($GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['pdo'])) {
			$GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['pdo'] = null;
			$this->_oPdo = null;
			return $this;
		} else {
			return false;
		}
	}
	
	public function getCurrentConnexion() {
		if(empty($this->_sCurrentConnexion)) {
			return false;
		} else {
			return $this->_sCurrentConnexion;
		}
	}
	
	public function setCurrentConnexion($sConnexion) {
		$this->_oPdo = $GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['pdo'];
	}
	
	public function exec($sQuery) {
		try {
			return $this->_oPdo->exec($sQuery);
		}
		catch (PDOException $e) {
			$this->_oPabanaDebug->exception(PE_ERROR, 'DATABASE_EXEC', $e->getMessage());
		}
	}
	
	public function getPdoObject($sConnexion) {
		return $GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['pdo'];
		var_dump($GLOBALS['pabanaInternalStorage']['database']);
	}
}
?>