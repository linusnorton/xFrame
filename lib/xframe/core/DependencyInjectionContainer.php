<?php

namespace xframe\core;
use xframe\util\Container;

/**
 * Used to store the applications dependencies.
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class DependencyInjectionContainer extends Container {

    /**
     * Stores the lambda functions that get the dependencies
     * @var array
     */
    protected $builders;
    
    /**
     * Constructor
     */
    public function __construct(array $attributes = array(),
                                array $builders = array()) {
        parent::__construct($attributes);
        $this->builders = $builders;
    }

    /**
     * Add a lambda function that returns a dependency
     *
     * @param string $name
     * @param callback $lambda
     */
    public function add($name, $lambda) {
        $this->builders[$name] = $lambda;
    }

    /**
     * If the requested dependency has not been set, if we have a lambda
     * to create it do so then return the dependency
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if (!isset($this->attributes[$name]) && isset($this->builders[$name])) {
            $this->attributes[$name] = $this->builders[$name]($this);
        }

        return $this->attributes[$name];
    }

}
