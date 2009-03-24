<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @version 0.1
 * @package database
 *
 * This is the implementation of an Active Record pattern, it provides the ability to load, save, delete
 * and display as xml to any record.
 */
class Record implements XML {
    private $attributes = array();
    private $schema;

    public function __construct($schema, $id) {
        if (empty($schema) || empty($id) ) {
            return;
        }
        $this->schema = $schema;

        try {
            $stmt = DB::dbh()->prepare('SELECT * FROM `'.addslashes($schema).'` WHERE `id` = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();


            if ($stmt->rowCount() === 0) {
                throw new MissingRecord("Could not find a {$schema} where id = {$id}");
            }
            if ($stmt->rowCount() > 1) {
                throw new MultipleRecord("Multiple records were matched");
            }

            $this->attributes = $stmt->fetch(PDO::FETCH_ASSOC);

        }
        catch (PDOException $ex) {
            throw FrameEx($ex->getMessage());
        }
    }

    public function setup($schema, array $attributes) {
        $this->schema = $schema;
        $this->attributes = $attributes;
    }

    public function save() {
        $sql = "INSERT INTO `".addslashes($this->schema)."` SET ";
        $updateSql = " ON DUPLICATE KEY UPDATE ";

        foreach($this->attributes as $key => $value) {
            $fields .= " `{$key}` = :".$key.",";
        }


        $fields = substr($fields,0 , -1);
        $sql = $sql.$fields.$updateSql.$fields; //oh yeah

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

    public function delete() {
        if ($this->schema == "" || !ctype_digit("{$this->id}")) {
            throw new FrameEx("You cannot delete a record that isn't initialised");
        }

        try {
            $stmt = DB::dbh()->prepare("DELETE FROM `".addslashes($this->schema)."` WHERE id = :id");
            $stmt->bindValue(':id', $this->attributes["id"]);
            $stmt->execute();
        }
        catch (PDOException $ex) {
            throw new FrameEx($ex->getMessage());
        }
    }

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

    public function __get($key) {
        return $this->attributes[$key];
    }

    public function __set($key, $value) {
        return $this->attributes[$key] = $value;
    }

}