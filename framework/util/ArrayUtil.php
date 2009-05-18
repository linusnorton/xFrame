<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package util
 *
 * This class provides some nifty functions for arrays
 */
class ArrayUtil {

    /**
     * This function recursively returns an array as XML. If it finds something that
     * implements the XML interface it will has it to display it's xml.
     *
     * @param array $array to convert to array
     */
	public static function getXML(array $array) {
		$xml = "";
		foreach ($array as $key => $value) {
			if (is_numeric($key)) {
				$key = "numericIndex".$key;
			}

			if ($value instanceof XML) {
				$xml .= "<{$key}>".$value->getXML()."</{$key}>";
			}
			else if (is_array($value)) {
				$xml .= "<{$key}>".self::getXML($value)."</{$key}>";
			}
			else {
				$xml .= "<{$key}>".htmlentities($value, ENT_COMPAT, "UTF-8", false)."</{$key}>";
			}
		}
		return $xml;
	}

}

?>