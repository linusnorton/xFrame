<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
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

    /**
     * Transform the XSL and XML and return out the result
     */
    public static function build() {
        $xsl = new DomDocument;

        //if the xsl has not been set or has been set incorrectly
        if (!file_exists(self::$xsl)) {
            throw new MalformedPage("Could not locate xsl file: ".self::$xsl);
        }

        //if the xsl contained errors
        if (!$xsl->load(self::$xsl)) {
            throw new MalformedPage("There are errors in the xsl file: ".self::$xsl);
        }

        $xml = '<?xml version="1.0" encoding="utf-8"?><root xmlns:xi="http://www.w3.org/2001/XInclude">';

        //add some xincludes
        foreach (self::$staticIncludes as $inc) {
            $xml .= '
                    <xi:include href="'.ROOT.$inc.'">
                        <xi:fallback>
                            <error>xinclude: '.ROOT.$inc.' not found</error>
                        </xi:fallback>
                    </xi:include>';
        }

        //add the xml that ive been given and errors and exceptions generated
        $xml .= self::$xml;
        $xml .= "<errors>".implode(self::$errors)."</errors>";
        $xml .= "<exceptions>".implode(self::$exceptions)."</exceptions>";
        $xml .= "</root>";

        $dom = new DOMDocument;
        if (!$dom->loadXML($xml)) {
            throw new MalformedPage("There was an error inside the xml:\n". htmlentities($xml));
        }

        $dom->xinclude();

        $xslt = new XSLTProcessor();
        $xslt->importStylesheet($xsl);
        $xslt->setParameter(null, self::$parameters);

        //unfortunately this doesn't capture any warnings generated whilst transforming
        if (!$transformation = $xslt->transformToXml($dom)) {
            throw new MalformedPage("There was an error tranforming the page");
        }

        //later I will make an option to turn this off
        if ($_GET["debug"] == "true") {
            $return = "<strong>Execution Time: ";
            $return .= number_format(microtime(true) - $GLOBALS["executionTime"], 2);
            $return .=" secs</strong><br /><strong>Page XML</strong><br /><pre>";
            $xml = str_replace("<", "&lt;" , $xml);
            $xml = str_replace(">", "&gt;" , $xml);
            $return .= $xml;
            $return .= "</pre>";
            return $return;
        }
        else if ($_GET["debug"] == "xml" ) {
            header("content-type: text/xml");
            return $xml;
        }
        else {
            return $transformation;
        }

    }

    /**
     * Adds the given XML to the XML for the page
     *
     * @param $xml string xml to add to the page
     */
    public static function addXML($xml) {
        self::$xml .= $xml;
    }

    /**
     * Adds the given XML to the exception XML for the page
     *
     * @param $xml string xml to add to the exceptions
     */
    public static function addException($xml) {
        self::$exceptions[] = $xml;
    }

    /**
     * Adds the given XML to the error XML for the page
     *
     * @param $xml string xml to add to the errors
     */
    public static function addError($xml) {
        self::$errors[] = $xml;
    }

    /**
     * Adds a parameter to be passed to the XSL, these must be scalar (I think)
     *
     * @param $key string key of the param
     * @param $value mixed value of the parameter
     */
    public static function addParameter($key, $value) {
        self::$parameters[$key] = $value;
    }

    /**
     * Adds the given XML file to the xinclude list for the page
     *
     * @param $path string filepath of the xml file to add to the static incs
     */
    public static function addStaticInclude($path) {
        self::$staticIncludes[] = $path;
    }

    /**
     * Apply the default error.xsl to the xml. This is a fallback method that outputs
     * a simple XHTML page with all the warnings, errors and exceptions on it
     */
    public static function displayErrors() {
        self::$xml = "";
        self::$staticIncludes = array();
        self::$parameters = array();
        self::$xsl = ROOT.Registry::get("ERROR_XSL");

        return self::build();
    }
}