<?php

/**
 * This is the base class for all controllers. It sets up the default view to
 * handle the given request.
 *
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class Controller {

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var View
     */
    protected $view;

    /**
     * @var string
     */
    private static $executionTime;

    /**
     *
     * @param Resource $resource
     * @param Request $request
     */
    public function  __construct(Resource $resource, Request $request) {
        $this->resource = $resource;
        $this->request = $request;

        $viewClass = $resource->getViewType() == null ? Registry::get("DEFAULT_VIEW") : $resource->getViewType();
        $this->view = new $viewClass($request->debug);
        
        if ($resource->getViewTemplate() != null) {
            $this->view->setTemplate($resource->getViewTemplate());
        }

        if (Registry::get("COMPRESS_OUTPUT")) {
            ob_start("ob_gzhandler");
        }

        if (!headers_sent()) {
            session_start();
        }
    }

    /**
     * Persist the given exception and redirect to another page
     * @param string $location URI
     * @param FrameEx $ex
     */
    public function persistAndRedirect($location, FrameEx $ex) {
        $ex->persist();
        $this->redirect($location);
    }

    /**
     * If headers haven't been sent, redirect to the given location
     * If the headers have been sent... throw an exception.
     * @param string $location
     */
    public function redirect($location) {
        if (headers_sent()) {
            throw new FrameEx("Could not redirect to {$location}, headers already sent");
        }

        header("Location: ".$location);
        die();
    }

    /**
     * Send 403 headers and die
     */
    public function forbidden() {
        if (headers_sent()) {
            throw new FrameEx("Error sending 403, headers already sent");
        }

        header('HTTP/1.1 403 Forbidden');
        die();
    }

    /**
     * Get the page response
     * @return string
     */
    public function getResponse() {
        try {
            $this->authorizeRequest();
            $response = $this->handleRequest();
            $this->cacheResponse($response);
        }
        catch (FrameEx $ex) {
            $response = $this->handleException($ex);
        }

        return $response;
    }

    /**
     * Cache the given response
     * @param string $response
     */
    protected function cacheResponse($response) {
        if (Registry::get("CACHE_ENABLED") && $this->resource->getCacheLength() !== false) {
            Cache::mch()->set($this->request->hash(), $response, false, $this->resource->getCacheLength());
        }
    }

    /**
     * Process the exception and add it to the current page
     */
    protected function processException(FrameEx $ex) {
        $ex->process();
        $this->view->addException($ex);
    }

    /**
     * Execute the controller method and return the response
     * @return string
     */
    protected function handleRequest() {
        $response = false;

        //see if we can grab it from the cache
        if (Registry::get("CACHE_ENABLED")) {
            $response =  $this->getResponseFromCache();
        }

        //if it wasn't in the cache or the cache is not on...
        if ($response === false) {
            //preform the init
            $this->init();
            //add any existing exceptions
            $this->addPersistedExceptions();
            //get the controller method
            $method = $this->resource->getMethod();
            //execute controller method
            $this->$method();
            //use the view to generate the response
            $response = $this->view->execute();
        }

        return $response;
    }

    /**
     * This is the default handler for exceptions that occur when building the page.
     *
     * By default it adds the exception to the page and uses the view to generate
     * and error page.
     *
     * It can be overridden to provide different functionality such as persisting
     * the exception and redirecting to an error page.
     *
     * @param FrameEx $ex
     * @return string
     */
    protected function handleException(FrameEx $ex) {
        $this->processException($ex);
        return $this->view->getErrorPage();
    }

    /**
     * Add all the exceptions that have been persisted to the current page
     */
    protected function addPersistedExceptions() {
        foreach (FrameEx::getPersistedExceptions() as $ex) {
            $this->processException($ex);
        }
    }

    /**
     * Provide the page response
     */
    protected function getResponseFromCache() {
        return Cache::mch()->get($this->request->hash());
    }

    /**
     * Authorizes the current request against the resource
     */
    protected function authorizeRequest() {
        //if there is an authenticator attached to the request
        if ($this->resource->getAuthenticator() != null) {
            //try to authorise
            $class = $this->resource->getAuthenticator();
            $authenticator = new $class;
            $authResult = $authenticator->authenticate($this->request);

            //if not authorised then forbidden
            if ($authResult === false) {
                $this->forbidden();
            }
            //if we got a url to redirect to
            else if ($authResult !== true) {
                $this->redirect($authResult);
            }
        }
    }

    /**
     * This method is class before the controller method is called
     */
    protected function init() {
        //to be overridden
    }

    /**
     * initialize the page
     */
    public static function boot() {
        self::$executionTime = microtime(true); //used for script execution time
    }

    /**
     * @return int
     */
    public static function getExecutionTime() {
        return self::$executionTime;
    }

    /**
     * issue with POST variable in PHP not be properly populated in some circumstances:
     * http://getluky.net/2009/02/24/php-_post-array-empty-although-phpinput-and-raw-post-data-is-available/
     */
    protected function getParamsFromRawHTTPRequest() {
        $keyValuePairs = explode("&", file_get_contents('php://input'));

        foreach ($keyValuePairs as $keyValue) {
            $data = explode("=", $keyValue);
            $this->request->$data[0] = $data[1];
        }
    }

}