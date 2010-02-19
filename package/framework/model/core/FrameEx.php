<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package core
 *
 * This class encapsulates the default behaviour for framework exceptions.
 *
 * The default behaviour is to add the error to the Page as xml
 */
class FrameEx extends Exception {
    public $backtrace;
    public $message;
    private $email;
    /**
     * Creates the exception with a message and an error code that are
     * shown when the output method is called.
     *
     * @param String $message
     * @param Integer $code
     */
    public function __construct($message = null, $code = null, $email = true) {
        $this->backtrace = debug_backtrace();
        $this->message = $message;
        $this->code = $code;
        $this->email = $email;
    }

    /**
     *
     * @param boolean $uncaught
     */
    public function output($uncaught = false, $return = false, $email = true) {
        $style = ($uncaught) ? "true" : "false";

        $out = "<exception uncaught='{$style}'>";
        $out .= "<message>".htmlentities($this->message, ENT_COMPAT, "UTF-8", false)."</message>";
        $out .= "<code>".htmlentities($this->code, ENT_COMPAT, "UTF-8", false)."</code>";
        $out .= "<backtrace>";
        $i = 1;

        foreach (array_reverse($this->backtrace) as $back) {
            $back['file'] = (array_key_exists("file", $back)) ? basename($back['file']) : "";
            $back['class'] = (array_key_exists("class", $back)) ? $back['class'] : "";
            $back['line'] = (array_key_exists("line", $back)) ? $back['line'] : "";

            if ($back["class"] != "FrameEx") {
                $out .= "<step number='".$i++."' line='{$back['line']}' file='{$back['file']}' class='{$back['class']}' function='{$back['function']}' />";
            }
        }
        $out .= "</backtrace>";
        $out .= "</exception>";
        //return the error do no more
        if ($return) {
            return $out;
        }

        //if email sending is enabled in the registry, this exception and the output method
        if (Registry::get("EMAIL_ERRORS") && $email && $this->email) {
            $xslFile = ROOT.Registry::get("PLAIN_TEXT_ERROR");
            $transformation = new Transformation("<root><exceptions>".$out."</exceptions></root>", $xslFile);
            $text = $transformation->execute();

            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
            mail(Registry::get("ADMIN"), "Error from: ".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],$text, $headers );
        }

        Page::addException($out);
        LoggerManager::getLogger("Exception")->error($this->message);
        error_log($this->message);
    }

    /**
     * Handles PHP generated errors
     */
    public function errorHandler($type, $msg, $filename, $line ) {
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

        $error = (array_key_exists($type, $errortype)) ? $errortype[$type] : $type;
        throw new FrameEx($error.": ".$msg." (line {$line} of ".basename($filename).")");
    }


    /**
     * If an exception is thrown that is not in a try catch statement it comes
     * here. It is then output to the screen and code execution stops
     *
     * @param Exception $exception
     */
    public function exceptionHandler($exception) {
        if ($exception instanceof FrameEx) {
            try {
                $exception->output(true);
                echo Page::displayErrors();
            }
            catch (Exception $e) {
                echo $e->getMessage();
                die("Error processing exception");
            }
        }
        else {
            echo $exception;
        }
    }

    /**
     * Setup the error handling
     */
    public static function init() {
        set_exception_handler(array("FrameEx", "exceptionHandler"));
        set_error_handler(array("FrameEx", "errorHandler"), ini_get("error_reporting"));

        //deal with any exceptions that were redirected
        if (array_key_exists("exception",$_SESSION)) {
            $ex = unserialize($_SESSION["exception"]);
            $ex->output();
            unset($_SESSION["exception"]);
        }
    }

    /**
     * Persist the error and redirect to another page
     * @param string $location
     */
    public function redirect($location) {
        $this->persist();
        Page::redirect($location);
    }

    public function persist() {
        $_SESSION["exception"] = serialize($this);
    }

    /**
     * Set the error code
     * @param int $code
     */
    public function setCode($code) {
        $this->code = $code;
    }

    /**
     * Set the message
     * @param string $message
     */
    public function setMessage($message) {
        $this->message = $message;
    }

}
