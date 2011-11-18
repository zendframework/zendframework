<?php

namespace Zend\Module\Listener;

use Traversable,
    Zend\Config\Config,
    Zend\Config\Xml as XmlConfig,
    Zend\Config\Ini as IniConfig,
    Zend\Config\Yaml as YamlConfig,
    Zend\Config\Json as JsonConfig,
    Zend\Module\ModuleEvent,
    Zend\Stdlib\IteratorToArray;

class ConfigListener extends AbstractListener implements ConfigMerger
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
    
    /**
     * __construct 
     * 
     * @param ListenerOptions $options 
     * @return void
     */
    public function __construct(ListenerOptions $options = null)
    {
        parent::__construct($options);
        if ($this->hasCachedConfig()) {
            $this->skipConfig = true;
            $this->setMergedConfig($this->getCachedConfig());
        }
    }

    public function __invoke(ModuleEvent $e)
    {
        if (true === $this->skipConfig) {
            return;
        }
        $module = $e->getModule();
        if (is_callable(array($module, 'getConfig'))) {
            $this->mergeModuleConfig($module);
        }
    }

    /**
     * getMergedConfig
     * 
     * @param bool $returnConfigAsObject 
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
     * @return ConfigListener
     */
    public function setMergedConfig(array $config)
    {
        $this->mergedConfig = $config;
        $this->mergedConfigObject = null;
        return $this;
    }

    /**
     * Merge all config files matching a glob 
     * 
     * @param mixed $globPath 
     * @return ConfigListener
     */
    public function mergeGlobDirectory($globPath)
    {
        foreach (glob($globPath, GLOB_BRACE) as $path) {
            $pathInfo = pathinfo($path);
            switch (strtolower($pathInfo['extension'])) {
                case 'php':
                case 'inc':
                    $config = include $path;
                    break;

                case 'xml':
                    $config = new XmlConfig($path);
                    break;

                case 'json':
                    $config = new JsonConfig($path);
                    break;

                case 'ini':
                    $config = new IniConfig($path);
                    break;

                case 'yaml':
                case 'yml':
                    $config = new YamlConfig($path);
                    break;

                default:
                    throw new Exception\RuntimeException(sprintf(
                        'Unable to detect config file type by extension: %s',
                        $path
                    ));
                    break;
            }
            $this->mergeTraversableConfig($config);
        }
    }

    /**
     * mergeModuleConfig 
     * 
     * @param mixed $module 
     * @return ConfigListener
     */
    protected function mergeModuleConfig($module)
    {
        if ((false === $this->skipConfig)
            && (is_callable(array($module, 'getConfig')))
        ) {
            $config = $module->getConfig($this->getOptions()->getApplicationEnvironment());
            try {
                $this->mergeTraversableConfig($config);
            } catch (Exception\InvalidArgumentException $e) {
                // Throw a more descriptive exception
                throw new Exception\InvalidArgumentException(
                    sprintf('getConfig() method of %s must be an array, '
                    . 'implement the \Traversable interface, or be an '
                    . 'instance of Zend\Config\Config. %s given.', 
                    get_class($module), gettype($config))
                );
            }
            if ($this->getOptions()->getConfigCacheEnabled()) {
                $this->updateCache();
            }
        }
        return $this;
    }

    protected function mergeTraversableConfig($config)
    {
        if ($config instanceof Traversable) {
            $config = IteratorToArray::convert($config);
        }
        if (!is_array($config)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Config being merged must be an array, '
                . 'implement the \Traversable interface, or be an '
                . 'instance of Zend\Config\Config. %s given.', gettype($config))
            );
        }
        $this->setMergedConfig(array_replace_recursive($this->mergedConfig, $config));
    }
    
    protected function hasCachedConfig()
    {
        if (($this->getOptions()->getConfigCacheEnabled())
            && (file_exists($this->getOptions()->getConfigCacheFile()))
        ) {
            return true;
        }
        return false;
    }

    protected function getCachedConfig()
    {
        return include $this->getOptions()->getConfigCacheFile();
    }

    protected function updateCache()
    {
        if (($this->getOptions()->getConfigCacheEnabled())
            && (false === $this->skipConfig)
        ) {
            $configFile = $this->getOptions()->getConfigCacheFile();
            $this->writeArrayToFile($configFile, $this->getMergedConfig(false));
        }
        return $this;
    }
}
