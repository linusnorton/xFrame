<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package database
 *
 * Encapsulates an array of results that implement the XML interface. Allows easy pagination and XML access
 */
class Results implements ArrayAccess, Countable, SeekableIterator, XML {
    private $results;
    private $maxNumResults;
    private $numResults;
    private $currentResultNumber;
    private $resultsPerPage;
    private $customTag;

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

    public function getXML() {
        $xml = "<{$this->customTag}>";
        $xml .= "<results>";

        //add the xml of the results
        foreach ($this->results as $record) {
            if ($record instanceof XML) {
                $xml .= $record->getXML();
            }
        }

        $xml .= "</results>";
        $xml .= "<pagination>";

        if (ctype_digit("{$this->resultsPerPage}")) {
            for ($i = 0; $i < $this->maxNumResults ; $i += $this->resultsPerPage) {
                $selected = ($i == $this->currentResultNumber) ? " selected='true'" : "";
                $end = ($i + $this->resultsPerPage < $this->maxNumResults) ? $i + $this->resultsPerPage : $this->maxNumResults;

                $xml .= "
                <page{$selected}>
                    <start>{$i}</start>
                    <end>{$end}</end>
                </page>";
            }
        }
        
        $xml .= "</pagination>";
        $xml .= "<maxNumResults>{$this->maxNumResults}</maxNumResults>" ;
        $xml .= "<numResults>{$this->numResults}</numResults>" ;
        $xml .= "<currentResultNumber>{$this->currentResultNumber}</currentResultNumber>" ;
        $xml .= "<resultsPerPage>{$this->resultsPerPage}</resultsPerPage>" ;
        $xml .= "</{$this->customTag}>";

        return $xml;
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

?>
