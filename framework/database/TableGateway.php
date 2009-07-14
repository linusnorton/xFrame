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
    const ASC = "ASC", DESC = "DESC";
    /**
     * If a Record is constructed with a tableName and id we will try to load the data from the database
     * If not we just create an empty record that can be populated using the setup() method
     *
     * @param $tableName string table name
     * @param $id mixed unique identifier, assumed to be id!!
     * @param $class class to instantiate (will be replaced with __STATIC__ in 5.3)
     */
    public static function load($tableName, $id, $class = "Record", $method = "create") {
        $attributes = false;

        //if we're not caching or the record is not in the cache
        if (Registry::get("CACHE") != "on" || false === ($attributes = Cache::mch()->get($tableName.$id))) {
            //lets try to get the data from the db
            try {
                $stmt = DB::dbh()->prepare('SELECT * FROM `'.addslashes($tableName).'` WHERE `id` = :id');
                $stmt->bindValue(':id', $id);
                $stmt->execute();

                //if we dont get any records or we get multiple throw an exception
                if ($stmt->rowCount() === 0) {
                    throw new MissingRecord("Could not find a {$tableName} where id = {$id}");
                }
                if ($stmt->rowCount() > 1) {
                    throw new MultipleRecord("Multiple records were matched");
                }
            }
            catch (PDOException $ex) {
                //there was some kind of database error
                throw new FrameEx($ex->getMessage());
            }

            $attributes = $stmt->fetch(PDO::FETCH_ASSOC);

            //if we're caching, put it in
            if (Registry::get("CACHE") == "on") {
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
     * @param $tableName string table to load
     * @param $class string class to instantiate as records
     */
    public static function loadAll($tableName,
                                   $class = "Record",
                                   $method = "create",
                                   $start = null,
                                   $num = null) {
        return self::loadMatching($tableName, array(), $class, $method, $start, $num);
    }


    /**
     * Loads all records that match the fields specified in the associative array
     * $criteria.  This allows for simple equality matching of fields but not for
     * complex comparisons such as less than, greater than, etc.
     *
     * @param $tableName string table to load
     * @param $criteria array A mapping from column names to values.  The returned records will
     * match all specified columns.
     * @param $class string class to instantiate as records
     */
    public static function loadMatching($tableName,
                                        array $criteria = array(),
                                        $class = "Record",
                                        $method = "create",
                                        $start = null,
                                        $num = null,
                                        array $orderBy = array()) {

        $criteriaSQL = self::generateCriteriaSQL($criteria);
        $orderSQL = self::generateOrderSQL($orderBy);
        $limitSQL = self::generateLimitSQL($start, $num);
        $sql = "SELECT * FROM `".addslashes($tableName)."` WHERE 1".$criteriaSQL.$orderSQL.$limitSQL;
        $countSQL = "SELECT count(id) as num FROM `".addslashes($tableName)."` WHERE 1".$criteriaSQL;

        $stmt = DB::dbh()->prepare($sql);

        //bind values
        foreach($criteria as $column => $value) {
            if ($value != null) {
                $stmt->bindValue(':'.$column, $value);
            }
        }

        $stmt->execute();

        //check to see if there are more records than we selected
        //if we didn't start 0 there might be more, if the num results we got was there num we asked for there might be more
        if ((ctype_digit("{$num}") && $stmt->rowCount() == $num) || ctype_digit("{$start}")) {
            $countSTMT = DB::dbh()->prepare($countSQL);
            //bind values
            foreach($criteria as $column => $value) {
                if ($value != null) {
                    $countSTMT->bindValue(':'.$column, $value);
                }
            }
            $countSTMT->execute();
            $numResults = current($countSTMT->fetch());
        }
        else {
            $numResults = $stmt->rowCount();
            $start = 0;
        }

        $records = array();

        if ($class == "Record") {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (Registry::get("CACHE") == "on") {
                    Cache::mch()->set($tableName.$row["id"], $row, false, 0);
                }
                $records[] = new Record($tableName, $row);
            }
        }
        else {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (Registry::get("CACHE") == "on") {
                    Cache::mch()->set($tableName.$row["id"], $row, false, 0);
                }
                //call the given object's create method, this will be replaced with __STATIC__
                $records[] = call_user_func(array($class, $method), $row);
            }
        }

        return new Results($records, $numResults, $start, $num, $tableName."-list");
    }

    private static function generateCriteriaSQL(array $criteria = array()) {
        $sql = "";
        //generate the sql string
        foreach($criteria as $column => $value) {
            if ($value == null) {
                $sql.= " AND `{$column}` IS NULL";
            }
            else {
                $sql.= " AND `{$column}` = :".$column;
            }
        }
        return $sql;
    }

    private static function generateOrderSQL(array $orderBy = array()) {
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