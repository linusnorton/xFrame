<?php

////////////////////////////////////////////////////////////////////////////////
// Setup request mapping here                                                 //
////////////////////////////////////////////////////////////////////////////////

//request name, class, method, [cache length], [param mapping], [authenticator]
Dispatcher::addListener("home", "Index", "run");
//if you want to load resource from the database you can do so like this:
//Resource::loadFromDB("resource");

////////////////////////////////////////////////////////////////////////////////
// Include class mappings (don't change)                                      //
////////////////////////////////////////////////////////////////////////////////

if (file_exists(ROOT."app/.classes.php")) {
    include(ROOT."app/.classes.php");
}

//if (file_exists(ROOT."Zend/.classes.php")) {
//    include(ROOT."Zend/.classes.php");
//}

