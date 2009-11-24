<?php

@define('ROOT', '../package/');
require(ROOT.'framework/init.php');

/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package core
 *
 * Welcome to xFrame, if you are just starting out don't look at this file.
 *
 * app/init.php contains the mapping between page requests and PHP classes the default request is
 * already mapped for you to the app/controller/Index.php controller with the default view being
 * app/view/index.xsl to get started just edit those files.
 *
 */

Request::process();
