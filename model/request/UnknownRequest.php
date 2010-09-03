<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package request
 *
 * If there is no mapping for a given request this exception is thrown
 */
class UnknownRequest extends FrameEx {

    public function output($uncaught = false, $return = false) {
        header("HTTP/1.0 404 Not Found");
        die();
    }
}
