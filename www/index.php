<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * Welcome to xFrame. This file dispatches the request to be handled by the framework.
 */

require(dirname(__FILE__).'/../init.php');
//pass the request URI, parameters and path of this file to the request
$request = new Request($_SERVER["REQUEST_URI"], $_REQUEST, $_SERVER["PHP_SELF"]);
//pass request to the dispatcher which maps it to a resource and controller 
echo Dispatcher::dispatch($request);
