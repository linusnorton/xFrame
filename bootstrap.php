<?php

define("ROOT", "./package/");
include(ROOT."framework/init.php");

// Whitelist
PHPUnit_Util_Filter::addDirectoryToWhitelist(ROOT.'framework/model');
PHPUnit_Util_Filter::addDirectoryToWhitelist(APP_DIR.'test');
// Don't include in the coverage report any files under the test directory.
PHPUnit_Util_Filter::addDirectoryToFilter(ROOT.'app/test');
PHPUnit_Util_Filter::addDirectoryToFilter(ROOT.'framework/test');
PHPUnit_Util_Filter::addDirectoryToFilter(ROOT.'framework/model/util/phpmailer');
