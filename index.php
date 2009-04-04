<?php

@define('ROOT', './');
require_once(ROOT.'framework/init.php');

//generate the event

try {
    //generate and dispatch the event
    $event = Event::buildEvent();
    $page = false;

    //check to see if we can get the cache version
    if (Dispatcher::getCacheLength($event) !== false && $_GET["cache"] != "no" && Registry::get("CACHE") == "on") {
        $page = Cache::mch()->get($event->hash());
    }

    //if the page wasnt in the cache or the cache is off
    if ($page === false) {
        //dispatch the event and build the page
        $event->dispatch();
        $page = Page::build();

        //store the event response if possible
        if (false !== ($cacheLength = Dispatcher::getCacheLength($event)) && Registry::get("CACHE") == "on") {
            Cache::mch()->set($event->hash(), $page, false, $cacheLength);
        }
    }
    else {
        $cacheMessage = "from the cache";
    }

    //output the page
    echo $page;
}
catch (FrameEx $ex) {
    //this exception can be UnknownEvent MalformedPage or just an uncaught FrameEx
    $ex->output();
    //replace the xslt with the standard errors.xsl and display the page
    echo Page::displayErrors();
}

echo "<!-- Page executed in: ".number_format(microtime(true) - $GLOBALS["executionTime"], 5)." secs {$cacheMessage}-->";

?>