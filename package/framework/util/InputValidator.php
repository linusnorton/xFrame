<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package util
 *
 * This is not a sanitizor, it just length, valid email etc
 */
class InputValidator {

    /**
     * Validate the given string to see if it is valid email
     * 
     * @param $email string email address input to validate
     */ 
    public static function isEmail($email) {
        $pattern = '/^([\w\-\.]+)@((\[([0-9]{1,3}\.){3}[0-9]{1,3}\])|(([\w\-]+\.)+)([a-zA-Z]{2,4}))$/';
        return (preg_match($pattern, $email)) ? true : false;        
    }

    /**
     * checks to see if the given string is greater than or
     * less than the given length
     *
     * @param $input string string to check
     * @param $length int length to check
     */
    public static function isLength($input, $length) {
        return (strlen($input) >= $length);
    }

    /**
     * check to see if the given input is not empty (has a length > 1)
     *
     * @param $input string string to check
     */
    public static function isEmpty($input) {
        return empty($input);
    }

    /**
     * Check to see if the string === null
     *
     * @param $input string string to check
     */
    public static function notNull($input) {
        return $input === null;
    }

    /**
     * Check to see if the string is a sha1 hash
     *
     * @param $input string string to check
     */
    public static function isSha1($input) {
        return preg_match("/^[a-f0-9]{40}$/", strtolower($input));
    }

    /**
     * Returns true if the given string is between the given lengths
     * @param string $input
     * @param int $minChars
     * @param int $maxChars
     * @return boolean
     */
    public static function isBetweenLength($input, $minChars, $maxChars) {
        $length = strlen($input);
        return ($length >= $minChars && $length <= $maxChars);
    }


     /**
     * Check if a string is in fact a PHP seralized string or not
     *
     * Bit ungracefull in forcing an error and then disabling the output
     * but PHP doesn't leave you with many options here.
     *
     * NOTE: A seralized boolean false will cause this function to fail.
     *
     * $data === "b:0;" - this checks for a seralized boolean false; cant
     * imagine why anyone would seralize this so its not checked for.
     * If you find you need this, perhaps you should look at why you are
     * seralizing a boolean value.
     *
     * @param string $data
     * @return boolean
     */
    public static function isSerialized($data) {
        return (@unserialize($data) !== false);
    }


    /**
     * Check if a $string is valid XML. This does not check against any schema
     * so it is just seing if it is well formed XML.
     *
     * @param string $string
     * @return boolean
     */
    public static function isValidXml ($string) {
        try {
            DOMDocument::loadXML($string);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Check if $postCode is a valid postcode.
     * Based on the rules at: http://www.mrs.org.uk/standards/downloads/postcodeformat.pdf
     * @param <type> $postCode
     * @return boolean
     */

    public static function isPostCode($postCode) {
        $pattern = '/^([A-PR-UWYZ0-9][A-HK-Y0-9][AEHMNPRTVXY0-9]?[ABEHMNPRVWXY0-9]? {1,2}[0-9][ABD-HJLN-UW-Z]{2}|GIR 0AA)$/';
        return (preg_match($postCode, $email));
    }
}
?>