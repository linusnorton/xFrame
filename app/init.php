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
Registry::set("DATABASE_USERNAME", $_SERVER["DB_USER"]);
Registry::set("DATABASE_PASSWORD", $_SERVER["DB_PASS"]);
Registry::set("DATABASE_HOST", $_SERVER["DB_HOST"]);
Registry::set("DATABASE_NAME", $_SERVER["DB_NAME"]);

//Memcache settings (optional)
//Registry::set("CACHE", "on");
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

?>
