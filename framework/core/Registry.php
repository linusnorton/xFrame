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

	public static function get($key) {
		if (!array_key_exists($key ,self::$settings)) {
			return null;
        }

		return self::$settings[$key];
	}

	public static function set($key, $value) {
		self::$settings[$key] = $value;
	}
   
}
?>
