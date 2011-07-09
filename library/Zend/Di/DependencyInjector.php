<?php

namespace Zend\Di;

class DependencyInjector implements DependencyInjection
{
    /**
     * @var Zend\Di\Definition
     */
    protected $definition = null;
    
    /**
     * @var Zend\Di\InstanceManager
     */
    protected $instanceManager = null;

    /**
     * All the class dependencies [source][dependency]
     * 
     * @var array 
     */
    protected $currentDependencies = array();
    
    /**
     * All the class references [dependency][source]
     * 
     * @var array 
     */
    protected $references = array();
    
    /**
     * @param Zend\DI\Configuration $config
     */
    public function __construct(Configuration $config = null)
    {
        if ($config) {
            $this->configure($config);
        }
    }
    
    public function configure(Configuration $config)
    {
        $config->configure($this);
    }
    
    public function setDefinition(Definition $definition)
    {
        $this->definition = $definition;
        return $this;
    }
    
    /**
     * Definition Factory
     * 
     * @param string $class
     */
    public function createDefinition($class)
    {
        $definition = new $class();
        if (!$definition instanceof Definition) {
            throw new Exception\InvalidArgumentException('The class provided to the Definition factory ' . $class . ' does not implement the Definition interface');
        }
        return $definition;
    }
    
    public function hasDefinition()
    {
        return ($this->definition !== null);
    }
    
    public function getDefinition()
    {
        if ($this->definition == null) {
            $this->definition = $this->createDefinition('Zend\Di\Definition\RuntimeDefinition');
        }
        return $this->definition;
    }
    
    /**
     * @return bool
     */
    public function hasInstanceManager()
    {
        return ($this->instanceManager !== null);
    }
    
    public function setInstanceManager(InstanceCollection $instanceManager)
    {
        $this->instanceManager = $instanceManager;
        return $this;
    }
    
    /**
     * InstanceManager factory
     * 
     * @param string $class
     * @return Zend\Di\InstanceManager
     */
    public function createInstanceManager($class)
    {
        $instanceManager = new $class();
        if (!$instanceManager instanceof InstanceCollection) {
            throw new Exception\InvalidArgumentException('The class provided to the InstanceManager factory ' . $class . ' does not implement the InstanceCollection interface');
        }
        return $instanceManager;
    }
    
    /**
     * @return Zend\Di\InstanceManager
     */
    public function getInstanceManager()
    {
        if ($this->instanceManager == null) {
            $this->instanceManager = $this->createInstanceManager('Zend\Di\InstanceManager');
        }
        return $this->instanceManager;
    }

    /**
     * Lazy-load a class
     *
     * Attempts to load the class (or service alias) provided. If it has been 
     * loaded before, the previous instance will be returned (unless the service
     * definition indicates shared instances should not be used).
     * 
     * @param  string $name Class name or service alias
     * @param  null|array $params Parameters to pass to the constructor
     * @return object|null
     */
    public function get($name, array $params = array())
    {
        $im = $this->getInstanceManager();
        
        if ($params) {
            if (($fastHash = $im->hasSharedInstanceWithParameters($name, $params, true))) {
                return $im->getSharedInstanceWithParameters(null, array(), $fastHash);
            }
        } else {
            if ($im->hasSharedInstance($name, $params)) {
                return $im->getSharedInstance($name, $params);
            }
        }
        return $this->newInstance($name, $params);
    }
    
    /**
     * Retrieve a new instance of a class
     *
     * Forces retrieval of a discrete instance of the given class, using the
     * constructor parameters provided.
     * 
     * @param  mixed $name Class name or service alias
     * @param  array $params Parameters to pass to the constructor
     * @return object|null
     */
    public function newInstance($name, array $params = array(), $isShared = true)
    {
        $definition      = $this->getDefinition();
        $instanceManager = $this->getInstanceManager();
        
        if ($instanceManager->hasAlias($name)) {
            $class = $instanceManager->getClassFromAlias($name);
            $alias = $name;
        } else {
            $class = $name;
            $alias = null;
        }

        if (!$definition->hasClass($class)) {
            $aliasMsg = ($alias) ? '(specified by alias ' . $alias . ') ' : '';
            throw new Exception\ClassNotFoundException('Class ' . $aliasMsg . $class . ' could not be located in provided definition.');
        }
        
        $instantiator     = $definition->getInstantiator($class);
        $injectionMethods = $definition->getInjectionMethods($class);

        if ($instantiator === '__construct') {
            $object = $this->createInstanceViaConstructor($class, $params, $alias);
            if (in_array('__construct', $injectionMethods)) {
                unset($injectionMethods[array_search('__construct', $injectionMethods)]);
            }
        } elseif (is_callable($instantiator)) {
            $object = $this->createInstanceViaCallback($instantiator, $params);
            // @todo make sure we can create via a real object factory
            throw new \Exception('incomplete implementation');
        } else {
            throw new Exception\RuntimeException('Invalid instantiator');
        }

        if ($injectionMethods) {
            foreach ($injectionMethods as $injectionMethod) {
                $this->handleInjectionMethodForObject($object, $injectionMethod, $params, $alias);
            }
        }
        
        if ($isShared) {
            if ($params) {
                $this->getInstanceManager()->addSharedInstanceWithParameters($object, $name, $params);
            } else {
                $this->getInstanceManager()->addSharedInstance($object, $name);
            }
        }
        
        return $object;
    }
    
    public function resolveObjectDependencies($object)
    {
        
    }
    
    /**
     * Retrieve a class instance based on class name
     *
     * Any parameters provided will be used as constructor arguments. If any 
     * given parameter is a DependencyReference object, it will be fetched
     * from the container so that the instance may be injected.
     * 
     * @param  string $class 
     * @param  array $params 
     * @return object
     */
    protected function createInstanceViaConstructor($class, $params, $alias = null)
    {
        $callParameters = array();
        if ($this->definition->hasInjectionMethod($class, '__construct')) {
            $callParameters = $this->resolveMethodParameters($class, '__construct', $params, true, $alias);
        }

        // Hack to avoid Reflection in most common use cases
        switch (count($callParameters)) {
            case 0:
                return new $class();
            case 1:
                return new $class($callParameters[0]);
            case 2:
                return new $class($callParameters[0], $callParameters[1]);
            case 3:
                return new $class($callParameters[0], $callParameters[1], $callParameters[3]);
            default:
                $r = new \ReflectionClass($class);
                return $r->newInstanceArgs($callParameters);
        }
    }
    
    
    /**
     * Get an object instance from the defined callback
     * 
     * @param  callback $callback 
     * @param  array $params 
     * @return object
     * @throws Exception\InvalidCallbackException
     */
    protected function createInstanceViaCallback($callback, $params)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidCallbackException('An invalid constructor callback was provided');
        }
        
        if (is_array($callback)) {
            $class = (is_object($callback[0])) ? get_class($callback[0]) : $callback[0];
            $method = $callback[1];
        }

        $callParameters = array();
        if ($this->definition->hasInjectionMethod($class, $method)) {
            $callParameters = $this->resolveMethodParameters($class, $method, $params, true);
        }
        return call_user_func_array($callback, $callParameters); 
    }
    
    /**
     * This parameter will handle any injection methods and resolution of
     * dependencies for such methods 
     * 
     * @param object $object
     * @param string $method
     * @param array $params
     */
    protected function handleInjectionMethodForObject($object, $method, $params, $alias)
    {
        // @todo make sure to resolve the supertypes for both the object & definition
        $callParameters = $this->resolveMethodParameters(get_class($object), $method, $params, false, $alias);
        if ($callParameters !== array_fill(0, count($callParameters), null)) {
            call_user_func_array(array($object, $method), $callParameters);
        }
    }
    
    /**
     * Resolve parameters referencing other services
     * 
     * @param  array $params 
     * @return array
     */
    protected function resolveMethodParameters($class, $method, array $userParams, $isInstantiator, $alias)
    {
        static $isSubclassFunc = null;
        static $isSubclassFuncCache = null;

        $isSubclassFunc = function($class, $type) use (&$isSubclassFuncCache) {
            /* @see https://bugs.php.net/bug.php?id=53727 */
            if ($isSubclassFuncCache === null) {
                $isSubclassFuncCache = array();
            }
            if (!array_key_exists($class, $isSubclassFuncCache)) {
                $isSubclassFuncCache[$class] = class_parents($class, true) + class_implements($class, true);
            }
            return (isset($isSubclassFuncCache[$class][$type]));
        };
        
        $resolvedParams = array();

        $injectionMethodParameters = $this->definition->getInjectionMethodParameters($class, $method);
        
        $computedValueParams = array();
        $computedLookupParams = array();
        $computedOptionalParams = array();
        
        foreach ($injectionMethodParameters as $name => $info) {
            list($type, $isOptional, $isTypeInstantiable) = $info;
            
            // first consult user provided parameters
            if (isset($userParams[$name])) {
                if (is_string($userParams[$name])) {
                    if ($this->instanceManager->hasAlias($userParams[$name])) {
                        $computedLookupParams[$name] = array($userParams[$name], $this->instanceManager->getClassFromAlias($userParams[$name]));    
                    } elseif ($this->definition->hasClass($userParams[$name])) {
                        $computedLookupParams[$name] = array($userParams[$name], $userParams[$name]);
                    } else {
                        $computedValueParams[$name] = $userParams[$name];
                    }
                } else {
                    $computedValueParams[$name] = $userParams[$name];
                }
                continue;
            }
            
            // next consult alias specific properties
            if ($alias && $this->instanceManager->hasProperty($alias, $name)) {
                $computedValueParams[$name] = $this->instanceManager->getProperty($alias, $name);
                continue;
            }
            
            // next consult alias level preferred instances
            if ($alias && $this->instanceManager->hasPreferredInstances($alias)) {
                $pInstances = $this->instanceManager->getPreferredInstances($alias);
                foreach ($pInstances as $pInstance) {
                    $pInstanceClass = ($this->instanceManager->hasAlias($pInstance)) ?
                         $this->instanceManager->getClassFromAlias($pInstance) : $pInstance;
                    if ($pInstanceClass === $type || $isSubclassFunc($pInstanceClass, $type)) {
                        $computedLookupParams[$name] = array($pInstance, $pInstanceClass);
                        continue 2;
                    }
                }
            }
            
            // next consult class level preferred instances
            if ($type && $this->instanceManager->hasPreferredInstances($type)) {
                $pInstances = $this->instanceManager->getPreferredInstances($type);
                foreach ($pInstances as $pInstance) {
                    $pInstanceClass = ($this->instanceManager->hasAlias($pInstance)) ?
                         $this->instanceManager->getClassFromAlias($pInstance) : $pInstance;
                    if ($pInstanceClass === $type || $isSubclassFunc($pInstanceClass, $type)) {
                        $computedLookupParams[$name] = array($pInstance, $pInstanceClass);
                        continue 2;
                    }
                }
            }
            
            // finally consult alias specific properties
            if ($this->instanceManager->hasProperty($class, $name)) {
                $computedValueParams[$name] = $this->instanceManager->getProperty($class, $name);
                continue;
            }

            if ($isOptional) {
                $computedOptionalParams[$name] = true;
            }
            
            if ($type && $isTypeInstantiable === true) {
                $computedLookupParams[$name] = array($type, $type);
            }
            
        }

        $index = 0;
        foreach ($injectionMethodParameters as $name => $value) {

            if (isset($computedValueParams[$name])) {
                $resolvedParams[$index] = $computedValueParams[$name];
            } elseif (isset($computedLookupParams[$name])) {
                if ($isInstantiator && in_array($computedLookupParams[$name][1], $this->currentDependencies)) {
                    throw new Exception\CircularDependencyException("Circular dependency detected: $class depends on {$value[0]} and viceversa");
                }
                array_push($this->currentDependencies, $class);
                $resolvedParams[$index] = $this->get($computedLookupParams[$name][0], $userParams);
                array_pop($this->currentDependencies);
            } elseif (!array_key_exists($name, $computedOptionalParams)) {
                throw new Exception\MissingPropertyException('Missing parameter named ' . $name . ' for ' . $class . '::' . $method);
            } else {
                $resolvedParams[$index] = null;
            }
            
            $index++;
        }

        return $resolvedParams;
    }

}
