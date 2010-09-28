<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 */
class Resource extends Record {

    public function __construct($name,
                                $class,
                                $method,
                                array $parameters = array(),
                                $authenticator = null,
                                $cacheLength = false,
                                $tableName = "resource",
                                array $attributes = array()) {

        parent::__construct($tableName, $attributes);
        $this->name = $name;
        $this->class = $class;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->authenticator = $authenticator;
        $this->cache_length = $cacheLength;
    }

    /**
     * Create resource from database record
     * @param array $attributes
     * @param string $tableName
     * @return Resource
     */
    public static function create(array $attributes, $tableName = "resource") {
        $parameters = unserialize($attributes["parameters"]);
        $parameters = is_array($parameters) ? $parameters : array();

        return new Resource($attributes["name"],
                            $attributes["class"],
                            $attributes["method"],
                            $parameters,
                            $attributes["authenticator"],
                            $attributes["cache_length"],
                            $tableName,
                            $attributes);
    }

    /**
     * Return the controller for this resource
     * @return Controller
     */
    public function getController(Request $request) {
        $request->applyParameterMap($this->parameters);
        return new $this->class($this, $request);
    }
    
    /**
     *
     * @return array
     */
    public function getParameterMap() {
        return $this->parameters;
    }

    /**
     *
     * @return mixed
     */
    public function getCacheLength() {
        return $this->cache_length;
    }

    /**
     *
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     *
     * @param boolean $cascade
     * @param array $saveGraph
     */
    public function save($cascade = false, array &$saveGraph = array()) {
        $this->parameters = serialize($this->parameters);
        parent::save($cascade, $saveGraph);
        $this->parameters = unserialize($this->parameters);
    }

    /**
     * Load resource from the given table name
     * @param string $tableName
     */
    public static function loadFromDB($tableName = "resource") {
        $resources = TableGateway::loadAll($tableName, null, null, array(), "Resource");

        foreach ($resources as $resource) {
            Dispatcher::addResource($resource);
        }
    }

    /**
     * Get the Resource for the given Request
     * @param Request $request
     * @return Resource
     */
    public static function getFromRequest(Request $request) {
        $resources = Dispatcher::getListeners();
        return $resources[$request->getRequestedResource()];
    }
}
