<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @version 0.1
 * @package core
 *
 * The factory class is in charge of creating new objects.
 *
 * It dynamically includes the files only when they are needed and makes
 * version control easier. Please see factory.txt in the notes folder
 * or read a book on design patterns or see php.net's section on patterns
 * (although they're object factory is lame)
 */
 class Factory {
    private static $objects = array();

    /** include the class do not create the object */
    public static function includeFile($className) {
        if (array_key_exists($className, self::$objects)) {
            require_once(ROOT.self::$objects[$className]);
        }
    }

    /**
     * Adds an instructions to load a class
     *
     * @param String $name key to use for the object
     * @param String $classPath path to the file defining the class
     */
    public static function add($name, $classPath) {
        self::$objects[$name] = $classPath;
    }

}

?>
