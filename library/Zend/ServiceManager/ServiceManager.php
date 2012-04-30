<?php

namespace Zend\ServiceManager;

class ServiceManager implements ServiceLocatorInterface
{

    /**@#+
     * Constants
     */
    const SCOPE_PARENT = 'parent';
    const SCOPE_CHILD = 'child';
    /**@#-*/

    /**
     * @var bool
     */
    protected $allowOverride = false;

    /**
     * @var array
     */
    protected $invokableClasses = array();

    /**
     * @var string|callable|Closure|InstanceFactoryInterface[]
     */
    protected $factories = array();

    /**
     * @var Closure|InstanceFactoryInterface[]
     */
    protected $abstractFactories = array();

    /**
     * @var array
     */
    protected $shared = array();

    /**
     * Registered services and cached values
     *
     * @var array
     */
    protected $instances = array();

    /**
     * @var array
     */
    protected $aliases = array();

    /**
     * @var array
     */
    protected $initializers = array();

    /**
     * @var ServiceManager[]
     */
    protected $peeringServiceManagers = array();

    /**
     * @var bool Track whether not ot throw exceptions during create()
     */
    protected $createThrowException = true;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration = null)
    {
        if ($configuration) {
            $configuration->configureServiceManager($this);
        }
    }

    /**
     * @param $allowOverride
     */
    public function setAllowOverride($allowOverride)
    {
        $this->allowOverride = (bool) $allowOverride;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAllowOverride()
    {
        return $this->allowOverride;
    }

    /**
     * @param $name
     * @param $invokableClass
     * @param bool $shared
     * @throws Exception\InvalidServiceNameException
     */
    public function setInvokableClass($name, $invokableClass, $shared = true)
    {
        $name = $this->canonicalizeName($name);

        if ($this->allowOverride === false && $this->has($name)) {
            throw new Exception\InvalidServiceNameException(
                'A service by this name or alias already exists and cannot be overridden, please use an alternate name.'
            );
        }
        $this->invokableClasses[$name] = $invokableClass;
        $this->shared[$name] = $shared;
        return $this;
    }

    /**
     * @param $name
     * @param $factory
     * @throws Exception\InvalidServiceNameException
     */
    public function setFactory($name, $factory, $shared = true)
    {
        $name = $this->canonicalizeName($name);

        if ($this->allowOverride === false && $this->has($name)) {
            throw new Exception\InvalidServiceNameException(
                'A service by this name or alias already exists and cannot be overridden, please use an alternate name.'
            );
        }

        $this->factories[$name] = $factory;
        $this->shared[$name] = $shared;
        return $this;
    }

    /**
     * @param $factory
     * @param bool $topOfStack
     */
    public function addAbstractFactory($factory, $topOfStack = true)
    {
        if ($topOfStack) {
            array_unshift($this->abstractFactories, $factory);
        } else {
            array_push($this->abstractFactories, $factory);
        }
        return $this;
    }

    /**
     * @param $initializer
     * @throws Exception\InvalidArgumentException
     */
    public function addInitializer($initializer)
    {
        if (!is_callable($initializer) && !$initializer instanceof InitializerInterface) {
            throw new Exception\InvalidArgumentException('$initializer should be callable.');
        }
        $this->initializers[] = $initializer;
    }

    /**
     * Register a service with the locator
     *
     * @param string $name
     * @param mixed $service
     * @param bool $shared
     * @param bool $shared
     * @return ServiceManager
     * @throws Exception\InvalidServiceNameException
     */
    public function setService($name, $service, $shared = true)
    {
        $name = $this->canonicalizeName($name);

        if ($this->allowOverride === false && $this->has($name)) {
            throw new Exception\InvalidServiceNameException(
                'A service by this name or alias already exists and cannot be overridden, please use an alternate name.'
            );
        }

        /**
         * @todo If a service is being overwritten, destroy all previous aliases
         */

        $this->instances[$name] = $service;
        $this->shared[$name] = (bool) $shared;
        return $this;
    }

    /**
     * @param $name
     * @param $isShared
     * @return ServiceManager
     * @throws Exception\ServiceNotFoundException
     */
    public function setShared($name, $isShared)
    {
        $name = $this->canonicalizeName($name);

        if (!isset($this->invokableClasses[$name]) && !isset($this->factories[$name])) {
            throw new Exception\ServiceNotFoundException('A service by the name provided was not found and could not be marked as shared.');
        }

        $this->shared[$name] = (bool) $isShared;
        return $this;
    }

    /**
     * Retrieve a registered instance
     *
     * @param  string $cName
     * @param  array $params
     * @return mixed
     */
    public function get($name)
    {
        if (is_array($name)) {
            list($cName, $rName) = $name;
        } else {
            $cName = $this->canonicalizeName($name);;
            $rName = $name;
        }

        if ($this->hasAlias($cName)) {
            do {
                $cName = $this->aliases[$cName];
            } while ($this->hasAlias($cName));
        }

        $instance = null;

        if (isset($this->instances[$cName])) {
            $instance = $this->instances[$cName];
        }

        if (!$instance) {
            $instance = $this->create(array($cName, $rName));
        }

        if (isset($this->shared[$cName]) && $this->shared[$cName] === true) {
            $this->instances[$cName] = $instance;
        }

        return $instance;
    }

    /**
     * @param $cName
     * @return false|object
     * @throws Exception\InstanceNotCreatedException
     * @throws Exception\InvalidServiceNameException
     */
    public function create($name)
    {
        $instance = false;
        $rName = null;

        if (is_array($name)) {
            list($cName, $rName) = $name;
        } else {
            $cName = $name;
        }

        $cName = $this->canonicalizeName($cName);

        if (isset($this->invokableClasses[$cName])) {
            $invokable = $this->invokableClasses[$cName];
            $instance = new $invokable;
        }

        if (!$instance && isset($this->factories[$cName])) {
            $source = $this->factories[$cName];
            if (is_string($source) && class_exists($source, true)) {
                $source = new $source;
                $this->factories[$cName] = $source;
            }
            if ($source instanceof FactoryInterface) {
                $instance = $this->createServiceViaCallback(array($source, 'createService'), $cName, $rName);
            } elseif (is_callable($source)) {
                $instance = $this->createServiceViaCallback($source, $cName, $rName);
            } else {
                throw new Exception\InstanceNotCreatedException(sprintf(
                    'While attempting to create %s%s an invalid factory was registered for this instance type.',
                    $cName,
                    ($rName ? '(alias: ' . $rName . ')' : '')
                ));
            }
        }

        if (!$instance && !empty($this->abstractFactories)) {
            foreach ($this->abstractFactories as $index => $abstractSource) {
                // support factories as strings
                if (is_string($abstractSource)) {
                    $this->abstractFactories[$index] = $abstractSource = new $abstractSource;
                }
                if ($abstractSource instanceof AbstractFactoryInterface) {
                    $instance = $this->createServiceViaCallback(array($abstractSource, 'createServiceWithName'), $cName, $rName);
                } elseif (is_callable($abstractSource)) {
                    $instance = $this->createServiceViaCallback($abstractSource, $cName, $rName);
                }
                if (is_object($instance)) {
                    break;
                }
            }
            if (!$instance) {
                throw new Exception\InstanceNotCreatedException(sprintf(
                    'While attempting to create %s%s an abstract factory could not produce a valid instance.',
                    $cName,
                    ($rName ? '(alias: ' . $rName . ')' : '')
                ));
            }
        }

        if (!$instance && $this->peeringServiceManagers) {
            foreach ($this->peeringServiceManagers as $peeringServiceManager) {
                $peeringServiceManager->createThrowException = false;
                $instance = $peeringServiceManager->get($rName ?: $cName);
            }
        }

        if ($this->createThrowException == true && $instance === false) {
            throw new Exception\InstanceNotCreatedException(sprintf(
                'No valid instance was found for %s%s',
                $cName,
                ($rName ? '(alias: ' . $rName . ')' : '')
            ));
        }

        /** @var $initializer InitializerInterface */
        foreach ($this->initializers as $initializer) {
            if ($initializer instanceof InitializerInterface) {
                $initializer->initialize($instance);
            } else {
                $initializer($instance);
            }
        }

        return $instance;
    }

    /**
     * @param $nameOrAlias
     * @return bool
     */
    public function has($nameOrAlias)
    {
        $nameOrAlias = $this->canonicalizeName($nameOrAlias);

        return (
            isset($this->invokableClasses[$nameOrAlias])
            || isset($this->factories[$nameOrAlias])
            || isset($this->aliases[$nameOrAlias])
            || isset($this->instances[$nameOrAlias])
        );
    }

    /**
     * @param $alias
     * @param $nameOrAlias
     * @return ServiceManager
     * @throws Exception\ServiceNotFoundException
     * @throws Exception\InvalidServiceNameException
     */
    public function setAlias($alias, $nameOrAlias)
    {
        $alias = $this->canonicalizeName($alias);
        $nameOrAlias = $this->canonicalizeName($nameOrAlias);

        if (!is_string($alias) || $alias == '') {
            throw new Exception\InvalidServiceNameException('Invalid service name alias');
        }

        if ($this->hasAlias($alias)) {
            throw new Exception\InvalidServiceNameException('An alias by this name already exists');
        }

        if (!$this->has($nameOrAlias)) {
            throw new Exception\ServiceNotFoundException('A target service or target alias could not be located');
        }

        $this->aliases[$alias] = $nameOrAlias;
        return $this;
    }

    /**
     * @param $alias
     * @return bool
     */
    public function hasAlias($alias)
    {
        $alias = $this->canonicalizeName($alias);
        return (isset($this->aliases[$alias]));
    }

    /**
     * @param string $peering
     * @return ServiceManager
     */
    public function createScopedServiceManager($peering = self::SCOPE_PARENT)
    {
        $instanceManager = new ServiceManager();
        if ($peering == self::SCOPE_PARENT) {
            $instanceManager->registerPeerInstanceManager($this);
        }
        if ($peering == self::SCOPE_CHILD) {
            $this->registerPeerInstanceManager($instanceManager);
        }
        return $instanceManager;
    }

    /**
     * @param ServiceManager $instanceManager
     */
    public function registerPeerInstanceManager(ServiceManager $instanceManager)
    {
        $this->peeringServiceManagers[] = $instanceManager;
    }

    /**
     * @param $name
     * @return string
     */
    protected function canonicalizeName($name)
    {
        return strtolower(str_replace(array('-', '_', ' ', '\\', '/'), '', $name));
    }

    /**
     * @param callable $callable
     * @param $cName
     * @param $rName
     * @throws Exception\InstanceNotCreatedException
     * @throws Exception\CircularDependencyFoundException
     * @return object
     */
    protected function createServiceViaCallback($callable, $cName, $rName)
    {
        static $circularDependencyResolver = array();

        if (isset($circularDependencyResolver[spl_object_hash($this) . '-' . $cName])) {
            $circularDependencyResolver = array();
            throw new Exception\CircularDependencyFoundException('Circular dependency for LazyServiceLoader was found for instance ' . $name);
        }

        $circularDependencyResolver[spl_object_hash($this) . '-' . $cName] = true;
        $instance = call_user_func($callable, $this, $cName, $rName);
        if ($instance === null) {
            throw new Exception\InstanceNotCreatedException('The factory was called but did not return an instance.');
        }
        unset($circularDependencyResolver[spl_object_hash($this) . '-' . $cName]);

        return $instance;
    }

}