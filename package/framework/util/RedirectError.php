<?php
/**
 * Persist errors in XML accross page views
 *
 * @author Dominic Webb <dominic.webb@assertis.co.uk>, Jason Paige
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

        foreach ($_SESSION['error_redirect'] as $key => $val) {

           $code = ErrorCodes::getDesc($val);

           if (empty($code['desc'])) {
               $code['desc'] = "Unknown error code description '{$val}'";
           }

           $xml .= "<error code=\"{$val}\">{$code['desc']}</error>";
        }
        Page::addXML("<user-error>{$xml}</user-error>");
        unset($_SESSION['error_redirect']);
        return true;
    }

}
?>
