<?php

namespace Zend\Di;

class DependencyInjector implements DependencyInjection
{
    /**
     * @var Zend\Di\Definition
     */
    protected $definition = null;
    
    /**
     * @var Zend\Di\InstanceCollection
     */
    protected $instanceManager = null;

    /**
     * All the class dependencies [source][dependency]
     * 
     * @var array 
     */
    protected $dependencies = array();
    
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
        if ($this->config) {
            $this->setConfiguration($config);
        }
    }
    
    public function setConfiguration(Configuration $config)
    {
        // @todo process this
    }
    
    public function setDefinition(Definition\DefinitionInterface $definition)
    {
        $this->definition = $definition;
        return $this;
    }
    
    
    public function getDefinition()
    {
        if ($this->definition == null) {
            $this->definition = new Definition\RuntimeDefinition();
        }
        return $this->definition;
    }
    
    /**
     * 
     * @return Zend\Di\InstanceManager
     */
    public function getInstanceManager()
    {
        if ($this->instanceManager == null) {
            $this->instanceManager = new InstanceManager();
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
        /*
        if ($params) {
            throw new \Exception('Implementation not complete: get needs to hash params');
        }
        */
        
        $im = $this->getInstanceManager();
        
        // Cached instance
        if ($im->hasSharedInstance($name, $params)) {
            return $im->getSharedInstance($name, $params);
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
        $this->getDefinition();
        
        // check if name is alias
        //$class = (array_key_exists($name, $this->aliases)) ? $this->aliases[$name] : $name;
        $class = $name;
        
        if (!$this->definition->hasClass($class)) {
            throw new Exception\InvalidArgumentException('Invalid class name or alias provided.');
        }
        
        $instantiator = $this->definition->getInstantiator($class);
        $injectionMethods = $this->definition->getInjectionMethods($class);
        
        if ($instantiator === '__construct') {
            $object = $this->createInstanceViaConstructor($class, $params);
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
                $this->handleInjectionMethodForObject($object, $injectionMethod, $params);
            }
        }
        
        if ($isShared) {
            $this->getInstanceManager()->addSharedInstance($object, $class, $params);
        }
        
        return $object;
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
    protected function createInstanceViaConstructor($class, $params)
    {
        $callParameters = array();
        if ($this->definition->hasInjectionMethod($class, '__construct')) {
            $callParameters = $this->resolveMethodParameters($class, '__construct', $params, true);
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
    protected function handleInjectionMethodForObject($object, $method, $params)
    {
        // @todo make sure to resolve the supertypes for both the object & definition
        $callParameters = $this->resolveMethodParameters(get_class($object), $method, $params);
        call_user_func_array(array($object, $method), $callParameters);
    }
    
    /**
     * Resolve parameters referencing other services
     * 
     * @param  array $params 
     * @return array
     */
    protected function resolveMethodParameters($class, $method, array $params, $isInstantiator = false)
    {
        $resultParams = array();
        
        $params = array_merge($params, $this->getInstanceManager()->getProperties($class));
        
        $index = 0;
        foreach ($this->definition->getInjectionMethodParameters($class, $method) as $name => $value) {
            if ($value === null && !array_key_exists($name, $params)) {
                throw new Exception\RuntimeException('Missing parameter named ' . $name . ' for ' . $class . '::' . $method);
            }
            
            // circular dep check
            if ($isInstantiator && $value !== null) {
                $this->dependencies[$class][$value]= true;
                //$this->references[$serviceName][$className]= true;
            }
            
            if ($value === null) {
                $resultParams[$index] = $params[$name];
            } else {
                $resultParams[$index] = $this->get($value, $params);
            }
            $index++;
        }

        return $resultParams;
    }
    
    /**
     * Check for Circular Dependencies
     *
     * @param string $class
     * @param array|string $dependency
     * @return boolean
     */
    protected function checkCircularDependency($class, $dependency)
    {
        if (is_array($dependency)) {
            foreach ($dependency as $dep) {
                if (isset($this->dependencies[$dep][$class]) && $this->dependencies[$dep][$class]) {
                    throw new Exception\RuntimeException("Circular dependency detected: $class depends on $dep and viceversa");
                }
            }
        } else {
            if (isset($this->dependencies[$dependency][$class]) && $this->dependencies[$dependency][$class]) {
                throw new Exception\RuntimeException("Circular dependency detected: $class depends on $dependency and viceversa");
            }
        }
        return true;
    }

    /**
     * Check the circular dependencies path between two definitions 
     * 
     * @param type $class
     * @param type $dependency 
     * @return void
     */
    protected function checkPathDependencies($class, $dependency)
    {
        if (!empty($this->references[$class])) {
            foreach ($this->references[$class] as $key => $value) {
                if ($this->dependencies[$key][$class]) {
                    $this->dependencies[$key][$dependency] = true;
                    $this->checkCircularDependency($key, $dependency);
                    $this->checkPathDependencies($key,$dependency);
                }
            }
        }
    }

}
