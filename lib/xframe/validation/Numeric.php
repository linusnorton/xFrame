<?php

namespace xframe\validation;

class Numeric implements Validator {

    /**
     * Checkes if a given value is numeric or not
     * @param mixed $value
     * @return boolean
     */
    public function validate($value) {
        return is_numeric($value);
    }

}

