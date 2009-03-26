<?php

Registry::set("SITE","http://yourwebsite.com/");
Registry::set("ADMIN","linusnorton@gmail.com");

//Database settings
Registry::set("DATABASE_ENGINE","MySQL");
Registry::set("DATABASE_USERNAME", "");
Registry::set("DATABASE_PASSWORD", "");
Registry::set("DATABASE_HOST", "");
Registry::set("DATABASE_NAME", "");

//set up the object factory
require_once(ROOT."app/.classes.php");
//set up the events
require_once(ROOT."app/events.php");

?>