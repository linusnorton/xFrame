<?php

namespace xframe\request;

use \xframe\validation\Validator;

/**
 * Request parameter. Encapsulates the validation logic 
 */
class Parameter {

    /**
     * Name to be used as the variable name of the parameter
     * @var string
     */
    private $name;

    /**
     * Instance of a Validator if present
     * @var Validator
     */
    private $validator;

    /**
     * @var boolean
     */
    private $required;

    /**
     * The default value to use for this parameter
     * @var mixed
     */
    private $default;

    /**
     * @param string $name
     * @param Validator $validator
     * @param boolean $required
     * @param mixed $default
     */
    public function __construct($name,
                                Validator $validator = null,
                                $required = false,
                                $default = null) {
        $this->name = $name;
        $this->validator = $validator;
        $this->required = $required;
        $this->default = $default;
    }

    /**
     *
     * @return boolean
     */
    public function isRequired() {
        return $this->required;
    }

    /**
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     *
     * @return mixed
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * @return boolean
     */
    public function validate($value) {
        //if there is no validator, or the value validates return true
        if ($this->validator == null || $this->validator->validate($value)) {
            return true;
        }
        
        // otherwise there was an error validating
        throw new InvalidParameterEx("Value {$value} is not valid for parameter {$this->name} using validator ".get_class($this->validator).".");
    }

}

