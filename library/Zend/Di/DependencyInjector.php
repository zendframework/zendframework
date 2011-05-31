<?php
namespace Zend\Di;

class DependencyInjector implements DependencyInjection
{
    /**
     * Aliases to attached definitions
     * @var array
     */
    protected $aliases = array();

    /**
     * Aggregated definitions
     * @var array
     */
    protected $definitions = array();

    /**
     * Already loaded instances, by classname
     */
    protected $instances = array();

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
        // Cached instance
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
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
    public function newInstance($name, array $params = array())
    {
        // Class name provided
        if (isset($this->definitions[$name])) {
            $instance = $this->getInstanceFromDefinition($this->definitions[$name], $params);
            if ($this->definitions[$name]->isShared()) {
                $this->instances[$name] = $instance;
            }
            return $instance;
        }

        // Alias resolved to definition
        if (false !== $definition = $this->getDefinitionFromAlias($name)) {
            $class = $definition->getClass();
            if (isset($this->instances[$class])) {
                return $this->instances[$class];
            }
            $instance = $this->getInstanceFromDefinition($definition, $params);
            if ($definition->isShared()) {
                $this->instances[$name]  = $instance;
                $this->instances[$class] = $instance;
            }
            return $instance;
        }

        // Test if class exists, and return instance if possible
        if (!class_exists($name)) {
            return null;
        }
        $instance = $this->getInstanceFromClassName($name, $params);
        return $instance;
    }
    
    /**
     * Set many definitions at once
     *
     * String keys will be used as the $serviceName argument to 
     * {@link setDefinition()}.
     *
     * @param  array|Traversable $definitions Iterable Definition objects
     * @return DependencyInjector
     */
    public function setDefinitions($definitions)
    {
        foreach ($definitions as $name => $definition) {
            if (!is_string($name) || is_numeric($name) || empty($name)) {
                $name = null;
            }
            $this->setDefinition($definition, $name);
        }
        return $this;
    }
    
    /**
     * Add a definition, optionally with a service name alias
     * 
     * @param  DependencyDefinition $definition 
     * @param  string $serviceName 
     * @return DependencyInjector
     */
    public function setDefinition(DependencyDefinition $definition, $serviceName = null)
    {
        $className = $definition->getClass();
        $this->definitions[$className] = $definition;
        if (null !== $serviceName && !empty($serviceName)) {
            $this->aliases[$serviceName] = $className;
        }
        return $this;
    }

    /**
     * Alias a given service/class name so that it may be referenced by another name
     * 
     * @param  string $alias 
     * @param  string $serviceName Class name or service/alias name
     * @return DependencyInjector
     */
    public function setAlias($alias, $serviceName)
    {
        $this->aliases[$alias] = $serviceName;
        return $this;
    }

    /**
     * Retrieve aggregated definitions
     * 
     * @return array
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * Retrieve defined aliases
     * 
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Get an object instance based on a Definition object
     * 
     * @param  DependencyDefinition $definition 
     * @param  array $params 
     * @return object
     */
    protected function getInstanceFromDefinition(DependencyDefinition $definition, array $params)
    {
        $class  = $definition->getClass();
        $params = array_merge($definition->getParams(), $params);

        if ($definition->hasConstructorCallback()) {
            $object = $this->getInstanceFromCallback($definition->getConstructorCallback(), $params);
        } else {
            $object = $this->getInstanceFromClassName($class, $params);
        }
        $this->injectMethods($object, $definition);
        return $object;
    }

    /**
     * Resolve a Definition class based on the alias provided
     * 
     * @param  string $name 
     * @return false|DependencyDefinition
     */
    protected function getDefinitionFromAlias($name)
    {
        if (!isset($this->aliases[$name])) {
            return false;
        }

        $service = $this->aliases[$name];
        if (!isset($this->definitions[$service])) {
            return $this->getDefinitionFromAlias($service);
        }

        return $this->definitions[$service];
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
    protected function getInstanceFromClassName($class, array $params)
    {
        // Hack to avoid Reflection in most common use cases
        switch (count($params)) {
            case 0:
                return new $class();
            case 1:
                $param = array_shift($params);
                if (null === $param) {
                    return new $class();
                }
                if ($param instanceof DependencyReference) {
                    $param = $this->get($param->getServiceName());
                }
                return new $class($param);
            case 2:
                $param1 = array_shift($params);
                if ($param1 instanceof DependencyReference) {
                    $param1 = $this->get($param1->getServiceName());
                }
                $param2 = array_shift($params);
                if ($param2 instanceof DependencyReference) {
                    $param2 = $this->get($param2->getServiceName());
                }
                return new $class($param1, $param2);
            default:
                $params = $this->resolveReferences($params);
                $r = new \ReflectionClass($class);
                return $r->newInstanceArgs($params);
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
    protected function getInstanceFromCallback($callback, array $params)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidCallbackException('An invalid constructor callback was provided');
        }
        $params = $this->resolveReferences($params);
        return call_user_func_array($callback, $params);
    }

    /**
     * Resolve parameters referencing other services
     * 
     * @param  array $params 
     * @return array
     */
    protected function resolveReferences(array $params)
    {
        foreach ($params as $key => $value) {
            if ($value instanceof DependencyReference) {
                $params[$key] = $this->get($value->getServiceName());
            }
        }
        return $params;
    }

    /**
     * Call setter methods in order to inject dependencies
     * 
     * @param  object $object 
     * @param  DependencyDefinition $definition 
     * @return void
     */
    protected function injectMethods($object, DependencyDefinition $definition)
    {
        foreach ($definition->getMethodCalls() as $name => $info)
        {
            if (!method_exists($object, $name)) {
                continue;
            }

            $params = $info->getParams();
            foreach ($params as $key => $param) {
                if ($param instanceof DependencyReference) {
                    $params[$key] = $this->get($param->getServiceName());
                }
            }
            call_user_func_array(array($object, $name), $params);
        }
    }
}
