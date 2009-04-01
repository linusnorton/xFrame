<?php

Registry::set("SITE",$_SERVER["SITE"]);
Registry::set("ADMIN","linusnorton@gmail.com");

//Database settings
Registry::set("DATABASE_ENGINE","MySQL");
Registry::set("DATABASE_USERNAME", $_SERVER["DB_USER"]);
Registry::set("DATABASE_PASSWORD", $_SERVER["DB_PASS"]);
Registry::set("DATABASE_HOST", $_SERVER["DB_HOST"]);
Registry::set("DATABASE_NAME", $_SERVER["DB_NAME"]);

//set up the object factory
include(ROOT."app/.classes.php");
//set up the events
include(ROOT."app/events.php");

?>