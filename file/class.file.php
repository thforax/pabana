<?php
/**
* Pabana : File Class (http://pabana.co)
*
* Licensed under new BSD License
* For full copyright and license information, please see the LICENSE.txt
*
* @link      	http://github.com/thforax/pabana for the canonical source repository
* @copyright 	Copyright (c) 2014-2015 FuturaSoft (http://www.futurasoft.net)
* @license   	http://pabana.co/about/license New BSD License
* @version		1.0.0.0
*/
class Pabana_File {
	private $sFilePath;
	private $hFile;
	
	public function __construct($sFilePath = '') {
		$this->sFilePath = $sFilePath;
    }
	
	public function __toString() {
		return $this->sFilePath;
    }
	
	public function append($sFileContent) {
		file_put_contents($this->sFilePath, $sFileContent, FILE_APPEND);
		return $this;
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
	
	public function close() {
		fclose($this->hFile);
		return $this;
	}
	
	public function copy($sNewFilePath) {
		copy($this->sFilePath, $sNewFilePath);
		return $this;
	}
	
	public function create() {
		touch($this->sFilePath);
		return $this;
	}
	
	public function exists() {
		return is_file($this->sFilePath);
	}
	
	public function getAbsolutePath() {
		return realpath($this->sFilePath);
	}
	
	public function getAccessTime() {
		return fileatime($this->sFilePath);
	}
	
	public function getEncoding() {
		$oFileInfo = new finfo(FILEINFO_MIME_ENCODING);
		$sEncoding = $oFileInfo->file($this->sFilePath);
		$oFileInfo->close();
		return $sEncoding;
	}
	
	public function getExtension() {
		return pathinfo($this->sFilePath, PATHINFO_EXTENSION);
	}
	
	public function getHandler() {
		return $this->hFile;
	}
	
	public function getMimeType() {
		$oFileInfo = new finfo(FILEINFO_MIME_TYPE);
		$sMimeType = $oFileInfo->file($this->sFilePath);
		$oFileInfo->close();
		return $sMimeType;
	}
	
	public function getModifyTime() {
		return filemtime($this->sFilePath);
	}
	
	public function getName() {
		return basename($this->sFilePath);
	}
	
	public function getParent() {
		return dirname($this->sFilePath);
	}
	
	public function getSize() {
		return filesize($this->sFilePath);
	}
	
	public function group($mNewGroup) {
		chgrp($this->sFilePath, $mNewGroup);
		return $this;
	}
	
	public function import() {
		include($this->sFilePath);
		return $this;
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
	
	public function move($sNewFilePath) {
		rename($this->sFilePath, $sNewFilePath);
		return $this;
	}
	
	public function open($sFileOpenMode, $bFileOpenBinary = true) {
		if($bFileOpenBinary == true) {
			$sFileOpenMode .= 'b';
		}
		$this->hFile = fopen($this->sFilePath, $sFileOpenMode);
		return $this;
	}
	
	public function owner($sNewFileOwner) {
		chown($this->sFilePath, $sNewFileOwner);
		return $this;
	}
	
	public function permission($nNewPermission) {
		chmod($this->sFilePath, $nNewPermission);
		return $this;
	}
	
	public function prepend($sFileContent) {
		$sFileContent = $sFileContent . $this->read();
		file_put_contents($this->sFilePath, $sFileContent);
		return $this;
	}
	
	public function read() {
		return file_get_contents($this->sFilePath);
	}
	
	public function rename($sNewFileName) {
		$this->move($sNewFileNam);
		return $this;
	}
	
	public function remove() {
		unlink($this->sFilePath);
		return $this;
	}
	
	public function write($sFileContent) {
		file_put_contents($this->sFilePath, $sFileContent);
		return $this;
	}
}
?>