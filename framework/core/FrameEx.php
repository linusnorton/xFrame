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

	/**
	 * Creates the exception with a message and an error code that are
	 * shown when the output method is called.
	 *
	 * @param String $message
	 * @param Integer $code
	 */
	public function __construct($message = null, $code = null) {
		$this->backtrace = debug_backtrace();
		$this->message = $message;
		$this->code = $code;
	}
    /**
     *
     * @param boolean $uncaught
     */
	public function output($uncaught = false, $return = false) {
		$style = ($uncaught) ? "true" : "false";

		$out .= "<exception uncaught='{$style}'>";
        $out .= "<message>".htmlentities($this->message, ENT_COMPAT, "UTF-8", false)."</message>";
        $out .= "<code>".htmlentities($this->code, ENT_COMPAT, "UTF-8", false)."</code>";
		$out .= "<backtrace>";
        $i = 1;

		foreach (array_reverse($this->backtrace) as $back) {
			$back['file'] = basename($back['file']);
            $out .= "<step number='".$i++."' line='{$back['line']}' file='{$back['file']}' class='{$back['class']}' function='{$back['function']}' />";
		}
        $out .= "</backtrace>";
        $out .= "</exception>";

        //return the error do no more
        if ($return) {
            return $out;
        }

        if (Registry::get("EMAIL_ERRORS") === true) {
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
            mail(Registry::get("ADMIN"), "Error from: ".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],$out, $headers );
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


        throw new FrameEx($errortype[$type].": ".$msg);
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
            }
            catch (Exception $e) {
                die("Error processing exception");
            }
        }
        else {
            echo $exception;
        }
    }
}
