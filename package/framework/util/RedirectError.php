<?php
/**
 * Persist errors in XML accross page views
 * 
 * @author Dominic Webb <dominic.webb@assertis.co.uk>
 */
class RedirectError {


    /**
     * Add an error code
     * @param integer $error
     */
    public static function add($error) {      
        $_SESSION['error_redirect'][] = $error;
    }


    /**
     * Determines if there are errors that have been set
     * @return boolean
     */
    public static function hasErrors () {
        return (isset($_SESSION['error_redirect']));
    }


    /**
     *  Builds the error XML and unsets the persisted errors. Always returns true
     *
     * @return true
     */
    public static function buildErrors () {

        while (list($key, $val) = each($_SESSION['error_redirect'])) {
           
           $code = ErrorCodes::getDesc($val);

           if (empty($code['desc'])) {
               $code['desc'] = "Unkown error code descrption '{$val}'";
           }

           $xml .= "<error code=\"{$val}\">{$code['desc']}</error>";
        }
        Page::addXML("<user-error>{$xml}</user-error>");
        unset($_SESSION['error_redirect']);
        return true;
    }

}
?>
