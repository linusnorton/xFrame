<?php

namespace demo\controller;
use \xframe\request\Controller;

/**
 * Endpoint for the xFrame CLI, displays help
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class Index extends Controller {

    /**
     * @Request("index")
     * @Parameter(name="userId", validator="Digit(1,1000000)", required=true)
     * @Parameter(name="username", validator="RegEx('/u[0-9]{3}[a-z]/i')", required=false, default="u123A")
     */
    public function run() {
        echo "<pre>";
        print_r($this->request);
        echo "</pre>";
    }

}

