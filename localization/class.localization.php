<?php
/**
* Pabana Framework : Localization Module (http://pabana.co)
*
* Licensed under new BSD License
* For full copyright and license information, please see the LICENSE.txt
*
* @link      	http://github.com/thforax/pabana for the canonical source repository
* @copyright 	Copyright (c) 2014-2015 FuturaSoft (http://www.futurasoft.net)
* @license   	http://pabana.co/about/license New BSD License
* @version		1.0.0.0
*/

/**
 * Pabana_Localization : Manipulation of charset and human language 
 *
 * Manipulation of charset and human language 
 *
 * @link http://pabana.co/documentation/class/name/localization
 */
class Pabana_Localization {
	
	/**
	 * Change charset of a string or array of strings
	 *
	 * @param string|array $mValue variable who must change of charset
	 * @param string $sInCharset Actual charset of $mValue
	 * @param string $sOutCharset Actual charset of $mValue
	 * @param boolean $bTranslit Enable transliteration
	 * @param boolean $bIgnore Enable ignore if char doesn't exist in out charset
	 * @return string|array Returns User string convert between in and out charset
	 */
	public function changeCharset($mValue, $sInCharset, $sOutCharset, $bTranslit = true, $bIgnore = true) {
		$mReturn = $mValue;
		if(is_string($mValue)) {
			if($bTranslit === true) {
				$sOutCharset .= '//TRANSLIT';
			} else if($bIgnore === true) {
				$sOutCharset .= '//IGNORE';
			}
			$mReturn = @iconv($sInCharset, $sOutCharset, $mValue);
		} else if(is_array($mValue)) {
			$mReturn = array();
			foreach($mValue as $mArrayKey=>$mArrayValue) {
				$mArrayKey = $this->changeCharset($mArrayKey, $sInCharset, $sOutCharset, $bTranslit, $bIgnore);
				$mArrayValue = $this->changeCharset($mArrayValue, $sInCharset, $sOutCharset, $bTranslit, $bIgnore);
				$mReturn[$mArrayKey] = $mArrayValue;
			}
		}
		return $mReturn;
	}
}
?>