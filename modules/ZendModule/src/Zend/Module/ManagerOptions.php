<?php

namespace Zend\Module;

// use Zend\StdLib\Options;

class ManagerOptions
    // extends Options
{
    /**
     * @var bool
     */
    protected $cacheConfig = false;

    /**
     * @var string
     */
    protected $cacheDir = NULL;
 
    /**
     * Get cacheConfig.
     *
     * @return bool
     */
    public function getCacheConfig()
    {
        return $this->cacheConfig;
    }
 
    /**
     * Set cacheConfig.
     *
     * @param bool $cacheConfig the value to be set
     * @return ManagerConfig
     */
    public function setCacheConfig($cacheConfig)
    {
        if (!is_bool($cacheConfig)) {
            throw new \InvalidArgumentException(
                'Parameter to \\Zend\\Module\\ManagerOption\'s '
                . 'setCacheConfig method must be boolean.'
            );
        }
        $this->cacheConfig = $cacheConfig;
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
        try {
            $this->{$setter}(null);
        } catch(\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(
                'The class property $' . $key . ' cannot be unset as'
                . ' NULL is an invalid value for it: ' . $e->getMessage()
            );
        }
    }
 
}
