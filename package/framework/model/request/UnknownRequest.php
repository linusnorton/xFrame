<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package request
 *
 * If there is no mapping for a given request this exception is thrown
 */
class UnknownRequest extends FrameEx {

    /**
     * This exception just takes the request that could not be processed
     * @param Request $request
     */
    public function __construct(Request $request) {
        parent::__construct("The requested resource: {$request->getRequestedResource()} could not be found.", 404, FrameEx::LOWEST);
    }

    /**
     * Most of the time the user will not see this error (the browser will)
     * so it does not need to be as verbose.
     */
    public function __toString() {
        if (!headers_sent()) {
            header("HTTP/1.0 404 Not Found");
        }
        die($this->message);
    }
}
