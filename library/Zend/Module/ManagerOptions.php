<?php

namespace Zend\Module;

// use Zend\StdLib\Options;

class ManagerOptions
    // extends Options
{
    /**
     * @var bool
     */
    protected $enableConfigCache = false;

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
    protected $autoInstallWhiteList = array();

    /**
     * Check if the config cache is enabled
     *
     * @return bool
     */
    public function getEnableConfigCache()
    {
        return $this->enableConfigCache;
    }
 
    /**
     * Set if the config cache should be enabled or not
     *
     * @param bool $enabled
     * @return ManagerOptions
     */
    public function setEnableConfigCache($enabled)
    {
        $this->enableConfigCache = (bool) $enabled;
        return $this;
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
     * Get the path to the config cache 
     * 
     * Should this be an option, or should the dir option include the 
     * filename, or should it simply remain hard-coded? Thoughts?
     *
     * @return string
     */
    public function getCacheFilePath()
    {
        return $this->getCacheDir() . '/module-config-cache.'.$this->getConfigCacheKey().'.php';
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

    /**
     * Begin Pádraic Brady's options methods.
     * (To be replaced with Zend\StdLib\Options
     */

    public function __construct($config = null)
    {
        if (!is_null($config)) {
            if (is_array($config) || $config instanceof \Traversable) {
                $this->processArray($config);
            } else {
                throw new \InvalidArgumentException(
                    'Parameter to \\Zend\\Stdlib\\Configuration\'s '
                    . 'constructor must be an array or implement the '
                    . '\\Traversable interface'
                );
            }
        }
    }

    protected function processArray($config)
    {
        foreach ($config as $key => $value) {
            $setter = $this->assembleSetterNameFromConfigKey($key);
            $this->{$setter}($value);
        }
    }
    
    protected function assembleSetterNameFromConfigKey($key)
    {
        $parts = explode('_', $key);
        $parts = array_map('ucfirst', $parts);
        $setter = 'set' . implode('', $parts);
        if (!method_exists($this, $setter)) {
            throw new \BadMethodCallException(
                'The configuration key "' . $key . '" does not '
                . 'have a matching ' . $setter . ' setter method '
                . 'which must be defined'
            );
        }
        return $setter;
    }
   
    protected function assembleGetterNameFromConfigKey($key)
    {
        $parts = explode('_', $key);
        $parts = array_map('ucfirst', $parts);
        $getter = 'get' . implode('', $parts);
        if (!method_exists($this, $getter)) {
            throw new \BadMethodCallException(
                'The configuration key "' . $key . '" does not '
                . 'have a matching ' . $getter . ' getter method '
                . 'which must be defined'
            );
        }
        return $getter;
    }
   
    public function __set($key, $value)
    {
        $setter = $this->assembleSetterNameFromConfigKey($key);
        $this->{$setter}($value);
    }
   
    public function __get($key)
    {
        $getter = $this->assembleGetterNameFromConfigKey($key);
        return $this->{$getter}();
    }
   
    public function __isset($key)
    {
        $getter = $this->assembleGetterNameFromConfigKey($key);
        return !is_null($this->{$getter}());
    }
   
    public function __unset($key)
    {
        $setter = $this->assembleSetterNameFromConfigKey($key);
        $this->{$setter}(null);
    }
}
