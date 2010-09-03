<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 */
class Assert {

    /**
     * Assert the given values are equal (weak ==)
     * @param mixed $value1
     * @param mixed $value2
     * @return boolean
     * @throws FrameEx
     */
    public static function equal($value1, $value2, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if ($value1 == $value2) {
            return true;
        }

        $message = is_null($message) ? "Failed to assert {$value1} == {$value2}" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Assert the given values are not equal (weak ==)
     * @param mixed $value1
     * @param mixed $value2
     * @return boolean
     * @throws FrameEx
     */
    public static function notEqual($value1, $value2, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if ($value1 != $value2) {
            return true;
        }

        $message = is_null($message) ? "Failed to assert {$value1} != {$value2}" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Assert the given values are equal (strong ===)
     * @param mixed $value1
     * @param mixed $value2
     * @return boolean
     * @throws FrameEx
     */
    public static function identical($value1, $value2, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if ($value1 === $value2) {
            return true;
        }

        $message = is_null($message) ? "Failed to assert {$value1} === {$value2}" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Assert the given values are not equal (weak ==)
     * @param mixed $value1
     * @param mixed $value2
     * @return boolean
     * @throws FrameEx
     */
    public static function notIdentical($value1, $value2, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if ($value1 !== $value2) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert {$value1} !== {$value2}" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Assert the given object is an instance of the given className
     * @param mixed $object
     * @param mixed $className
     * @return boolean
     * @throws FrameEx
     */
    public static function isInstance($object, $className, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if ($object instanceof $className) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert ".get_class($object)." instanceof {$className}" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Assert the given object is an instance of the given className
     * @param mixed $object
     * @param mixed $className
     * @return boolean
     * @throws FrameEx
     */
    public static function notInstance($object, $className, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if (!($object instanceof $className)) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert ".get_class($object)." is not instanceof {$className}" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Assert $value1 is less than $value2
     * @param mixed $value1
     * @param mixed $value2
     * @return boolean
     * @throws FrameEx
     */
    public static function lessThan($value1, $value2, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if ($value1 < $value2) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert {$value1} < {$value2}" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Assert $value1 is greater than $value2
     * @param mixed $value1
     * @param mixed $value2
     * @return boolean
     * @throws FrameEx
     */
    public static function greaterThan($value1, $value2, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if ($value1 > $value2) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert {$value1} > {$value2}" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Check the given parameter is empty
     * @param mixed $value
     * @return boolean
     * @throws FrameEx
     */
    public static function isEmpty($value, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if (empty($value)) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert given value is empty" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Check the given parameter is not empty
     * @param mixed $value
     * @return boolean
     * @throws FrameEx
     */
    public static function isNotEmpty($value, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if (!empty($value)) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert given value is not empty" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Check the given parameter is null
     * @param mixed $value
     * @return boolean
     * @throws FrameEx
     */
    public static function isNull($value, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if (is_null($value)) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert {$value} is null" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Check the given parameter is not null
     * @param mixed $value
     * @return boolean
     * @throws FrameEx
     */
    public static function isNotNull($value, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if (!is_null($value)) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert {$value} is not null" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Check the given parameter is a boolean
     * @param mixed $value
     * @return boolean
     * @throws FrameEx
     */
    public static function isBoolean($value, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if (is_bool($value)) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert {$value} is boolean" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Check the given parameter is not a boolean
     * @param mixed $value
     * @return boolean
     * @throws FrameEx
     */
    public static function isNotBoolean($value, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if (!is_bool($value)) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert {$value} is not boolean" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Check the given parameter is true
     * @param mixed $value
     * @return boolean
     * @throws FrameEx
     */
    public static function isTrue($value, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if ($value === true) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert {$value} is true" : $message;
        throw new FrameEx($message, $code, $severity);
    }

    /**
     * Check the given parameter is false
     * @param mixed $value
     * @return boolean
     * @throws FrameEx
     */
    public static function isFalse($value, $message = null, $code = null, $severity = FrameEx::HIGH) {
        if ($value === false) {
            return true;
        }
        $message = is_null($message) ? "Failed to assert {$value} is false" : $message;
        throw new FrameEx($message, $code, $severity);
    }

}
