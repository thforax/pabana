<?php
Class Pabana_Core_View {
	private $sDoctype;
	private $sCharset;
	private $sHtmlTitle;
	private $arsHtmlStyle = array();
	private $arsHtmlScript = array();
	
	public function getContent() {
		echo '|s|CONTENT|s|';
	}
	
	public function setDoctype($sDoctype) {
		$this->sDoctype = strtoupper($sDoctype);
	}
	
	public function getDoctype() {
		if($this->sDoctype == 'HTML5') {
			return '<!DOCTYPE html>' . PHP_EOL;
		}
	}
	
	public function setCharset($sCharset) {
		$this->sCharset = strtoupper($sCharset);
	}
	
	public function getCharset() {
		if($this->sDoctype == 'HTML5') {
			if($this->sCharset == 'UTF8') {
				return '<meta charset="utf-8">' . PHP_EOL;
			}
		}
	}
	
	/*
	Title
	*/
	public function setTitle($sTitle) {
		$this->sHtmlTitle = $sTitle;
	}
	
	public function appendTitle($sTitle) {
		$this->sHtmlTitle .= $sTitle;
	}
	
	public function prependTitle($sTitle) {
		$this->sHtmlTitle = $sTitle . $this->sHtmlTitle;
	}
	
	public function getTitle() {
		return $this->sHtmlTitle;
	}
	
	/*
	Style
	*/
	public function setStyle($sStylePath, $sMimeType = 'text/css', $sRel = 'stylesheet') {
		$this->arsHtmlStyle = array(
			array($sStylePath, $sMimeType, $sRel)
		);
	}
	
	public function appendStyle($sStylePath, $sMimeType = 'text/css', $sRel = 'stylesheet') {
		$this->arsHtmlStyle[] = array($sStylePath, $sMimeType, $sRel);
	}
	
	public function prependStyle($sStylePath, $sMimeType = 'text/css', $sRel = 'stylesheet') {
		array_unshift($this->arsHtmlStyle, array($sStylePath, $sMimeType, $sRel));
	}
	
	public function getStyle() {
		$sHtmlStyle = '';
		foreach($this->arsHtmlStyle as $arsStyle) {
			$sHtmlStyle .= '<link href="' . $arsStyle[0] . '" type="' . $arsStyle[1] . '" rel="' . $arsStyle[2] . '">' . PHP_EOL;
		}
		return $sHtmlStyle;
	}
	
	/*
	Script
	*/
	public function setScript($sScriptPath, $sMimeType = 'text/javascript') {
		$this->arsHtmlScript = array(
			array($sStylePath, $sMimeType)
		);
	}
	
	public function appendScript($sScriptPath, $sMimeType = 'text/javascript') {
		$this->arsHtmlScript[] = array($sScriptPath, $sMimeType);
	}
	
	public function prependScript($sScriptPath, $sMimeType = 'text/javascript') {
		array_unshift($this->arsHtmlScript, array($sStylePath, $sMimeType));
	}
}
?>