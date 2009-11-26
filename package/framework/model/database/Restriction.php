<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package database
 *
 * Attempt to recreate something similar to Hibernate's Restictions
 */
class Restriction implements Condition {
    const IS = " = ",
          IS_NOT = " != ",
          NULL_VALUE = " IS NULL",
          NOT_NULL = " IS NOT NULL ",
          LIKE = " LIKE ",
          NOT_LIKE = " NOT LIKE ",
          BETWEEN = " BETWEEN ",
          NOT_BETWEEN = " NOT BETWEEN ",
          LESS = " < ",
          LESS_OR_EQUAL = " <= ",
          GREATER = " > ",
          GREATER_OR_EQUAL = " >= ";

    private $type;
    private $field;
    private $value1;
    private $value2;

    /**
     * @param string $type
     * @param string $field
     * @param [string] $value1
     * @param [string] $value2
     */
    public function __construct($type, $field, $value1 = null, $value2 = null) {
        $this->type = $type;
        $this->field = addslashes($field);
        $this->value1 = $value1;
        $this->value2 = $value2;
    }

    /**
     * @param string $field
     * @param string $value
     * @return Restriction
     */
    public static function is($field, $value) {
        return new Restriction(Restriction::IS, $field, $value);
    }

    /**
     * @param string $field
     * @param string $value
     * @return Restriction
     */
    public static function isNot($field, $value) {
        return new Restriction(Restriction::IS_NOT, $field, $value);
    }

    /**
     * @param string $field
     * @param string $value
     * @return Restriction
     */
    public static function isLess($field, $value) {
        return new Restriction(Restriction::LESS, $field, $value);
    }

    /**
     * @param string $field
     * @param string $value
     * @return Restriction
     */
    public static function isLessOrEqual($field, $value) {
        return new Restriction(Restriction::LESS_OR_EQUAL, $field, $value);
    }

    /**
     * @param string $field
     * @param string $value
     * @return Restriction
     */
    public static function isGreater($field, $value) {
        return new Restriction(Restriction::GREATER, $field, $value);
    }

    /**
     * @param string $field
     * @param string $value
     * @return Restriction
     */
    public static function isGreaterOrEqual($field, $value) {
        return new Restriction(Restriction::GREATER_OR_EQUAL, $field, $value);
    }

    /**
     * @param string $field
     * @return Restriction
     */
    public static function isNull($field) {
        return new Restriction(Restriction::NULL_VALUE, $field);
    }

    /**
     * @param string $field
     * @return Restriction
     */
    public static function isNotNull($field) {
        return new Restriction(Restriction::NOT_NULL, $field);
    }

    /**
     * @param string $field
     * @param string $value
     * @return Restriction
     */
    public static function like($field, $value) {
        return new Restriction(Restriction::LIKE, $field, $value);
    }

    /**
     * @param string $field
     * @param string $value
     * @return Restriction
     */
    public static function notLike($field, $value) {
        return new Restriction(Restriction::NOT_LIKE, $field, $value);
    }

    /**
     * @param string $field
     * @param string $value1
     * @param string $value2
     * @return Restriction
     */
    public static function between($field, $value1, $value2) {
        return new Restriction(Restriction::BETWEEN, $field, $value1, $value2);
    }

    /**
     * @param string $field
     * @param string $value1
     * @param string $value2
     * @return Restriction
     */
    public static function notBetween($field, $value1, $value2) {
        return new Restriction(Restriction::NOT_BETWEEN, $field, $value1, $value2);
    }

    /**
     * Returns the SQL representation of the Criteria
     *
     * @param [int] $count internal counter for param number
     * @return string SQL
     */
    public function toSQL(&$count = 1) {
        $sql = " `{$this->field}` {$this->type}";

        if ($this->type !== Restriction::NULL_VALUE && $this->type !== Restriction::NOT_NULL ) {
            $sql .= " :field".$count++;
        }

        if ($this->type === Restriction::BETWEEN || $this->type === Restriction::NOT_BETWEEN) {
            $sql .= " AND :field".$count++;
        }

        return $sql;
    }

    /**
     * Binds the values in the criteria to the given PDOStatement
     *
     * @param PDOStatement $stmt
     */
    public function bind(PDOStatement $stmt, &$count = 1) {
        if ($this->value1 !== null) {
            $stmt->bindValue(":field".$count++, $this->value1);
        }
        if ($this->value2 !== null) {
            $stmt->bindValue(":field".$count++, $this->value2);
        }
    }

}
