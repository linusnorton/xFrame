<?php

//Object Factory
require_once(dirname(__FILE__)."/model/core/Factory.php");

Factory::init();
Controller::boot();
Registry::init();
Cache::init();
Registry::loadDBSettings();
FrameEx::init();

//boot the app
Factory::boot(APP_DIR);