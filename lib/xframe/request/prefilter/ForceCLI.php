<?php
namespace xframe\request\prefilter;
use \xframe\request\Prefilter;
use \xframe\request\Request;
use \xframe\request\Controller;
use \Exception;

/**
 * Forces a request to be performed on the CLI
 */
class ForceCLI implements Prefilter {

    /**
     * Checks if the current request is being made on the cli and throws an Exception if not
     * @param Request $request
     * @param Controller $controller
     * @throws Exception
     */
    public function run(Request $request, Controller $controller) {
        if (!$request->cli) {
            throw new Exception("This request must be performed on the CLI.");
        }
    }
    
}

