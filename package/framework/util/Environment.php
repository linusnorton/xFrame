<?php
/**
 * Provides the $_SERVER, $_SESSION and $_COOKIE variable arrays as XML.
 *
 * Each array index is returned as key=>value pairs with the key name lowercased
 * and the underscore character replaced with a hyphon. An un-altered string of
 * the key is set as the 'realKey' attribute.
 *
 * @author dominic.webb@assertis.co.uk
 */
class Enviroment implements XML {

    /**
     * XML representation
     * @return string XML
     */
    public function getXML () {
        $xml = ArrayUtil::getXML($_SERVER);
        $xml .= ArrayUtil::getXML($_REQUEST);
        $xml .= ArrayUtil::getXML($_COOKIE);
        $xml .= ArrayUtil::getXML($_FILES);
        return "<enviroment>{$xml}</enviroment>";
    }
}
?>
