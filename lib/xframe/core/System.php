<?php

namespace xframe\core;
use xframe\exception\ErrorHandler;
use xframe\exception\Logger;
use xframe\exception\Mailer;
use xframe\exception\ExceptionHandler;
use xframe\exception\ExceptionOutputter;
use xframe\request\FrontController;
use xframe\registry\Registry;
use \Memcache;

/**
 * The System class provides access to the core resources, this includes the
 * FrontController and Registry.
 *
 * It also boots the application by registering the error and exception
 * handling methods.
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package core
 */
class System {

    /**
     * @var string root directory
     */
    private $root;

    /**
     * @var string path to configuration ini
     */
    private $configFilename;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    /**
     * @var ExceptionHandler
     */
    private $exceptionHandler;

    /**
     * @var FrontController
     */
    private $frontController;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var PDO
     */
    private $database;

    /**
     * @var Cache
     */
    private $cache;
    
    /**
     * If the errorHandler, exceptionHandler, frontController or registry are
     * passed as null a default object will be created the first time they are
     * accessed.
     *
     * @param string $root
     * @param string $configFilename
     * @param ErrorHandler $errorHandler
     * @param ExceptionHandler $exceptionHandler
     * @param FrontController $frontController
     * @param Registry $registry
     * @param PDO $database
     */
    public function __construct($root,
                                $configFilename = "config/dev.ini",
                                ErrorHandler $errorHandler = null,
                                ExceptionHandler $exceptionHandler = null,
                                FrontController $frontController = null,
                                Registry $registry = null,
                                PDO $database = null,
                                Memcache $cache = null) {

        $this->root = $root;
        $this->tmp = $root."tmp".DIRECTORY_SEPARATOR;
        $this->configFilename = $configFilename;        
        $this->registry = $registry;
        $this->errorHandler = $errorHandler;
        $this->exceptionHandler = $exceptionHandler;
        $this->frontController = $frontController;
        $this->database = $database;
        $this->cache = $cache;
    }

    /**
     * Register the error and exception handler, load the registry
     */
    public function boot() {
        $this->getRegistry()->load($this->configFilename, $this->root);
        $this->getErrorHandler()->register();
        $this->getExceptionHandler()->register();
        $this->getExceptionHandler()->attach(new Logger());
        $this->getExceptionHandler()->attach(new ExceptionOutputter());

        $recipients = $this->registry->get("ADMIN");
        $this->getExceptionHandler()->attach(new Mailer($recipients));

        if ($this->cache === null && $this->registry->get("CACHE_ENABLED")) {
            $this->cache = new Memache();
            $this->cache->addServer(
                $this->registry->get("MEMCACHE_HOST"),
                $this->registry->get("MEMCACHE_PORT")
            );
        }

        if (!headers_sent()) {
            session_start();
        }
    }

    /**
     * @return ErrorHandler
     */
    public function getErrorHandler() {
        if ($this->errorHandler === null) {
            $this->errorHandler = new ErrorHandler();
        }

        return $this->errorHandler;
    }

    /**
     * @return ExceptionHandler
     */
    public function getExceptionHandler() {
        if ($this->exceptionHandler === null) {
            $this->exceptionHandler = new ExceptionHandler();
        }

        return $this->exceptionHandler;
    }

    /**
     * @return FrontController
     */
    public function getFrontController() {
        if ($this->frontController === null) {
            $this->frontController = new FrontController($this);
        }

        return $this->frontController;
    }

    /**
     * @return Registry
     */
    public function getRegistry() {
        if ($this->registry === null) {
            $this->registry = new Registry();
        }

        return $this->registry;
    }

    public function getDatabase() {
        if ($this->database === null) {
            $db = $this->registry->get("DATABASE_ENGINE");
            $host = $this->registry->get("DATABASE_HOST");
            $name = $this->registry->get("DATABASE_NAME");
            $user = $this->registry->get("DATABASE_USERNAME");
            $pass = $this->registry->get("DATABASE_PASSWORD");

            $this->database = new PDO(
                $db.":host=".$host.";dbname=".$name,
                $user,
                $pass
            );
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $this->database;
    }

    /**
     * Return the path to the project root
     * @return string
     */
    public function getRoot() {
        return $this->root;
    }

    /**
     * Return the path to the temporary directory
     * @return string
     */
    public function getTmp() {
        return $this->tmp;
    }

    /**
     * Provide public read-only access to private variables via the public
     * getters. This allows users to easily access $system->database but also
     * ensures that they are setup correctly by using the getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        $method = "get".ucfirst($name);
        return $this->$method();
    }
}
