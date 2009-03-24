<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @version 0.1
 * @package database
 *
 * This is essentially a singleton for a PDO database
 */
class DB {
	private static $instance = false;


	/** this had to be done so you could use DB statically */
	private static function connect() {
        $db = Registry::get("DATABASE_ENGINE");

        try {
            if ($db == "MySQL") {
                self::$instance = new PDO("mysql:host=".Registry::get("DATABASE_HOST").";dbname=".Registry::get("DATABASE_NAME"),
                                          Registry::get("DATABASE_USERNAME"),
                                          Registry::get("DATABASE_PASSWORD"));

                self::$instance->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            }
        }
		catch (PDOException $ex) {
            throw new FrameEx($ex->getMessage());
        }

	}

    public static function dbh() {
        if (!self::$instance instanceof PDO) {
            self::connect();
        }

        return self::$instance;
    }

    public static function setInstance(PDO $newInstance) {
        self::$instance = $newInstance;
    }


}


