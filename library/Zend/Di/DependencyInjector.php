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
     * @var string
     */
    protected $instanceContext = array();
    
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
            throw new Exception\InvalidArgumentException(
                'The class provided to the Definition factory ' . $class 
                . ' does not implement the Definition interface'
            );
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
        if (!$instanceManager instanceof InstanceManager) {
            throw new Exception\InvalidArgumentException(
                'The class provided to the InstanceManager factory ' . $class 
                . ' does not implement the InstanceCollection interface'
            );
        }
        return $instanceManager;
    }
    
    /**
     * 
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
        array_push($this->instanceContext, array('GET', $name));
        
        $im = $this->getInstanceManager();
        
        if ($params) {
            if (($fastHash = $im->hasSharedInstanceWithParameters($name, $params, true))) {
                array_pop($this->instanceContext);
                return $im->getSharedInstanceWithParameters(null, array(), $fastHash);
            }
        } else {
            if ($im->hasSharedInstance($name, $params)) {
                array_pop($this->instanceContext);
                return $im->getSharedInstance($name, $params);
            }
        }
        $instance = $this->newInstance($name, $params);
        array_pop($this->instanceContext);
        return $instance;
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
        // localize dependencies (this also will serve as poka-yoke)
        $definition      = $this->getDefinition();
        $instanceManager = $this->getInstanceManager();
        
        if ($instanceManager->hasAlias($name)) {
            $class = $instanceManager->getClassFromAlias($name);
            $alias = $name;
        } else {
            $class = $name;
            $alias = null;
        }

        array_push($this->instanceContext, array('NEW', $class, $alias));
        
        if (!$definition->hasClass($class)) {
            $aliasMsg = ($alias) ? '(specified by alias ' . $alias . ') ' : '';
            throw new Exception\ClassNotFoundException(
                'Class ' . $aliasMsg . $class . ' could not be located in provided definition.'
            );
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

            $iConfig = ($instanceManager->hasAlias($alias) && $instanceManager->hasConfiguration($alias))
                ? $instanceManager->getConfiguration($alias)
                : $instanceManager->getConfiguration(get_class($object));

            if ($iConfig['methods']) {
                foreach ($iConfig['methods'] as $iConfigMethod => $iConfigMethodParams) {
                    // skip methods processed by handleInjectionMethodForObject
                    if (in_array($iConfigMethod, $injectionMethods) && $iConfigMethod !== '__construct') continue; 
                    call_user_func_array(array($object, $iConfigMethod), array_values($iConfigMethodParams));
                }
            }
        }
        
        if ($isShared) {
            if ($params) {
                $this->getInstanceManager()->addSharedInstanceWithParameters($object, $name, $params);
            } else {
                $this->getInstanceManager()->addSharedInstance($object, $name);
            }
        }
        
        array_pop($this->instanceContext);
        return $object;
    }
    
    /**
     * @todo 
     * Enter description here ...
     * @param unknown_type $object
     */
    // public function resolveObjectDependencies($object)
    // {
    //     
    // }
    
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
    protected function resolveMethodParameters($class, $method, array $callTimeUserParams, $isInstantiator, $alias)
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
        
        // parameters for this method, in proper order, to be returned
        $resolvedParams = array();
        
        // parameter requirements from the definition
        $injectionMethodParameters = $this->definition->getInjectionMethodParameters($class, $method);
        
        // computed parameters array
        $computedParams = array(
            'value'    => array(),
            'lookup'   => array(),
            'optional' => array()
        );
        
        // retrieve instance configurations for all contexts
        $iConfig = array();
        $aliases = $this->instanceManager->getAliases();
        
        // for the alias in the dependency tree
        if ($alias && $this->instanceManager->hasConfiguration($alias)) {
            $iConfig['thisAlias'] = $this->instanceManager->getConfiguration($alias);
        }
        
        // for the current class in the dependency tree
        if ($this->instanceManager->hasConfiguration($class)) {
            $iConfig['thisClass'] = $this->instanceManager->getConfiguration($class);
        }
        
        // for the parent class, provided we are deeper than one node
        list($requestedClass, $requestedAlias) = ($this->instanceContext[0][0] == 'NEW')
            ? array($this->instanceContext[0][1], $this->instanceContext[0][2])
            : array($this->instanceContext[1][1], $this->instanceContext[1][2]);

        if ($requestedClass != $class && $this->instanceManager->hasConfiguration($requestedClass)) {
            $iConfig['requestedClass'] = $this->instanceManager->getConfiguration($requestedClass);
            if ($requestedAlias) {
                $iConfig['requestedAlias'] = $this->instanceManager->getConfiguration($requestedAlias);
            }
        }

        // This is a 2 pass system for resolving parameters
        // first pass will find the sources, the second pass will order them and resolve lookups if they exist
        // MOST methods will only have a single parameters to resolve, so this should be fast
        
        foreach ($injectionMethodParameters as $name => $info) {
            list($type, $isOptional, $isTypeInstantiable) = $info;

            // PRIORITY 1 - consult user provided parameters
            if (isset($callTimeUserParams[$name])) {
                if (is_string($callTimeUserParams[$name])) {
                    if ($this->instanceManager->hasAlias($callTimeUserParams[$name])) {
                        // was an alias provided?
                        $computedParams['lookup'][$name] = array(
                            $callTimeUserParams[$name],
                            $this->instanceManager->getClassFromAlias($callTimeUserParams[$name])
                        );    
                    } elseif ($this->definition->hasClass($callTimeUserParams[$name])) {
                        // was a known class provided?
                        $computedParams['lookup'][$name] = array(
                            $callTimeUserParams[$name],
                            $callTimeUserParams[$name]
                        );
                    } else {
                        // must be a value
                        $computedParams['value'][$name] = $callTimeUserParams[$name]; 
                    }
                } else {
                    // int, float, null, object, etc
                    $computedParams['value'][$name] = $callTimeUserParams[$name];
                }
                continue;
            }
            
            // PRIORITY 2 -specific instance configuration (thisAlias) - this alias
            // PRIORITY 3 -THEN specific instance configuration (thisClass) - this class
            // PRIORITY 4 -THEN specific instance configuration (requestedAlias) - requested alias
            // PRIORITY 5 -THEN specific instance configuration (requestedClass) - requested class
            
            foreach (array('thisAlias', 'thisClass', 'requestedAlias', 'requestedClass') as $thisIndex) {
                // check the provided parameters config
                if (isset($iConfig[$thisIndex]['parameters'][$name])) {
                    if (isset($aliases[$iConfig[$thisIndex]['parameters'][$name]])) {
                        $computedParams['lookup'][$name] = array(
                            $iConfig[$thisIndex]['parameters'][$name],
                            $this->instanceManager->getClassFromAlias($iConfig[$thisIndex]['parameters'][$name])
                        );
                    } elseif ($this->definition->hasClass($iConfig[$thisIndex]['parameters'][$name])) {
                        $computedParams['lookup'][$name] = array(
                            $iConfig[$thisIndex]['parameters'][$name],
                            $iConfig[$thisIndex]['parameters'][$name]
                        );
                    } else {
                        $computedParams['value'][$name] = $iConfig[$thisIndex]['parameters'][$name];
                    }
                    continue 2;
                }
                // check the provided method config
                if (isset($iConfig[$thisIndex]['methods'][$method][$name])) {
                    if (is_string(is_string($iConfig[$thisIndex]['methods'][$method][$name]))
                        && isset($aliases[$iConfig[$thisIndex]['methods'][$method][$name]])) {
                        $computedParams['lookup'][$name] = array(
                            $iConfig[$thisIndex]['methods'][$method][$name],
                            $this->instanceManager->getClassFromAlias($iConfig[$thisIndex]['methods'][$method][$name])
                        );
                    } elseif (is_string(is_string($iConfig[$thisIndex]['methods'][$method][$name]))
                        && $this->definition->hasClass($iConfig[$thisIndex]['methods'][$method][$name])) {
                        $computedParams['lookup'][$name] = array(
                            $iConfig[$thisIndex]['methods'][$method][$name],
                            $iConfig[$thisIndex]['methods'][$method][$name]
                        );
                    } else {
                        $computedParams['value'][$name] = $iConfig[$thisIndex]['methods'][$method][$name];
                    }
                    continue 2;
                }
            
            }
            
            // PRIORITY 6 - globally preferred implementations
            
            // next consult alias level preferred instances
            if ($alias && $this->instanceManager->hasTypePreferences($alias)) {
                $pInstances = $this->instanceManager->getTypePreferences($alias);
                foreach ($pInstances as $pInstance) {
                    $pInstanceClass = ($this->instanceManager->hasAlias($pInstance)) ?
                         $this->instanceManager->getClassFromAlias($pInstance) : $pInstance;
                    if ($pInstanceClass === $type || $isSubclassFunc($pInstanceClass, $type)) {
                        $computedParams['lookup'][$name] = array($pInstance, $pInstanceClass);
                        continue 2;
                    }
                }
            }

            // next consult class level preferred instances
            if ($type && $this->instanceManager->hasTypePreferences($type)) {
                $pInstances = $this->instanceManager->getTypePreferences($type);
                foreach ($pInstances as $pInstance) {
                    $pInstanceClass = ($this->instanceManager->hasAlias($pInstance)) ?
                         $this->instanceManager->getClassFromAlias($pInstance) : $pInstance;
                    if ($pInstanceClass === $type || $isSubclassFunc($pInstanceClass, $type)) {
                        $computedParams['lookup'][$name] = array($pInstance, $pInstanceClass);
                        continue 2;
                    }
                }
            }

            if ($isOptional) {
                $computedParams['optional'][$name] = true;
            }
            
            if ($type && $isTypeInstantiable === true && !$isOptional) {
                $computedParams['lookup'][$name] = array($type, $type);
            }
            
        }

        $index = 0;
        foreach ($injectionMethodParameters as $name => $value) {

            if (isset($computedParams['value'][$name])) {
                $resolvedParams[$index] = $computedParams['value'][$name];
            } elseif (isset($computedParams['lookup'][$name])) {
                if ($isInstantiator && in_array($computedParams['lookup'][$name][1], $this->currentDependencies)) {
                    throw new Exception\CircularDependencyException(
                        "Circular dependency detected: $class depends on {$value[0]} and viceversa"
                    );
                }
                array_push($this->currentDependencies, $class);
                $resolvedParams[$index] = $this->get($computedParams['lookup'][$name][0], $callTimeUserParams);
                array_pop($this->currentDependencies);
            } elseif (!array_key_exists($name, $computedParams['optional'])) {
                throw new Exception\MissingPropertyException(
                    'Missing parameter named ' . $name . ' for ' . $class . '::' . $method
                );
            } else {
                $resolvedParams[$index] = null;
            }
            
            $index++;
        }

        return $resolvedParams;
    }

}
