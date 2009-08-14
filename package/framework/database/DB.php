<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package database
 *
 * This is essentially a singleton for a PDO database
 */
class DB {
    private static $instance = false;


    /**
     * Create a PDO instance based on the settings in the registry
     */
    private static function connect() {
        $db = Registry::get("DATABASE_ENGINE");

        try {
            if ($db == "MySQL") {
                self::$instance = new PDO("mysql:host=".Registry::get("DATABASE_HOST").";dbname=".Registry::get("DATABASE_NAME"),
                                          Registry::get("DATABASE_USERNAME"),
                                          Registry::get("DATABASE_PASSWORD"));

                self::$instance->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                //self::$instance->setAttribute (PDO::ATTR_STATEMENT_CLASS, array ('LoggedPDOStatement', array()));

            } else if ($db == "MySQLDebug") {
                self::$instance = new LoggedPDO("mysql:host=".Registry::get("DATABASE_HOST").";dbname=".Registry::get("DATABASE_NAME"),
                                                  Registry::get("DATABASE_USERNAME"),
                                                  Registry::get("DATABASE_PASSWORD"));

                self::$instance->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            }
        }
        catch (PDOException $ex) {
            throw new FrameEx($ex->getMessage());
        }

    }

    /**
     * Return the current PDO database instance or create one if one does not exist
     */
    public static function dbh() {
        if (!self::$instance instanceof PDO) {
            self::connect();
        }

        return self::$instance;
    }

    /**
     * Override the current instance with the given instance
     *
     * @param $newInstance PDO new PDO instance
     */
    public static function setInstance(PDO $newInstance) {
        self::$instance = $newInstance;
    }


    /**
     * Executes the specified callback within the context of a database transaction.
     * If the commit fails (due to data inconsistency) the callback will be tried
     * again (the number of attempts is controlled by the second parameter, if not
     * specified it will retry indefinitely).
     * If the callback throws an exception the transaction will be rolled back and
     * the exception propagated.
     *
     * If you need to change the transaction isolation level, do it before invoking
     * this method.
     *
     * @param callback $callback The transactional logic.
     * @param integer $attempts The number of times to try before giving up.
     * @return mixed The result of the specified function, or false if the transaction
     * did not succeed.
     *
     * Any additional parameters that are passed to this method will be passed to the callback
     * function.
     */
    public static function doInTransaction($callback, $attempts = -1) {
        $transactional = self::dbh()->beginTransaction();
        if (!$transactional) {
            throw new FrameEx('Failed initiating database transaction.');
        }

        while ($attempts != 0) {
            try {
                $args = func_get_args();
                $args .= array_slice($args, 2);
                $result = call_user_func($callback, $args);
                $success = self::dbh()->commit();
                if ($success) {
                    return $result;
                }
                --$attempts;
            }
            catch (Exception $ex) {
                self::dbh()->rollBack();
                throw $ex;
            }
        }
        return false;
    }
}


