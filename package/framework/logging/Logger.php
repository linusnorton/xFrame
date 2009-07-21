<?php

/**
 * @author Linus Norton <linus.norton@assertis.co.uk>
 *
 * This class logs to a database record
 *
 * SQL Required for logger:


CREATE TABLE log (
    `id` INT(11) UNSIGNED auto_increment,
    `ip` VARCHAR(255),
    `key` VARCHAR(255),
    `level` VARCHAR(5),
    `message` TEXT,
    `date_time` DATETIME,
    `session_id` VARCHAR(255),
    `execution_time` VARCHAR(11),
    PRIMARY KEY(id)
);

 *
 */
class Logger {
    const OFF = 0, DEBUG = 5, INFO = 4, WARN = 3, ERROR = 2, FATAL = 1;
    private $key;
    private $tableName;
    private $logLevel;

    public function __construct($key) {
        $this->key = $key;
        $this->tableName = (Registry::get("LOG_TABLE")) ? Registry::get("LOG_TABLE") : "log";
        $this->logLevel = (Registry::get("LOG_LEVEL")) ? Registry::get("LOG_LEVEL") : self::OFF;
    }

    /**
     * Override the level of logging set in the Registry
     */
    public function setLogLevel($level) {
        $this->logLevel = $level;
    }

    /**
     * Override the level of logging set in the Registry
     */
    public function setLogTable($tableName) {
        $this->tableName = $tableName;
    }


    /**
     * Log a debug message (dependant on the level of logging)
     */
    public function debug($message) {
        if ($this->logLevel >= self::DEBUG){ 
            $this->log("debug", $message);
        }
    }

    public function info($message) {
        if ($this->logLevel >= self::INFO){ 
            $this->log("info", $message);
        }
    }

    public function warn($message) {
        if ($this->logLevel >= self::DEBUG){ 
            $this->log("warn", $message);
        }
    }

    public function error($message) {
        if ($this->logLevel >= self::DEBUG){ 
            $this->log("error", $message);
        }
    }

    public function fatal($message) {
        if ($this->logLevel >= self::DEBUG){ 
            $this->log("fetal", $message);
        }
    }

    private function log($level, $message) {
        $log = new Record($this->tableName);
        $log->ip = $_SERVER['REMOTE_ADDR'];;
        $log->key = $this->key;
        $log->level = $level;
        $log->message = $message;
        $log->date_time = date("Y-m-d H:i:s");
        $log->session_id = session_id();
        $log->execution_time = number_format(microtime(true) - Page::getExecutionTime(), 5);

        $log->save();
    }

}


?>