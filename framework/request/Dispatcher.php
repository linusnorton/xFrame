<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package request
 *
 * This dispatcher stores a mapping of requests to handlers and dispatches requests to their correct handler
 */
class Dispatcher {
	private static $listeners = array();

    /**
     * This method takes the given request finds the request handler and passes the request to the handler
     *
     * @param Event $e
     * @return unknown
     */
	public static function dispatch(Request $r) {

	    if (array_key_exists($r->getName(), self::$listeners)) {
            $object = new self::$listeners[$r->getName()]["class"];
            $method = self::$listeners[$r->getName()]["method"];
	        return $object->$method($r);
        }
	    else {
	       throw new UnknownRequest("No handler for ".$r->getName());
        }
    }

	/**
	 * Ok this registers a method to call for a given a request
	 *
	 * @param String $request
	 * @param String $class
	 * @param String $method
     * @param int $cacheLength
     * @param array $parameterMap
	 */
	public static function addListener($requestName, $class, $method, $cacheLength = false, array $parameterMap = array()) {
	    self::$listeners[$requestName] = array("class" => $class, "method" => $method, "cacheLength" => $cacheLength, "parameterMap" => $parameterMap);
	}

    /**
     * get the cache length for the given request
     *
     * @param $request Request to get the cache length for
     */
    public static function getCacheLength(Request $r) {
	    if (array_key_exists($r->getName(), self::$listeners)) {
	        return self::$listeners[$r->getName()]["cacheLength"];;
        }
	    else {
	       return false;
        }
    }

    public static function getParameterMap($requestName) {
        return self::$listeners[$requestName]["parameterMap"];
    }
}

?>
