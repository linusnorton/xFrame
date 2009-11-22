<?php

//Object Factory
require_once(ROOT."framework/model/core/Factory.php");

Factory::init();
Page::init();
Registry::init();
Cache::init();
Registry::loadDBSettings();
FrameEx::init();

//boot the app
require_once(APP_DIR."init.php");