<?php
namespace xframe\request;
use xframe\validation\Validator;

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
     * Whether or not this parameter is required
     * @var boolean
     */
    private $required;

    /**
     * The default value to use for this parameter
     * @var mixed
     */
    private $default;

    /**
     *
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

}

