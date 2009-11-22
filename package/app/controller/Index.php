<?php

class Index {

    /**
     * This class provides the implementation for the home request as specified
     * in the app/init.php file
     *
     * @param Request $r encapsulation of the request variables
     */
    public function run(Request $r) {
        $log = LoggerManager::getLogger("home");
        $log->debug("Entering Index->run() for handling of home event");

        /*Example PDO/Active Record database interaction
          needs this sql table:

        CREATE TABLE test_table (
            `id` INT(11) UNSIGNED auto_increment,
            `name` VARCHAR(255),
            PRIMARY KEY(id)
        );

        INSERT INTO test_table VALUES (1,"Linus");
        INSERT INTO test_table VALUES (2,"Jason");
        INSERT INTO test_table VALUES (3,"Dan");
        INSERT INTO test_table VALUES (4,"John");
        INSERT INTO test_table VALUES (5,"Jon");
        INSERT INTO test_table VALUES (6,"Jez");


        //Example 1: load one record
        $record = TableGateway::load("test_table", 1);
        Page::add($record);

        //Example 2: load entire table
        $records = TableGateway::loadAll("test_table");
        Page::add($records);

        //Example 3: custom load condition
        $stmt = DB::dbh()->prepare("SELECT * FROM test_table WHERE id < :maxId");
        $stmt->bindValue(":maxId", 10);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //the static create method instantiates a record from a associative array
            //if you were explicitly mapping fields from the array in your overridden
            //create function you wouldnt need PDO::FETCH_ASSOC
            $record = Record::create($row, "test_table");
            Page::add($record);
        }

        //Example 4: modifying a record
        $record = Record::load("test_table", 1);
        $record->name = "bigface";
        $record->save();

        //Example 5: deleting a record
        $record = Record::load("test_table", 1);
        $record->delete();

        //Example 6: creating a record
        $record = Record::create(array("id" => 1, "name" => "Linus"), "test_table");
        $record->save();

        //or without an id (you could pass just name in the constructor array
        $record = new Record("test_table");
        $record->name = "Some other Person";
        $record->save();
        //record->id will now be set
        $record->delete();

        //Example 7: adding a custom field into the xml
        //this is usually used to format date fields
        $record = Record::load("test_table", 1);
        $record->fieldThatIsNotInDB = "Ha, this isn't in the database";
        Page::add($record);

        //Example 8: using the TableGateway
        $records = TableGateway::loadMatching("test_table",  Restriction::like("name", "Li%"));
        Page::add($records);

        //Example 9: using the TableGateway with Criteria
        $criteria = new Criteria( Restriction::is("name", "Linus") );
        $criteria->addOr( Restriction::is("name", "John") );
        $criteria->addAnd( Restriction::isNotNull("name") );
        $records = TableGateway::loadMatching("test_table",  $criteria);
        Page::add($records);

        //Example 10: using the TableGateway with Pagination
        $records = TableGateway::loadMatching("test_table",  Restriction::like("name", "Li%"), 0, 3);
        Page::add($records);
        */
        
        Page::$xsl = ROOT."app/view/index.xsl";
    }


}
?>
