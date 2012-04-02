<?php

namespace xframe\request;
use \xframe\core\DependencyInjectionContainer;

/**
 * @package request
 *
 * The prefilter runs before a request is executed, it can used to provide
 * authentication and other goodies.
 */
abstract class Prefilter {

    /**
     *
     * @var DependencyInjectionContainer 
     */
    protected $dic;

    /**
     *
     * @param DependencyInjectionContainer $dic
     */
    public function __construct(DependencyInjectionContainer $dic) {
        $this->dic = $dic;
    }

    public abstract function run(Request $request, Controller $controller);

}