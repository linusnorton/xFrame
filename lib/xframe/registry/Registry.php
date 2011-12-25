<?php

namespace xframe\registry;
use \Exception;

/**
 * Implementation of the Registry pattern. Used to store configuration.
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package registry
 */
class Registry {
    private $settings;

    /**
     * @param array $settings
     */
    public function __construct(array $settings = array()) {
        $this->settings = $settings;
    }

    /**
     * This method will try to locate and parse the ini file. First it just
     * looks for the filename, if that does not exist it will prepend the
     * context and try again.
     *
     * @param string $filename
     * @param string $context
     */
    public function load($filename, $context = null) {
        if (is_file($filename)) {
            $file = $filename;
        }
        else if (is_file($context.DIRECTORY_SEPARATOR.$filename)) {
            $file = $context.DIRECTORY_SEPARATOR.$filename;
        }
        else {
            throw new Exception("Couldn't find: ".$filename." in: ".$context);
        }

        $this->settings = parse_ini_file($file);

        if ($this->settings === false) {
            throw new Exception("Could not process ini file: ".$file);
        }
    }

    /**
     * Get the value from the registry
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return isset($this->settings[$key]) ? $this->settings[$key] : null;
    }

    /**
     * Set the value in the registry
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->settings[$key] = $value;
    }
}
