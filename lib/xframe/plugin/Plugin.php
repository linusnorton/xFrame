<?php
namespace xframe\plugin;
use xframe\core\DependencyInjectionContainer;


/**
 * Abstract class to allow developers to create classes accessible via the
 */
abstract class Plugin {

    /**
     *
     * @var xframe\core\DependencyInjectionContainer
     */
    protected $dic;

    public function __construct(DependencyInjectionContainer $dic) {
        $this->dic = $dic;
    }

    /**
     * Abstract function to be implemented by each plugin
     */
    abstract public function init();

}

