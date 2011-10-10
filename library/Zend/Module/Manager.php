<?php

namespace Zend\Module;

use Traversable,
    Zend\Config\Config,
    Zend\Config\Writer\ArrayWriter,
    Zend\Module\Installable,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

class Manager
{
    /**
     * @var array An array of Module classes of loaded modules
     */
    protected $loadedModules = array();

    /**
     * @var EventCollection
     */
    protected $events;

    /**
     * @var ManagerOptions
     */
    protected $options;

    /**
     * @var Zend\Config\Config
     */
    protected $mergedConfig;

    /**
     * @var bool
     */
    protected $skipConfig = false;

    /**
     * An array containing all of the provided data for modules
     * @var array
     */
    protected $provided = array();

    /**
     * An array containing all of the dependencies for modules
     * @var array
     */
    protected $dependencies = array();

    /**
     * regex for getting version operators
     * @var string
     */
    protected $operators = '/(<|lt|<=|le|>|gt|>=|ge|==|=|eq|!=|<>|ne)?(\d.*)/';

    /**
     * The manifest of installed modules
     * 
     * @var array
     */
    protected $manifest = array();

    /**
     * __construct 
     * 
     * @param array|Traversable $modules 
     * @param ManagerOptions $options 
     * @return void
     */
    public function __construct($modules, ManagerOptions $options = null)
    {
        if ($options === null) {
            $this->setOptions(new ManagerOptions);
        } else {
            $this->setOptions($options);
        }
        if ($this->hasCachedConfig()) {
            $this->skipConfig = true;
            $this->setMergedConfig($this->getCachedConfig());
        }
        
        $install = $options->getEnableSelfInstallation();
        
        if ($install) $this->loadInstallationManifest();
        
        $this->loadModules($modules);
        
        if ($install) $this->saveInstallationManifest();
        
        $this->updateCache();
        $this->events()->trigger('init.post', $this);
    }

    /**
     * loadModules 
     * 
     * @param array|Traversable $modules 
     * @return Manager
     */
    public function loadModules($modules)
    {
        if (is_array($modules) || $modules instanceof Traversable) {
            foreach ($modules as $moduleName) {
                $this->loadModule($moduleName);
            }
        } else {
            throw new \InvalidArgumentException(
                'Parameter to \\ZendModule\\Manager\'s '
                . 'loadModules method must be an array or '
                . 'implement the \\Traversable interface'
            );
        }
        
        if ($this->getOptions()->getEnableDependencyCheck()) {
            $this->resolveDependencies();
        }
        
        return $this;
    }

    /**
     * loadModule 
     * 
     * @param string $moduleName 
     * @return mixed Module's Module class
     */
    public function loadModule($moduleName)
    {
        if (!isset($this->loadedModules[$moduleName])) {
            $class = $moduleName . '\Module';
            $module = new $class;

            $this->addProvision($module);
            $this->addDependency($module);
            $this->installModule($module);
            $this->runModuleInit($module);
            $this->mergeModuleConfig($module);
            $this->loadedModules[$moduleName] = $module;
        }
        return $this->loadedModules[$moduleName];
    }
    
    /**
     * Check if module requires self installation
     * 
     * If installation required then set up installation
     * manifest to allow version tracking
     * 
     * Method will create/update manifest file in data directory
     * 
     * Installation method does not concern itself with what or how 
     * it is to be installed just that it requires installation
     * 
     * @param Module $module
     */
    public function installModule($module)
    {
        if ($module instanceof Installable) {
            // check "manifest"
            foreach ($module->getProvides() as $moduleName => $data) { 
                if (!isset($this->manifest->{$moduleName})) { // if doesnt exist in manifest
                    if ($module->install()) {
                        $this->manifest->{$moduleName} = $data;
                        $this->manifest->{'_dirty'} = true;
                    } else { // if $result is false then throw Exception
                        throw new \RuntimeException("Self installation for {$moduleName} failed");
                    }
                } elseif (isset($this->manifest->{$moduleName}) && // does exists in manifest
                          version_compare($this->manifest->{$moduleName}->version,$data['version']) < 0 // and manifest version is less than current version
                  ){
                    if ($module->upgrade($this->manifest->{$moduleName}->version)) {
                        $this->manifest->{$moduleName} = $data;
                        $this->manifest->{'_dirty'} = true;
                    } else { // if $result is false then throw Exception
                        throw new \RuntimeException("Self upgrade for {$moduleName} failed");
                    }
                }
            }
        }
    }
    
    /**
     * get manifest of currently installed modules
     * 
     * @return Config
     */
    public function loadInstallationManifest()
    {
        $path = $this->getOptions()->getManifestDir() . '/manifest.php';
        if (file_exists($path)) {
            $this->manifest = new Config(include $path, true);
        } else {
            $this->manifest = new Config(array(), true);
        }
        return $this;
    }
    
    public function saveInstallationManifest()
    {
        if ($this->manifest->get('_dirty', false)) {
            unset($this->manifest->{'_dirty'});
            $path = $this->getOptions()->getManifestDir() . '/manifest.php';
            $writer = new ArrayWriter();
            $writer->write($path, $this->manifest);
        }
        return $this;
    }

    /**
     * add details of module provisions
     * 
     * test for an uses getProvides method from module class
     * 
     * getProvides need to return as a minimum
     * 
     * <code>
     * return array(
     *		__NAMESPACE__ => array(
     *	 		'version' => $this->version,
     *		),
     *	);
     * </code>
     * 
     * @param Module $module
     * @return Manager
     */
    public function addProvision($module)
    {
         // check for and load provides
        if (method_exists($module, 'getProvides')) {
            $provision = $module->getProvides();
               foreach ($provision AS $name => $info) {
                if (isset($this->provided[$name])) {
                    throw new \RuntimeException("Double provision has occured for: {$name} {$info['version']}");
                }
                $this->provided[$name] = $info;	
        	}
        }
    	return $this;
    }

    /**
     * add dependencies from module
     * 
     * test for and use getDependencies from Module class
     * 
     * get dependencies must return 
     * 
     * <code>
     * return array(
     *      'php' => array(
     *          'version' => '5.3.0',
     *          'required' => true,
     *      ),
     *      'ext/pdo_mysql' => true
     * );
     * </code>
     * 
     * version and required data are optional
     * 
     * @param Module $module
     * @param Manager
     */
    public function addDependency($module)
    {
        // check for an load dependencies required
        if (method_exists($module, 'getDependencies')) {
            // iterate over dependencies to evaluate min required version
            foreach ($module->getDependencies() AS $dep => $depInfo) {
                if (!isset($this->dependencies[$dep])) { // if the dep isnt present just add it
                    $this->dependencies[$dep] = $depInfo; 	
                } else { // dep already present
                    if (is_array($depInfo)) { // if is array need to check versions
                        if (isset($this->dependencies[$dep]['version']) && isset($depInfo)) {
                            if (version_compare($this->dependencies[$dep]['version'], $depInfo['version']) >= 0) {
                                $depInfo['version'] = $this->dependencies[$dep]['version'];// set to highest version
                            }
                        }
                        if (!is_array($this->dependencies[$dep])) { 
                            $this->dependencies[$dep] = $depInfo;
                        } else {
                            $this->dependencies[$dep] = array_merge($this->dependencies[$dep], $depInfo);
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * return the currently loaded dependencies
     * 
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * return the currently loaded provisions
     * 
     * @retrun array
     */
    public function getProvisions()
    {
        return $this->provided;
    }
    
    /**
     * Takes arrays created for provides and depends on module load 
     * and iterates over to check for satisfied dependencies  
     * 
     * updates dependency array with key 'satisfied' for each dependency
     * 
     * This allows quick retrieval of provisions and dependencies 
     * 
     * @todo: add library detection withoiy having to load files
     * @todo: review code for performance 
     * 
     * @return Manager
     */
    public function resolveDependencies()
    {
        foreach ($this->getDependencies() as $dep => $depInfo) {
            if (isset($depInfo['version'])) {
                preg_match($this->operators,$depInfo['version'], $matches, PREG_OFFSET_CAPTURE);
                $version = $matches[2][0];
                $operator = $matches[1][0] ?: '>=';
            } else {
                $version = 0;
                $operator = '>=';
            }

            if ($dep === 'php') { // is php version requirement
                $this->dependencies[$dep]['satisfied'] = true;
                if (!version_compare(PHP_VERSION, $version, $operator)) {
                    if (isset($depInfo['required']) && $depInfo['required'] == true) {
                        throw new \RuntimeException("Required dependency unsatisfied: {$dep} {$depInfo['version']}");
                    } else {
                        $this->dependencies[$dep]['satisfied'] = false;
                    }
                }
            } elseif (substr($dep, 0, 4) === 'ext/') { // is php extension requirement
                $extName = substr($dep, 4);
                $this->dependencies[$dep]['satisfied'] = true;
                if (!version_compare(phpversion($extName), $version, $operator ?: '>=')) {
                    if (isset($depInfo['required']) && $depInfo['required'] == true) {
                        throw new \RuntimeException("Required dependency unsatisfied: {$dep} {$depInfo['version']}");
                    } else {
                        $this->dependencies[$dep]['satisfied'] = false;
                    }
                }
            } elseif (substr($dep, 0, 4) === 'lib/') { // is library requirement
                // @todo: add library detection

            } else { // is module requirement
                if (is_scalar($this->dependencies[$dep])) {
                    $this->dependencies[$dep] = array();
                }
                if (!isset($depInfo['satisfied'])) {
                    $this->dependencies[$dep]['satisfied'] = false;
                    if (isset($this->provided[$dep])) { // if provided set satisfaction
                        if (isset($depInfo['version'])){ // if dep have version requirement
                            if (version_compare($this->provided[$dep]['version'], $version, $operator) >= 0) {
                                $this->dependencies[$dep]['satisfied'] = true;
                            }
                        } else {
                            $this->dependencies[$dep]['satisfied'] = true;
                        }
	                 }
                }
                if (!$this->dependencies[$dep]['satisfied']) {
                    if (isset($depInfo['required']) && $depInfo['required'] == true ) {
                        throw new \RuntimeException("Required dependency unsatisfied: {$dep} " . (isset($depInfo['version']) ?: ''));
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Set the event manager instance used by this context
     * 
     * @param  EventCollection $events 
     * @return Manager
     */
    public function setEventManager(EventCollection $events)
    {
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     * 
     * @return EventCollection
     */
    public function events()
    {
        if (!$this->events instanceof EventCollection) {
            $this->setEventManager(new EventManager(array(__CLASS__, get_class($this))));
        }
        return $this->events;
    }

    /**
     * Get options.
     *
     * @return ManagerOptions
     */
    public function getOptions()
    {
        return $this->options;
    }
 
    /**
     * Set options 
     * 
     * @param ManagerOptions $options 
     * @return Manager
     */
    public function setOptions(ManagerOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get loadedModules.
     *
     * @return array
     */
    public function getLoadedModules()
    {
        return $this->loadedModules;
    }

    /**
     * getMergedConfig
     * Build a merged config object for all loaded modules
     * 
     * @return Zend\Config\Config
     */
    public function getMergedConfig($readOnly = true)
    {
        if (null === $this->mergedConfig) {
            $this->setMergedConfig(new Config(array(), true));
        }
        if (true === $readOnly) {
            $this->mergedConfig->setReadOnly();
        }
        return $this->mergedConfig;
    }

    /**
     * setMergedConfig 
     * 
     * @param Config $config 
     * @return Manager
     */
    public function setMergedConfig(Config $config)
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
    public function mergeModuleConfig($module)
    {
        if ((false === $this->skipConfig)
            && (is_callable(array($module, 'getConfig')))
        ) {
            $this->getMergedConfig(false)->merge($module->getConfig($this->getOptions()->getApplicationEnv()));
        }
        return $this;
    }

    protected function runModuleInit($module)
    {
        if (is_callable(array($module, 'init'))) {
            $module->init($this);
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
        return new Config(include $this->getOptions()->getCacheFilePath());
    }

    protected function updateCache()
    {
        if (($this->getOptions()->getEnableConfigCache())
            && (false === $this->skipConfig)
        ) {
            $this->saveConfigCache($this->getMergedConfig());
        }
        return $this;
    }

    protected function saveConfigCache($config)
    {
        $content = "<?php\nreturn " . var_export($config->toArray(), 1) . ';';
        file_put_contents($this->getOptions()->getCacheFilePath(), $content);
        return $this;
    }
}
