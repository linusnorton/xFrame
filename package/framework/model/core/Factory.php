<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package core
 *
 * The factory class is in charge of creating new objects.
 *
 * It dynamically includes the files only when they are needed and makes
 * version control easier. Please see factory.txt in the notes folder
 * or read a book on design patterns or see php.net's section on patterns
 * (although their object factory is lame)
 */
 class Factory {
    private static $objects = array();
    private static $fileTypes = array(".php", ".php4", ".php5", ".mphp", ".phpm"); //allowed file types

    /** include the class do not create the object */
    public static function includeFile($className) {
        //if we have a mapping for the object
        if (isset(self::$objects[$className])) {
            //but it doesn't exist, we need to rebuild
            if (!file_exists(ROOT.self::$objects[$className])) {
                self::rebuild();
                return self::includeFile($className);
            }
            require_once(ROOT.self::$objects[$className]);
            return true;
        }

        return false;
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

    /**
     * Goes through every directory in the root looks for a init.php
     * if so it scans subdirectories for .php files with classes inside
     */
    public static function rebuild() {
        // Open the root
        if (!is_dir(ROOT)) {
            //the only place I've ever had this happen is when this method is
            //called as part of a destruct method.
            die("ROOT is not a directory!");
        }

        //open the root
        if ($dh = opendir(ROOT)) {
            //loop over the files
            while (($file = readdir($dh)) !== false) {
                //if there is an init.php it MUST be part of the framework
                if (file_exists(ROOT.$file."/init.php")) {
                    //rebuild the directories classes.php file
                    Factory::rebuildDirectory(ROOT.$file."/");
                }
                else if (ucfirst($file) == "Zend") {
                    //go into "zend mode"
                    Factory::rebuildDirectory(ROOT.$file."/", true);
                }
            }
            closedir($dh);
        }

    }

    /**
     * Rebuilds a particular directories class mapping
     */
    public static function rebuildDirectory($dir, $zendMode = false) {
        $classes = array();
        Factory::getClassesInDirectory($dir, $classes, $zendMode);
        Factory::buildClassFile($dir.".classes.php", $classes);
        include($dir.".classes.php");
    }

    /**
     * Gets all the classes out of a directory. Assumes that any
     * php file starting with a capital has a class inside with the
     * same name
     *
     * @param $dir string directory to look in
     * @param &$classes array stores the classes that are found
     */
    private static function getClassesInDirectory($dir, &$classes, $zendMode = false) {        
        if (!is_dir($dir)) {
            return;
        }

        //lets get into that directory
        if ($dh = opendir($dir)) {
            //for each file
            while (($file = readdir($dh)) !== false) {
                if ($file == "." || $file == ".."  || $file == ".svn" || $file == "PHPTAL") {
                    continue;
                }

                $ext = ".".pathinfo($dir.$file , PATHINFO_EXTENSION);


                //if im a directory
                if (is_dir($dir.$file)) {
                    //lets get recursive and add all the classes in this dir
                    Factory::getClassesInDirectory($dir.$file."/", $classes, $zendMode);
                }
                //if im an acceptable file and the first char is upper case(!!!!)
                else if (in_array($ext, self::$fileTypes) && ctype_upper($file[0])) {
                    $class = str_replace($ext, "", $file);

                    if ($zendMode) {
                        $class = str_replace(ROOT, "", $dir.$class);
                        $class = str_replace("/", "_", $class);
                    }
                    $classes[$class] = $dir.$file;
                }
            }
            closedir($dh);
        }
    }

    /**
     * builds the class file
     *
     * @param $filename string place to put the classes mapping
     * @param $classes array array of classes and files
     */
    private static function buildClassFile($filename, array $classes) {
        if (file_exists($filename) && !is_writable($filename)) {
            die("Could not write {$filename}, please check permissions.");
        }

        $fp = fopen($filename, 'w');

        if ($fp === false) {
            die("Could not write {$filename}, please check permissions.");
        }


        $contents = "<?php \n\n//This file is automatically generated and should not be changed\n";
        $contents .= "//It contains the class/file map for the autoloading Factory object\n";

        foreach ($classes as $class => $file) {
            $contents .= "Factory::add('{$class}','".str_replace(ROOT,"",$file)."');\n";
        }

        fwrite($fp, $contents);
        fclose($fp);
    }

    /**
     * include the framework files
     */
    public static function init() {
        ini_set("include_path", ini_get("include_path").":".ROOT);
        
        //if the framework .classes.php hasn't been built, build it
        if (!file_exists(ROOT."framework/.classes.php")) {
            Factory::rebuild();
        }

        //include the paths to the classes for the framework
        include(ROOT."framework/.classes.php");
    }

    /**
     * Include the class mapping and run the init script of the given package
     * @param string $package
     */
    public static function boot($package) {
        //check package exists
        if (!file_exists(ROOT.$package."/init.php")) {
            throw new FrameEx("Unable to boot package: {$package}, {$package}/init.php does not exist.");
        }
        //load the class mapping
        if (file_exists(ROOT.$package."/.classes.php")) {
            include(ROOT.$package."/.classes.php");
        }
        //boot
        include(ROOT.$package."/init.php");
    }

}

/**
 * If new <object> is called this function calls the Factory to include the file
 *
 * @param String $className
 */
function __autoload($className) {
    //if the factory does not have the class
    if (!Factory::includeFile($className)) {
        //rebuild the class/file mapping
        Factory::rebuild();
        //try to see if we have it now
        Factory::includeFile($className);
    }
}

