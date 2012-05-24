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
     * @var Closure|AbstractFactoryInterface[]
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
    protected $throwExceptionInCreate = true;

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
     * @param bool $throwExceptionInCreate
     * @return ServiceManager
     */
    public function setThrowExceptionInCreate($throwExceptionInCreate)
    {
        $this->throwExceptionInCreate = $throwExceptionInCreate;
        return $this;
    }

    /**
     * @return bool
     */
    public function getThrowExceptionInCreate()
    {
        return $this->throwExceptionInCreate;
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
            throw new Exception\InvalidServiceNameException(sprintf(
                'A service by the name or alias "%s" already exists and cannot be overridden; please use an alternate name',
                $name
            ));
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

        if (!is_string($factory) && !$factory instanceof FactoryInterface && !is_callable($factory)) {
            throw new Exception\InvalidArgumentException(
                'Provided abstract factory must be the class name of an abstract factory or an instance of an AbstractFactoryInterface.'
            );
        }

        if ($this->allowOverride === false && $this->has($name)) {
            throw new Exception\InvalidServiceNameException(sprintf(
                'A service by the name or alias "%s" already exists and cannot be overridden, please use an alternate name',
                $name
            ));
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
        if (!is_string($factory) && !$factory instanceof AbstractFactoryInterface && !is_callable($factory)) {
            throw new Exception\InvalidArgumentException(
                'Provided abstract factory must be the class name of an abstract factory or an instance of an AbstractFactoryInterface.'
            );
        }

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
    public function addInitializer($initializer, $topOfStack = true)
    {
        if (!is_callable($initializer) && !$initializer instanceof InitializerInterface) {
            throw new Exception\InvalidArgumentException('$initializer should be callable.');
        }

        if ($topOfStack) {
            array_unshift($this->initializers, $initializer);
        } else {
            array_push($this->initializers, $initializer);
        }
        return $this;
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
        $cName = $this->canonicalizeName($name);

        if (!isset($this->invokableClasses[$cName]) && !isset($this->factories[$cName])) {
            throw new Exception\ServiceNotFoundException(sprintf(
                '%s: A service by the name "%s" was not found and could not be marked as shared',
                __METHOD__,
                $name
            ));
        }

        $this->shared[$cName] = (bool) $isShared;
        return $this;
    }

    /**
     * Retrieve a registered instance
     *
     * @param  string $cName
     * @param  array $params
     * @return mixed
     */
    public function get($name, $usePeeringServiceManagers = true)
    {
        $cName = $this->canonicalizeName($name);
        $rName = $name;

        if ($this->hasAlias($cName)) {
            do {
                $cName = $this->aliases[$cName];
            } while ($this->hasAlias($cName));
        }

        $instance = null;

        if (isset($this->instances[$cName])) {
            $instance = $this->instances[$cName];
        } 

        $selfException = null;

        if (!$instance) {
            try {
                $instance = $this->create(array($cName, $rName));
            } catch (Exception\ServiceNotCreatedException $selfException) {
                foreach ($this->peeringServiceManagers as $peeringServiceManager) {
                    try {
                        $instance = $peeringServiceManager->get($name);
                        break;
                    } catch (Exception\ServiceNotCreatedException $e) {
                        continue;
                    }
                }
            }
        }

        if (!$instance) {

            // Still no instance? raise an exception
            if (!$instance) {
                throw new Exception\ServiceNotCreatedException(sprintf(
                        '%s was unable to fetch or create an instance for %s',
                        __METHOD__,
                        $name
                    ),
                    null,
                    ($selfException === null) ? null : $selfException->getPrevious()
                );
            }
        }

        if (isset($this->shared[$cName]) && $this->shared[$cName] === true) {
            $this->instances[$cName] = $instance;
        }

        return $instance;
    }

    /**
     * @param $cName
     * @return false|object
     * @throws Exception\ServiceNotCreatedException
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
            if (!class_exists($invokable)) {
                throw new Exception\ServiceNotCreatedException(sprintf(
                    '%s: failed retrieving "%s" via invokable class "%s"; class does not exist',
                    __METHOD__,
                    $name,
                    $cName
                ));
            }
            $instance = new $invokable;
        }

        if (!$instance && isset($this->factories[$cName])) {
            $factory = $this->factories[$cName];
            if (is_string($factory) && class_exists($factory, true)) {
                $factory = new $factory;
                $this->factories[$cName] = $factory;
            }
            if ($factory instanceof FactoryInterface) {
                $instance = $this->createServiceViaCallback(array($factory, 'createService'), $cName, $rName);
            } elseif (is_callable($factory)) {
                $instance = $this->createServiceViaCallback($factory, $cName, $rName);
            } else {
                throw new Exception\ServiceNotCreatedException(sprintf(
                    'While attempting to create %s%s an invalid factory was registered for this instance type.',
                    $cName,
                    ($rName ? '(alias: ' . $rName . ')' : '')
                ));
            }
        }

        if (!$instance && !empty($this->abstractFactories)) {
            foreach ($this->abstractFactories as $index => $abstractFactory) {
                // support factories as strings
                if (is_string($abstractFactory) && class_exists($abstractFactory, true)) {
                    $this->abstractFactories[$index] = $abstractFactory = new $abstractFactory;
                }
                if ($abstractFactory instanceof AbstractFactoryInterface) {
                    $instance = $this->createServiceViaCallback(array($abstractFactory, 'createServiceWithName'), $cName, $rName);
                } elseif (is_callable($abstractFactory)) {
                    $instance = $this->createServiceViaCallback($abstractFactory, $cName, $rName);
                } else {
                    throw new Exception\ServiceNotCreatedException(sprintf(
                        'While attempting to create %s%s an abstract factory could not produce a valid instance.',
                        $cName,
                        ($rName ? '(alias: ' . $rName . ')' : '')
                    ));
                }
                if (is_object($instance)) {
                    break;
                }
            }
        }

        if ($this->throwExceptionInCreate == true && $instance === false) {
            throw new Exception\ServiceNotCreatedException(sprintf(
                'No valid instance was found for %s%s',
                $cName,
                ($rName ? '(alias: ' . $rName . ')' : '')
            ));
        }

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
    public function has($nameOrAlias, $usePeeringServiceManagers = true)
    {
        if (is_array($nameOrAlias)) {
            list($cName, $rName) = $nameOrAlias;
        } else {
            $cName = $this->canonicalizeName($nameOrAlias);
            $rName = $cName;
        }

        $has = (
            isset($this->invokableClasses[$cName])
            || isset($this->factories[$cName])
            || isset($this->aliases[$cName])
            || isset($this->instances[$cName])
        );

        if ($has) {
            return true;
        }

        // check abstract factories
        foreach ($this->abstractFactories as $index => $abstractFactory) {
            // Support string abstract factory class names
            if (is_string($abstractFactory) && class_exists($abstractFactory, true)) {
                $this->abstractFactory[$index] = $abstractFactory = new $abstractFactory();
            }

            // Bad abstract factory; skip
            if (!$abstractFactory instanceof AbstractFactoryInterface) {
                continue;
            }
            
            if ($abstractFactory->canCreateServiceWithName($cName, $rName)) {
                return true;
            }
        }

        if ($usePeeringServiceManagers) {
            foreach ($this->peeringServiceManagers as $peeringServiceManager) {
                if ($peeringServiceManager->has($rName)) {
                    return true;
                }
            }
        }

        return false;
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
        if (!is_string($alias) || !is_string($nameOrAlias)) {
            throw new Exception\InvalidServiceNameException('Service or alias names must be strings.');
        }

        $alias = $this->canonicalizeName($alias);
        $nameOrAlias = $this->canonicalizeName($nameOrAlias);

        if ($alias == '' || $nameOrAlias == '') {
            throw new Exception\InvalidServiceNameException('Invalid service name alias');
        }

        if ($this->allowOverride === false && $this->hasAlias($alias)) {
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
        $scopedServiceManager = new ServiceManager();
        if ($peering == self::SCOPE_PARENT) {
            $scopedServiceManager->peeringServiceManagers[] = $this;
        }
        if ($peering == self::SCOPE_CHILD) {
            $this->peeringServiceManagers[] = $scopedServiceManager;
        }
        return $scopedServiceManager;
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
     * @throws Exception\ServiceNotCreatedException
     * @throws Exception\CircularDependencyFoundException
     * @return object
     */
    protected function createServiceViaCallback($callable, $cName, $rName)
    {
        static $circularDependencyResolver = array();

        if (isset($circularDependencyResolver[spl_object_hash($this) . '-' . $cName])) {
            $circularDependencyResolver = array();
            throw new Exception\CircularDependencyFoundException('Circular dependency for LazyServiceLoader was found for instance ' . $rName);
        }

        $circularDependencyResolver[spl_object_hash($this) . '-' . $cName] = true;
        try {
            $instance = call_user_func($callable, $this, $cName, $rName);
        } catch (\Exception $e) {
            throw new Exception\ServiceNotCreatedException(
                sprintf('Abstract factory raised an exception when creating "%s"; no instance returned', $rName),
                $e->getCode(),
                $e
            );
        }
        if ($instance === null) {
            throw new Exception\ServiceNotCreatedException('The factory was called but did not return an instance.');
        }
        unset($circularDependencyResolver[spl_object_hash($this) . '-' . $cName]);

        return $instance;
    }

    /**
     * Retrieve a keyed list of all registered services. Handy for debugging!
     *
     * @return array
     */
    public function getRegisteredServices()
    {
        return array(
            'invokableClasses' => array_keys($this->invokableClasses),
            'factories' => array_keys($this->factories),
            'aliases' => array_keys($this->aliases),
            'instances' => array_keys($this->instances),
        );
    }

}
