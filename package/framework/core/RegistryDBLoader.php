<?php

/**
 * This class provides an interface to load registry settings from the database
 */
class RegistryDBLoader {

    /**
     * load registry settings from db table
     * @param mixed $table
     */
    public function __construct($table) {
        $records = TableGateway::loadAll($table);

        foreach ($records as $record) {
            Registry::set($record->key, $record->value);
        }
    }
}