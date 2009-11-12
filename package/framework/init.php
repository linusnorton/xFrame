<?php

//Object Factory
require_once(ROOT."framework/core/Factory.php");

Factory::init();
Page::init();
Registry::init();
Cache::init();
FrameEx::init();
Registry::loadDBSettings();

//boot the app
require_once(ROOT."app/init.php");