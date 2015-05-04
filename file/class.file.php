<?php
class Pabana_File {
	private $sFilePath;
	
	public function __construct($sFilePath = '') {
		$this->sFilePath = $sFilePath;
    }
	
	public function __toString() {
		return $this->sFilePath;
    }
	
	public function canExecute() {
		return is_executable($this->sFilePath);
	}
	
	public function canRead() {
		return is_readable($this->sFilePath);
	}
	
	public function canWrite() {
		return is_writable($this->sFilePath);
	}
	
	public function exists() {
		return is_file($this->sFilePath);
	}
	
	public function getAbsolutePath() {
		return realpath($this->sFilePath);
	}
	
	public function getContent() {
		return file_get_contents($this->sFilePath);
	}
	
	public function getExtension() {
		return pathinfo($this->sFilePath, PATHINFO_EXTENSION);
	}
	
	public function import() {
		include($this->sFilePath);
	}
	
	public function isDir() {
		return is_dir($this->sFilePath);
	}
	
	public function isFile() {
		return is_file($this->sFilePath);
	}
	
	public function isLink() {
		return is_link($this->sFilePath);
	}
	
	public function isUpload() {
		return is_uploaded_file($this->sFilePath);
	}
	
	public function name() {
		return basename($this->sFilePath);
	}
	
	public function size() {
		return filesize($this->sFilePath);
	}
}
?>