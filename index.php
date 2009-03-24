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
    $event->dispatch();

    //output the page
    Page::display();
}
catch (FrameEx $ex) {
    //this exception can be UnknownEvent MalformedPage or just an uncaught FrameEx
    $ex->output();
    //replace the xslt with the standard errors.xsl and display the page
    Page::displayErrors();
}

?>