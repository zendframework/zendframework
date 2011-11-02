<?php

namespace Zend\Di;

class Di implements DependencyInjection
{
    /**
     * @var DefinitionList
     */
    protected $definitions = null;
    
    /**
     * @var InstanceManager
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
     * @param null|Configuration $config
     * @return \Di\Di\DependencyInjector
     */
    public function __construct(DefinitionList $definitions = null, InstanceManager $instanceManager = null, Configuration $config = null)
    {
        $this->definitions = ($definitions) ?: new DefinitionList(new Definition\RuntimeDefinition());
        $this->instanceManager = ($instanceManager) ?: new InstanceManager();

        if ($config) {
            $this->configure($config);
        }
    }

    /**
     * Provide a configuration object to configure this instance
     *
     * @param Configuration $config
     * @return void
     */
    public function configure(Configuration $config)
    {
        $config->configure($this);
    }

    /**
     * @param Definition $definition
     * @return Di
     */
    public function setDefinitionList(DefinitionList $definitions)
    {
        $this->definitions = $definitions;
        return $this;
    }

    /**
     * @return DefinitionList
     */
    public function definitions()
    {
        return $this->definitions;
    }

    /**
     * Set the instance manager
     *
     * @param InstanceManager $instanceManager
     * @return Di
     */
    public function setInstanceManager(InstanceManager $instanceManager)
    {
        $this->instanceManager = $instanceManager;
        return $this;
    }

    /**
     * 
     * @return InstanceManager
     */
    public function instanceManager()
    {
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
        
        $im = $this->instanceManager;
        
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
     * @param mixed $name Class name or service alias
     * @param array $params Parameters to pass to the constructor
     * @param bool $isShared
     * @return object|null
     */
    public function newInstance($name, array $params = array(), $isShared = true)
    {
        // localize dependencies (this also will serve as poka-yoke)
        $definitions      = $this->definitions;
        $instanceManager = $this->instanceManager();
        
        if ($instanceManager->hasAlias($name)) {
            $class = $instanceManager->getClassFromAlias($name);
            $alias = $name;
        } else {
            $class = $name;
            $alias = null;
        }

        array_push($this->instanceContext, array('NEW', $class, $alias));
        
        if (!$definitions->hasClass($class)) {
            $aliasMsg = ($alias) ? '(specified by alias ' . $alias . ') ' : '';
            throw new Exception\ClassNotFoundException(
                'Class ' . $aliasMsg . $class . ' could not be located in provided definition.'
            );
        }
        
        $instantiator     = $definitions->getInstantiator($class);
        $injectionMethods = $definitions->getMethods($class);

        if ($instantiator === '__construct') {
            $object = $this->createInstanceViaConstructor($class, $params, $alias);
            if (array_key_exists('__construct', $injectionMethods)) {
                unset($injectionMethods['__construct']);
            }
        } elseif (is_callable($instantiator)) {
            $object = $this->createInstanceViaCallback($instantiator, $params, $alias);
        } else {
            throw new Exception\RuntimeException('Invalid instantiator');
        }

        if ($isShared) {
            if ($params) {
                $this->instanceManager->addSharedInstanceWithParameters($object, $name, $params);
            } else {
                $this->instanceManager->addSharedInstance($object, $name);
            }
        }

        if ($injectionMethods) {
            foreach ($injectionMethods as $injectionMethod => $methodIsRequired) {
                if ($injectionMethod !== '__construct'){
                    $this->handleInjectionMethodForObject($object, $injectionMethod, $params, $alias, $methodIsRequired);
                }
            }

            $instanceConfiguration = $instanceManager->getConfiguration($name);

            if ($instanceConfiguration['injections']) {
                $objectsToInject = $methodsToCall = array();
                foreach ($instanceConfiguration['injections'] as $injectName => $injectValue) {
                    if (is_int($injectName) && is_string($injectValue)) {
                        $objectsToInject[] = $this->get($injectValue, $params);
                    } elseif (is_string($injectName) && is_array($injectValue)) {
                        if (is_string(key($injectValue))) {
                            $methodsToCall[] = array('method' => $injectName, 'args' => $injectValue);
                        } else {
                            foreach ($injectValue as $methodCallArgs) {
                                $methodsToCall[] = array('method' => $injectName, 'args' => $methodCallArgs);
                            }
                        }
                    } elseif (is_object($injectValue)) {
                        $objectsToInject[] = $injectValue;
                    } elseif (is_int($injectName) && is_array($injectValue)) {
                        // @todo must find method name somehow
                        throw new Exception\RuntimeException(
                            'An injection was provided with a keyed index and an array of data, try using'
                            . ' the name of a particular method as a key for your injection data.'
                        );
                    }
                }
                if ($objectsToInject) {
                    foreach ($objectsToInject as $objectToInject) {
                        foreach ($injectionMethods as $injectionMethod => $methodIsRequired) {
                            if ($methodParams = $definitions->getMethodParameters($class, $injectionMethod)) {
                                foreach ($methodParams as $methodParam) {
                                    if ($this->isSubclassOf(get_class($objectToInject), $methodParam[1])) {
                                        $callParams = $this->resolveMethodParameters(get_class($object), $injectionMethod,
                                            array($methodParam[0] => $objectToInject), false, $alias, true
                                        );
                                        if ($callParams) {
                                            call_user_func_array(array($object, $injectionMethod), $callParams);
                                        }
                                        continue 3;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($methodsToCall) {
                    foreach ($methodsToCall as $methodInfo) {
                        $callParams = $this->resolveMethodParameters(get_class($object), $methodInfo['method'],
                            $methodInfo['args'], false, $alias, true
                        );
                        call_user_func_array(array($object, $methodInfo['method']), $callParams);
                    }
                }
            }
        }
        

        
        array_pop($this->instanceContext);
        return $object;
    }
    
    /**
     * @todo 
     * @param unknown_type $object
     */
    public function injectObjects($targetObject, array $objects = array())
    {
        if ($objects === array()) {
            throw new \Exception('Not yet implmeneted');
        }

        $targetClass = get_class($targetObject);
        if (!$this->definitions()->hasClass($targetClass)) {
            throw new Exception\RuntimeException('A definition for this object type cannot be found');
        }

        foreach ($objects as $objectToInject) {

        }
    }

    /**
     * Retrieve a class instance based on class name
     *
     * Any parameters provided will be used as constructor arguments. If any
     * given parameter is a DependencyReference object, it will be fetched
     * from the container so that the instance may be injected.
     *
     * @param string $class
     * @param array $params
     * @param string|null $alias
     * @return object
     */
    protected function createInstanceViaConstructor($class, $params, $alias = null)
    {
        $callParameters = array();
        if ($this->definitions->hasMethod($class, '__construct')) {
            $callParameters = $this->resolveMethodParameters($class, '__construct', $params, true, $alias, true);
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
                return new $class($callParameters[0], $callParameters[1], $callParameters[2]);
            default:
                $r = new \ReflectionClass($class);
                return $r->newInstanceArgs($callParameters);
        }
    }

    /**
     * Get an object instance from the defined callback
     *
     * @param callback $callback
     * @param array $params
     * @param string $alias
     * @return object
     * @throws Exception\InvalidCallbackException
     */
    protected function createInstanceViaCallback($callback, $params, $alias)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidCallbackException('An invalid constructor callback was provided');
        }
        
        if (is_array($callback)) {
            $class = (is_object($callback[0])) ? get_class($callback[0]) : $callback[0];
            $method = $callback[1];
        } else {
            throw new Exception\RuntimeException('Invalid callback type provided to ' . __METHOD__);
        }

        $callParameters = array();
        if ($this->definitions->hasMethod($class, $method)) {
            $callParameters = $this->resolveMethodParameters($class, $method, $params, true, $alias, true);
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
     * @param string $alias
     */
    protected function handleInjectionMethodForObject($object, $method, $params, $alias, $methodIsRequired)
    {
        // @todo make sure to resolve the supertypes for both the object & definition
        $callParameters = $this->resolveMethodParameters(get_class($object), $method, $params, false, $alias, $methodIsRequired);
        if ($callParameters == false) {
            return;
        }
        if ($callParameters !== array_fill(0, count($callParameters), null)) {
            call_user_func_array(array($object, $method), $callParameters);
        }
    }

    /**
     * Resolve parameters referencing other services
     *
     * @param string $class
     * @param string $method
     * @param array $callTimeUserParams
     * @param bool $isInstantiator
     * @param string $alias
     * @return array
     */
    protected function resolveMethodParameters($class, $method, array $callTimeUserParams, $isInstantiator, $alias, $methodIsRequired)
    {
        // parameters for this method, in proper order, to be returned
        $resolvedParams = array();
        
        // parameter requirements from the definition
        $injectionMethodParameters = $this->definitions->getMethodParameters($class, $method);

        // computed parameters array
        $computedParams = array(
            'value'    => array(),
            'required' => array(),
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

        foreach ($injectionMethodParameters as $fqName => $info) {
            list($name, $type, $isRequired) = $info;

            // PRIORITY 1 - consult user provided parameters
            if (isset($callTimeUserParams[$fqName]) || isset($callTimeUserParams[$name])) {

                // @todo FQ Name in call time params
                if (isset($callTimeUserParams[$fqName])) {
                    $callTimeCurValue =& $callTimeUserParams[$fqName];
                } else {
                    $callTimeCurValue =& $callTimeUserParams[$name];
                }

                if (is_string($callTimeCurValue)) {
                    if ($this->instanceManager->hasAlias($callTimeCurValue)) {
                        // was an alias provided?
                        $computedParams['required'][$fqName] = array(
                            $callTimeUserParams[$name],
                            $this->instanceManager->getClassFromAlias($callTimeCurValue)
                        );    
                    } elseif ($this->definitions->hasClass($callTimeUserParams[$name])) {
                        // was a known class provided?
                        $computedParams['required'][$fqName] = array(
                            $callTimeCurValue,
                            $callTimeCurValue
                        );
                    } else {
                        // must be a value
                        $computedParams['value'][$fqName] = $callTimeCurValue;
                    }
                } else {
                    // int, float, null, object, etc
                    $computedParams['value'][$fqName] = $callTimeCurValue;
                }
                unset($callTimeCurValue);
                continue;
            }
            
            // PRIORITY 2 -specific instance configuration (thisAlias) - this alias
            // PRIORITY 3 -THEN specific instance configuration (thisClass) - this class
            // PRIORITY 4 -THEN specific instance configuration (requestedAlias) - requested alias
            // PRIORITY 5 -THEN specific instance configuration (requestedClass) - requested class
            
            foreach (array('thisAlias', 'thisClass', 'requestedAlias', 'requestedClass') as $thisIndex) {
                // check the provided parameters config
                if (isset($iConfig[$thisIndex]['parameters'][$fqName]) || isset($iConfig[$thisIndex]['parameters'][$name])) {

                    // @todo FQ Name in config parameters
                    if (isset($iConfig[$thisIndex]['parameters'][$fqName])) {
                        $iConfigCurValue =& $iConfig[$thisIndex]['parameters'][$fqName];
                    } else {
                        $iConfigCurValue =& $iConfig[$thisIndex]['parameters'][$name];
                    }

                    if (is_string($iConfigCurValue)
                        && $type === false) {
                        $computedParams['value'][$fqName] = $iConfigCurValue;
                    } elseif (is_string($iConfigCurValue)
                        && isset($aliases[$iConfigCurValue])) {
                        $computedParams['required'][$fqName] = array(
                            $iConfig[$thisIndex]['parameters'][$name],
                            $this->instanceManager->getClassFromAlias($iConfigCurValue)
                        );
                    } elseif (is_string($iConfigCurValue)
                        && $this->definitions->hasClass($iConfigCurValue)) {
                        $computedParams['required'][$fqName] = array(
                            $iConfigCurValue,
                            $iConfigCurValue
                        );
                    } elseif (is_object($iConfigCurValue)
                        && $iConfigCurValue instanceof \Closure
                        && $type !== 'Closure') {
                        $computedParams['value'][$fqName] = $iConfigCurValue();
                    } else {
                        $computedParams['value'][$fqName] = $iConfigCurValue;
                    }
                    unset($iConfigCurValue);
                    continue 2;
                }

            }
            
            // PRIORITY 6 - globally preferred implementations
            
            // next consult alias level preferred instances
            if ($alias && $this->instanceManager->hasTypePreferences($alias)) {
                $pInstances = $this->instanceManager->getTypePreferences($alias);
                foreach ($pInstances as $pInstance) {
                    if (is_object($pInstance)) {
                        $computedParams['value'][$fqName] = $pInstance;
                        continue 2;
                    }
                    $pInstanceClass = ($this->instanceManager->hasAlias($pInstance)) ?
                         $this->instanceManager->getClassFromAlias($pInstance) : $pInstance;
                    if ($pInstanceClass === $type || $this->isSubclassOf($pInstanceClass, $type)) {
                        $computedParams['required'][$fqName] = array($pInstance, $pInstanceClass);
                        continue 2;
                    }
                }
            }

            // next consult class level preferred instances
            if ($type && $this->instanceManager->hasTypePreferences($type)) {
                $pInstances = $this->instanceManager->getTypePreferences($type);
                foreach ($pInstances as $pInstance) {
                    if (is_object($pInstance)) {
                        $computedParams['value'][$fqName] = $pInstance;
                        continue 2;
                    }
                    $pInstanceClass = ($this->instanceManager->hasAlias($pInstance)) ?
                         $this->instanceManager->getClassFromAlias($pInstance) : $pInstance;
                    if ($pInstanceClass === $type || $this->isSubclassOf($pInstanceClass, $type)) {
                        $computedParams['required'][$fqName] = array($pInstance, $pInstanceClass);
                        continue 2;
                    }
                }
            }

            if (!$isRequired) {
                $computedParams['optional'][$fqName] = true;
            }

            if ($type && $isRequired && $methodIsRequired) {
                $computedParams['required'][$fqName] = array($type, $type);
            }
            
        }

        $index = 0;
        foreach ($injectionMethodParameters as $fqName => $value) {
            $name = $value[0];

            if (isset($computedParams['value'][$fqName])) {

                // if there is a value supplied, use it
                $resolvedParams[$index] = $computedParams['value'][$fqName];

            } elseif (isset($computedParams['required'][$fqName])) {

                // detect circular dependencies! (they can only happen in instantiators)
                if ($isInstantiator && in_array($computedParams['required'][$fqName][1], $this->currentDependencies)) {
                    throw new Exception\CircularDependencyException(
                        "Circular dependency detected: $class depends on {$value[1]} and viceversa"
                    );
                }
                array_push($this->currentDependencies, $class);
                $resolvedParams[$index] = $this->get($computedParams['required'][$fqName][0], $callTimeUserParams);
                array_pop($this->currentDependencies);

            } elseif (!array_key_exists($fqName, $computedParams['optional'])) {

                if ($methodIsRequired) {
                    // if this item was not marked as optional,
                    // plus it cannot be resolve, and no value exist, bail out
                    throw new Exception\MissingPropertyException(sprintf(
                        'Missing %s for parameter ' . $name . ' for ' . $class . '::' . $method,
                        (($value[0] === null) ? 'value' : 'instance/object' )
                    ));
                } else {
                    return false;
                }

            } else {
                $resolvedParams[$index] = null;
            }
            
            $index++;
        }

        return $resolvedParams; // return ordered list of parameters
    }

    /**
     * @see https://bugs.php.net/bug.php?id=53727
     *
     * @param $class
     * @param $type
     * @return bool
     */
    protected function isSubclassOf($class, $type)
    {
        /* @var $isSubclassFunc Closure */
        static $isSubclassFuncCache = null; // null as unset, array when set

        if ($isSubclassFuncCache === null) {
            $isSubclassFuncCache = array();
        }

        if (!array_key_exists($class, $isSubclassFuncCache)) {
            $isSubclassFuncCache[$class] = class_parents($class, true) + class_implements($class, true);
        }
        return (isset($isSubclassFuncCache[$class][$type]));
    }

}
