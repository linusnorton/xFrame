<?php

//if root not defined, define it so we can correctly include
@define("ROOT", "../");

$GLOBALS["executionTime"] = microtime(true); //used for script execution time
require_once(ROOT."framework/core/Factory.php");//Object Factory

/////////////////////////////////////////////////////////////////////////////////////
// Error handling                                                                  //
/////////////////////////////////////////////////////////////////////////////////////
set_exception_handler(array("FrameEx", "exceptionHandler"));
set_error_handler(array("FrameEx", "errorHandler"), ini_get("error_reporting"));


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
        Factory::includeFile($className);
    }
}

//set up the object factory
include_once(ROOT."framework/.classes.php");
//setup the project
session_start();
require_once(ROOT."app/init.php");