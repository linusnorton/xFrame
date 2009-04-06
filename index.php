<?php

@define('ROOT', './');
require_once(ROOT.'framework/init.php');

//generate the request

try {
    //generate and dispatch the request
    $request = Request::buildRequest();
    $page = false;

    //check to see if we can get the cache version
    if (Dispatcher::getCacheLength($request) !== false && $_GET["cache"] != "no" && Registry::get("CACHE") == "on") {
        $page = Cache::mch()->get($request->hash());
    }

    //if the page wasnt in the cache or the cache is off
    if ($page === false) {
        //dispatch the request and build the page
        $request->dispatch();
        $page = Page::build();

        //store the request response if possible
        if (false !== ($cacheLength = Dispatcher::getCacheLength($request)) && Registry::get("CACHE") == "on") {
            Cache::mch()->set($request->hash(), $page, false, $cacheLength);
        }
    }
    else {
        $cacheMessage = "from the cache";
    }

    //output the page
    echo $page;
}
catch (FrameEx $ex) {
    //this exception can be UnknownRequest MalformedPage or just an uncaught FrameEx
    $ex->output();
    //replace the xslt with the standard errors.xsl and display the page
    echo Page::displayErrors();
}

echo "<!-- Page executed in: ".number_format(microtime(true) - $GLOBALS["executionTime"], 5)." secs {$cacheMessage}-->";

?>
