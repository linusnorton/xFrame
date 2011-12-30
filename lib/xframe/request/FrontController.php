<?php

namespace xframe\request;

use \xframe\core\DependencyInjectionContainer;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package request
 *
 * This encapsulates a given request. Usually this object will be routed
 * through the front controller and handled by a request controller
 */
class FrontController {

    /**
     * Stores the root directory and provides access to the database handle
     * @var DependencyInjectionContainer
     */
    private $dic;

    /**
     * Default handler for 404 requests
     * @var Controller
     */
    private $notFoundController;

    /**
     * Setup the initial state
     * @param DependencyInjectionContainer $dic
     */
    public function __construct(DependencyInjectionContainer $dic,
                                Controller $notFoundController = null) {
        $this->dic = $dic;
        $this->notFoundController = $notFoundController;
    }
    
    /**
     * Dispatches the given request to it's controller
     * @param Request $request 
     */
    public function dispatch(Request $request) {
        $filename = $this->dic->tmp.$request->getRequestedResource().'.php';
        
        //if we have a mapping for the request
        if (file_exists($filename)) {
            //return the response from the controller
            $controller = require($filename);
        }
        //if we rebuild on 404, disable this for performance
        else if ($this->dic->registry->get('AUTO_REBUILD_REQUEST_MAP')) {
            $this->rebuildRequestMap();
            $filename = $this->dic->tmp.$request->getRequestedResource().'.php';
            
            //try again, in case it has just been added
            if (file_exists($filename)) {
                $controller = require($filename);
            }
        }

        // if we still don't have a controller 404 it
        if (!isset($controller)) {
            $controller = $this->get404Controller();
        }

        $controller->handleRequest($request);
    }
    
    /**
     *
     * @return Controller
     */
    public function get404Controller() { 
        if ($this->notFoundController === null) {
            $this->notFoundController = new NotFoundController();
        }
        
        return $this->notFoundController;
    }

    private function rebuildRequestMap() {
        $mapper = new RequestMapGenerator($this->dic);
        $mapper->scan($this->dic->root.'src');
    }
}