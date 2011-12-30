<?php

use \xframe\autoloader\Autoloader;
use \xframe\core\System;
use \xframe\request\Request;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * Welcome to xFrame. This file is the entry point for the front controller.
 * It registers the autoloader, boots the framework and dispatches the request.
 */

$root = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
require($root.'lib/xframe/autoloader/Autoloader.php');

$autoloader = new Autoloader($root);
$autoloader->register();

$system = new System($root, $_SERVER['CONFIG']);
$system->boot();

$request = new Request($_SERVER['REQUEST_URI'], $_REQUEST);
$system->getFrontController()->dispatch($request);
