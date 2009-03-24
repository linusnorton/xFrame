<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @version 0.1
 * @package event
 *
 * This dispatcher stores a mapping of events to handlers and dispatches events to their correct handler
 */
class Dispatcher {
	private static $listeners = array();

    /**
     * This method takes the given event finds the event handler and passes the event to the handler
     *
     * @param Event $e
     * @return unknown
     */
	static function dispatch(Event $e) {

	    if (array_key_exists($e->getName(), self::$listeners)) {
            $object = new self::$listeners[$e->getName()]["class"];
            $method = self::$listeners[$e->getName()]["method"];
	        return $object->$method($e);
        }
	    else {
	       throw new UnknownEvent("No handler for ".$e->getName());
        }
    }

	/**
	 * Ok this registers a method to call for a given a event
	 *
	 * @param String $event
	 * @param String $class
	 * @param String $method
	 */
	static function addListener($event, $class, $method) {
	    self::$listeners[$event] = array("class" => $class, "method" => $method);
	}
}

?>
