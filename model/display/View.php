<?php

/**
 * This interface specifies the requirements for a view.
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
abstract class View {
    protected $template;
    protected $parameters;
    protected $viewDirectory;
    protected $viewExtension;
    protected $exceptions;

    /**
     * Initialize the view
     */
    public function __construct($viewDirectory = null, $viewExtension = null) {
        $this->parameters = array();
        $this->exceptions = array();
        $this->viewDirectory = $viewDirectory == null ? APP_DIR."view".DIRECTORY_SEPARATOR : $viewDirectory;
        $this->viewExtension = $viewExtension;
    }

    /**
     * Set the view template file
     * @param string $template
     */
    public function setTemplate($template) {
        $this->template = $this->viewDirectory.$template.$this->viewExtension;
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
     * Add data to the view
     * @param $data
     */
    public abstract function add($data, $key = null);

    /**
     * Generate the contents of the page response
     */
    public abstract function execute();

    /**
     * Provide a fallback error page
     */
    public abstract function getErrorPage();
}
