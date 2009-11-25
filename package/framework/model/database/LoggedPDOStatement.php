<?php
/** 
 * author Jason Paige
 * Decorator class for PDOStatement to allow logging of queries and their bound values
 */  
class LoggedPDOStatement {  
  
    private $postBindingStatement;
    private $statement;

    public function __construct(PDOStatement $statement) {
        $this->statement = $statement;
        $this->postBindingStatement = $statement->queryString;
    }

    public function execute() {  
        $start = microtime(true);  
        $result = $this->statement->execute(); 
        $time = microtime(true) - $start;  
        DB::dbh()->log($this->postBindingStatement, round($time * 1000, 3));
        return $result;  
    }

    /*
     * does some 'stupid' string replace at the moment
     * @todo PDO escaping on $value when it goes into the post binding
     */
    public function bindValue($parameter, $value, $dataType = PDO::PARAM_STR) {        
        $this->postBindingStatement = str_replace("{$parameter}", "'{$value}'", $this->postBindingStatement);
        return $this->statement->bindValue($parameter, $value, $dataType);
    }

    public function getPostBindingStatement() {
        return $this->postBindingStatement;
    }

    function __call ($method, $params) {
        return call_user_func_array (array ($this->statement, $method), $params); 
    }
}  
?>
