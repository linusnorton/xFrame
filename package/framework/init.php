<?php

//Object Factory
require_once(ROOT."framework/model/core/Factory.php");

Factory::init();
Controller::boot();
Registry::init();
Cache::init();
Registry::loadDBSettings();
FrameEx::init();

//boot the app
Factory::bootRaw(APP_DIR);