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
    private $schema;

    /**
     * Manually setup a record. Please remember that there is no sanity checking here
     * if you give it rubbish, there will be problems if you try to commit it to the db
     *
     * @param $schema string table name
     * @param $attributes array associative array containing the fields in the schema
     */
    public function __construct($schema = null, array $attributes = array()) {
        //check schema has been set to something
        if ($schema == null) {
            return; //if not dont worry we may be populated manually
        }
        
        $this->schema = addslashes($schema);
        $this->attributes = $attributes;
    }
    
    /**
     * If a schema is constructed with a schema and id we will try to load the data from the database
     * If not we just create an empty record that can be populated using the setup() method
     *
     * @param $schema string table name
     * @param $id mixed unique identifier, assumed to be id!!
     */
    public function load($schema, $id) {
        $this->schema = addslashes($schema);

        //lets try to get the data from the db
        try {
            $stmt = DB::dbh()->prepare('SELECT * FROM `'.$schema.'` WHERE `id` = :id');
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            //if we dont get any records or we get multiple throw an exception
            if ($stmt->rowCount() === 0) {
                throw new MissingRecord("Could not find a {$schema} where id = {$id}");
            }
            if ($stmt->rowCount() > 1) {
                throw new MultipleRecord("Multiple records were matched");
            }

            $this->attributes = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch (PDOException $ex) {
            //there was some kind of database error
            throw FrameEx($ex->getMessage());
        }
    }

    /**
     * Commit this record to the db.
     */
    public function save() {
        //create the SQL string
        $sql = "INSERT INTO `".$this->schema."` SET ";
        $updateSql = " ON DUPLICATE KEY UPDATE ";

        foreach($this->attributes as $key => $value) {
            $fields .= " `{$key}` = :".$key.",";
        }

        $fields = substr($fields,0 , -1);
        $sql = $sql.$fields.$updateSql.$fields; //combine the sql parts

        try {
            $stmt = DB::dbh()->prepare($sql);

            foreach($this->attributes as $key => $value) {
                $stmt->bindValue(':'.$key, $value);
            }

            $stmt->execute();
        }
        catch (PDOException $ex) {
            throw new FrameEx($ex->getMessage());
        }
    }

    /**
     * Delete the record from the database
     */
    public function delete() {
        if ($this->schema == "" || $this->id == "") {
            throw new FrameEx("You cannot delete a record that isn't initialised");
        }

        try {
            $stmt = DB::dbh()->prepare("DELETE FROM `".$this->schema."` WHERE id = :id");
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
        $xml = "\n<record schema='{$this->schema}' id='{$this->attributes['id']}'>";

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