<?php

/**
 * This view uses an XSL transformation to generate HTML
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class XSLTView extends View {
    const OUTPUT_XSL = 0,
          OUTPUT_XML = 1,
          OUTPUT_OFF = 2,
          OUTPUT_XML_RAW = 3;

    private $outputMode;
    private $staticIncludes;
    private $staticIncludeDirectory;
    private $staticIncludeExtension;
    private $data;

    /**
     * Build the XSLT View
     */
    public function __construct($debugMode = false,
                                $viewDirectory = "view/",
                                $viewExtension = ".xsl",
                                $staticIncludeDirectory = "xml/",
                                $staticIncludeExtension = ".xml") {
        parent::__construct(APP_DIR.$viewDirectory, $viewExtension);

        $this->outputMode = self::OUTPUT_XSL;
        $this->staticIncludeDirectory = APP_DIR.$staticIncludeDirectory;
        $this->staticIncludeExtension = $staticIncludeExtension;
        $this->data = "";
        $this->staticIncludes = array();

        if ($debugMode && Registry::get("DEBUG_ENABLED")) {
            $this->outputMode = self::OUTPUT_XML;
        }
    }

    /**
     * @param $xml
     */
    public function add($xml, $key = null) {
        if ($xml instanceof XML) {
            $this->data .= $xml->getXML();
        }
        else if ($xml instanceof DomDocument) {
            $this->data .= $xml->saveXML();
        }
        else if (is_array($xml)) {
            $this->data .= ArrayUtil::getXML($xml);
        }
        else {
            $this->data .= $xml;
        }
    }

    /**
     * Set the output mode (use the class consts)
     * @param int $mode
     */
    public function setOutputMode($mode) {
        $this->outputMode = $mode;
    }

    /**
     * Build the transformation and output
     */
    public function execute() {
        if ($this->outputMode == self::OUTPUT_OFF) {
            return; //nothing to do
        }

        if ($this->outputMode == self::OUTPUT_XML_RAW) {
            if (!headers_sent()) {
                header("content-type: text/xml");
            }
            return $this->data;
        }

        $xml = $this->generateXML();
        $transformation = new Transformation($xml, $this->template);

        if ($this->outputMode == self::OUTPUT_XML) {
            if (!headers_sent()) {
                header("content-type: text/xml");
            }
            return $transformation->getXML();
        }

        return $transformation->execute($this->parameters);
    }

    private function generateXML() {
        $xml = '<?xml version="1.0" encoding="utf-8"?><root xmlns:xi="http://www.w3.org/2001/XInclude">';

        //add some xincludes
        foreach ($this->staticIncludes as $inc) {
            $xml .= '
                    <xi:include href="'.ROOT.$inc.'">
                        <xi:fallback>
                            <error>xinclude: '.ROOT.$inc.' not found</error>
                        </xi:fallback>
                    </xi:include>';
        }

        //add the xml that ive been given and errors and exceptions generated
        $xml .= $this->data;
        $xml .= "<exceptions>";

        foreach ($this->exceptions as $ex) {
            $xml .= $ex->getXML();
        }

        $xml .= "</exceptions>";
        $xml .= "</root>";

        return $xml;
    }

    /**
     * Adds a parameter to be passed to the XSL, these must be scalar (I think)
     *
     * @param $key string key of the param
     * @param $value mixed value of the parameter
     */
    public function addParameter($key, $value) {
        $this->parameters[$key] = $value;
    }

    /**
     * Clears the page parameters
     */
    public function clearParameters() {
        $this->parameters = array();
    }

    /**
     * Adds the given XML file to the xinclude list for the page
     *
     * @param $path string filepath of the xml file to add to the static incs
     */
    public function addStaticInclude($path) {
        $this->staticIncludes[] = $this->staticIncludeDirectory.$path.$this->staticIncludeExtension;
    }

    /**
     * Apply the default error.xsl to the xml. This is a fallback method that outputs
     * a simple XHTML page with all the warnings, errors and exceptions on it
     */
    public function getErrorPage() {
        $this->template = ROOT.Registry::get("ERROR_VIEW");
        $this->outputMode = self::OUTPUT_XSL;
        return $this->execute();
    }

}
