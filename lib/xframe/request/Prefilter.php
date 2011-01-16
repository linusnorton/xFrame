<?php

namespace xframe\request;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package request
 *
 * The prefilter runs before a request is executed, it can used to provide
 * authentication and other goodies.
 */
interface Prefilter {

    public function run(Request $request, Controller $controller);

}