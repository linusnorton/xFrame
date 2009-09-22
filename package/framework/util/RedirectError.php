<?php
/**
 *
 * @author Dominic Webb <dominic.webb@assertis.co.uk>
 */
class RedirectError implements XML {

    public static function add($error) {      
        $_SESSION['error_redirect'][] = $error;
    }

    public static function hasErrors () {
        return (isset($_SESSION['error_redirect']));
    }

    public static function buildErrors () {

        
        while (list($key, $val) = each($_SESSION['error_redirect'])) {
//var_dump($val);die();
//var_dump($_SESSION['error_redirect']); die();
           $desc = ErrorCodes::getDesc($val);
           $xml .= "<error code=\"{$val}\">{$desc}</error>";
        }
        Page::addXML("<user-error>{$xml}</user-error>");
        unset($_SESSION['error_redirect']);
    }

    public function getXML () {}

    public function __destructor () {
        
    }
}
?>
