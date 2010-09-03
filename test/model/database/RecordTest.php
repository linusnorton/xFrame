<?php

require_once 'PHPUnit/Framework.php';


class RecordTest extends PHPUnit_Framework_TestCase {

    public function testConstructAndSettersAndGetters() {
        $record = new Record();
        $record->field = "test";

        $this->assertEquals("test", $record->field);
    }

    /**
     * @expectedException FrameEx
     */
    public function testDelete() {
        $record = self::getTestRecord();

        $record->save();
        $record->delete();

        $loaded = Record::load($record->getTableName(), $record->getId());
    }

    public function testSaveAndLoad() {
        $this->saveAndLoad(self::getTestRecord());
    }

    public function saveAndLoad(Record $record) {
        $record->save();
        $loadedRecord = Record::load($record->getTableName(),
                                     $record->getId(),
                                     get_class($record));

        foreach ($record->getAttributes() as $key => $value) {
            $this->assertEquals($value, $loadedRecord->$key);
        }

        $record->delete();
    }

    /**
     *
     * @return Record
     */
    public static function getTestRecord() {
        $log = new Record("log");
        $log->ip = "127.0.0.1";
        $log->key = "key";
        $log->level = "debug";
        $log->message = "testing";
        $log->date_time = "2010-01-15 18:56:00";
        $log->session_id = null;
        $log->execution_time = null;
        return $log;
    }
}