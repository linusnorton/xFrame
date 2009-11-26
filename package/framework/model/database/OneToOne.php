<?php
/**
 * Maps a field in a record to it's class in order to lazy load objects from Records
 *
 * @author jason
 */
class OneToOne implements MappedField {

    private $id;
    private $className;
    private $methodName;

    /**
     * @param int $id
     * @param string $className
     */
    function __construct($id, $className = "Record", $methodName = "load") {
        $this->id = $id;
        $this->className = $className;
        $this->methodName = $methodName;
    }

    /**
     * @return Object of type $className
     */
    public function load() {
        if ($this->id == null) {
            return null;
        }
        return call_user_func(array($this->className, $this->methodName), $this->id);
    }

}
