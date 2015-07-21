<?php
class Pabana_Mail {
	private $_armRecipient = array(
		'to' => array(),
		'cc' => array(),
		'bcc' => array()
	);
	private $_armAttachment = array();
	private $_armSender = array();
	private $_armReply = array();
	private $_sSubject = 'No title';
	private $_sHtmlContent = '';
	private $_sTextContent = '';
	private $_sMailer = 'Pabana Mail';
	private $_nPriority = 1;
	private $_sBoundary;
	private $_sCharset;
	private $_sMailContent;
	private $_sHeaderContent;
	
	public function __construct(){
		$this->setBoundary();
		$this->_sCharset = strtolower($GLOBALS['pabanaConfigStorage']['pabana']['charset']);
	}
	
	private function addAttachment($sAttachmentPath){
		$this->_armAttachment[] = array($sAttachmentPath);
	}
	
	private function addRecipient($sRecipientType, $sRecipientAddress, $sRecipientName = ''){
		if(filter_var($sRecipientAddress, FILTER_VALIDATE_EMAIL)) {
			$this->_armRecipient[$sRecipientType][] = array($sRecipientAddress, $sRecipientName);
		}
	}
	
	public function addRecipientTo($sRecipientAddress, $sRecipientName = ''){
		$this->addRecipient('to', $sRecipientAddress, $sRecipientName);
	}
	
	public function addRecipientCc($sRecipientAddress, $sRecipientName = ''){
		$this->addRecipient('cc', $sRecipientAddress, $sRecipientName);
	}
	
	public function addRecipientBcc($sRecipientAddress, $sRecipientName = ''){
		$this->addRecipient('bcc', $sRecipientAddress, $sRecipientName);
	}
	
	public function setCharset($sCharset){
		$this->_sCharset = $sCharset;
	}
	
	public function setSender($sSenderAddress, $sSenderName = ''){
		$this->_armSender = array($sSenderAddress, $sSenderName);
	}
	
	public function setReply($sReplyAddress, $sReplyName = ''){
		$this->_armReply = array($sReplyAddress, $sReplyName);
	}
	
	public function setSubject($sSubject){
		$this->_sSubject = $sSubject;
	}
	
	public function setHtmlContent($sHtmlContent){
		$this->_sHtmlContent = $sHtmlContent;
	}
	
	public function setTextContent($sTextContent){
		$this->_sTextContent = $sTextContent;
	}
	
	public function setMailer($sMailer){
		$this->_sMailer = $sMailer;
	}
	
	public function setPriority($nPriority){
		$this->_nPriority = $nPriority;
	}
	
	private function setBoundary() {
		$this->_sBoundary = uniqid('Pabana-Mail-') . '-' . md5(rand());
		$this->_sBoundaryAlt = uniqid('Pabana-Mail-') . '-' . md5(rand());
	}
	
	public function getSender() {
		if(!empty($this->_armSender)) {
			$sSender = '';
			if(!empty($this->_armSender[1])) {
				$sSender .= '"' . $this->_armSender[1] . '" ';
			}
			$sSender .= '<' . $this->_armSender[0] . '>';
			return $sSender;
		} else {
			return NULL;
		}
	}
	
	public function getReply() {
		if(!empty($this->_armReply)) {
			$sReply = '';
			if(!empty($this->_armReply[1])) {
				$sReply .= '"' . $this->_armReply[1] . '" ';
			}
			$sReply .= '<' . $this->_armReply[0] . '>';
			return $sReply;
		} else {
			return NULL;
		}
	}
	
	public function getRecipientTo() {
		return $this->getRecipient('to');
	}
	
	public function getRecipientCc() {
		return $this->getRecipient('cc');
	}
	
	public function getRecipientBcc() {
		return $this->getRecipient('bcc');
	}
	
	private function getRecipient($sRecipientArray) {
		$armRecipientList = array();
		if(!empty($this->_armRecipient[$sRecipientArray])) {
			$armRecipientList = '';
			foreach($this->_armRecipient[$sRecipientArray] as $armRecipient) {
				$sRecipient = '';
				if(!empty($armRecipient[1])) {
				$sRecipient .= '"' . $armRecipient[1] . '" ';
				}
				$sRecipient .= '<' . $armRecipient[0] . '>';
				$armRecipientList[] = $sRecipient;
			}
		}
		return implode(', ', $armRecipientList);
	}
	
	public function getHeaderContent() {
		/* Header content */
		$this->_sHeaderContent = '';
		$sSender = $this->getSender();
		if(!empty($sSender)) {
			$this->_sHeaderContent .= 'From: ' . $sSender . PHP_EOL;
		}
		$sReply = $this->getReply();
		if(!empty($sReply)) {
			$this->_sHeaderContent .= 'Reply-to: ' . $sReply . PHP_EOL;
		}
		$sRecipientCc = $this->getRecipientCc();
		if(!empty($sRecipientCc)) {
			$this->_sHeaderContent .= 'Cc: ' . $sRecipientCc . PHP_EOL;
		}
		$sRecipientBcc = $this->getRecipientBcc();
		if(!empty($sRecipientBcc)) {
			$this->_sHeaderContent .= 'Bcc: ' . $sRecipientBcc . PHP_EOL;
		}
		if(!empty($this->_sMailer)) {
			$this->_sHeaderContent .= 'X-Mailer: ' . $this->_sMailer . PHP_EOL;
		}
		$this->_sHeaderContent .= 'MIME-Version: 1.0' . PHP_EOL;
		if(!empty($this->_armAttachment)) {
			$sContentType = 'multipart/mixed';
		} else {
			$sContentType = 'multipart/alternative';
		}
		$this->_sHeaderContent .= 'Content-Type: ' . $sContentType . '; boundary="' . $this->_sBoundary . '"' . PHP_EOL . PHP_EOL;
	}
	
	public function getEmailContent() {
		// Load localization class
		$oLocalization = new Pabana_Localization();
		/* Text content */
		if(!empty($this->_sTextContent)) {
			if($GLOBALS['pabanaConfigStorage']['pabana']['charset'] != $this->_sCharset) {
				$sTextContent = $oLocalization->changeCharset($this->_sTextContent, $GLOBALS['pabanaConfigStorage']['pabana']['charset'], $this->_sCharset);
			} else {
				$sTextContent = $this->_sTextContent;
			}
			$this->_sMailContent .= '--' . $this->_sBoundary . PHP_EOL;
			$this->_sMailContent .= 'Content-Type: text/plain; charset="' . $this->_sCharset . '"' . PHP_EOL;
			$this->_sMailContent .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
			$this->_sMailContent .= $sTextContent . PHP_EOL . PHP_EOL;
		}
		/* Html content */
		if(!empty($this->_sHtmlContent)) {
			if($GLOBALS['pabanaConfigStorage']['pabana']['charset'] != $this->_sCharset) {
				$sHtmlContent = $oLocalization->changeCharset($this->_sHtmlContent, $GLOBALS['pabanaConfigStorage']['pabana']['charset'], $this->_sCharset);
			} else {
				$sHtmlContent = $this->_sHtmlContent;
			}
			$this->_sMailContent .= '--' . $this->_sBoundary . PHP_EOL;
			$this->_sMailContent .= 'Content-Type: text/html; charset="' . $this->_sCharset . '"' . PHP_EOL;
			$this->_sMailContent .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
			$this->_sMailContent .= $sHtmlContent . PHP_EOL . PHP_EOL;
		}
		$this->_sMailContent .= '--' . $this->_sBoundary . '--';
	}
	
	public function save(){
		
	}
	
	public function send(){
		$this->getHeaderContent();
		$this->getEmailContent();
		return mail($this->getRecipientTo(), $this->_sSubject, $this->_sMailContent, $this->_sHeaderContent);
	}
}
?>