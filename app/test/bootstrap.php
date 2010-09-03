<?php

define("ROOT", "./package/");
include(ROOT."framework/init.php");

// Don't include in the coverage report any files under the test directory.
PHPUnit_Util_Filter::addDirectoryToFilter(APP_DIR.'test');
// Whitelist
PHPUnit_Util_Filter::addDirectoryToWhitelist(APP_DIR.'controller');
PHPUnit_Util_Filter::addDirectoryToWhitelist(APP_DIR.'model');
