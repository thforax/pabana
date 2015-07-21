<?php
class Pabana_Database {
	private $armDatabaseConnexion = array();
	private $_oPabanaDebug;
	private $_oCurrentPdo;
	private $_sCurrentConnexion = null;
	private $_armInternalDatabase;
	private $_oCurrentStatement;
	
	public function __construct() {
		$this->_oPabanaDebug = $GLOBALS['pabanaInternalStorage']['debug'];
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
		$sDatabaseCharset = '';
		if(isset($armConnexion['charset'])) {
			$sDatabaseCharset = $armConnexion['charset'];
		}
		if($armConnexion['dbms'] == 'access') {
			$sDsn = $this->_getAccessDsn($armConnexion);
		} elseif($armConnexion['dbms'] == 'mysql') {
			$sDsn = $this->_getMysqlDsn($armConnexion);
		}
		$GLOBALS['pabanaInternalStorage']['database'][$sConnexion] = array(
			'dsn' => $sDsn,
			'user' => $sDatabaseUser,
			'password' => $sDatabasePassword,
			'charset' => $sDatabaseCharset
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
			$GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['pdo'] = $cnxPdo;
			$this->_oCurrentPdo = $cnxPdo;
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
			if($this->_sCurrentConnexion == $sConnexion) {
				$this->_sCurrentConnexion = null;
			}
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
		$this->_sCurrentConnexion = $sConnexion;
		$this->_oCurrentPdo = $GLOBALS['pabanaInternalStorage']['database'][$sConnexion]['pdo'];
		return $this;
	}
	
	private function charsetConversion($mValue, $bOut = true) {
		if(isset($GLOBALS['pabanaConfigStorage']['database']['charset_conversion']) && $GLOBALS['pabanaConfigStorage']['database']['charset_conversion'] == 'true') {
			$sPabanaCharset = $GLOBALS['pabanaConfigStorage']['pabana']['charset'];
			$sDatabaseCharset = $GLOBALS['pabanaInternalStorage']['database'][$this->_sCurrentConnexion]['charset'];
			if($bOut === true) {
				$sInCharset = $sPabanaCharset;
				$sOutCharset = $sDatabaseCharset;
			} else {
				$sInCharset = $sDatabaseCharset;
				$sOutCharset = $sPabanaCharset;
			}
			$oLocalization = new Pabana_Localization();
			$mValue = $oLocalization->changeCharset($mValue, $sInCharset, $sOutCharset);
		}
		return $mValue;
	}
	
	public function exec($sQuery) {
		$sCharsetQuery = $this->charsetConversion($sQuery, true);
		try {
			return $this->_oCurrentPdo->exec($sCharsetQuery);
		}
		catch (PDOException $e) {
			$this->_oPabanaDebug->exception(PE_ERROR, 'DATABASE_EXEC', $sQuery . '<br />' . $e->getMessage());
			return false;
		}
	}
	
	public function query($sQuery) {
		$sCharsetQuery = $this->charsetConversion($sQuery, true);
		try {
			$this->_oCurrentStatement = $this->_oCurrentPdo->query($sCharsetQuery);
			return $this;
		}
		catch (PDOException $e) {
			$this->_oPabanaDebug->exception(PE_ERROR, 'DATABASE_QUERY', $sQuery . '<br />' . $e->getMessage());
			return false;
		}
	}
	
	public function fetch() {
		try {
			$armRow = $this->_oCurrentStatement->fetch(PDO::FETCH_ASSOC);
			return $this->charsetConversion($armRow, false);
		}
		catch (PDOException $e) {
			$this->_oPabanaDebug->exception(PE_ERROR, 'DATABASE_FETCH', $e->getMessage());
			return false;
		}
	}
	
	public function fetchAll() {
		try {
			$armRow = $this->_oCurrentStatement->fetchAll(PDO::FETCH_ASSOC);
			return $this->charsetConversion($armRow, false);
		}
		catch (PDOException $e) {
			$this->_oPabanaDebug->exception(PE_ERROR, 'DATABASE_FETCHALL', $e->getMessage());
			return false;
		}
	}
	
	public function getPdoObject($sConnexion) {
		return $this->_oCurrentPdo;
	}
	
	public function escape($sQuery) {
		return str_replace("'", "''", $sQuery);
	}
}
?>