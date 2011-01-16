<?php

namespace xframe\util;

/**
 * Contain for arbitrary data, provides __get and __set methods
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class Container {
    private $attributes;

    public function __construct() {
        $this->attributes = array();
    }

    /**
     * Return the given property
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return $this->attributes[$key];
    }

    /**
     * Set the given property
     * 
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }

    /**
     * Returns true of the given property isset
     * 
     * @param string $key
     * @return boolean
     */
    public function __isset($key) {
        return isset($this->attributes[$key]);
    }
}
