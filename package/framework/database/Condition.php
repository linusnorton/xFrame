<?php

/**
 * The condition interface ensures an object can be applied to an
 * SQL query as part of the WHERE clause
 */
interface Condition {

    public function toSQL(&$count = 1);

    public function bind(PDOStatement $stmt, &$count = 1);
}