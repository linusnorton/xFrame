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

		$out .= "<exception caught='{$style}'>";
        $out .= "<message><![CDATA[{$this->message}]]></message>";
		$out .= "<backtrace>";
        $i = 1;

		foreach (array_reverse($this->backtrace) as $back) {
			$back['file'] = basename($back['file']);
            $out .= "<step number='".$i++."' line='{$back['line']}' file='{$back['file']}' class='{$back['class']}' function='{$back['function']}' />";
		}
        $out .= "</backtrace>";
        //$out .= "<session><![CDATA[".print_r($_SESSION,true)."]]></session>";
        $out .= "</exception>";

        //return the error do no more
        if ($return) {
            return $out;
        }

        if (Registry::get("EMAIL_ERRORS") === true) {
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/xml; charset=iso-8859-1' . "\r\n";
            mail(Registry::get("ADMIN"), "Error from: ".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],$out, $headers );
        }

        Page::addException($out);
        error_log($this->message);
    }
}
