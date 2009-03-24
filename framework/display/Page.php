<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @version 0.1
 * @package display
 *
 * This object provides the view transformation. It takes XML and an XSLT file and transforms them
 */
class Page {
    private static $exceptions = array();
    private static $errors = array();
    private static $staticIncludes = array();
    private static $parameters = array();
    public static $xsl = null;
    public static $xml = "";

    public static function display() {
        $xsl = new DomDocument;

        if (!file_exists(self::$xsl)) {
            throw new MalformedPage("Could not locate xsl file: ".self::$xsl);
        }
        if (!$xsl->load(self::$xsl)) {
            throw new MalformedPage("There are errors in the xsl file: ".self::$xsl);
        }

        $xml = '<?xml version="1.0" encoding="utf-8"?><root xmlns:xi="http://www.w3.org/2001/XInclude">';

        foreach (self::$staticIncludes as $inc) {
            $xml .= '
                    <xi:include href="'.ROOT.$inc.'">
                        <xi:fallback>
                            <error>xinclude: '.ROOT.$inc.' not found</error>
                        </xi:fallback>
                    </xi:include>';
        }
        $xml .= self::$xml;
        $xml .= "<errors>".implode(self::$errors)."</errors>";
        $xml .= "<exceptions>".implode(self::$exceptions)."</exceptions>";
        $xml .= "</root>";

        $dom = new DOMDocument;
        $dom->loadXML($xml);
        $dom->xinclude();        // substitute xincludes

        $xslt = new Xsltprocessor;
        $xsl = $xslt->importStylesheet($xsl);
        $xslt->setParameter(null, self::$parameters);
        $transformation = $xslt->transformToXml($dom);

        if ($_GET["debug"] == "true") {
            echo "<strong>Execution Time: ";
            echo number_format(microtime(true) - $GLOBALS["executionTime"], 2);
            echo " secs</strong><br /><strong>Page XML</strong><br /><pre>";
            $xml = str_replace("<", "&lt;" , $xml);
            $xml = str_replace(">", "&gt;" , $xml);
            echo $xml;
            echo "</pre>";
        }
        else if ($_GET["debug"] == "xml" ) {
            header("content-type: text/xml");
            die($xml);
        }
        else {
            echo $transformation;
        }

    }

    public static function addXML($xml) {
        self::$xml .= $xml;
    }

    public static function addException($xml) {
        self::$exceptions[] = $xml;
    }

    public static function addError($xml) {
        self::$errors[] = $xml;
    }

    public static function addParameter($key, $value) {
        self::$parameters[$key] = $value;
    }

    public static function addStaticInclude($path) {
        self::$staticIncludes[] = $path;
    }

    public static function displayErrors() {
        self::$xml = "";
        self::$staticIncludes = array();
        self::$parameters = array();
        self::$xsl = ROOT."app/xsl/error.xsl";

        self::display();
    }
}