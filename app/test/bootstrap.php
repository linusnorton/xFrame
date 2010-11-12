<?php

/**
 * Boot the framework before we start running the tests.
 *
 * Please note that this bootstrap file requires PHPUnit 3.5 or later.
 *
 * Remove the whitelist and blacklist lines to make backwards compatible.
 */

$_SESSION = array();

foreach ($_SERVER["argv"] as $arg) {
    if (strpos($arg, "XFRAME_CONFIG") !== false) {
        $parts = explode("=", $arg);
        $_SERVER["XFRAME_CONFIG"] = $parts[1];
    }
}

//running from netbeans
if ($_SERVER["XFRAME_CONFIG"] == "") {
    $_SERVER["XFRAME_CONFIG"] = "../config/dev.ini";
}

set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__)."/../../");
require_once 'xframe/init.php';


// Don't include in the coverage report any files under the test directory.
PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(APP_DIR.'test');
// Whitelist
PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist(APP_DIR.'controller');
PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist(APP_DIR.'model');
