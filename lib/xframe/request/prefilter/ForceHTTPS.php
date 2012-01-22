<?php
namespace xframe\request\prefilter;
use xframe\request\Prefilter;
use xframe\request\Request;
use xframe\request\Controller;

/**
 * Forces a web request to be over https
 */
class ForceHTTPS implements Prefilter {

    /**
     * Checks if the current request is secure and redirects to a secure protocol if not
     * @param Request $request
     * @param Controller $controller
     */
    public function run(Request $request, Controller $controller) {
        if (!$request->https) {
            $controller->redirect("https://" . $request->server['SERVER_NAME'] . $request->server['REQUEST_URI']);
        }
    }

}

