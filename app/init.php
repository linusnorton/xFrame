<?php

Registry::set("SITE","http://dev.assertis.net/svn/linus/xframe/");
Registry::set("ADMIN","linusnorton@gmail.com");

//Database settings
Registry::set("DATABASE_ENGINE","MySQL");
Registry::set("DATABASE_USERNAME", "root");
Registry::set("DATABASE_PASSWORD", "XPMnwLk");
Registry::set("DATABASE_HOST", "localhost");
Registry::set("DATABASE_NAME", "cmsve");

//set up the object factory
require_once(ROOT."app/.classes.php");
//set up the events
require_once(ROOT."app/events.php");

?>