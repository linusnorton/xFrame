<?php

namespace xframe\request;
use \xframe\core\DependencyInjectionContainer;
use \xframe\view\View;
use \Exception;

/**
 * This is the base class for all controllers. It sets up the default view to
 * handle the given request.
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class Controller {

    /**
     * @var DependencyInjectionContainer
     */
    protected $dic;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \xframe\view\View
     */
    protected $view;
    
    /**
     * @var array of Prefilter
     */
    private $prefilters;

    /**
     * @var int
     */
    private $cacheLength;

    /**
     * @var boolean
     */
    private $cacheEnabled;
    
    /**
     * @var string
     */
    private $method;

    /**
     * Constructor
     * 
     * @param DependencyInjectionContainer $dic
     * @param Request $request
     * @param string $method
     * @param View $view
     * @param array $parameterMap
     * @param array $prefilters
     * @param int $cacheLength
     */
    public function  __construct(DependencyInjectionContainer $dic,
                                 Request $request,
                                 $method,
                                 View $view,
                                 array $parameterMap = array(),
                                 array $prefilters = array(),
                                 $cacheLength = false) {
        $this->dic = $dic;
        $this->request = $request;
        $this->request->applyParameterMap($parameterMap);
        $this->method = $method;
        $this->prefilters = $prefilters;
        $this->cacheLength = $cacheLength;
        $this->cacheEnabled = $this->dic->registry->get("CACHE_ENABLED");
        $this->view = $view;
    }

    /**
     * If headers haven't been sent, redirect to the given location
     * If the headers have been sent... throw an exception.
     * @param string $location
     */
    public function redirect($location) {
        if (headers_sent()) {
            throw new Exception("Could not redirect to {$location}, headers already sent");
        }

        header("Location: ".$location);
        die();
    }

    /**
     * Send 403 headers and die
     */
    public function forbidden() {
        if (headers_sent()) {
            throw new Exception("Error sending 403, headers already sent");
        }

        header('HTTP/1.1 403 Forbidden');
        die();
    }

    /**
     * Run the prefilter (if set), run the controller and output the response
     */
    public function handleRequest() {
        //if we continue running after the prefilter has run
        if ($this->runPrefilters()) {
            //process the response
            $response = $this->processRequest();
            //cache the response
            $this->cacheResponse($response);
            //output the response
            echo $response;
        }
    }

    /**
     * Cache the given response
     * @param string $response
     */
    protected function cacheResponse($response) {
        if ($this->cacheEnabled && $this->cacheLength !== false) {
            $this->dic->cache->set(
                $this->request->hash(),
                $response,
                false,
                $this->resource->getCacheLength()
            );
        }
    }

    /**
     * Execute the controller method and return the response
     * @return string
     */
    protected function processRequest() {
        $response = $this->getResponseFromCache();

        //if it wasn't in the cache or the cache is not on...
        if ($response === false) {
            //preform the init
            $this->init();
            //execute controller method
            $this->{$this->method}();
            //use the view to generate the response
            $response = $this->view->execute();
        }

        return $response;
    }

    /**
     * Provide the page response
     * @return string|boolean
     */
    protected function getResponseFromCache() {
        //see if we can grab it from the cache
        if ($this->cacheEnabled && $this->cacheLength !== false) {
            return $this->dic->cache->get($this->request->hash());
        }
        return false;
    }

    /**
     * Run the prefilter and return a boolean that indicates whether to continue
     * execution
     *
     * @return boolean
     */
    protected function runPrefilters() {
        // run each prefilter
        foreach ($this->prefilters as $filter) {
            // if there is a prefilter for this request, run it
            if ($filter instanceof Prefilter && !$filter->run($this->request, $this)) {
                return false;
            }
        }
        // all prefilters passed
        return true;
    }

    /**
     * This method is class before the controller method is called
     */
    protected function init() {
        
    }

}