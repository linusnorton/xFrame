<?php

namespace xframe\validation;

/**
 * Provides regular expression validation of strings 
 */
class Regex implements Validator {

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var int
     */
    private $flags;

    /**
     * @var offset
     */
    private $offset;

    /**
     *
     * @param string $pattern
     * @param int $flags
     * @param int $offset
     */
    public function __construct($pattern, $flags = 0, $offset = 0) {
        $this->pattern = $pattern;
        $this->flags = $flags;
        $this->offset = $offset;
    }

    /**
     * Checkes if a given value matches a regular expression pattern
     * 
     * @param mixed $value
     * @return boolean
     */
    public function validate($value) {
        $result = preg_match(
            $this->pattern, 
            $value, 
            $null,
            $this->flags, 
            $this->offset
        );
        
        return (boolean) $result;
    }

}
