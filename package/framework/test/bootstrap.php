<?php

define("ROOT", "./package/");
ini_set("include_path", ini_get("include_path").":".ROOT);
include("framework/init.php");

// Don't include in the coverage report any files under the test directory.
PHPUnit_Util_Filter::addDirectoryToFilter(ROOT.'framework/test');
PHPUnit_Util_Filter::addDirectoryToFilter(ROOT.'framework/model/util/phpmailer');
// Whitelist
PHPUnit_Util_Filter::addDirectoryToWhitelist(ROOT.'framework/model');

