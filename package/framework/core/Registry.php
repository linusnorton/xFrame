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
     * Load all the settings in the config directory
     */
    public static function init() {
        $configDir = ROOT."../config/";
        //open the config dir
        if ($dh = opendir($configDir)) {
            //loop over the files
            while (($file = readdir($dh)) !== false) {
                //if it is a conf file, it must be needed!
                if (pathinfo($file, PATHINFO_EXTENSION) == "conf") {
                    self::$settings = array_merge(self::$settings, parse_ini_file($configDir.$file));
                }
            }
            closedir($dh);
        }


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
     * Load settings from a database table
     * @param string $table
     */
    public static function loadFromDB($table) {
        $records = TableGateway::loadAll($table);

        foreach ($records as $record) {
            self::$settings[$record->key] = $record->value;
        }
    }
}

