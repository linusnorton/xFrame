<?php

//Object Factory
require_once(ROOT."framework/core/Factory.php");

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

//if the framework .classes.php hasn't been built
if (!file_exists(ROOT."framework/.classes.php")) {
    //build it now
    Factory::rebuild();
}

//include the paths to the classes for the framework
include(ROOT."framework/.classes.php");

//some ugly settings (needs cleaning)
set_exception_handler(array("FrameEx", "exceptionHandler"));
set_error_handler(array("FrameEx", "errorHandler"), ini_get("error_reporting"));
ini_set("include_path", ini_get("include_path").":".ROOT);
Page::init();
Registry::init();

//setup caching
if (Registry::get("CACHE_ENABLED")) {
    Cache::mch()->addServer(Registry::get("MEMCACHE_HOST"), Registry::get("MEMCACHE_PORT"));
}

//boot the app
require_once(ROOT."app/init.php");