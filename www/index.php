<?php

@define('ROOT', '../package/');
ini_set("include_path", ini_get("include_path").":".ROOT);
require('framework/init.php');

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * Welcome to xFrame. This file dispatches the request to be handled.
 *
 * app/init.php contains the mapping between requests and controllers. The default request is
 * already mapped for you to the app/controller/Index.php controller with the default view being
 * app/view/index.xsl to get started just edit those files.
 *
 */

//pass the request URI, parameters and path of this file to the request
$request = new Request($_SERVER["REQUEST_URI"], $_REQUEST, $_SERVER["PHP_SELF"]);
//pass request to the dispatcher which maps it to a resource and controller 
echo Dispatcher::dispatch($request);
