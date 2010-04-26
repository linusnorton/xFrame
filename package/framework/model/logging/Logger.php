<?php

/**
 * @author Linus Norton <linus.norton@assertis.co.uk>
 *
 * This class logs to a database record
 *
 * SQL Required for logger is in install/logger.sql
 *
 */
class Logger {
    const DEBUG = 6, INFO = 5, WARN = 4, AUDIT = 3, ERROR = 2, FATAL = 1, OFF = 0;
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
        if ($this->logLevel >= self::WARN){
            $this->log("warn", $message);
        }
    }

    public function audit($message) {
        if ($this->logLevel >= self::AUDIT){
            $this->log("audit", $message);
        }
    }

    public function error($message) {
        if ($this->logLevel >= self::ERROR){
            $this->log("error", $message);
        }
    }

    public function fatal($message) {
        if ($this->logLevel >= self::FATAL){
            $this->log("fetal", $message);
        }
    }

    private function log($level, $message) {
        $log = new Record($this->tableName);

        //check ip from share internet
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //to check ip is pass from proxy
        else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $log->ip = $ip;
        $log->key = $this->key;
        $log->level = $level;
        $log->message = $message;
        $log->date_time = date("Y-m-d H:i:s");
        $log->session_id = session_id();
        $log->execution_time = number_format(microtime(true) - Controller::getExecutionTime(), 5);

        $log->save();
    }

}
