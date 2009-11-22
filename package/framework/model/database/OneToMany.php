<?php
/**
 * Maps a field in a record to multiple objects in order to lazy load objects from Records
 *
 * @author jason
 */
class OneToMany Implements MappedField {

    private $tableName;
    private $condition;
    private $className;
    private $methodName;
    private $start;
    private $num;
    private $orderBy;

    /**
     * @param string $tableName
     * @param Condition $condition
     * @param int $start
     * @param int $num
     * @param array $orderBy
     * @param string $className
     * @param string $methodName 
     */
    function __construct($tableName,
                         Condition $condition,
                         $start = null,
                         $num = null,
                         $orderBy = array(),
                         $className = "Record",
                         $methodName = "create") {

        $this->tableName = $tableName;
        $this->condition = $condition;
        $this->className = $className;
        $this->methodName = $methodName;
        $this->start = $start;
        $this->num = $num;
        $this->orderBy = $orderBy;
    }

    /**
     * @return array of type $this->className
     */
    public function load() {
        return TableGateway::loadMatching($this->tableName,
                                          $this->condition,
                                          $this->start,
                                          $this->num,
                                          $this->orderBy,
                                          $this->className,
                                          $this->methodName)->getArray();

    }

}
