<?php

namespace xframe\view;
use \xframe\registry\Registry;

/**
 * PHPView is the view for the pure PHP view scripts.
 */
class PHPView extends TemplateView {
    
    /**
     * @var boolean
     */
    private $debug;

    /**
     * Set up the view
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
        parent::__construct(
            $root."view".DIRECTORY_SEPARATOR,
            ".phtml",
            $template
        );

        $this->debug = $debug;
    }

    /**
     * Generate some HTML
     * @return string
     */
    public function execute() {
        // capture output
        ob_start();
        // run view
        require $this->template;
        // store result
        $result = ob_get_contents();
        // turn off the output buffer
        ob_end_clean();

        return $result;
    }

}

