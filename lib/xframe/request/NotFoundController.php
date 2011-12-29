<?php

namespace xframe\request;

/**
 * Handles 404 requests
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class NotFoundController extends Controller {
    
    public function __construct() {
        
    }

    /**
     * Send back a 404 response
     */
    public function handleRequest(Request $request) {  
        if (PHP_SAPI !== 'cli') {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
        }
        
        die('Resource: '.$request->getRequestedResource().' not found.'.PHP_EOL);
    }
}
