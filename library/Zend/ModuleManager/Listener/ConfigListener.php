<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */
namespace Zend\ModuleManager\Listener;

use ArrayAccess;
use Traversable;
use Zend\Config\Config;
use Zend\Config\Factory as ConfigFactory;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\Stdlib\ArrayUtils;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * Config listener
 * 
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage Listener
 */
class ConfigListener extends AbstractListener implements 
    ConfigMergerInterface, 
    ListenerAggregateInterface
{
	const STATIC_PATH = 'static_path';
	const GLOB_PATH = 'glob_path';

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
    protected $paths = array();

    /**
     * __construct
     *
     * @param  ListenerOptions $options
     * @return void
     */
    public function __construct(ListenerOptions $options = null)
    {
        parent::__construct($options);
        if ($this->hasCachedConfig()) {
            $this->skipConfig = true;
            $this->setMergedConfig($this->getCachedConfig());
        } else {
            $this->addConfigGlobPaths($this->getOptions()->getConfigGlobPaths());
        }
    }

    /**
     * __invoke proxy to loadModule for easier attaching
     *
     * @param  ModuleEvent $e
     * @return ConfigListener
     */
    public function __invoke(ModuleEvent $e)
    {
        return $this->loadModule($e);
    }

    /**
     * Attach one or more listeners
     *
     * @param  EventManagerInterface $events
     * @return ConfigListener
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('loadModule', array($this, 'loadModule'), 1000);
        $this->listeners[] = $events->attach('loadModules.pre', array($this, 'loadModulesPre'), 9000);
        $this->listeners[] = $events->attach('loadModules.post', array($this, 'loadModulesPost'), 9000);
        return $this;
    }

    /**
     * Pass self to the ModuleEvent object early so everyone has access.
     *
     * @param  ModuleEvent $e
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
     * @param  ModuleEvent $e
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
     * @param  ModuleEvent $e
     * @return ConfigListener
     */
    public function loadModulesPost(ModuleEvent $e)
    {
        if (true === $this->skipConfig) {
            return $this;
        }
        foreach ($this->paths as $path) {
            $this->mergePath($path);
        }
        return $this;
    }

    /**
     * Detach all previously attached listeners
     *
     * @param  EventManagerInterface $events
     * @return ConfigListener
     */
    public function detach(EventManagerInterface $events)
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
     * @param  bool $returnConfigAsObject
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
     * @param  array $config
     * @return ConfigListener
     */
    public function setMergedConfig(array $config)
    {
        $this->mergedConfig = $config;
        $this->mergedConfigObject = null;
        return $this;
    }

    /**
     * Add a path of config files to merge after loading modules
     *
     * @param  string $path
     * @return ConfigListener
     */
    protected function addConfigPath($path, $type)
    {
        if (!is_string($path)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Parameter to %s::%s() must be a string; %s given.',
                __CLASS__, __METHOD__, gettype($path))
            );
        }
        $this->paths[] = array('type' => $type, 'path' => $path);
        return $this;
    }

    /**
     * Add an array of glob paths of config files to merge after loading modules
     *
     * @param  mixed $globPaths
     * @return ConfigListener
     */
    public function addConfigGlobPaths($globPaths)
    {
        $this->addConfigPaths($globPaths, self::GLOB_PATH);
        return $this;
    }

    /**
     * Add a glob path of config files to merge after loading modules
     *
     * @param  string $globPath
     * @return ConfigListener
     */
    public function addConfigGlobPath($globPath)
    {
        $this->addConfigPath($globPath, self::GLOB_PATH);
        return $this;
    }

    /**
     * Add an array of static paths of config files to merge after loading modules
     *
     * @param  mixed $staticPaths
     * @return ConfigListener
     */
    public function addConfigStaticPaths($staticPaths)
    {
    	$this->addConfigPaths($staticPaths, self::STATIC_PATH);
        return $this;
    }

    /**
     * Add a static path of config files to merge after loading modules
     *
     * @param  string $globPath
     * @return ConfigListener
     */
    public function addConfigStaticPath($staticPath)
    {
    	$this->addConfigPath($staticPath, self::STATIC_PATH);
        return $this;
    }

    /**
     * Add an array of paths of config files to merge after loading modules
     *
     * @param  mixed $paths
     * @return ConfigListener
     */
    protected function addConfigPaths($paths, $type)
    {
    	if ($paths instanceof Traversable) {
            $paths = ArrayUtils::iteratorToArray($paths);
        }

        if (!is_array($paths)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Argument passed to %::%s() must be an array, '
                . 'implement the \Traversable interface, or be an '
                . 'instance of Zend\Config\Config. %s given.',
                __CLASS__, __METHOD__, gettype($paths))
            );
        }

        foreach ($paths as $path) {
            $this->addConfigPath($path, $type);
        }
    }

    /**
     * Merge all config files matching a glob
     *
     * @param  mixed $path
     * @return ConfigListener
     */
    protected function mergePath($path)
    {
        switch ($path['type']) {
            case self::STATIC_PATH:
                $config = ConfigFactory::fromFile($path['path']);
                break;

            case self::GLOB_PATH:
                $config = ConfigFactory::fromFiles(glob($path['path'], GLOB_BRACE));
                break;
        }
        $this->mergeTraversableConfig($config);
        if ($this->getOptions()->getConfigCacheEnabled()) {
            $this->updateCache();
        }
        return $this;
    }

    /**
     * mergeModuleConfig
     *
     * @param  mixed $module
     * @return ConfigListener
     */
    protected function mergeModuleConfig($module)
    {
        if (false !== $this->skipConfig
            || (!$module instanceof ConfigProviderInterface
                && !is_callable(array($module, 'getConfig')))
        ) {
            return $this;
        }

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

        return $this;
    }

    /**
     * @param  $config
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    protected function mergeTraversableConfig($config)
    {
        if ($config instanceof Traversable) {
            $config = ArrayUtils::iteratorToArray($config);
        }
        if (!is_array($config)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Config being merged must be an array, '
                . 'implement the \Traversable interface, or be an '
                . 'instance of Zend\Config\Config. %s given.', gettype($config))
            );
        }
        $this->setMergedConfig(ArrayUtils::merge($this->mergedConfig, $config));
    }

    /**
     * @return bool
     */
    protected function hasCachedConfig()
    {
        if (($this->getOptions()->getConfigCacheEnabled())
            && (file_exists($this->getOptions()->getConfigCacheFile()))
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    protected function getCachedConfig()
    {
        return include $this->getOptions()->getConfigCacheFile();
    }

    /**
     * @return ConfigListener
     */
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
