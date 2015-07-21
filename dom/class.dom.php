<?php
Class Pabana_Dom {
	private $_oPabanaDebug;
	private $arsDoctypes = array();
	private $arsCharsets = array();
	private $arsTitles = array();
	private $arsLinks = array();
	private $arsMetas = array();
	private $arsScripts = array();
	
	public function __construct() {
		$this->_oPabanaDebug = $GLOBALS['pabanaInternalStorage']['debug'];
	}
	
	public function mergeDom($oDom) {
		$this->arsDoctypes = array_merge($this->arsDoctypes, $oDom->getDoctypes());
		$this->arsCharsets = array_merge($this->arsCharsets, $oDom->getCharsets());
		$this->arsTitles = array_merge($this->arsTitles, $oDom->getTitles());
		$this->arsLinks = array_merge($this->arsLinks, $oDom->getLinks());
		$this->arsMetas = array_merge($this->arsMetas, $oDom->getMetas());
		$this->arsScripts = array_merge($this->arsScripts, $oDom->getScripts());
	}
	
	/*
	Getter
	*/
	public function getDoctypes() {
		return $this->arsDoctypes;
	}
	
	public function getCharsets() {
		return $this->arsCharsets;
	}
	
	public function getTitles() {
		return $this->arsTitles;
	}
	
	public function getLinks() {
		return $this->arsLinks;
	}
	
	public function getMetas() {
		return $this->arsMetas;
	}
	
	public function getScripts() {
		return $this->arsScripts;
	}
	
	/* 
	Doctype 
	*/
	public function setDoctype($sDoctype) {
		$this->arsDoctypes[] = array(
			'type' => 'set',
			'value' => strtoupper($sDoctype)
		);
	}
	
	public function getDoctype() {
		$sDoctype = 'HTML5';
		foreach($this->arsDoctypes as $arsDoctype) {
			if($arsDoctype['type'] == 'set') {
				$sDoctype = $arsDoctype['value'];
			}
		}
		if($sDoctype == 'HTML5') {
			return '<!DOCTYPE html>' . PHP_EOL;
		} else if($sDoctype == 'XHTML11') {
			return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . PHP_EOL;
		} else if($sDoctype == 'XHTML1_STRICT') {
			return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . PHP_EOL;
		} else if($sDoctype == 'XHTML1_TRANSITIONAL') {
			return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . PHP_EOL;
		} else if($sDoctype == 'XHTML1_FRAMESET') {
			return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">' . PHP_EOL;
		} else if($sDoctype == 'HTML4_STRICT') {
			return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . PHP_EOL;
		} else if($sDoctype == 'HTML4_LOOSE') {
			return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . PHP_EOL;
		} else if($sDoctype == 'HTML4_FRAMESET') {
			return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">' . PHP_EOL;
		}
	}
	
	/* 
	Charset 
	*/
	public function setCharset($sCharset) {
		$this->arsCharsets[] = array(
			'type' => 'set',
			'value' => strtoupper($sCharset)
		);
	}
	
	public function getCharset() {
		$sDoctype = 'HTML5';
		foreach($this->arsDoctypes as $arsDoctype) {
			if($arsDoctype['type'] == 'set') {
				$sDoctype = $arsDoctype['value'];
			}
		}
		$sCharset = 'UTF-8';
		foreach($this->arsCharsets as $arsCharset) {
			if($arsCharset['type'] == 'set') {
				$sCharset = $arsCharset['value'];
			}
		}
		$arsCharset = array(
			'UTF-8' => 'utf-8',
			'UTF-16' => 'utf-16',
			'ISO-8859-1' => 'iso-8859-1',
			'ISO-8859-5' => 'iso-8859-5',
			'ISO-8859-15' => 'iso-8859-15',
			'CP1251' => 'windows-1251',
			'CP1252' => 'windows-1252',
			'KOI8-R' => 'koi8-r',
			'BIG5' => 'big5',
			'GB2312' => 'gb2312',
			'BIG5-HKSCS' => 'big5-hkscs',
			'SHIFT_JIS' => 'shift_jis',
			'EUC-JP' => 'euc-jp',
			'MACROMAN' => 'x-mac-roman'
		);
		$arsKeyCharset = array_keys($arsCharset);
		if(!in_array($sCharset, $arsKeyCharset)) {
			$this->_oPabanaDebug->exception(PE_ERROR, 'DOM_CHARSET_NAME', 'Charset ' . $sCharset . ' isn\'t defined');
		} else {
			if($sDoctype == 'HTML5') {
				return '<meta charset="' . $arsCharset[$sCharset] . '">' . PHP_EOL;
			} else {
				if(substr($sDoctype, 0, 1) == 'X') {
					return '<meta http-equiv="Content-Type" content="text/html; charset=' . $arsCharset[$sCharset] . '" />' . PHP_EOL;
				} else {
					return '<meta http-equiv="Content-Type" content="text/html; charset=' . $arsCharset[$sCharset] . '">' . PHP_EOL;
				}
			}
		}
	}
	
	/*
	Title
	*/
	public function setTitle($sTitle) {
		$this->arsTitles[] = array(
			'type' => 'set',
			'value' => $sTitle
		);
	}
	
	public function appendTitle($sTitle) {
		$this->arsTitles[] = array(
			'type' => 'append',
			'value' => $sTitle
		);
	}
	
	public function prependTitle($sTitle) {
		$this->arsTitles[] = array(
			'type' => 'prepend',
			'value' => $sTitle
		);
	}
	
	public function getTitle() {
		$sTitle = '';
		foreach($this->arsTitles as $arsTitle) {
			if($arsTitle['type'] == 'prepend') {
				$sTitle = $arsTitle['value'] . $sTitle;
			} else if($arsTitle['type'] == 'set') {
				$sTitle = $arsTitle['value'];
			} else if($arsTitle['type'] == 'append') {
				$sTitle .= $arsTitle['value'];
			}
		}
		return '<title>' . $sTitle . '</title>' . PHP_EOL;
	}
	
	/*
	Link head
	*/
	public function appendFileLink($sLinkPath, $sRel = 'stylesheet', $arsAttribs = array()) {
		$this->arsLinks[] = array(
			'type' => 'append',
			'path' => $sLinkPath,
			'rel' => $sRel,
			'attribs' => $arsAttribs
		);
		return $this;
	}
	
	public function offsetFileLink($nOffset, $sLinkPath, $sRel = 'stylesheet', $arsAttribs = array()) {
		$this->arsLinks[] = array(
			'type' => 'offset',
			'offset' => $nOffset,
			'path' => $sLinkPath,
			'rel' => $sRel,
			'attribs' => $arsAttribs
		);
		return $this;
	}
	
	public function prependFileLink($sLinkPath, $sRel = 'stylesheet', $arsAttribs = array()) {
		$this->arsLinks[] = array(
			'type' => 'prepend',
			'path' => $sLinkPath,
			'rel' => $sRel,
			'attribs' => $arsAttribs
		);
		return $this;
	}
	
	public function getLink() {
		$armSortLinks = array();
		foreach($this->arsLinks as $arsLink) {
			if($arsLink['type'] == 'prepend') {
				array_unshift($armSortLinks, $arsLink);
			} else if($arsLink['type'] == 'offset') {
				$nOffset = $arsLink['offset'];
				$armSortLinks[$nOffset] = $arsLink;
			} else if($arsLink['type'] == 'append') {
				$armSortLinks[] = $arsLink;
			}
		}
		$sHtmlLink = '';
		foreach($armSortLinks as $arsLink) {
			$sHtmlLink .= '<link href="' . $arsLink['path'] . '" rel="' . $arsLink['rel'] . '"';
			if(isset($arsLink[2]['media'])) {
				$sHtmlLink .= ' media="' . $arsLink['attribs']['media'] . '"';
			}
			if(isset($arsLink[2]['type'])) {
				$sHtmlLink .= ' type="' . $arsLink['attribs']['type'] . '"';
			}
			$sHtmlLink .= ' />' . PHP_EOL;
		}
		return $sHtmlLink;
	}
	
	/*
	Script
	*/
	public function appendFileScript($sScriptPath, $sMimeType = 'text/javascript', $arsAttribs = array()) {
		$this->arsScripts[] = array(
			'type' => 'append',
			'file' => true,
			'path' => $sScriptPath,
			'mime' => $sMimeType,
			'attribs' => $arsAttribs
		);
		return $this;
	}
	
	public function offsetFileScript($nOffset, $sScriptPath, $sMimeType = 'text/javascript', $arsAttribs = array()) {
		$this->arsScripts[] = array(
			'type' => 'offset',
			'offset' => $nOffset,
			'file' => true,
			'path' => $sScriptPath,
			'mime' => $sMimeType,
			'attribs' => $arsAttribs
		);
		return $this;
	}
	
	public function prependFileScript($sScriptPath, $sMimeType = 'text/javascript', $arsAttribs = array()) {
		$this->arsScripts[] = array(
			'type' => 'prepend',
			'file' => true,
			'path' => $sScriptPath,
			'mime' => $sMimeType,
			'attribs' => $arsAttribs
		);
		return $this;
	}
	
	public function appendScript($sScript, $sMimeType = 'text/javascript', $arsAttribs = array()) {
		$this->arsScripts[] = array(
			'type' => 'append',
			'file' => false,
			'script' => $sScript,
			'mime' => $sMimeType,
			'attribs' => $arsAttribs
		);
		return $this;
	}
	
	public function offsetScript($nOffset, $sScript, $sMimeType = 'text/javascript', $arsAttribs = array()) {
		$this->arsScripts[] = array(
			'type' => 'offset',
			'offset' => $nOffset,
			'file' => false,
			'script' => $sScript,
			'mime' => $sMimeType,
			'attribs' => $arsAttribs
		);
		return $this;
	}
	
	public function prependScript($sScript, $sMimeType = 'text/javascript', $arsAttribs = array()) {
		$this->arsScripts[] = array(
			'type' => 'prepend',
			'file' => false,
			'script' => $sScript,
			'mime' => $sMimeType,
			'attribs' => $arsAttribs
		);
		return $this;
	}
	
	public function getScript() {
		$armSortScripts = array();
		foreach($this->arsScripts as $arsScript) {
			if($arsScript['type'] == 'prepend') {
				array_unshift($armSortScripts, $arsScript);
			} else if($arsScript['type'] == 'offset') {
				$nOffset = $arsLink['offset'];
				$armSortScripts[$nOffset] = $arsScript;
			} else if($arsScript['type'] == 'append') {
				$armSortScripts[] = $arsScript;
			}
		}
		$arsHtmlScript = '';
		foreach($armSortScripts as $arsScript) {
			if(isset($arsScript['attribs']['conditional'])) {
				$arsHtmlScript .= '<!--[if ' . $arsScript['attribs']['conditional'] . ']>';
			}
			if($arsScript['file'] == true) {
				$arsHtmlScript .= '<script src="' . $arsScript['path'] . '" type="' . $arsScript['mime'] . '"></script>';
			}
			if($arsScript['file'] == false) {
				$arsHtmlScript .= '<script type="' . $arsScript['mime'] . '">' . $arsScript['script'] . '</script>';
			}
			if(isset($arsScript['attribs']['conditional'])) {
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
		$this->arsMetas[] = array(
			'type' => 'append',
			'type_meta' => 'name',
			'name' => $sName,
			'content' => $sContent
		);
		return $this;
	}
	
	public function offsetMeta($nOffset, $sName, $sContent) {
		$this->arsMetas[] = array(
			'type' => 'offset',
			'offset' => $nOffset,
			'type_meta' => 'name',
			'name' => $sName,
			'content' => $sContent
		);
		return $this;
	}
	
	public function prependMeta($sName, $sContent) {
		$this->arsMetas[] = array(
			'type' => 'prepend',
			'type_meta' => 'name',
			'name' => $sName,
			'content' => $sContent
		);
		return $this;
	}
	
	public function appendHttpEquiv($sName, $sContent) {
		$this->arsMetas[] = array(
			'type' => 'append',
			'type_meta' => 'http-equiv',
			'name' => $sName,
			'content' => $sContent
		);
		return $this;
	}
	
	public function offsetHttpEquiv($nOffset, $sName, $sContent) {
		$this->arsMetas[] = array(
			'type' => 'http-equiv',
			'offset' => $nOffset,
			'type_meta' => 'name',
			'name' => $sName,
			'content' => $sContent
		);
		return $this;
	}
	
	public function prependHttpEquiv($sName, $sContent) {
		$this->arsMetas[] = array(
			'type' => 'prepend',
			'type_meta' => 'http-equiv',
			'name' => $sName,
			'content' => $sContent
		);
		return $this;
	}
	
	public function getMeta() {
		$armSortMetas = array();
		foreach($this->arsMetas as $arsMeta) {
			if($arsMeta['type'] == 'prepend') {
				array_unshift($armSortMetas, $arsMeta);
			} else if($arsMeta['type'] == 'offset') {
				$nOffset = $arsMeta['offset'];
				$armSortMetas[$nOffset] = $arsMeta;
			} else if($arsMeta['type'] == 'append') {
				$armSortMetas[] = $arsMeta;
			}
		}
		$arsHtmlMeta = '';
		foreach($armSortMetas as $arsMeta) {
			$arsHtmlMeta .= '<meta ' . $arsMeta['type_meta'] . '="' . $arsMeta['name'] . '" content="' . $arsMeta['content'] . '" />' . PHP_EOL;
		}
		return $arsHtmlMeta;
	}
}
?>