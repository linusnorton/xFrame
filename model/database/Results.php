<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package database
 *
 * Encapsulates an array of results that implement the XML interface. Allows easy pagination and XML access
 */
class Results implements ArrayAccess, Countable, SeekableIterator, XML, Transformable {
    private $results;
    private $maxNumResults;
    private $numResults;
    private $currentResultNumber;
    private $resultsPerPage;
    private $customTag;
    private $transformer;

    /**
     * @param array $results array of {@link XML} results of a database query
     * @param int $maxNumResults total number of results the query could have returned (without LIMIT)
     * @param int $currentResultNumber use to calculate the current page
     * @param int $resultsPerPage
     * @param string $customTag custom XML tag that encapsulates the results
     */
    public function __construct(array $results, $maxNumResults, $currentResultNumber = 0, $resultsPerPage = 10, $customTag = "query" ) {
        $this->results = $results;
        $this->numResults = count($results);
        $this->maxNumResults = $maxNumResults;
        $this->currentResultNumber = $currentResultNumber;
        $this->resultsPerPage = $resultsPerPage;
        $this->customTag = $customTag;
    }

    /**
     * Return an XML string representation of the record
     * @return string $xml
     */
    public function getXML() {
        return $this->getTransformer()->getXML($this);
    }

    /**
     * Return the transformer for this object
     * @return XMLTransformer
     */
    public function getTransformer() {
        if ($this->transformer == null) {
            $this->transformer = new DefaultResultsTransformer();
        }
        return $this->transformer;
    }

    /**
     * Set the current transformer
     * @param XMLTransformer $transformer
     */
    public function setTransformer(XMLTransformer $transformer) {
        $this->transformer = $transformer;
    }

    /**
     * Return the array of results return from the query
     * @return array
     */
    public function getResults() {
        return $this->results;
    }

    /**
     * Return the number of results
     * @return int
     */
    public function getNumResults() {
        return $this->numResults;
    }

    /**
     * Return the maximum number of results the query could have returned if it didnt have a limit
     * @return int
     */
    public function getMaxNumResults() {
        return $this->maxNumResults;
    }

    /**
     * Get the start value
     * @return int
     */
    public function getCurrentResultNumber() {
        return $this->currentResultNumber;
    }

    /**
     * Get the limit value
     * @return int
     */
    public function getResultsPerPage() {
        return $this->resultsPerPage;
    }

    /**
     * Get the XML tag that will wrap the results and pagination
     * @return string
     */
    public function getCustomTag() {
        return $this->customTag;
    }

    /** grabs you the first item from the array */
    public function first() {
        reset($this->results);
        return current($this->results);
    }

    /** grabs you the last item from the deally*/
    public function last() {
        return end($this->results);
    }


    /** gets the item at the given key */
    public function get($key) {
        return $this->results[$key];
    }

    /** returns true if we're at the end of the internal array */
    public function done() {
        return is_null(key($this->results));
    }

    public function __get($key) {
        return $this->results[$key];
    }

    public function __set($key, $value) {
        return $this->results[$key] = $value;
    }

    /** give the people the power they want, oh yes hand over the array big boy */
    public function getArray() {
        return $this->results;
    }

    public function count() {
        return count ($this->results);
    }

    public function reset() {
        reset($this->results);
    }

    public function sort($function) {
        usort($this->results, $function);
        reset($this->results);
    }

    /* time to implement some ArrayAccess SPL*/
    public function offsetExists($key) {
        return array_key_exists($key, $this->results);
    }

    public function offsetGet($key) {
        return $this->get($key);
    }

    public function offsetSet($key, $value) {
        return $this->set($key, $value);
    }

    public function offsetUnset($key) {
        unset($this->results[$key]);
    }

    /* now for some Iterator SPL */

    /**
     * grabs you the current item in the array
     *
     */
    public function next() {
        $current = current($this->results);
        next($this->results);
        return $current;
    }

    /** grabs you the next item in the array */
    public function prev() {
        return prev($this->results);
    }

    /** gets the current item without changing the internal position */
    public function current() {
        return current($this->results);
    }

    public function rewind() {
        $this->reset();
    }

    public function key() {
        return key($this->results);
    }

    public function valid() {
        $key = key($this->results);
        return isset( $this->results[$key] );
    }

    /* now the finale SPL SeekableIterator */

    public function seek($index) {
        if (is_numeric($index) && $index >= 0 && $index < $this->count()) {
            reset($this->results);
            for ($i = 0; $i < $index; $i++) {
                next($this->results);
            }
        }
    }

}

