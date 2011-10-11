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
    protected $manifestDir = NULL;

    /**
     * @var bool
     */
    protected $enableDependencycheck = false;

    /**
     * @var bool
     */
    protected $enableSelfInstallation = false;
    
    /**
     * array of modules that have been whitelisted to allow self installation
     * 
     * @var array
     */
    protected $selfInstallWhiteList = array();

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
     * Set configCacheEnabled.
     *
     * @param bool $enabled the value to be set
     * @return ManagerConfig
     */
    public function setEnableConfigCache($enabled)
    {
        $this->enableConfigCache = (bool) $enabled;
        return $this;
    }

    /**
     * Get cacheDir.
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }
 
    /**
     * Set cacheDir.
     *
     * @param string $cacheDir the value to be set
     * @return ManagerConfig
     */
    public function setCacheDir($cacheDir)
    {
        if (null === $cacheDir) {
            $this->cacheDir = $cacheDir;
        } else {
            $this->cacheDir = rtrim(rtrim($cacheDir, '/'), '\\');
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
     * @return ManagerConfig
     */
    public function setManifestDir($manifestDir)
    {
        if (null === $manifestDir) {
            $this->manifestDir = $manifestDir;
        } else {
            $this->manifestDir = rtrim(rtrim($manifestDir, '/'), '\\');
        }
        return $this;
    }

    /**
     * getCacheFilePath 
     * 
     * Should this be an option, or should the dir option include the 
     * filename, or should it simply remain hard-coded? Thoughts?
     *
     * @return string
     */
    public function getCacheFilePath()
    {
        return $this->getCacheDir() . '/module-config-cache.'.$this->getApplicationEnv().'.php';
    }

    /**
     * set if dependency checking should be enabled
     * 
     * @param bool $bool
     * @return Manager
     */
    public function setEnableDependencyCheck($bool)
    {
        $this->enableDependencycheck = (bool) $bool;
        return $this;
    }
    
    /**
     * get if dependency checking is enabled
     * 
     * @return bool
     */
    public function getEnableDependencyCheck()
    {
        return $this->enableDependencycheck;
    }
    
    /**
     * set if self installation is enabled
     * 
     * @param bool $bool
     * @return Manager
     */
    public function setEnableSelfInstallation($bool)
    {
        $this->enableSelfInstallation = (bool) $bool;
        return $this;
    }
    
    /**
     * gets if self installation is enabled
     * 
     * @return bool
     */
    public function getSelfInstallWhitelist()
    {
        return $this->selfInstallWhitelist;
    }

    /**
     * set if self installation is enabled
     * 
     * @param bool $bool
     * @return Manager
     */
    public function setSelfInstallWhitelist($list)
    {
        $this->selfInstallWhitelist = $list;
        return $this;
    }
    
    /**
     * gets if self installation is enabled
     * 
     * @return bool
     */
    public function getEnableSelfInstallation()
    {
        return $this->enableSelfInstallation;
    }
    
    public function getApplicationEnv()
    {
        return defined('APPLICATION_ENV') ? APPLICATION_ENV : NULL;
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
