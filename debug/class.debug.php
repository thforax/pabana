<?php
include('constant.debug.php');
class Pabana_Debug {
	private $arbDebugShow;
	private $arbDebugFile;
	private $arbDebugDatabase;
	private $bDebugBacktrace;
	private $bDebugVariable;
	private $sDebugFile;
	private $cnxDebugDatabase;
	private $arsErrorLevel = array(
		1 => 'DEBUG',
		2 => 'INFO',
		4 => 'DEPRECATED',
		8 => 'WARNING',
		16 => 'ERROR',
		32 => 'CRITICAL'
	);
	
	public function __construct($nDebugShow = 0, $nDebugFile = 0, $nDebugDatabase = 0, $bDebugEnvironment = true, $bDebugBacktrace = true, $bDebugLink = true) {
		$this->arbDebugShow = $this->getIntToArrayDebug($nDebugShow);
		$this->arbDebugFile = $this->getIntToArrayDebug($nDebugFile);
		$this->arbDebugDatabase = $this->getIntToArrayDebug($nDebugDatabase);
		$this->bDebugEnvironment = $bDebugEnvironment;
		$this->bDebugBacktrace = $bDebugBacktrace;
		$this->bDebugLink = $bDebugLink;
    }
	
	public function setDebugFile($sDebugFile) {
		$this->sDebugFile = $sDebugFile;
	}
	
	public function setDebugDatabase($cnxDebugDatabase) {
		$this->cnxDebugDatabase = $cnxDebugDatabase;
	}
	
	private function getIntToArrayDebug($nDebugLevel) {
		$arsErrorLevel = $this->arsErrorLevel;
		$arbErrorLevelReturn = $arsErrorLevel;
		krsort($arsErrorLevel);
		foreach($arsErrorLevel as $nKeyErrorLevel => $sValueErrorLevel) {
			if($nDebugLevel >= $nKeyErrorLevel || $nDebugLevel == -1) {
				$arbErrorLevelReturn[$nKeyErrorLevel] = true;
				if($nDebugLevel != -1) {
					$nDebugLevel = $nDebugLevel - $nKeyErrorLevel;
				}
			} else {
				$arbErrorLevelReturn[$nKeyErrorLevel] = false;
			}
			
		}
		return $arbErrorLevelReturn;
	}
	
	public function exception($nErrorLevel = 1, $sErrorCode = 'UNKNOW', $sErrorMessage = 'Unknow error') {
		$this->armError = array(
			'level' => $nErrorLevel,
			'code' => $sErrorCode,
			'message' => $sErrorMessage
		); 
		$this->armBackTrace = debug_backtrace();
		$this->armEnvironment = array(
			'date' => date('Y/m/d H:i:s'),
			'memory' => round(memory_get_usage() / 1000000, 3),
			'generation' => round(microtime(true) - $GLOBALS['pabanaInternalStorage']['pabana']['startTime'], 4)
		);
		if($this->arbDebugShow[$nErrorLevel] == true) {
			if(PH_CONSOLE == true) {
				echo $this->exceptionText();
			} else {
				echo $this->exceptionHtml();
			}
		}
		if($this->arbDebugFile[$nErrorLevel] == true) {
			if(!empty($this->sDebugFile)) {
				$sExceptionText = $this->exceptionText();
				file_put_contents($this->sDebugFile, $sExceptionText, FILE_APPEND);
			} else {
				$this->arbDebugFile = 0;
				$this->exception(8, 'DEBUG_FILE', 'It\'s impossible to Log error in file cause log file isn\'t defined');
			}
		}
		// Stop application if error level is ERROR or CRITICAL
		if($nErrorLevel >= 16) {
			$GLOBALS['pabanaInternalStorage']['pabana']['fatalException'] = 1;
			exit(1);
		}
    }
	
	private function exceptionText($nErrorLevel, $sErrorCode, $sErrorMessage, $armBackTrace) {
		$sReturnText = $this->arsErrorLevel[$nErrorLevel] . ': ';
		$sReturnText .= strip_tags($sErrorMessage) . ' ';
		$sReturnText .= '(Error code : ' . $sErrorCode .')' . PHP_EOL;
		$sReturnText .= 'Class: ' . $this->armBackTrace[1]['class'] . ' ';
		$sReturnText .= 'on method ' . $this->armBackTrace[1]['function'] . PHP_EOL;
		if(isset($this->armBackTrace[1]['file'])) {
			$sReturnText .= 'File: ' . $this->armBackTrace[1]['file'] . ' ';
			$sReturnText .= 'on line ' . $this->armBackTrace[1]['line'] . PHP_EOL;
		}
		if($this->bDebugEnvironment == true) {
			$sReturnText .= 'Date: ' . $this->armEnvironment['date'] . ' | ';
			$sReturnText .= 'Memory usage: ' . $this->armEnvironment['memory'] . 'Mo | ';
			$sReturnText .= 'Generation time: ' . $this->armEnvironment['generation'] . 's' . PHP_EOL;
		}
		$sReturnText .= PHP_EOL;
		return $sReturnText;
    }
	
	private function exceptionHtml() {
		$sErrorUrl = 'http://pabana.com/doc/error/err_id/' . $this->armError['code'];
		$sCssStyle = 'border: 1px solid; margin: 10px; padding: 10px; border-radius: 5px; font-size: 14px; font-weight:normal; text-transformation:none; font-family:verdana;';
		if($this->armError['level'] >= 16) {
			$sCssStyle .= ' color: #D8000C; background-color: #FFBABA;';
		}
		elseif($this->armError['level'] >= 4) {
			$sCssStyle .= ' color: #9F6000; background-color: #FEEFB3;';
		}
		else {
			$sCssStyle .= ' color: #00529B; background-color: #BDE5F8;';
		}
		$sReturnText = '<p style="'.$sCssStyle.'">';
		$sReturnText .= '<strong>' . $this->arsErrorLevel[$this->armError['level']] . ':</strong> ';
		$sReturnText .= $this->armError['message'] . ' ';
		$sReturnText .= '(Error code : <a href="'.$sErrorUrl.'" target="_blank" title="Pabana online documentation">' . $this->armError['code'] .'</a>)<br />';
		$sReturnText .= 'Class : <strong>' . $this->armBackTrace[1]['class'] . '</strong> ';
		$sReturnText .= 'on method  <strong>' . $this->armBackTrace[1]['function'] . '</strong><br />';
		if(isset($this->armBackTrace[1]['file'])) {
			$sReturnText .= 'File : <strong>' . $this->armBackTrace[1]['file'] . '</strong> ';
			$sReturnText .= 'on line <strong>' . $this->armBackTrace[1]['line'] . '</strong><br />';
		}
		if($this->bDebugEnvironment == true) {
			$sReturnText .= 'Memory usage : ' . $this->armEnvironment['memory'] . 'Mo | ';
			$sReturnText .= 'Generation time : ' . $this->armEnvironment['generation'] . 's<br />';
		}
		if($this->bDebugBacktrace == true) {
			$sReturnText .= '<br /><strong>Backtrace:</strong><br />';
			$nCountBacktrace = count($this->armBackTrace);
			for($i = 1; $i < $nCountBacktrace; $i++) {
				$sArgument = print_r($this->armBackTrace[$i]['args'], true);
				$sReturnText .= '#' . $i . ' ';
				$sReturnText .= $this->armBackTrace[$i]['class'] . $this->armBackTrace[$i]['type'] . $this->armBackTrace[$i]['function'] . '(' . $sArgument .')';
				$sReturnText .= ' called at [' . $this->armBackTrace[$i]['file'] . ':' . $this->armBackTrace[$i]['line'] . ']<br />';
			}
		}
		$sReturnText .= '</p>';
		return $sReturnText;
    }
}
?>