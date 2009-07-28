<?php

require_once(ROOT."framework/core/Factory.php");//Object Factory

/**
 * If new <object> is called this function calls the Factory to include the file
 *
 * @param String $className
 * @return [Object]
 */
function __autoload($className) {
    //if the factory does not have the class
    if (!Factory::includeFile($className)) {
        //rebuild the class/file mapping
        Factory::rebuild();
        //try to see if we have it now
        if (!Factory::includeFile($className)) {
            die("Could not find class: {$className}");
        }
    }
}

if (!file_exists(ROOT."framework/.classes.php")) {
    Factory::rebuild();
}

include(ROOT."framework/.classes.php");
set_exception_handler(array("FrameEx", "exceptionHandler"));
set_error_handler(array("FrameEx", "errorHandler"), ini_get("error_reporting"));
ini_set("include_path", ini_get("include_path").":".ROOT);
session_start();
Page::init();

require_once(ROOT."app/init.php");