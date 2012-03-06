<?php

namespace Zend\Module\Listener;

use ArrayAccess,
    Traversable,
    Zend\Config\Config,
    Zend\Config\Factory as ConfigFactory,
    Zend\Module\ModuleEvent,
    Zend\Stdlib\IteratorToArray,
    Zend\EventManager\EventCollection,
    Zend\EventManager\ListenerAggregate;

class ConfigListener extends AbstractListener
    implements ConfigMerger, ListenerAggregate
{
    /**
     * @var array
     */
    protected $listeners = array();

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
     * @var array
     */
    protected $globPaths = array();

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

    /**
     * __invoke proxy to loadModule for easier attaching 
     * 
     * @param ModuleEvent $e 
     * @return ConfigListener
     */
    public function __invoke(ModuleEvent $e)
    {
        return $this->loadModule($e);
    }

    /**
     * Attach one or more listeners
     *
     * @param EventCollection $events
     * @return ConfigListener
     */
    public function attach(EventCollection $events)
    {
        $this->listeners[] = $events->attach('loadModule', array($this, 'loadModule'), 1000);
        $this->listeners[] = $events->attach('loadModules.pre', array($this, 'loadModulesPre'), 9000);
        $this->listeners[] = $events->attach('loadModules.post', array($this, 'loadModulesPost'), 9000);
        return $this;
    }

    /**
     * Pass self to the ModuleEvent object early so everyone has access. 
     * 
     * @param ModuleEvent $e 
     * @return ConfigListener
     */
    public function loadModulesPre(ModuleEvent $e)
    {
        $e->setConfigListener($this);
        return $this;
    }

    /**
     * Merge the config for each module 
     * 
     * @param ModuleEvent $e 
     * @return ConfigListener
     */
    public function loadModule(ModuleEvent $e)
    {
        if (true === $this->skipConfig) {
            return;
        }
        $module = $e->getParam('module');
        if (is_callable(array($module, 'getConfig'))) {
            $this->mergeModuleConfig($module);
        }
        return $this;
    }

    /**
     * Merge all config files matched by the given glob()s
     *
     * This should really only be called by the module manager.
     *
     * @param ModuleEvent $e 
     * @return ConfigListener
     */
    public function loadModulesPost(ModuleEvent $e)
    {
        if (true === $this->skipConfig) {
            return $this;
        }
        foreach ($this->globPaths as $globPath) {
            $this->mergeGlobPath($globPath);
        }
        return $this;
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventCollection $events
     * @return void
     */
    public function detach(EventCollection $events)
    {
        foreach ($this->listeners as $key => $listener) {
            $events->detach($listener);
            unset($this->listeners[$key]);
        }
        $this->listeners = array();
        return $this;
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
     * Add a glob path of config files to merge after loading modules
     *
     * @param string $globPath
     * @return ConfigListener
     */
    public function addConfigGlobPath($globPath)
    {
        if (!is_string($globPath)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Parameter to %s::%s() must be a string; %s given.',
                __CLASS__, __METHOD__, gettype($globPath))
            );
        }
        $this->globPaths[] = $globPath;
        return $this;
    }

    /**
     * Add an array of glob paths of config files to merge after loading modules
     *
     * @param mixed $globPaths
     * @return ConfigListener
     */
    public function addConfigGlobPaths($globPaths)
    {
        if ($globPaths instanceof Traversable) {
            $globPaths = IteratorToArray::convert($globPaths);
        }

        if (!is_array($globPaths)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Argument passed to %::%s() must be an array, '
                . 'implement the \Traversable interface, or be an '
                . 'instance of Zend\Config\Config. %s given.',
                __CLASS__, __METHOD__, gettype($globPaths))
            );
        }

        foreach ($globPaths as $globPath) {
            $this->addConfigGlobPath($globPath);
        }

        return $this;
    }

    /**
     * Merge all config files matching a glob
     *
     * @param mixed $globPath
     * @return ConfigListener
     */
    protected function mergeGlobPath($globPath)
    {
        // @TODO Use GlobIterator
        $config = ConfigFactory::fromFiles(glob($globPath, GLOB_BRACE));
        $this->mergeTraversableConfig($config);
        if ($this->getOptions()->getConfigCacheEnabled()) {
            $this->updateCache();
        }
        return $this;
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
            $config = $module->getConfig();
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
