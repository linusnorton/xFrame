<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package database
 *
 * Attempt to recreate something similar to Hibernate's Restictions.
 *
 * A criteria object contains a collection of Conditions, these can either
 * be Restrictions or other Criteria, you can AND or OR them together to
 * make a WHERE clause for an SQL statement
 */
class Criteria implements Condition {
    private $conditions;

    /**
     * The constuctor takes the first condition and adds it to the list,
     * it doesn't need an AND or OR
     *
     * @param Condition $condition
     */
    public function __construct(Condition $condition) {
        $this->conditions[] = array("joiner" => "", "object" => $condition);
    }

    /**
     * AND's the given condition with the previous condition
     *
     * @param Condition $condition
     * @return Criteria $this
     */
    public function addAnd(Condition $condition) {
        $this->conditions[] = array("joiner" => " AND ", "object" => $condition);
        return $this;
    }

    /**
     * OR's the given condition with the previous condition
     *
     * @param Condition $condition
     * @return Criteria $this
     */
    public function addOr(Condition $condition) {
        $this->conditions[] = array("joiner" => " OR ", "object" => $condition);
        return $this;
    }

    /**
     * Returns the SQL representation of the Criteria
     *
     * @param [int] $count internal counter for param number
     * @return string SQL
     */
    public function toSQL(&$count = 1) {
        $sql = " ( ";

        foreach ($this->conditions as $condition) {
            $sql .= $condition["joiner"] . $condition["object"]->toSQL($count);
        }

        return $sql." ) ";
    }

    /**
     * Binds the values in the criteria to the given PDOStatement
     *
     * @param PDOStatement $stmt
     */
    public function bind(PDOStatement $stmt, &$count = 1) {
        foreach ($this->conditions as $condition) {
            $condition["object"]->bind($stmt, $count);
        }
    }

}

