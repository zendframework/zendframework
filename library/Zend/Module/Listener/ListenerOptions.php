<?php

namespace Zend\Module\Listener;

use Zend\Stdlib\Options;

class ListenerOptions extends Options
{
    /**
     * @var bool
     */
    protected $configCacheEnabled = false;

    /**
     * @var string
     */
    protected $configCacheKey;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var string
     */
    protected $applicationEnvironment;

    /**
     * Check if the config cache is enabled
     *
     * @return bool
     */
    public function getConfigCacheEnabled()
    {
        return $this->configCacheEnabled;
    }
 
    /**
     * Set if the config cache should be enabled or not
     *
     * @param bool $enabled
     * @return ManagerOptions
     */
    public function setConfigCacheEnabled($enabled)
    {
        $this->configCacheEnabled = (bool) $enabled;
        return $this;
    }

    /**
     * Get key used to create the cache file name
     *
     * @return string
     */
    public function getConfigCacheKey() 
    {
        if ($this->configCacheKey !== null) {
            return $this->configCacheKey;
        }
        return $this->getApplicationEnvironment();
    }

    /**
     * Set key used to create the cache file name
     *
     * @param string $configCacheKey the value to be set
     * @return ManagerOptions
     */
    public function setConfigCacheKey($configCacheKey) 
    {
        $this->configCacheKey = $configCacheKey;
        return $this;
    }

    /**
     * Get the path to the config cache 
     * 
     * Should this be an option, or should the dir option include the 
     * filename, or should it simply remain hard-coded? Thoughts?
     *
     * @return string
     */
    public function getConfigCacheFile()
    {
        return $this->getCacheDir() . '/module-config-cache.'.$this->getConfigCacheKey().'.php';
    }

    /**
     * Get the path where cache file(s) are stored
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }
 
    /**
     * Set the path where cache files can be stored
     *
     * @param string $cacheDir the value to be set
     * @return ManagerOptions
     */
    public function setCacheDir($cacheDir)
    {
        if (null === $cacheDir) {
            $this->cacheDir = $cacheDir;
        } else {
            $this->cacheDir = static::normalizePath($cacheDir);
        }
        return $this;
    }

    /**
     * Get the application environment being used
     *
     * @return string
     */
    public function getApplicationEnvironment()
    {
        return $this->applicationEnvironment ?: 'production';
    }
 
    /**
     * Set the application environment to use
     *
     * @param string $applicationEnvironment the value to be set
     */
    public function setApplicationEnvironment($applicationEnvironment)
    {
        $this->applicationEnvironment = $applicationEnvironment;
        return $this;
    }

    /**
     * Normalize a path for insertion in the stack
     * 
     * @param  string $path 
     * @return string
     */
    public static function normalizePath($path)
    {
        $path = rtrim($path, '/');
        $path = rtrim($path, '\\');
        return $path;
    }
}
