<?php
namespace xframe\plugin;

use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Configuration;

class DoctrineEntityManager extends Plugin {

    public function init() {
        if (extension_loaded('apc')) {
            $cache = new \Doctrine\Common\Cache\ApcCache();
        }
        else if ($this->dic->registry->get('CACHE_ENABLED')) {
            $cache = new \Doctrine\Common\Cache\MemcacheCache();
            $cache->setMemcache($this->dic->cache);
        }
        else {
            $cache = new \Doctrine\Common\Cache\ArrayCache();
        }

        $config = new Configuration();
        $config->setMetadataCacheImpl($cache);
        $driver = $config->newDefaultAnnotationDriver(
            array(
                $this->dic->root.'src',
                $this->dic->root.'lib'
            )
        );
        $config->setMetadataDriverImpl($driver);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir($this->dic->tmp.DIRECTORY_SEPARATOR);
        $config->setProxyNamespace('Project\Proxies');

        $rebuild = $this->dic->registry->get('AUTO_REBUILD_PROXIES');
        $config->setAutoGenerateProxyClasses($rebuild);

        $connectionOptions = array('pdo' => $this->dic->getDatabase());
        return EntityManager::create($connectionOptions, $config);
    }

}

