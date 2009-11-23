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
        $configDir = ROOT."../config/";
        $site = $_SERVER["SERVER_NAME"] == "" ? $_SERVER["argv"][1] : $_SERVER["SERVER_NAME"];
        $file = $configDir.$site.".conf";

        if (!file_exists($file)) {
            $file = $configDir."default.conf";
        }

        self::$settings = parse_ini_file($file);
        //setup the app dir
        $appDir = self::$settings["APP_DIR"];

        if ($appDir == null) {
            die("Unable to find APP_DIR setting in {$file}");
        }

        define("APP_DIR", ROOT.$appDir);
    }

    /**
     * This function gets a value from the registry
     *
     * @param $get mixed key of variable get from the registry
     */
    public static function get($key) {
        if (!array_key_exists($key ,self::$settings)) {
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
        $databaseSettings = Registry::get("DATABASE_SETTING");

        if (is_array($databaseSettings)) {
            foreach ($databaseSettings as $table) {
                Registry::loadFromDB($table);
            }
        }
    }

    /**
     * Load settings from a database table
     * @param string $table
     */
    public static function loadFromDB($table) {
        $records = TableGateway::loadAll($table);

        foreach ($records as $record) {
            if ($record->value === "true") {
                self::$settings[$record->key] = true;
            }
            else if ($record->value === "false") {
                self::$settings[$record->key] = false;
            }
            else {
                self::$settings[$record->key] = $record->value;
            }
        }
    }
}

