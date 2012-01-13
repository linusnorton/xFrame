<?php
namespace xframe\validation;

/**
 * The validator interface allows objects to become annotation based validators.
 */
interface Validator {

    /**
     * Perform the validation of the given value
     * 
     * @param string $value
     * @return boolean
     */
    public function validate($value);

}
