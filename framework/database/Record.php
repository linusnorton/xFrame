<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package database
 *
 * This is the implementation of an Active Record pattern, it provides the ability to load, save, delete
 * and display as xml to any record.
 */
class Record implements XML {
    private $attributes = array();
    private $tableName;

    /**
     * Manually setup a record. Please remember that there is no sanity checking here
     * if you give it rubbish, there will be problems if you try to commit it to the db
     *
     * @param $tableName string table name
     * @param $attributes array associative array containing the fields in the tableName
     */
    public function __construct($tableName = null, array $attributes = array()) {
        //check tableName has been set to something
        if ($tableName == null) {
            return; //if not dont worry we may be populated manually
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
     *      return new Address($attributes["address1"], $attributes["city"], $attribute["country"], $customer);
     *
     * }
     *
     * This means that you can have a constructor for Address that type checks the Customer input
     *
     * @param $tableName string name of table
     * @param $attributes array associative array of fields loaded from db
     */
    public static function create($tableName, array $attributes = array()) {
        return new Record($tableName, $attributes);
    }

    /**
     * If a tableName is constructed with a tableName and id we will try to load the data from the database
     * If not we just create an empty record that can be populated using the setup() method
     *
     * @param $tableName string table name
     * @param $id mixed unique identifier, assumed to be id!!
     * @param $class class to instantiate (will be replaced with __STATIC__ in 5.3)
     */
    public static function load($tableName, $id, $class = "Record") {

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

            if ($class == "Record") {
                return new Record($tableName, $stmt->fetch(PDO::FETCH_ASSOC));
            }
            else {
                //call the given object's create method, this will be replaced with __STATIC__
                return call_user_func(array($class, 'create'), $stmt->fetch(PDO::FETCH_ASSOC));
            }
        }
        catch (PDOException $ex) {
            //there was some kind of database error
            throw new FrameEx($ex->getMessage());
        }
    }

    /**
     * Load a whole table of results and return an array of objects of type $class
     *
     * @param $tableName string table to load
     * @param $class string class to instantiate as records
     */
    public static function loadAll($tableName, $class = "Record") {
        $records = array();
        $results = DB::dbh()->query("SELECT * FROM ".addslashes($tableName));

        if ($class == "Record") {
            while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                $records[] = new Record($tableName, $row);
            }
        }
        else {
            while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                //call the given object's create method, this will be replaced with __STATIC__
                $records[] = call_user_func(array($class, 'create'), $row);
            }
        }

        return $records;
    }

    /**
     * This function is called before a save, it flattens the record so it
     * can inserted into the database. 
     *
     * @param $cascade boolean save related records
     */
    private function flatten($cascade, array &$saveGraph = array()) {
        $flatAttributes = array();

        //foreach attribute
        foreach($this->attributes as $key => $value) {
            //if i am also a record
            if ($value instanceof Record) {
                //check if I need to cascade the save
                if ($cascade && !array_key_exists($value->hash(), $saveGraph)) {
                    $saveGraph[$value->hash()] = true;
                    $value->save($cascade, $saveGraph); //this could throw an error
                }

                //and store my id
                $flatAttributes[$key] = $value->id;
            }
            else if ($cascade && is_array($value)) {
               foreach ($value as $item) {
                   if ($item instanceof Record && !array_key_exists($value->hash(), $saveGraph)) {
                       $saveGraph[$value->hash()] = true;
                       $item->save(true, $saveGraph);
                   }
               }
            }
            else {
                $flatAttributes[$key] = $value;
            }
        }

        return $flatAttributes;
     }

    /**
     * Commit this record to the db.
     *
     * @param $cascade boolean save related records as well
     */
    public function save($cascade = false, array &$saveGraph = array()) {
        try {
            $transactional = DB::dbh()->beginTransaction();
        }
        catch (PDOException $ex) { // Thrown if there is already a transaction in progress.
            $transactional = false;
        }

        try {
            //before we save convert objects to ids
            $flatAttributes = $this->flatten($cascade, $saveGraph);

            //create the SQL string
            $sql = "INSERT INTO `".$this->tableName."` SET ";
            $updateSql = " ON DUPLICATE KEY UPDATE ";

            foreach($flatAttributes as $key => $value) {
                $fields .= " `{$key}` = :".$key.",";
            }

            $fields = substr($fields,0 , -1);
            $sql = $sql.$fields.$updateSql.$fields; //combine the sql parts

            $stmt = DB::dbh()->prepare($sql);

            foreach($flatAttributes as $key => $value) {
                $stmt->bindValue(':'.$key, $value);
            }

            $stmt->execute();

            // Have to read the insert ID before committing the transaction, even though
            // we only want to set it after a successful commit.
            $insertId = DB::dbh()->lastInsertId();

            if ($transactional) {
                $success = DB::dbh()->commit();
                if (!$success) {
                    throw new FrameEx('Failed to commit transaction.');
                }
            }
            
            // Set the ID assigned for this record.
            if ($this->id == "") {
                $this->id = $insertId;
            }
        }
        catch (PDOException $ex) {
            if ($transactional) {
                DB::dbh()->rollBack();
            }
            throw new FrameEx($ex->getMessage());
        }
    }

    /**
     * Delete the record from the database
     */
    public function delete() {
        if ($this->tableName == "" || $this->id == "") {
            throw new FrameEx("You cannot delete a record that isn't initialised");
        }

        try {
            $stmt = DB::dbh()->prepare("DELETE FROM `".$this->tableName."` WHERE id = :id");
            $stmt->bindValue(':id', $this->attributes["id"]);
            $stmt->execute();
        }
        catch (PDOException $ex) {
            throw new FrameEx($ex->getMessage());
        }
    }

    /**
     * Return an XML string representation of the record
     */
    public function getXML() {
        $xml = "\n<record table='{$this->tableName}' id='{$this->attributes['id']}'>";

        foreach ($this->attributes as $key => $value) {
            $xml .= "\n\t<{$key}>";

            if ($value instanceof XML) {
                $xml .= $value->getXML();
            }
            else if (is_array($value)) {
                $xml .= ArrayUtil::getXML($value);
            }
            else {
				$xml .= "<![CDATA[{$value}]]>";
			}

            $xml .= "</{$key}>";
        }

        $xml .= "\n</record>";

        return $xml;
    }

    /**
     * @return The name of the database table that the record maps to.
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * @return An associative array that maps field names to values.
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @return A hash of the record consisting of the the table name and id
     */
    public function hash() {
        return $value->getTableName().$value->id;
    }
    /**
     * Make all the attributes public using this getter
     */
    public function __get($key) {
        return $this->attributes[$key];
    }

    /**
     * Make all the attributes public using this setter
     */
    public function __set($key, $value) {
        return $this->attributes[$key] = $value;
    }

}
