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
    protected $configCacheKey = null;

    /**
     * @var string
     */
    protected $cacheDir = null;

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
        return $this->getApplicationEnv();
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

    public function getApplicationEnv()
    {
        return defined('APPLICATION_ENV') ? APPLICATION_ENV : null;
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
