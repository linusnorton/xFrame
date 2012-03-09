<?php

namespace xframe\view;
use \Exception;
use \xframe\util\Container;

/**
 * This abstract class specifies the requirements for a view.
 */
abstract class View extends Container {
    protected $parameters;
    protected $exceptions;

    /**
     * Initialize the view
     */
    public function __construct() {
        parent::__construct();

        $this->parameters = array();
        $this->exceptions = array();
    }

    /**
     * Add a parameter to the view for the template
     * @param string $key
     * @param mixed $value
     */
    public function addParameter($key, $value) {
        $this->parameters[$key] = $value;
    }

    /**
     * Adds the given exception to the page
     *
     * @param FrameEx $ex exception to add to the view
     */
    public function addException(FrameEx $ex) {
        $this->exceptions[] = $ex;
    }

    /**
     * Clears the exceptions on the page
     */
    public function clearExceptions() {
        $this->exceptions = array();
    }

    /**
     * Generate the contents of the page response
     */
    public abstract function execute();

}
