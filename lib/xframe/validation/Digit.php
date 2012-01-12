<?php

namespace xframe\validation;

class Digit implements Validator {

    /**
     * Checkes if a given value contains only digits
     * @param mixed $value
     * @return boolean
     */
    public function validate($value) {
        return ctype_digit("{$value}");
    }

}

