<?php

/**
 * PHPView provides a PHP view interface - you can script a view in PHP
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class PHPView extends View {
    private $data;
    /**
     * Constructor sets up the PHPTAL object
     */
    public function  __construct() {
        parent::__construct(APP_DIR."view".DIRECTORY_SEPARATOR, ".php");
        $this->data = array();
    }

    /**
     * Add data to the PHPTAL view
     * @param mixed $data
     * @param mixed $key
     */
    public function add($data, $key = null) {
        if ($key == null) {
            throw new FrameEx("You cannot add data to a PHP view without a key");
        }
        $this->data[$key] = $data;
    }

    /**
     * Use a PHP script to generate the output. Unlike an XSLTView the PHP
     * script will output as it goes (unless COMPRESS_OUTPUT is true)
     * @return 
     */
    public function execute() {
        include($this->template);
    }

    /**
     * Fall back to a default error view.
     * @return string
     */
    public function getErrorPage() {
        $this->template = $this->viewDirectory.Registry::get("ERROR_VIEW").$this->viewExtension;
        return $this->execute();
    }

    /**
     * Add the data
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        $this->add($value, $key);
    }

    /**
     * Get the data
     * @param string $key
     * @param mixed $value
     */
    public function __get($key) {
        return $this->data[$key];
    }

}

