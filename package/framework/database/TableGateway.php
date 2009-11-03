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
     * If a Record is constructed with a tableName and id we will try to load the data from the database
     * If not we just create an empty record that can be populated using the setup() method
     *
     * @param $tableName string table name
     * @param $id mixed unique identifier, assumed to be id!!
     * @param $class class to instantiate (will be replaced with __STATIC__ in 5.3)
     * @param $method function to call to initialize the class
     */
    public static function load($tableName, $id, $class = "Record", $method = "create") {
        $attributes = false;

        //if we're not caching or the record is not in the cache
        if (!Registry::get("CACHE_ENABLED") || false === ($attributes = Cache::mch()->get($tableName.$id))) {
            //lets try to get the data from the db
            try {
                $stmt = DB::dbh()->prepare('SELECT * FROM `'.addslashes($tableName).'` WHERE `id` = :id');
                $stmt->bindValue(':id', $id);
                $stmt->execute();

                //if we dont get any records or we get multiple throw an exception
                if ($stmt->rowCount() === 0) {
                    throw new MissingRecord("Could not find a {$tableName} where id = {$id}", 127);
                }
                if ($stmt->rowCount() > 1) {
                    throw new MultipleRecord("Multiple records were matched", 128);
                }
            }
            catch (PDOException $ex) {
                //there was some kind of database error
                throw new FrameEx($ex->getMessage(), 129);
            }

            $attributes = $stmt->fetch(PDO::FETCH_ASSOC);

            //if we're caching, put it in
            if (Registry::get("CACHE_ENABLED")) {
                Cache::mch()->set($tableName.$id, $attributes, false, 0);
            }
        }

        if ($class == "Record") {
            return new Record($tableName, $attributes);
        }
        else {
            //call the given object's create method, this will be replaced with __STATIC__
            return call_user_func(array($class, $method), $attributes);
        }


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

        $stmt->execute();

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

        if ($class == "Record") {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (Registry::get("CACHE_ENABLED")) {
                    Cache::mch()->set($tableName.$row["id"], $row, false, 0);
                }
                $records[] = new Record($tableName, $row);
            }
        }
        else {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (Registry::get("CACHE_ENABLED")) {
                    Cache::mch()->set($tableName.$row["id"], $row, false, 0);
                }
                //call the given object's create method, this will be replaced with __STATIC__
                $records[] = call_user_func(array($class, $method), $row);
            }
        }

        return new Results($records, $numResults, $start, $num, $tableName."-list");
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