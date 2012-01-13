<?php

namespace xframe\validation;

/**
 * Validate an input as a numeric digit 
 */
class Digit implements Validator {

    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $max;

    /**
     * @param int $min
     * @param int $max
     */
    public function __construct($min = null, $max = null) {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Checkes if a given value contains only digits and is within the min and
     * max constraints
     * 
     * @param mixed $value
     * @return boolean
     */
    public function validate($value) {
        if (!ctype_digit("{$value}")) {
            return false;
        }
        if ($this->min != null && $value < $this->min) {
            return false;
        }
        if ($this->max != null && $value > $this->max) {
            return false;
        }
        
        return true;
    }

}

