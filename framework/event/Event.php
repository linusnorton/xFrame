<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package event
 *
 * An event encapsulates a given request
 */
class Event implements ArrayAccess {
	private $params = array();
    private $name;

    /**
     * The event to be passed to the dispatcher. The $name is used to get the
     * type of event and the argArray is all the properties you want the event
     * to have so if your updating a product for example this could be the
     * $_POST variable containing all the new lovely product values.
     *
     * @param String $name
     * @param array $argArray
     */
	public function __construct($name, array $argArray = null) {
		$this->name = $name;
		$this->params = $argArray;
	}

	/**
	 * Magic function overload. If a variable on this object is accessed but
	 * it doesnt exist try get it from the params array. This means that you
	 * can now give an array like $_POST or $_GET in the constructor and then
	 * access the fields like $e->myVar etc. Enjoy.
	 *
	 * @param unknown_type $key
	 * @return unknown
	 */
	public function __get($key) {
		if (array_key_exists($key, $this->params)) {
			return $this->params[$key];
        }

	}

	/**
	 * Magic function overload. If you try to set a variable that doesnt exist
	 * this function is called. So setting $e->face = "your" when the variable
	 * face doesn't exists sets it in the interal array for later access.
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public function __set($key, $value) {
		$this->params[$key] = $value;
	}

	/**
	 * Returns the name of the event
	 *
	 * @return String
	 */
	public function getName() {
		return $this->name;
	}
	/**
	 * Sets the name of the event
	 *
	 * @param String $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
	/**
	 * Returns the params of the event
	 *
	 * @return String
	 */
	public function getParams() {
		return $this->params;
	}

    /**
     * Dispatches the event using Dispatcher::dispatch
     */
    public function dispatch() {
        return Dispatcher::dispatch($this);
    }

    /**
     * Return a hash of the Event
     */
    public function hash() {
        return md5($this->name.implode($this->params).implode(array_keys($this->params)));
    }

    ////////////////////////////////////////////////////////////////////
    // ArrayAccess implementation
    ////////////////////////////////////////////////////////////////////

    /**
     * check to see whether an array key exists
     *
     * @param $key string array key to check
     */
    public function offsetExists($key) {
        return array_key_exists($key, $this->params);
    }

    /**
     * return a value
     *
     * @param $key string value to return
     */
    public function offsetGet($key) {
        return $this->params[$key];
    }

    /**
     * set a value
     *
     * @param $key string key of the value to set
     * @param $value mixed value to set
     */
    public function offsetSet($key, $value) {
        return $this->params[$key] = $value;
    }

    /**
     * unset a value from the array
     *
     * @param $key string key to unset
     */
    public function offsetUnset($key) {
        unset($this->params[$key]);
    }

}

?>
