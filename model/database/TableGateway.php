<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package database
 *
 * This is the implementation of a table gateway pattern, it is responsible
 * for doing all your SQL
 */
class TableGateway {
    const ASC = "ASC", DESC = "DESC", NOT_NULL = "NOT NULL";

    /**
     * Delete all rows from the given table that match the given condition
     * @param string $tableName
     * @param Condition $condition
     * @param int $limit
     * @return int number of rows affected
     */
    public static function delete($tableName,
                                  Condition $condition = null,
                                  $limit = null) {

        $limitSQL = ctype_digit($limit."") ? " LIMIT {$limit}" : "";
        $whereSQL = $condition != null ? " WHERE ".$condition->toSQL() : "";

        $sql = "DELETE FROM `".addslashes($tableName)."` ".$whereSQL.$limitSQL;
        $stmt = DB::dbh()->prepare($sql);

        if ($condition != null) {
            $condition->bind($stmt);
        }

        try {
            $stmt->execute();
        }
        catch (PDOException $ex) {
            throw new TableGatewayException($ex, FrameEx::HIGH);
        }
        return $stmt->rowCount(); //mysql returns number of rows deleted
    }

    /**
     * Update the given table with the given key value pairs
     * @param string $tableName
     * @param array $values
     * @param Condition $condition
     * @param int $limit
     * @return int affected rows
     */
    public static function update($tableName,
                                  array $values,
                                  Condition $condition = null,
                                  $limit = null) {

        $limitSQL = ctype_digit($limit."") ? " LIMIT {$limit}" : "";
        $whereSQL = $condition != null ? " WHERE ".$condition->toSQL() : "";
        $setSQL = self::getSetSQL($values);

        $sql = "UPDATE  `".addslashes($tableName)."` SET ".$setSQL.$whereSQL.$limitSQL;
        $stmt = DB::dbh()->prepare($sql);

        if ($condition != null) {
            $condition->bind($stmt);
        }

        try {
            $stmt->execute();
        }
        catch (PDOException $ex) {
            throw new TableGatewayException($ex, FrameEx::HIGH);
        }
        return $stmt->rowCount(); //mysql returns number of rows deleted
    }

    /**
     * Convert the key value pairs to SQL
     * @param array $values
     * @return string
     */
    private static function getSetSQL(array $values) {
        $sql = "";

        foreach ($values as $key => $value) {
            //@todo something better than addslashes here, at least prepare the insert value
            $sql .= "`".addslashes($key)."` = '".addslashes($value)."',";
        }

        return substr($sql, 0, -1);
    }

    /**
     * Load a whole table of results and return an array of objects of type $class
     *
     * @param string $tableName table to load
     * @param int $start start number for LIMIT
     * @param int $num number of results to return
     * @param array $orderBy
     * @param string $class class to instantiate as records
     * @param string $method to call to initialize the class
     * @return Results
     */
    public static function loadAll($tableName,
                                   $start = null,
                                   $num = null,
                                   array $orderBy = array(),
                                   $class = "Record",
                                   $method = "create") {
        return self::loadMatching($tableName, null, $start, $num, $orderBy, $class, $method);
    }

    /**
     * Loads all records that match the fields specified in the associative array
     * $criteria.  This allows for simple equality matching of fields but not for
     * complex comparisons such as less than, greater than, etc.
     *
     * @param string $tableName table to load
     * @param Criteria $criteria
     * @param int $start start number for LIMIT
     * @param int $num number of results to return
     * @param array $orderBy
     * @param string $class class to instantiate as records
     * @param string $method to call to initialize the class
     * @return Results
     */
    public static function loadMatching($tableName,
                                        Condition $criteria = null,
                                        $start = null,
                                        $num = null,
                                        array $orderBy = array(),
                                        $class = "Record",
                                        $method = "create") {

        $criteriaSQL = ($criteria != null) ? $criteria->toSQL() : "1";
        $orderSQL = self::generateOrderSQL($orderBy);
        $limitSQL = self::generateLimitSQL($start, $num);

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `".addslashes($tableName)."` WHERE ".$criteriaSQL.$orderSQL.$limitSQL;

        $stmt = DB::dbh()->prepare($sql);

        if ($criteria != null) {
            $criteria->bind($stmt);
        }

        try {
            $stmt->execute();
        }
        catch (PDOException $ex) {
            throw new TableGatewayException($ex, FrameEx::HIGH);
        }

        //check to see if there are more records than we selected
        //if we didn't start 0 there might be more, if the num results we got was there num we asked for there might be more
        if ((ctype_digit("{$num}") && $stmt->rowCount() == $num) || ctype_digit("{$start}")) {
            $numResults = current(DB::dbh()->query("SELECT FOUND_ROWS()")->fetch());
        }
        else {
            $numResults = $stmt->rowCount();
            $start = 0;
        }

        $records = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (Registry::get("CACHE_ENABLED")) {
                Cache::mch()->set($tableName.$row["id"], $row, false, 0);
            }
            //call the given object's create method, this will be replaced with __STATIC__
            $records[] = call_user_func(array($class, $method), $row, $tableName);
        }
        return new Results($records, $numResults, $start, $num, $tableName."-list");
    }

    /**
     * @param string $tableName
     * @param Condition $criteria
     * @return int
     */
    public static function countMatching($tableName, Condition $criteria = null) {
        $criteriaSQL = ($criteria != null) ? $criteria->toSQL() : "1";

        // we assume id because Record always has id
        $sql = "SELECT count(`id`) AS 'count' FROM `".addslashes($tableName)."` WHERE ".$criteriaSQL;

        $stmt = DB::dbh()->prepare($sql);

        if ($criteria != null) {
            $criteria->bind($stmt);
        }

        try {
            $stmt->execute();
        }
        catch (PDOException $ex) {
            throw new TableGatewayException($ex, FrameEx::HIGH);
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    private static function generateOrderSQL(array $orderBy = array()) {
        if (count($orderBy) == 0) {
            return;
        }

        $sql = " ORDER BY ";

        foreach ($orderBy as $field => $order) {
            if ($order == self::ASC || $order == self::DESC) {
                $sql .= "`".addslashes($field)."` ".$order.",";
            }
        }

        return substr($sql, 0, -1);
    }

    private static function generateLimitSQL($start, $num) {
        if (ctype_digit("{$start}") && ctype_digit("{$num}")) {
            return " LIMIT {$start}, {$num} ";
        }
        else if (ctype_digit("{$num}")) {
            return " LIMIT {$num} ";
        }
    }
}