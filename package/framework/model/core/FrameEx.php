<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package core
 *
 * This class encapsulates the default behaviour for framework exceptions.
 *
 */
class FrameEx extends Exception {
    protected $severity;

    const OFF = 0,
          CRITICAL = 1,
          HIGH = 2,
          MEDIUM = 3,
          LOW = 4,
          LOWEST = 5;

    /**
     * Creates the exception with a message and an error code that are
     * shown when the output method is called.
     *
     * @param String $message
     * @param int $code
     * @param int $severity
     * @param Exception $previous
     */
    public function __construct($message = null,
                                $code = 0,
                                $severity = self::HIGH,
                                Exception $previous = null) {
        parent::__construct($message, (int) $code, $previous);
        $this->severity = $severity;
    }

    /**
     * Reset the severity level
     * @param int $severity
     */
    public function setSeverity($severity) {
        $this->severity = $severity;
    }

    /**
     * Use the current registry settings to determine whether this error needs
     * to be logged or emailed (or both)
     */
    public function process() {
        if (Registry::get("ERROR_LOG_LEVEL") >= $this->severity ){
            $this->log();
        }
        if (Registry::get("ERROR_EMAIL_LEVEL") >= $this->severity ){
            $this->email();
        }
    }

    /**
     * Log using the error_log and LoggerManager
     */
    protected function log() {
        LoggerManager::getLogger("Exception")->error($this->message);
        error_log($this->message);
    }

    /**
     * Email the error to the ADMIN
     */
    protected function email() {
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'From: "'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"].'" <xframe@'.$_SERVER["SERVER_NAME"].'>' . "\r\n";
        $headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";


        mail(Registry::get("ADMIN"),
             $this->message,
             $this->getContent(),
             $headers);
    }

    /**
     * Get the readable content for this exception
     */
    private function getContent() {
        $xslFile = ROOT.Registry::get("PLAIN_TEXT_ERROR");
        $transformation = new Transformation("<root><exceptions>".$this->getXML()."</exceptions></root>", $xslFile);
        return $transformation->execute();
    }

    /**
     * Get the XML for this exception
     */
    public function getXML() {
        $out = "<exception>";
        $out .= "<message>".htmlspecialchars($this->message, ENT_COMPAT, "UTF-8", false)."</message>";
        $out .= "<code>".htmlspecialchars($this->code, ENT_COMPAT, "UTF-8", false)."</code>";
        $out .= "<backtrace>";
        $i = 1;

        foreach ($this->getReversedTrace() as $back) {
            if ($back["class"] != "FrameEx") {
                $out .= "<step number='".$i++."' line='{$back['line']}' file='{$back['file']}' class='{$back['class']}' function='{$back['function']}' />";
            }
        }
        $out .= "</backtrace>";
        $out .= "</exception>";
        return $out;
    }

    /**
     * Return the array reversed back trace
     * @return array
     */
    public function getReversedTrace() {
        $trace = array();

        foreach (array_reverse($this->getTrace()) as $back) {
            $back['file'] = (array_key_exists("file", $back)) ? basename($back['file']) : "";
            $back['class'] = (array_key_exists("class", $back)) ? $back['class'] : "";
            $back['line'] = (array_key_exists("line", $back)) ? $back['line'] : "";
            $trace[] = $back;
        }

        return $trace;
    }

    /**
     * @return string
     */
    public function __toString() {
        try {
            return $this->getContent();
        }
        catch (Exception $e) {
            return "Error generating exception content. Original message: ".$this->message;
        }
    }

    /**
     * Save the exception for the next page
     */
    public function persist() {
        if (!is_array($_SESSION["exceptions"])) {
            $_SESSION["exceptions"] = array();
        }

        $_SESSION["exceptions"][] = $this;
    }

    /**
     * Return an array of exceptions that were persisted
     * @return array
     */
    public static function getPersistedExceptions() {
        //deal with any exceptions that were redirected
        if (array_key_exists("exceptions",$_SESSION) && is_array($_SESSION["exceptions"])) {
            //store in temp var
            $execptions = $_SESSION["exceptions"];
            //clear from session
            $_SESSION["exceptions"] = array();
            //return
            return $execptions;
        }

        return array();
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
                $exception->process();
            }
            catch (Exception $e) {
                echo $e->getMessage();
                die("Error logging exception");
            }
        }

        //finally echo it out
        echo $exception;
    }

    /**
     * Setup the error handling
     */
    public static function init() {
        set_exception_handler(array("FrameEx", "exceptionHandler"));
        set_error_handler(array("FrameEx", "errorHandler"), ini_get("error_reporting"));
    }

}
