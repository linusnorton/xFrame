<?php

namespace xframe\request;
use \Exception;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package request
 *
 * This encapsulates a given request. Usually this object will be routed
 * through the front controller and handled by a request controller
 */
class Request {
    private $requestedResource;
    private $parameters;
    private $mappedParameters;
    private $server;
    private $https;
    private $cli;

    public $files;
    public $cookie;
    
    /**
     *
     * @param string $requestURI
     * @param array $parameters
     */
    public function __construct($requestURI, array $parameters = array()) {
        $request = $requestURI;
        //see if there is a query attached
        $end = strpos($request, '?');
        //remove the query from the request
        $request = $end === false ? substr($request, 1) : substr($request, 1, $end - 1);
        //check for blank request
        $request = $request == '' ? 'index' : $request;
        //support for urls with request/param/param
        $request = explode('/', $request);
        //get the request name
        $this->requestedResource = $request[0];
        //get the parameters out of the request URI
        $this->mappedParameters = array_slice($request, 1);
        //store the other params (usually from $_REQUEST)
        $this->parameters = $parameters;        
        //get the $_FILES array
        $this->files = &$_FILES;
        $this->cookie = &$_COOKIE;
        $this->server = &$_SERVER;
        $this->https = $this->server['HTTPS'] == 'on' || $this->server['HTTP_X_SECURE'] == 'true';
        $this->cli = php_sapi_name() == 'cli';
    }

    /**
     * Apply the given parameter map to this request.
     *
     * loop over the parameters from the request URI and inject them into the
     * parameters given in the constructor (usually these come from $_REQUEST)
     * 
     * @param array $map
     */
    public function applyParameterMap(array &$map) {
        // use the given map to put the parameters in an associative array
        foreach ($map as $i => $parameter) {
            
            // if there is no key value for this param, throw an exception
            if ($parameter->isRequired() && !isset($this->mappedParameters[$i])) {
                throw new RequiredParameterEx("Parameter #{$i}({$parameter->getName()}) has not been provided and is required");
            }
            
            // if there is no value, try to get the default value
            if (!isset($this->mappedParameters[$i])) {
                $this->mappedParameters[$i] = $parameter->getDefault();
            }
            
            // pass the parameter through the request validation
            $parameter->validate($this->mappedParameters[$i]);  
            
            // add the parameter
            $this->parameters[$parameter->getName()] = $this->mappedParameters[$i];
        }
    }
    
    /**
     * @return array
     */
    public function getMappedParameters() {
        return $this->mappedParameters;
    }
    
    /**
     * @param array $parameters 
     */
    public function setMappedParameters(array $parameters) {
        $this->mappedParameters = $parameters;
    }

    /**
     * @return string
     */
    public function getRequestedResource() {
        return $this->requestedResource;
    }

    /**
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * Magic function overload. Allows east access to the request parameters.
     *
     * @param mixed $key
     * @return mixed
     */
    public function __get($key) {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }
    }

    /**
     * Magic function overload. Allows easy access to the request parameters
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        $this->parameters[$key] = $value;
    }

    /**
     * Unset the given variable
     * 
     * @param mixed $key
     */
    public function __unset($key) {
        unset($this->parameters[$key]);
    }

    /**
     * @param $key
     * @return boolean
     */
    public function __isset($key) {
        return isset($this->parameters[$key]);
    }

    /**
     * Return a hash of the Request
     */
    public function hash() {
        return md5($this->requestedResource.
                   implode($this->parameters).
                   implode(array_keys($this->parameters)));
    }

}
