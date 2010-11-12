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
        parent::__construct(APP_DIR."view".DIRECTORY_SEPARATOR, ".xhtml");

        try {
            include_once "phptal/PHPTAL.php";
        }
        catch(FrameEx $ex) {
            $ex->setMessage("PHPTAL not installed");
            throw $ex;
        }
        
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
        try {
            $this->phptal->setTemplate($this->template);
            return $this->phptal->execute();
        }
        catch (Exception $e) {
            $ex = new FrameEx($e->getMessage());
            $ex->backtrace = $e->getTrace();
            throw $ex;
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

