<?php
/**
 * Description of Resource
 *
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

    public static function create(array $attributes, $tableName = "resource") {
        $parameters = unserialize($attributes["parameters"]);
        $parameters = is_array($parameters) ? $parameters : array();

        return new Resource($attributes["name"],
                            $attributes["class"],
                            $attributes["method"],
                            $parameters,
                            $attributes["authenticator"],
                            $attributes["cache_length"],
                            $tableName);
    }

    /**
     * Execute the page
     * @param Request $r
     * @return mixed
     */
    public function execute(Request $r) {
        //if there is an authenticator attached to the request
        if ($this->authenticator != null) {
            //try to authorise
            $authenticator = new $this->authenticator;
            $authResult = $authenticator->authenticate($r);

            //if authorised do the request
            if ($authResult === true) {
                $object = new $this->class;
                return $object->{$this->method}($r);
            }
            //if not then forbidden
            else if ($authResult === false) {
                header('HTTP/1.1 403 Forbidden');
                die();
            }
            //else url to redirect to?
            else {
                Page::redirect($authResult);
            }
        }
        $object = new $this->class;
        return $object->{$this->method}($r);
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
    public function loadFromDB($tableName = "resource") {
        $resources = TableGateway::loadAll($tableName, null, null, array(), "Resource");

        foreach ($resources as $resource) {
            Dispatcher::addResource($resource);
        }
    }
}
