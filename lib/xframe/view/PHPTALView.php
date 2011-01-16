<?php

namespace xframe\view;
use \PHPTAL;
/**
 * PHPTAL view wrapper
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class PHPTALView extends View {

    /**
     * @var PHPTAL
     */
    private $phptal;

    /**
     * Constructor sets up the PHPTAL object
     */
    public function  __construct($root, $template, $debug = false) {
        parent::__construct(
            $root."view".DIRECTORY_SEPARATOR,
            ".xhtml",
            $template
        );
        $this->phptal = new PHPTAL();
    }

    /**
     * Add data to the PHPTAL view
     * @param mixed $data
     * @param mixed $key
     */
    public function add($data, $key = null) {
        if ($key != null) {
            $this->phptal->$key = $data;
        }
    }

    /**
     * Use PHPTAL to generate some XHTML
     * @return string
     */
    public function execute() {
        $this->phptal->setTemplate($this->template);
        return $this->phptal->execute();
    }

    /**
     * Fall back to a default error view.
     * @return string
     */
    public function getErrorPage() {
        //$this->template = ROOT.Registry::get("ERROR_VIEW");
        return $this->execute();
    }

    /**
     * Pass the magic set on to PHPTAL
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        $this->add($value, $key);
    }
}

