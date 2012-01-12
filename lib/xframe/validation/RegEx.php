<?php

namespace xframe\validation;

class Regex implements Validator {

    /**
     *
     * @var string
     */
    protected $pattern;

    /**
     *
     * @var int
     */
    protected $flags;

    /**
     *
     * @var offset
     */
    protected $offset;

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
     * @param mixed $value
     * @return boolean
     */
    public function validate($value) {
        return (boolean)preg_match($this->pattern, $value, $null, $this->flags, $this->offset);
    }

}
