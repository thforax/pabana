<?php
Class Pabana_Dom {
	private $sDoctype = 'HTML5';
	private $sCharset = 'UTF8';
	private $sHtmlTitle = '';
	private $arsHtmlLink = array();
	private $arsHtmlMeta = array();
	private $arsHtmlScript = array();
	
	public function setDoctype($sDoctype = 'HTML5') {
		$this->sDoctype = strtoupper($sDoctype);
	}
	
	public function getDoctype() {
		if($this->sDoctype == 'HTML5') {
			return '<!DOCTYPE html>' . PHP_EOL;
		} else if($this->sDoctype == 'XHTML11') {
			return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . PHP_EOL;
		} else if($this->sDoctype == 'XHTML1_STRICT') {
			return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . PHP_EOL;
		} else if($this->sDoctype == 'XHTML1_TRANSITIONAL') {
			return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . PHP_EOL;
		} else if($this->sDoctype == 'XHTML1_FRAMESET') {
			return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">' . PHP_EOL;
		} else if($this->sDoctype == 'HTML4_STRICT') {
			return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . PHP_EOL;
		} else if($this->sDoctype == 'HTML4_LOOSE') {
			return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . PHP_EOL;
		} else if($this->sDoctype == 'HTML4_FRAMESET') {
			return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">' . PHP_EOL;
		}
	}
	
	public function setCharset($sCharset = 'UTF8') {
		$this->sCharset = strtoupper($sCharset);
	}
	
	public function getCharset() {
		if($this->sDoctype == 'HTML5') {
			if($this->sCharset == 'UTF8') {
				return '<meta charset="utf-8">' . PHP_EOL;
			}
		} else if($this->sDoctype == 'XHTML11' || $this->sDoctype == 'XHTML1_STRICT' || $this->sDoctype == 'XHTML1_TRANSITIONAL' || $this->sDoctype == 'XHTML1_FRAMESET') {
			if($this->sCharset == 'UTF8') {
				return '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . PHP_EOL;
			}
		} else if($this->sDoctype == 'HTML4_STRICT' || $this->sDoctype == 'HTML4_LOOSE' || $this->sDoctype == 'HTML4_FRAMESET') {
			if($this->sCharset == 'UTF8') {
				return '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . PHP_EOL;
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
		return '<title>' . $this->sHtmlTitle . '</title>' . PHP_EOL;
	}
	
	/*
	Style
	*/
	public function appendLink($sLinkPath, $sRel = 'stylesheet', $arsAttribs = array()) {
		$this->arsHtmlLink[] = array($sLinkPath, $sRel, $arsAttribs);
		return $this;
	}
	
	public function prependLink($sLinkPath, $sRel = 'stylesheet', $arsAttribs = array()) {
		array_unshift($this->arsHtmlLink, array($sLinkPath, $sRel, $arsAttribs));
		return $this;
	}
	
	public function getLink() {
		$sHtmlLink = '';
		foreach($this->arsHtmlLink as $arsLink) {
			$sHtmlLink .= '<link href="' . $arsLink[0] . '" rel="' . $arsLink[1] . '"';
			if(isset($arsLink[2]['media'])) {
				$sHtmlLink .= ' media="' . $arsLink[2]['media'] . '"';
			}
			if(isset($arsLink[2]['type'])) {
				$sHtmlLink .= ' type="' . $arsLink[2]['type'] . '"';
			}
			$sHtmlLink .= ' />' . PHP_EOL;
		}
		return $sHtmlLink;
	}
	
	/*
	Script
	*/	
	public function appendFileScript($sScriptPath, $sMimeType = 'text/javascript', $arsAttribs = array()) {
		$this->arsHtmlScript[] = array(1, $sScriptPath, $sMimeType, $arsAttribs);
		return $this;
	}
	
	public function prependFileScript($sScriptPath, $sMimeType = 'text/javascript', $arsAttribs = array()) {
		array_unshift($this->arsHtmlScript, array(1, $sScriptPath, $sMimeType, $arsAttribs));
		return $this;
	}
	
	public function appendScript($sScript, $sMimeType = 'text/javascript', $arsAttribs = array()) {
		$this->arsHtmlScript[] = array(0, $sScript, $sMimeType, $arsAttribs);
		return $this;
	}
	
	public function prependScript($sScript, $sMimeType = 'text/javascript', $arsAttribs = array()) {
		array_unshift($this->arsHtmlScript, array(0, $sScript, $sMimeType, $arsAttribs));
		return $this;
	}
	
	public function getScript() {
		$arsHtmlScript = '';
		foreach($this->arsHtmlScript as $arsScript) {
			if(isset($arsScript[3]['conditional'])) {
				$arsHtmlScript .= '<!--[if ' . $arsScript[3]['conditional'] . ']>';
			}
			if($arsScript[0] == 1) {
				$arsHtmlScript .= '<script src="' . $arsScript[1] . '" type="' . $arsScript[2] . '"></script>';
			}
			if($arsScript[0] == 0) {
				$arsHtmlScript .= '<script type="' . $arsScript[2] . '">' . $arsScript[1] . '</script>';
			}
			if(isset($arsScript[3]['conditional'])) {
				$arsHtmlScript .= '<![endif]-->';
			}
			$arsHtmlScript .= PHP_EOL;
		}
		return $arsHtmlScript;
	}
	
	/*
	Meta
	*/
	public function appendMeta($sName, $sContent) {
		$this->arsHtmlMeta[] = array('name', $sName, $sContent);
		return $this;
	}
	
	public function prependMeta($sName, $sContent) {
		array_unshift($this->arsHtmlMeta, array('name', $sName, $sContent));
		return $this;
	}
	
	public function appendHttpEquiv($sName, $sContent) {
		$this->arsHtmlMeta[] = array('http-equiv', $sName, $sContent);
		return $this;
	}
	
	public function prependHttpEquiv($sName, $sContent) {
		array_unshift($this->arsHtmlMeta, array('http-equiv', $sName, $sContent));
		return $this;
	}
	
	public function getMeta() {
		$arsHtmlMeta = '';
		foreach($this->arsHtmlMeta as $arsMeta) {
			$arsHtmlMeta .= '<meta ' . $arsMeta[0] . '="' . $arsMeta[1] . '" content="' . $arsMeta[2] . '">' . PHP_EOL;
		}
		return $arsHtmlMeta;
	}
}
?>