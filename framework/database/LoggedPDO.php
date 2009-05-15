<?php
/** 
 * author Jason Paige
 * Subclass of PDO to allow logging of queries and their bound values
 */  
class LoggedPDO extends PDO {  
    private $log;  
  
    public function __construct($dsn, $username = null, $password = null) { 
        $this->log = array(); 
        parent::__construct($dsn, $username, $password);  
    }  
  
    public function query($query) {  
        $start = microtime(true);  
        $result = parent::query($query);  
        $time = microtime(true) - $start;  
        $this->log($query, round($time * 1000, 3));
        return $result;  
    }

    public function prepare ($statement) {
        $stmt = parent::prepare($statement);

        return new LoggedPDOStatement ($stmt);
    }

    public function log($query, $time) {
        $this->log[] = array("query" => $query, "time" => $time);
    }

    public function displayLog($toString = false) {
        return print_r($this->log, $toString);
    }
}
?>