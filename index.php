<?php

@define('ROOT', './');
require_once(ROOT.'framework/init.php');

//use the site address to work out the sub dir
$path = (parse_url(Registry::get('SITE')));
//remove sub dir and params to leave request
$request = str_replace(array($path['path'],'?'.$_SERVER['QUERY_STRING']), '', $_SERVER['REQUEST_URI']);
$request = ($request == '' || $request == '/') ? 'Index' : $request;

try {
    //generate and dispatch the event
    $event = new Event($request, $_REQUEST);
    $page = false;

    //check to see if we can get the cache version
    if (Dispatcher::getCacheLength($event) !== false && $_GET["cache"] != "no" && Registry::get("CACHE") == "on") {
        $page = Cache::mch()->get($event->hash()); 
    }

    if ($page === false) {
        $event->dispatch();
        $page = Page::build();

        if (false !== ($cacheLength = Dispatcher::getCacheLength($event)) && Registry::get("CACHE") == "on") {
            Cache::mch()->set($event->hash(), $page, false, $cacheLength);
        }
    }
    else {
        $page .= "<!--This page was generated from the cache-->";
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

?>