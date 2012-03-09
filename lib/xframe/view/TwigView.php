<?php

namespace xframe\view;
use \Twig_Environment;
use \Twig_Loader_Filesystem;
use \xframe\registry\Registry;

/**
 * TwigView is the view for Fabien Potiencier's Twig templating language.
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class TwigView extends TemplateView {
    
    /**
     * @var Twig
     */
    private $twig;


    /**
     * Creates the Twig objects
     *
     * @param Registry $registry
     * @param string $root
     * @param string $tmpDir
     * @param string $template
     * @param boolean $debug
     */
    public function  __construct(Registry $registry,
                                 $root,
                                 $tmpDir,
                                 $template,
                                 $debug = false) {
        parent::__construct("", ".html", $template);
        $this->model = array();
        
        $this->twig = new Twig_Environment(
            new Twig_Loader_Filesystem($root."view".DIRECTORY_SEPARATOR),
            array(
                'cache' => $tmpDir,
                'debug' => $debug,
                'auto_reload' => $registry->get("AUTO_REBUILD_TWIG")
            )
        );
    }

    /**
     * Use Twig to generate some HTML
     * @return string
     */
    public function execute() {
        $template = $this->twig->loadTemplate($this->template);
        return $template->render($this->attributes);
    }

}

