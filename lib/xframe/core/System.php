<?php

namespace xframe\core;
use \xframe\exception\ErrorHandler;
use \xframe\exception\Logger;
use \xframe\exception\ExceptionHandler;
use \xframe\exception\ExceptionOutputter;
use \xframe\request\FrontController;
use \xframe\registry\Registry;
use \Memcache;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Configuration;
use \PDO;
use \xframe\request\Session;

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
class System extends DependencyInjectionContainer {
    
    /**
     * @param string $root
     * @param string $config
     */
    public function __construct($root, $config) {
        parent::__construct(array(
            'root' => $root,
            'tmp' => $root.'tmp'.DIRECTORY_SEPARATOR,
            'configFilename' => 'config'.DIRECTORY_SEPARATOR.$config.'.ini',
        ));        
        
        $this->setDefaultDatabase();
        $this->setDefaultEm();
        $this->setDefaultErrorHandler();
        $this->setDefaultExceptionHandler();
        $this->setDefaultFrontController();
        $this->setDefaultRegistry();
        $this->setDefaultPluginContainer();
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

        if ($this->registry->get('CACHE_ENABLED')) {
            $this->getDefaultCache();
        }               
    }

    /**
     * Set the lambda for errorHandler
     */
    private function setDefaultErrorHandler() {
        $this->add('errorHandler', function ($dic) {
            return new ErrorHandler();
        });
    }

    /**
     * Set the default ExceptionHandler
     */
    private function setDefaultExceptionHandler() {
        $this->add('exceptionHandler', function ($dic) {
            return new ExceptionHandler();
        });
    }

    /**
     * Set the lambda for frontController
     */
    private function setDefaultFrontController() {
        $this->add('frontController', function ($dic) {
            return new FrontController($dic);
        });
    }

    /**
     * Set the lambda for registry
     */
    private function setDefaultRegistry() {
        $this->add('registry', function ($dic) {
            return new Registry();
        });
    }

    /**
     * Set the lambda for database
     */
    private function setDefaultDatabase() {
        $this->add('database', function ($dic) {
            $db = $dic->registry->get('DATABASE_ENGINE');
            $host = $dic->registry->get('DATABASE_HOST');
            $port = $dic->registry->get('DATABASE_PORT');
            $name = $dic->registry->get('DATABASE_NAME');
            $user = $dic->registry->get('DATABASE_USERNAME');
            $pass = $dic->registry->get('DATABASE_PASSWORD');
            
            $database = new PDO(
                $db.':host='.$host.';dbname='.$name.($port?';port='.$port:''),
                $user,
                $pass
            );
            $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $database;
        });
    }

    /**
     * Sets up the project plugins with access to the dic
     */
    private function setDefaultPluginContainer() {
        $this->add('plugin', function($dic) {
            $pluginContainer = new DependencyInjectionContainer();
            foreach ($dic->registry->get("PLUGIN") as $key => $plugin) {
                $pluginContainer->add($key,function($pDic) use ($dic, $plugin) {
                    $p = new $plugin($dic);
                    return $p->init();
                });
            }

            return $pluginContainer;
        });
    }

    /**
     * Set the lambda function for the memcache
     */
    private function getDefaultCache() {
        $this->add('cache', function ($dic) {
            $cache = new Memcache();
            $cache->addServer(
                $dic->registry->get('MEMCACHE_HOST'),
                $dic->registry->get('MEMCACHE_PORT')
            );

            return $cache;
        });
    }

    /**
     * Set up doctrine
     */
    private function setDefaultEm() {
        $this->add('em', function ($dic) {
            if (extension_loaded('apc')) {
                $cache = new \Doctrine\Common\Cache\ApcCache();
            }
            else if ($dic->registry->get('CACHE_ENABLED')) {
                $cache = new \Doctrine\Common\Cache\MemcacheCache();
                $cache->setMemcache($dic->cache);
            }
            else {
                $cache = new \Doctrine\Common\Cache\ArrayCache();
            }

            $config = new Configuration();
            $config->setMetadataCacheImpl($cache);
            $driver = $config->newDefaultAnnotationDriver(
                array(
                    $dic->root.'src',
                    $dic->root.'lib'
                )
            );
            $config->setMetadataDriverImpl($driver);
            $config->setQueryCacheImpl($cache);
            $config->setProxyDir($dic->tmp.DIRECTORY_SEPARATOR);
            $config->setProxyNamespace('Project\Proxies');

            $rebuild = $dic->registry->get('AUTO_REBUILD_PROXIES');
            $config->setAutoGenerateProxyClasses($rebuild);

            $connectionOptions = array('pdo' => $dic->getDatabase());
            return EntityManager::create($connectionOptions, $config);
        });
    }
}
