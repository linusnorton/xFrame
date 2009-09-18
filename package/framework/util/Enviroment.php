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

        // not checking if _SERVER is an array
        // if its not lets face it PHP is pretty fcuked :-)
        $xml .= "<server>";
        while (list($key, $val) = each($_SERVER)) {
            $xmlKey = str_replace("_", "-", strtolower($key));

            $try = "<{$xmlKey} realKey=\"{$key}\">{$val}</{$xmlKey}>";

            // try the xml and CDATA it if its got stuff that will cause a proble
            if (InputValidator::isValidXml($try)) {
                $xml .= $try;
            } else {
                $xml .= "<{$xmlKey} realKey=\"{$key}\"><![CDATA[ {$val} ]]></{$xmlKey}>";
            }
        }
        $xml .= "</server>";


        /*
         * SESSION  variables are an arse as they can contain seralized strings
         * which the xml parser even inside CDATA throws a mental over.
         *
         * If theres a seralized string then we assume its an object so everything
         * get var_export ed.
         */
        if (is_array($_SESSION)) {
            $xml .= "<session>";
            while (list($key, $val) = each($_SESSION)) {

                $xmlKey = str_replace("_", "-", strtolower($key));

                if (InputValidator::isSerialized($val)) {
                    $xml .= "<{$xmlKey} realKey=\"{$key}\">\n".var_export(unserialize($val), TRUE)."\n</{$xmlKey}>";
                } else {
                    $xml .= "<{$xmlKey} realKey=\"{$key}\">\n".var_export($val, TRUE)."\n</{$xmlKey}>";
                }
            }
            $xml .= "</session>";
        }

        
        if (is_array($_COOKIE)) {
            $xml .= "<cookie>";
            while (list($key, $val) = each($_COOKIE)) {
                $xmlKey = str_replace("_", "-", strtolower($key));

                $try = "<{$xmlKey} realKey=\"{$key}\">{$val}</{$xmlKey}>";
                // try the xml and CDATA it if its got stuff that will cause a problem
                if (InputValidator::isValidXml($try)) {
                    $xml .= $try;
                } else {
                    $xml .= "<{$xmlKey} realKey=\"{$key}\"><![CDATA[ {$val} ]]></{$xmlKey}>";
                }
            }
            $xml .= "</cookie>";
        }

        return "<enviroment>{$xml}</enviroment>";
    }
}
?>
