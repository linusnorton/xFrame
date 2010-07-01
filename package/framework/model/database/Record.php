<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package database
 *
 * This is the implementation of an Active Record pattern, it provides the ability to load, save, delete
 * and display as xml to any record.
 */
class Record implements XML, Transformable {
    protected $attributes;
    private $tableName;
    private $transformer;

    /**
     * Manually setup a record. Please remember that there is no sanity checking here
     * if you give it rubbish, there will be problems if you try to commit it to the db
     *
     * @param $tableName string table name
     * @param $attributes array associative array containing the fields in the tableName
     */
    public function __construct($tableName = null, array $attributes = array()) {
        //check tableName has been set to something, if not assume class name
        if ($tableName == null) {
            $tableName = __CLASS__;
        }

        $this->tableName = addslashes($tableName);
        $this->attributes = $attributes;
    }

    /**
     * Instantiate new record from the given associative array. This method
     * should take a flat packed array (usually from a db query) and map
     * those values to the constructor. For the default implementation this
     * is a simple copy but you're implementation might look something like
     *
     * public static function create(array $attributes) {
     *     $customer = Customer::load($attributes["customer_id"]);
     *     return new Address($attributes["address1"], $attributes["city"], $attribute["country"], $customer);
     * }
     *
     * This means that you can have a constructor for Address that type checks the Customer input
     *
     * @param $attributes array associative array of fields loaded from db
     * @param $tableName string name of table
     */
    public static function create(array $attributes, $tableName = "table") {
        return new Record($tableName, $attributes);
    }

    /**
     * If a Record is constructed with a tableName and id we will try to load the data from the database
     * If not we just create an empty record that can be populated using the setup() method
     *
     * @param $tableName string table name
     * @param $id mixed unique identifier, assumed to be id!!
     * @param $class class to instantiate (will be replaced with __STATIC__ in 5.3)
     * @param $method function to call to initialize the class
     */
    public static function load($tableName, $id, $class = "Record", $method = "create") {
        $attributes = false;

        //if we're not caching or the record is not in the cache
        if (!Registry::get("CACHE_ENABLED") || false === ($attributes = Cache::mch()->get($tableName.$id))) {
            //lets try to get the data from the db
            try {
                $stmt = DB::dbh()->prepare('SELECT * FROM `'.addslashes($tableName).'` WHERE `id` = :id');
                $stmt->bindValue(':id', $id);
                $stmt->execute();

                //if we dont get any records or we get multiple throw an exception
                if ($stmt->rowCount() === 0) {
                    throw new MissingRecord("Could not find a {$tableName} where id = {$id}");
                }
                if ($stmt->rowCount() > 1) {
                    throw new MultipleRecord("Multiple records were matched");
                }
            }
            catch (PDOException $ex) {
                //there was some kind of database error
                throw new FrameEx($ex->getMessage());
            }

            $attributes = $stmt->fetch(PDO::FETCH_ASSOC);

            //if we're caching, put it in
            if (Registry::get("CACHE_ENABLED")) {
                Cache::mch()->set($tableName.$id, $attributes, false, 0);
            }
        }

        //call the given object's create method, this will be replaced with __STATIC__
        return call_user_func(array($class, $method), $attributes, $tableName);
    }


    /**
     * This function is called before a save, it flattens the record so it
     * can be inserted into the database.
     *
     * @param boolean $cascade boolean save related records
     * @param array $saveGraph
     */
    private function flatten($cascade, array &$saveGraph = array()) {
        $flatAttributes = array();

        //foreach attribute
        foreach($this->attributes as $key => $value) {
            $value = $this->$key;
            //if ($value instanceof MappedField) {
            //    continue; //lazy field that is unchanged
            //}
            //if i am also a record
            if ($value instanceof Record) {
                //check if I need to cascade the save to get the id
                if ($cascade && !array_key_exists($value->hash(), $saveGraph)) {
                    $value->save($cascade, $saveGraph); //this could throw an error
                }
                //if i dont have an id and im in the save graph we have an unresolvable cycle
                else if ($value->id == "" && array_key_exists($value->hash(), $saveGraph)) {
                    throw new CyclicalRelationshipException("Found cyclical reference between {$this->tableName} and {$value->getTableName()}", 110);
                }

                //and store my id
                $flatAttributes[$key] = $value->id;
            }
            else if (is_array($value)) {
                if ($cascade) {
                    foreach ($value as $item) {
                        if ($item instanceof Record && !array_key_exists($item->hash(), $saveGraph)) {
                            $item->save(true, $saveGraph);
                        }
                        //if i dont have an id and im in the save graph we have an unresolvable cycle
                        else if ($item->id == "" && array_key_exists($item->hash(), $saveGraph)) {
                            throw new CyclicalRelationshipException("Found cyclical reference between {$this->tableName} and {$item->getTableName()}", 111);
                        }
                    }
                }
                // If they are not full of records to be persisted recursively, arrays are ignored.
            }
            else {
                $flatAttributes[$key] = $value;
            }
        }

        return $flatAttributes;
     }

     /**
      * Attempt to start a database transaction
      * @return boolean
      */
     private function startTransaction() {
         try {
             $transactional = DB::dbh()->beginTransaction();
         }
         catch (PDOException $ex) { // Thrown if there is already a transaction in progress.
             $transactional = false;
         }

         return $transactional;
     }

    /**
     * Commit this record to the db.
     *
     * @param $cascade boolean save related records as well
     */
    public function save($cascade = false, array &$saveGraph = array()) {
        $saveGraph[$this->hash()] = true;
        $transactional = $this->startTransaction();

        try {
            //before we save convert objects to ids
            $flatAttributes = $this->flatten($cascade, $saveGraph);
            $stmt = DB::dbh()->prepare($this->createSaveSQL($flatAttributes));

            foreach($flatAttributes as $key => $value) {
                $stmt->bindValue(':'.$key, $value);
            }

            $stmt->execute();

            // Have to read the insert ID before committing the transaction, even though
            // we only want to set it after a successful commit.
            $insertId = DB::dbh()->lastInsertId();
            Assert::isFalse($transactional && !DB::dbh()->commit(), 'Failed to commit transaction.');

            // Set the ID assigned for this record.
            if ($this->id == "" && $insertId == "") {
                throw new FrameEx("Tried to insert a record but didn't get an ID back - usually a constraint error", 113);
            }
            else if ($this->id == "") {
                $this->id = $insertId;
            }
        }
        catch (Exception $ex) {
            if ($transactional) {
                DB::dbh()->rollBack();
            }
            
            //many PDO errors are not integers, even though Exception requires them to be
            $code = ((int) $ex->getCode() == 0) ? 115 : $ex->getCode();
            throw new FrameEx("Error saving {$this->tableName} - ".$ex->getMessage(), $code, FrameEx::HIGH, $ex);
        }

        if (Registry::get("CACHE_ENABLED")) {
            Cache::mch()->delete($this->tableName.$this->id);
        }
    }

    private function createSaveSQL($flatAttributes) {
        if (empty($flatAttributes)) { // Special case for unsaved records with no explicitly set fields.
            $sql = "INSERT INTO `".$this->tableName."` SET `id`=DEFAULT";
        }
        else {
            $sql = "INSERT INTO `".$this->tableName."` SET ";
            $updateSql = " ON DUPLICATE KEY UPDATE ";
            $fields = "";

            foreach($flatAttributes as $key => $value) {
                $fields .= " `{$key}` = :".$key.",";
            }

            $fields = substr($fields,0 , -1);
            $sql = $sql.$fields.$updateSql.$fields; //combine the sql parts
        }
        return $sql;
    }

    /**
     * Delete the record from the database
     */
    public function delete() {
        if ($this->tableName == "" || $this->id == "") {
            throw new FrameEx("You cannot delete a record that isn't initialised" , 116);
        }

        $stmt = DB::dbh()->prepare("DELETE FROM `".$this->tableName."` WHERE id = :id");
        $stmt->bindValue(':id', $this->attributes["id"]);
        $stmt->execute();

        if (Registry::get("CACHE_ENABLED")) {
            Cache::mch()->delete($this->tableName.$this->id);
        }
    }

    /**
     * Return an XML string representation of the record
     * @return string $xml
     */
    public function getXML() {
        return $this->getTransformer()->getXML($this);
    }

    /**
     * @return The name of the database table that the record maps to.
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * Set the table name for this record
     * @param string $tableName
     */
    public function setTableName($tableName) {
        return $this->tableName = $tableName;
    }

    /**
     * @return An associative array that maps field names to values.
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * Return the transformer for this object
     * @return XMLTransformer
     */
    public function getTransformer() {
        if ($this->transformer == null) {
            $this->transformer = new DefaultRecordTransformer();
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
     * @return A hash of the record consisting of the the table name and id
     */
    public function hash() {
        return spl_object_hash($this);
    }

    /**
     * Free getters and setters for everyone
     */
    public function __call($method, array $arguments) {
        $action = substr($method ,0, 3);
        $property = substr($method, 3);

        //if we are calling a getter
        if ($action == "get") {

            //loop each attribute
            foreach ($this->attributes as $key => $value) {
                // and try to find the right one
                $key = explode("_",$key);
                $key = array_map("ucfirst", $key);
                $key = implode($key);

                if ($key == $property) {
                    return $value;
                }
            }
        }
        else if ($action == "set") {
            //loop each attribute
            foreach ($this->attributes as $key => &$value) {
                // and try to find the right one
                $key = explode("_",$key);
                $key = array_map("ucfirst", $key);
                $key = implode($key);

                if ($key == $property) {
                    return $value = current($arguments);
                }
            }
        }

        throw new FrameEx("Unknown method {$method}", 117);
    }

    /**
     * Make all the attributes public using this getter
     * @param mixed $key
     * @return mixed $value
     */
    public function __get($key) {
        if ($this->attributes[$key] instanceof MappedField) {
            $this->attributes[$key] = $this->attributes[$key]->load();
        }

        return $this->attributes[$key];
    }

    /**
     * Make all the attributes public using this setter
     * @param mixed $key
     * @param mixed $value
     * @return mixed $value
     */
    public function __set($key, $value) {
        return $this->attributes[$key] = $value;
    }

    /**
     * Unset the given variable
     * @param mixed $key
     */
    public function __unset($key) {
        unset($this->attributes[$key]);
    }

    /**
     * @param $key
     * @return boolean
     */
    public function __isset($key) {
        return isset($this->attributes[$key]);
    }

}
