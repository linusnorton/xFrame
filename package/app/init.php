<?php

////////////////////////////////////////////////////////////////////////////////////
// Setup Registry settings here                                                   //
////////////////////////////////////////////////////////////////////////////////////

//Error settings
Registry::set("ERROR_XSL","app/view/error.xsl");
//Registry::set("ADMIN","you@yourdomain.com");
//Registry::set("EMAIL_ERRORS",true);

//Database settings
Registry::set("DATABASE_ENGINE","MySQL");
Registry::set("DATABASE_USERNAME", "root");
Registry::set("DATABASE_PASSWORD", "XPMnwLk");
Registry::set("DATABASE_HOST", "localhost");
Registry::set("DATABASE_NAME", "test");

//Memcache settings (optional)
//Registry::set("CACHE_ENABLED", true);
//Memcache::mch()->addServer("localhost", "11211");

//Logging settings (optional)
//Registry::set("LOG_LEVEL", Logger::DEBUG);

////////////////////////////////////////////////////////////////////////////////////
// Setup request mapping here                                                     //
////////////////////////////////////////////////////////////////////////////////////

//request name, class handler, method, [cache length], [param mapping]
$parameterMap = array("param1", "param2");
Dispatcher::addListener("home", "Index", "run", 60, $parameterMap);


////////////////////////////////////////////////////////////////////////////////////
// Include class mappings (don't change)                                          //
////////////////////////////////////////////////////////////////////////////////////

include(ROOT."app/.classes.php");
//include(ROOT."Zend/.classes.php");

?>
