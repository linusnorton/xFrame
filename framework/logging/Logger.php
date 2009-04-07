<?php
/**
 * @author Linus Norton <linus.norton@assertis.co.uk>
 *
 * This class logs to a file
 */
class Logger {
    private $key;
    private $file;
    private $maxLogSize;

    public function __construct($key) {
        $this->key = $key;
        $this->file = Registry::get("LOGGER_FILE") or "/var/log/httpd/logger_log";
        $this->maxLogSize = Registry::get("MAX_LOG_FILESIZE") or 1048576;
    }

    public function debug($message) {
        $this->log("debug", $message);
    }

    public function info($message) {
        $this->log("info", $message);
    }

    public function warn($message) {
        $this->log("warn", $message);
    }

    public function error($message) {
        $this->log("error", $message);
    }

    public function fatal($message) {
        $this->log("fetal", $message);
    }

    private function log($level, $message) {
        // open file
        $fd = fopen($this->file, "a");

        if ($fd === false) {
            throw new FrameEx("Could not open: {$this->file} for logging");
        }

        // append date/time to message
        $log = "[" . date("Y/m/d h:i:s", mktime()) . "] " . $message;

        // write string
        fwrite($fd, $log . "\n");

        // close file
        fclose($fd);
    }

}


?>