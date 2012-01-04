<?php

namespace xframe\request;
use \xframe\util\Container;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package request
 *
 * Session storage that calls session_write_close() after writes to enable more
 * concurrent requests.
 */
class Session extends Container {    
    private $closeOnWrite;
    
    /**
     * @param array $defaults
     * @param boolean $close
     */
    public function __construct(array $defaults = array(), $close = true) {
        parent::__construct(&$defaults);
        
        $this->closeOnWrite = $close;
    }
    
    /**
     * Set closeOnWrite
     * 
     * @param type $closeOnWrite 
     */
    public function setCloseOnWrite($closeOnWrite) {
        $this->closeOnWrite = $closeOnWrite;
    }

    /**
     * If closeOnWrite is true the session will be closed after this call.
     * 
     * If you are making frequent calls it is best to disable closeOnWrite
     * 
     * @param type $key
     * @param type $value 
     */
    public function __set($key, $value) {
        if ('' === session_id()) {
            session_start();        
        }
        
        parent::__set($key, $value);
        
        if ($this->closeOnWrite) {
            session_write_close();            
        }
    }

}