<?php

define("ROOT", "./package/");
include(ROOT."framework/init.php");

// Don't include in the coverage report any files under the test directory.
PHPUnit_Util_Filter::addDirectoryToFilter(ROOT.'framework/test');
PHPUnit_Util_Filter::addDirectoryToFilter(ROOT.'framework/model/util/phpmailer');
// Whitelist
PHPUnit_Util_Filter::addDirectoryToWhitelist(ROOT.'framework/model');

