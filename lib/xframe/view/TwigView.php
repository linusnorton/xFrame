<?php

namespace xframe\view;
use \Twig_Environment;
use \Twig_Loader_Filesystem;

/**
 * TwigView is the view for Fabien Potiencier's Twig templating language.
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class TwigView extends View {
    
    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var array
     */
    private $model;

    /**
     * Creates the Twig objects
     * 
     * @param string $root
     * @param string $template
     * @param boolean $debug 
     */
    public function  __construct($root, $template, $debug = false) {
        parent::__construct("", ".html", $template);
        $this->model = array();
        
        $this->twig = new Twig_Environment(
            new Twig_Loader_Filesystem($root."view".DIRECTORY_SEPARATOR),
            array(
                'cache' => $root."tmp".DIRECTORY_SEPARATOR,
                'debug' => $debug
            )
        );
    }

    /**
     * Add data to the Twig view
     * @param mixed $data
     * @param mixed $key
     */
    public function add($data, $key = null) {
        if ($key != null) {
            $this->model[$key] = $data;
        }
    }

    /**
     * Use Twig to generate some HTML
     * @return string
     */
    public function execute() {
        $template = $this->twig->loadTemplate($this->template);
        return $template->render($this->model);
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
     * Pass the magic set on to Twig
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        $this->add($value, $key);
    }
}

