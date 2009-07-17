<?php

@define('ROOT', '../package/');
require_once(ROOT.'framework/init.php');

/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package core
 *
 * Welcome to xFrame, if you are just starting out don't look at this file - it will just scare you.
 *
 * app/init.php contains the mapping between page requests and PHP classes the default request is
 * already mapped for you to the app/controller/Index.php controller with the default view being
 * app/view/index.xsl to get started just edit those files.
 */

//generate the request
$request = Request::buildRequest();
$cacheOn = (Dispatcher::getCacheLength($request) !== false && array_key_exists("cache",$_GET) && $_GET["cache"] != "no" && Registry::get("CACHE_ENABLED"));
$page = false;

//check to see if we can get the cache version
if ($cacheOn) {
    $page = Cache::mch()->get($request->hash());
}

//if the page wasnt in the cache or the cache is off
if ($page === false) {
    try {
        //dispatch the request and build the page
        $request->dispatch();
        //transform the page and get the html
        $page = Page::build();

        //store the request response if possible
        if ($cacheOn) {
            Cache::mch()->set($request->hash(), $page, false, $cacheLength);
        }
    }
    catch (FrameEx $ex) {
        //this exception can be UnknownRequest MalformedPage or just an uncaught FrameEx
        $ex->output();
        //replace the xslt with the standard errors.xsl and display the page
        $page = Page::displayErrors();
    }
    $cacheMessage = "- not cached";
}
else {
    $cacheMessage = "from the cache";
}

//output the page
echo $page;
echo "<!-- Page executed in: ".number_format(microtime(true) - Page::getExecutionTime(), 5)." secs {$cacheMessage}-->";

?>