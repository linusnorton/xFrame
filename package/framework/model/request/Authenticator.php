<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package request
 *
 * An authenticator authenticates a supplied resource. When a request handler is added to the
 * dispatcher you can optionally pass an authenticator that implements the authenticate method.
 *
 * When the dispatcher receives a request for a resource with an authenticator attached to it
 * the authenticate method will be called. It should return true, false or a url to redirect to
 */
interface Authenticator {

    public function authenticate(Request $request);

}
?>
