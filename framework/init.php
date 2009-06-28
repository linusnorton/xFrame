<?php

//if root not defined, define it so we can correctly include
@define("ROOT", "../");

$GLOBALS["executionTime"] = microtime(true); //used for script execution time
require_once(ROOT."framework/core/Factory.php");//Object Factory

/////////////////////////////////////////////////////////////////////////////////////
// Error handling                                                                  //
/////////////////////////////////////////////////////////////////////////////////////
set_error_handler("custom_error");
set_exception_handler("custom_exception");

function custom_error($type, $msg, $filename, $line ) {
    //filter out notices

    if (!(ini_get("error_reporting") & $type))
		return;

	$errortype = array (
                E_ERROR              => 'Error',
                E_WARNING            => 'Warning',
                E_PARSE              => 'Parsing Error',
                E_NOTICE             => 'Notice',
                E_CORE_ERROR         => 'Core Error',
                E_CORE_WARNING       => 'Core Warning',
                E_COMPILE_ERROR      => 'Compile Error',
                E_COMPILE_WARNING    => 'Compile Warning',
                E_USER_ERROR         => 'User Error',
                E_USER_WARNING       => 'User Warning',
                E_USER_NOTICE        => 'User Notice',
                E_STRICT             => 'Runtime Notice',
                E_RECOVERABLE_ERROR  => 'Recoverable Error'
                );


	$filename = basename($filename);

    $string = "<error type='{$errortype[$type]}' line='{$line}' file='{$filename}'>";
	$string .= "<message>".htmlentities($msg, ENT_COMPAT, "UTF-8", false)."</message>";
	$string .= "<backtrace>";
    $i = 1;

    foreach (array_reverse(debug_backtrace()) as $back) {
        if (!array_key_exists("file",$back)) {
            continue;
        }

        $back['file'] = basename($back['file']);
        $back['class'] = array_key_exists('class', $back) ? $back['class'] : '';
        $string .= "<step number='".$i++."' class='{$back['class']}' function='{$back['function']}' line='{$back['line']}' file='{$back['file']}'/>";
    }

    $string .= "</backtrace>";
	$string .= "</error>";

    if (Registry::get("EMAIL_ERRORS") === true) {
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
        mail(Registry::get("ADMIN"), "Error from: ".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],$string, $headers );
    }

    Page::addError($string);
    LoggerManager::getLogger("Error")->error($msg);
    error_log($msg);
}

/**
 * If an exception is thrown that is not in a try catch statement it comes
 * here. It is then output to the screen and code execution stops
 *
 * @param Exception $exception
 */
function custom_exception($exception) {
    if ($exception instanceof FrameEx) {
        $exception->output(true);
    }
    else {
        echo $exception;
    }
}

/**
 * If new <object> is called this function calls the Factory to include the file
 *
 * @param String $className
 * @return [Object]
 */
function __autoload($className) {
    //if the factory does not have the class
    if (!Factory::includeFile($className)) {
        //rebuild the class/file mapping
        Factory::rebuild();
        //try to see if we have it now
        Factory::includeFile($className);
    }
}

//set up the object factory
include_once(ROOT."framework/.classes.php");
//setup the project
session_start();
require_once(ROOT."app/init.php");