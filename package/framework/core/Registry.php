<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @version 0.1
 * @package core
 *
 * This is an implementation of the registry pattern it globally stores key/value pairs
 */
class Registry {
    private static $settings = array();

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

}
?>
