<?php

Registry::set("SITE","http://home.linusnorton.co.uk/xframe");
Registry::set("ADMIN","linusnorton@gmail.com");

//Database settings
Registry::set("DATABASE_ENGINE","MySQL");
Registry::set("DATABASE_USERNAME", "");
Registry::set("DATABASE_PASSWORD", "");
Registry::set("DATABASE_HOST", "");
Registry::set("DATABASE_NAME", "");

//set up the object factory
include(ROOT."app/.classes.php");
//set up the events
include(ROOT."app/events.php");

?>