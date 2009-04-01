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
    public static isEmail($email) {
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
    public static isLength($input, $length) {
        return (strlen($input) >= $length);
    }

    /**
     * check to see if the given input is not empty (has a length > 1)
     *
     * @param $input string string to check
     */
    public static notEmpty($input) {
        return isset($input[0]);
    }

    /**
     * Check to see if the string === null
     *
     * @param $input string string to check
     */
    public static notNull($input) {
        return $input === null;
    }

}

?>