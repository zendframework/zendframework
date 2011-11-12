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
    protected $cacheDir = NULL;

    /**
     * @var string
     */
    protected $configCacheKey = NULL;
    
    /**
     * @var string
     */
    protected $manifestDir = NULL;

    /**
     * @var bool
     */
    protected $enableDependencycheck = false;

    /**
     * @var bool
     */
    protected $enableAutoInstallation = false;
    
    /**
     * array of modules that have been whitelisted to allow auto installation
     * 
     * @var array
     */
    protected $autoInstallWhitelist = array();

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

    /**
     * Get manifestDir.
     *
     * @return string
     */
    public function getManifestDir()
    {
        return $this->manifestDir;
    }
 
    /**
     * Set manifestDir.
     *
     * @param string $manifestDir the value to be set
     * @return ManagerOptions
     */
    public function setManifestDir($manifestDir)
    {
        if (null === $manifestDir) {
            $this->manifestDir = $manifestDir;
        } else {
            $this->manifestDir = static::normalizePath($manifestDir);
        }
        return $this;
    }

    /**
     * Check if dependency checking is enabled
     * 
     * @return bool
     */
    public function getEnableDependencyCheck()
    {
        return $this->enableDependencycheck;
    }

    /**
     * Set if dependency checking is enabled
     * 
     * @param bool $enabled
     * @return Manager
     */
    public function setEnableDependencyCheck($enabled)
    {
        $this->enableDependencycheck = (bool) $enabled;
        return $this;
    }
    
    /**
     * Check if auto installation is enabled
     * 
     * @return bool
     */
    public function getEnableAutoInstallation()
    {
        return $this->enableAutoInstallation;
    }

    /**
     * Set if auto installation is enabled application-wide. If this is 
     * disabled, no auto install/upgrades will be ran; even if the modules are 
     * in the whitelist.
     * 
     * @param bool $enabled
     * @return Manager
     */
    public function setEnableAutoInstallation($enabled)
    {
        $this->enableAutoInstallation = (bool) $enabled;
        return $this;
    }
    
    /**
     * Get the array of modules enabled for auto install or upgrade
     * 
     * @return array
     */
    public function getAutoInstallWhitelist()
    {
        return $this->autoInstallWhitelist;
    }

    /**
     * Set auto installation whitelist
     * 
     * @param array $list An array of module names which to allow auto install or upgrade
     * @return Manager
     */
    public function setAutoInstallWhitelist($list)
    {
        $this->autoInstallWhitelist = $list;
        return $this;
    }
    
    public function getApplicationEnv()
    {
        return defined('APPLICATION_ENV') ? APPLICATION_ENV : NULL;
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
