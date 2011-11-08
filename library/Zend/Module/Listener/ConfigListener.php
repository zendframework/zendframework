<?php

namespace Zend\Module\Listener;

use Traversable,
    Zend\Config\Config,
    Zend\Stdlib\IteratorToArray,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

class ConfigListener extends AbstractListener
{
    /**
     * @var array
     */
    protected $mergedConfig = array();

    /**
     * @var Config
     */
    protected $mergedConfigObject;

    /**
     * @var bool
     */
    protected $skipConfig = false;
    
    public function __invoke($e)
    {
        $module = $e->getParam('module');
        $this->mergeModuleConfig($module);
        //if ($this->hasCachedConfig()) {
        //    $this->skipConfig = true;
        //    $this->setMergedConfig($this->getCachedConfig());
        //}
    }

    /**
     * getMergedConfig
     * 
     * @param array $returnConfigAsObject 
     * @return mixed
     */
    public function getMergedConfig($returnConfigAsObject = true)
    {
        if ($returnConfigAsObject === true) {
            if ($this->mergedConfigObject === null) {
                $this->mergedConfigObject = new Config($this->mergedConfig);
            }
            return $this->mergedConfigObject;
        } else {
            return $this->mergedConfig;
        }
    }

    /**
     * setMergedConfig 
     * 
     * @param array $config 
     * @return Manager
     */
    protected function setMergedConfig($config)
    {
        $this->mergedConfig = $config;
        return $this;
    }

    /**
     * mergeModuleConfig 
     * 
     * @param mixed $module 
     * @return Manager
     */
    protected function mergeModuleConfig($module)
    {
        if ((false === $this->skipConfig)
            && (is_callable(array($module, 'getConfig')))
        ) {
            $config = $module->getConfig($this->getOptions()->getApplicationEnv());
            if ($config instanceof Traversable) {
                $config = IteratorToArray::convert($config);
            }
            if (!is_array($config)) {
                throw new \InvalidArgumentException(
                    sprintf('getConfig() method of %s must be an array, '
                    . 'implement the \Traversable interface, or be an '
                    . 'instance of Zend\Config\Config', get_class($module))
                );
            }
            $this->mergedConfig = array_replace_recursive($this->mergedConfig, $config);
        }
        return $this;
    }
    
    protected function hasCachedConfig()
    {
        if (($this->getOptions()->getEnableConfigCache())
            && (file_exists($this->getOptions()->getCacheFilePath()))
        ) {
            return true;
        }
        return false;
    }

    protected function getCachedConfig()
    {
        return include $this->getOptions()->getCacheFilePath();
    }

    protected function updateCache()
    {
        if (($this->getOptions()->getEnableConfigCache())
            && (false === $this->skipConfig)
        ) {
            $this->saveConfigCache($this->getMergedConfig(false));
        }
        return $this;
    }

    protected function saveConfigCache($config)
    {
        $content = "<?php\nreturn " . var_export($config, 1) . ';';
        file_put_contents($this->getOptions()->getCacheFilePath(), $content);
        return $this;
    }
}
