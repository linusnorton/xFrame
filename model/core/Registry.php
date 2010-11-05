<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package core
 *
 * This is an implementation of the registry pattern it globally stores key/value pairs
 */
class Registry {
    private static $settings = array();

    /**
     * Load the settings in the config directory for the current
     */
    public static function init() {
        $file = $_SERVER["XFRAME_CONFIG"] == "" ? "config/".$_SERVER["argv"][1].".ini" : $_SERVER["XFRAME_CONFIG"];

        //if the file does not exist, try to find the dev.ini
        if (!file_exists($file)) {
            if (!file_exists("config/dev.ini")) {
                die("Unabled to find configuration file: ".$file);
            }
            else {
                $file = "config/dev.ini";
            }
        }

        self::$settings = parse_ini_file($file);
        //setup the app dir
        $appDir = self::$settings["APP_DIR"];

        if ($appDir == null) {
            die("Unable to find APP_DIR setting in {$file}");
        }

        define("APP_DIR", $appDir);
    }

    /**
     * This function gets a value from the registry
     *
     * @param $get mixed key of variable get from the registry
     */
    public static function get($key) {
        if (!isset(self::$settings[$key])) {
            return null;
        }

        return self::$settings[$key];
    }

    /**
     * Sets a value in the registry
     *
     * @param key key of the variable to return
     */
    public static function set($key, $value) {
        self::$settings[$key] = $value;
    }

    /**
     * @param array $newSettings
     */
    public static function setAll(array $newSettings) {
        self::$settings = $newSettings;
    }

    /**
     * @param array $newSettings
     */
    public static function getAll() {
        return self::$settings;
    }

    /**
     * Load settings from tables in the conf file
     */
    public static function loadDBSettings() {
        $site = $_SERVER["SERVER_NAME"] == "" ? $_SERVER["argv"][1] : $_SERVER["SERVER_NAME"];

        //if (!Registry::get("CACHE_ENABLED") || false === ($settings = Cache::mch()->get($site."config"))) {
            $databaseSettings = Registry::get("DATABASE_SETTING");

            if (is_array($databaseSettings)) {
                foreach ($databaseSettings as $table) {
                    Registry::loadFromDB($table);
                }
            }
            
            if (Registry::get("CACHE_ENABLED")) {
                Cache::mch()->set($site."config", self::$settings);
            }
//        }
//        else {
//            Registry::$settings = $settings;
//        }
    }

    /**
     * Load settings from a database table
     * @param string $table
     */
    public static function loadFromDB($table) {
        $results = DB::dbh()->query("SELECT `key`,`value` FROM `{$table}`");

        while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
            if ($row['value'] === "true") {
                self::$settings[$row['key']] = true;
            }
            else if ($row['value'] === "false") {
                self::$settings[$row['key']] = false;
            }
            else {
                self::$settings[$row['key']] = $row['value'];
            }
        }

    }
}

