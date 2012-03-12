<?php

namespace xframe\view;
use \PHPTAL;
use \xframe\registry\Registry;

/**
 * PHPTAL view wrapper
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class PHPTALView extends TemplateView {

    /**
     * @var PHPTAL
     */
    private $phptal;

    /**
     * Constructor sets up the PHPTAL object
     * @param Registry $registry
     * @param string $root
     * @param string $template
     * @param boolean $debug
     */
    public function  __construct(Registry $registry,
                                 $root,
                                 $tmpDir,
                                 $template,
                                 $debug = false) {
        parent::__construct(
            $root."view".DIRECTORY_SEPARATOR,
            ".xhtml",
            $template
        );
        $this->phptal = new PHPTAL();
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
     * Pass the magic set on to PHPTAL
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        $this->phptal->$key = $value;
    }

    /**
     * Pass the magic get on to PHPTAL
     * @param string $key
     */
    public function __get($key) {
        return $this->phptal->$key;
    }

}

