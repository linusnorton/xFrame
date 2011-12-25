<?php

/**
 * xFrame Bootloader for the Doctrine CLI application
 */
use xframe\autoloader\Autoloader;
use xframe\core\System;

$root = __DIR__.DIRECTORY_SEPARATOR;
require_once($root.'lib/xframe/autoloader/Autoloader.php');

//include addendum
require_once $root."lib/addendum/annotations.php";

$autoloader = new Autoloader($root);
$autoloader->register();

$system = new System($root, "dev");
$system->boot();

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($system->em)
));