<?php

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
    public function  __construct() {
        parent::__construct(APP_DIR."view/", ".xhtml");
        $this->phptal = new PHPTAL();
    }

    /**
     * Add data to the PHPTAL view
     * @param mixed $data
     * @param mixed $key
     */
    public function add($data, $key = null) {
        if ($key == null) {
            throw new FrameEx("You cannot add data to a PHPTAL view without a key");
        }
        $this->phptal->$key = $data;
    }

    /**
     * Use PHPTAL to generate some XHTML
     * @return string
     */
    public function execute() {
        try {
            $this->phptal->exceptions = $this->exceptions;
            $this->phptal->setTemplate($this->template);
            return $this->phptal->execute();
        }
        catch (Exception $e) {
            throw new FrameEx($e->getMessage(), $e->getCode, FrameEx::HIGH, $e);             
        }

    }

    /**
     * Fall back to a default error view.
     * @return string
     */
    public function getErrorPage() {
        $this->template = ROOT.Registry::get("ERROR_VIEW");
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

