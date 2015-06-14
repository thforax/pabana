<?php
class Pabana_Mail {

	private $armRecipient = '';
	private $sSenderName = '';
	private $sSenderAddress = '';
	private $sSubject = '';
	private $sHtmlContent = '';
	
	public function __construct($armSender, $armAddressRecipient, $sEmailSubject = 'E-mail automatique merci de ne pas repondre'){
		if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $armSender[1])){
			$this->sSenderName = $armSender[0];
			$this->sSenderAddress = $armSender[1];
		}
		else{
			echo 'Erreur : le format du mail de l\'expéditeur n\'est pas correct';
		}
		foreach($armAddressRecipient as $sAddressRecipient){
			if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $sAddressRecipient)){
				$this->armRecipient[] = $sAddressRecipient;
			}
			else{
				echo 'Erreur : le format du mail du destinataire n\'est pas correct';
			}
		}
		$this->sSubject = $sEmailSubject;
		$this->sHtmlContent = '';
	}
	
	public function setContent($sNewContent){
		$this->sHtmlContent = 
'<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta charset="utf-8" />
	</head>
	<body style="background: #F5F5F5;">
		<table width="100%" height="100%"cellspacing="0" cellpadding="0" border="0" bgcolor="#DFDFDF" style="font-family:Helvetica,Arial,sans-serif;border-collapse:collapse;width:100%!important;font-family:Helvetica,Arial,sans-serif;margin:0;padding:0">
			<tbody>
				<tr>
					<td>
						<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" style="table-layout:fixed">
							<tbody>
								<tr>
									<td align="center">
										<table width="800" cellspacing="0" cellpadding="0" border="0" style="font-family:Helvetica,Arial,sans-serif;min-width:290px">
											<tbody>
												<tr>
													<td>
														<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#DDDDDD" style="font-family:Helvetica,Arial,sans-serif">
															<tbody>
																<tr>
																	<td height="21" width="95" valign="middle" align="left">
																		<a style="text-decoration:none;border:none;display:block;min-height:21px;width:100%" href="http://dev.chauffeurexpert.com/img/logo.png" target="_blank">
																			<img class="CToWUd" height="38" width="165" src="http://dev.chauffeurexpert.com/img/logo.png" alt="ChauffeurExpert" style="border:none;text-decoration:none">
																		</a>
																	</td>
																</tr>
																<tr>
																	<td height="20" valign="middle" align="left"></td>
																</tr>
																<tr>
																	<td height="10" bgcolor="#0072c6" valign="middle" align="left"></td>
																</tr>
																<tr>
																	<td height="10" bgcolor="#FFFFFF" valign="middle" align="left">
																		<table width="100%" cellspacing="0" cellpadding="0" border="0" >
																			<tbody>
																				<tr>
																					<td width="20"></td>
																					<td align="left" style="color:#333333;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:18px">
																						<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" style="font-family:Helvetica,Arial,sans-serif">
																							<tbody>
																								<tr><td height="20"></td></tr>'.str_replace(array('\n','\r','<br>','<br />','<br/>'),'</td></tr><tr><td height="15"></td></tr><tr><td>',htmlspecialchars($sNewContent)).'
																							</tbody>
																						</table>
																					</td>
																					<td width="20"></td>
																				</tr>
																			</tbody>
																		</table>
																	</td>
																</tr>																	
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>';
	}
	
	public function setSenderName($sNewSenderName){
		$this->sSenderName = $sNewSenderName;
	}
	
	public function setSenderAddress($sNewSenderAdress){
		$this->sSenderAddress = $sNewSenderAdress;
	}
	
	public function setRecepient($armNewRecipient){
		$this->armRecipient = $armNewRecipient;
	}
	
	public function setSubject($sNewSubject){
		$this->sSubject = $sNewSubject;
	}
	
	public function send(){
		$backSpace = chr(13).chr(10);
		$boundary = '-----='.md5(rand());
		$header = 'From: "'.$this->sSenderName.'"<'.$this->sSenderAddress.'>"'.$backSpace;
		$header.= 'Reply-to: "'.$this->sSenderName.'"<'.$this->sSenderAddress.'>"'.$backSpace;
		$header.= 'MIME-Version: 1.0'.$backSpace;
		$header.= 'Content-Type: multipart/alternative;'.$backSpace.' boundary="'.$boundary.'"'.$backSpace;
		$message = $backSpace.'--'.$boundary.$backSpace;
		$message.= 'Content-Type: text/html; charset="ISO-8859-1"'.$backSpace;
		$message.= 'Content-Transfer-Encoding: 8bit'.$backSpace;
		$message.= $backSpace.$this->sHtmlContent.$backSpace;
		$message.= $backSpace.'--'.$boundary.'--'.$backSpace;
		$message.= $backSpace.'--'.$boundary.'--'.$backSpace;
		try{
			return mail(implode(',',$this->armRecipient),$this->sSubject,$message,$header);
		}
		catch(Exception $e) { 
			echo 'Erreur : '.$e->getMessage().'<br />';
			echo 'N° : '.$e->getCode();
		}
	}
	
}
?>