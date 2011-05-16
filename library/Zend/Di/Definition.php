<?php
namespace Zend\Di;

use ReflectionClass;

/**
 * Representation of a class, constructor arguments, and methods expecting injection
 *
 * @copyright Copyright (C) 2006-Present, Zend Technologies, Inc.
 * @license   New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class Definition implements DependencyDefinition
{
    /**
     * @var string Class name represented by this definition
     */
    protected $className;

    /**
     * Callback that will return an instance of the defined class
     * @var false|callback
     */
    protected $constructorCallback = false;

    /**
     * Map of constructor argument names to positions
     * @var array
     */
    protected $constructorParamMap;

    /**
     * Parameters to pass to the constructor
     * 
     * @var array
     */
    protected $constructorParams = array();

    /**
     * Collection of methods to inject
     * 
     * @var Methods
     */
    protected $injectibleMethods;

    /**
     * Sorted and ordered list of constructor parameters
     * 
     * @var null|array
     */
    protected $orderedConstructorParams;

    /**
     * Whether or not the container should return new instances, or the same instance
     * 
     * @var bool
     */
    protected $shareInstances = true;

    /**
     * Tags by which this definition may be alternately retrieved
     * 
     * @var array
     */
    protected $tags = array();

    /**
     * Create a definition for the given class name
     * 
     * @param  string $className 
     * @return void
     */
    public function __construct($className)
    {
        $this->className         = $className;
        $this->injectibleMethods = new Methods();
    }

    /**
     * Get the defined class name
     * 
     * @return string
     */
    public function getClass()
    {
        return $this->className;
    }

    /**
     * Provide a callback to use in order to get an instance
     * 
     * @param  callback $callback 
     * @return Definition
     */
    public function setConstructorCallback($callback)
    {
        $this->constructorCallback = $callback;
        return $this;
    }

    /**
     * Retrieve the constructor callback, if any
     * 
     * @return false|callback
     */
    public function getConstructorCallback()
    {
        return $this->constructorCallback;
    }

    /**
     * Do we define a constructor callback?
     * 
     * @return bool
     */
    public function hasConstructorCallback()
    {
        if (false === $this->constructorCallback) {
            return false;
        }

        return true;
    }

    /**
     * Set a constructor parameter
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return Definition
     */
    public function setParam($name, $value)
    {
        $this->constructorParams[$name] = $value;
        $this->orderedConstructorParams = null;
        return $this;
    }

    /**
     * Set all parameters at once
     * 
     * @param  array $params 
     * @return Definition
     */
    public function setParams(array $params)
    {
        $this->constructorParams = $params;
        $this->orderedConstructorParams = null;
        return $this;
    }

    /**
     * Define the constructor parameter map
     *
     * Maps parameter names to position in order to specify argument order.
     *
     * @param  array $map Map of name => position pairs for constructor arguments
     * @return Definition
     */
    public function setParamMap(array $map)
    {
        foreach ($map as $name => $position) {
            if (!is_int($position) && !is_numeric($position)) {
                throw new Exception\InvalidPositionException();
            }
            if (!is_string($name) || empty($name)) {
                throw new Exception\InvalidParamNameException();
            }
        }
        $positions = array_values($map);
        sort($positions);
        if (!empty($positions) && ($positions != range(0, count($positions) - 1))) {
            throw new Exception\InvalidPositionException('Positions are non-sequential');
        }
        $this->constructorParamMap = $map;
        $this->orderedConstructorParams = null;
        return $this;
    }

    /**
     * Retrieve constructor parameters
     *
     * Returns the constructor parameters in the order in which they should be
     * passed to the constructor, as an indexed array.
     * 
     * @return array
     */
    public function getParams()
    {
        if (null !== $this->orderedConstructorParams) {
            return $this->orderedConstructorParams;
        }

        if (null === $this->constructorParamMap) {
            $this->buildConstructorParamMapFromReflection();
        }
        $map = $this->constructorParamMap;

        // Sort map, and flip such that positions become keys
        asort($map);
        $map = array_flip($map);

        $params = array();
        foreach ($map as $key) {
            $value = isset($this->constructorParams[$key]) ? $this->constructorParams[$key] : null;
            $params[] = $value;
        }

        $this->orderedConstructorParams = $params;
        return $params;
    }
    
    /**
     * Should the container return the same or different instances?
     * 
     * @param  bool $flag 
     * @return Definition
     */
    public function setShared($flag = true)
    {
        $this->shareInstances = (bool) $flag;
        return $this;
    }

    /**
     * Should the container return the same or different instances?
     * 
     * @return bool
     */
    public function isShared()
    {
        return $this->shareInstances;
    }
    
    /**
     * Add a tag
     *
     * Tags may be used by container-defined classes to retrieve dependencies, 
     * plugins, helpers, etc.
     * 
     * @param  string $tag 
     * @return Definition
     */
    public function addTag($tag)
    {
        if (!is_string($tag) || empty($tag)) {
            throw new Exception\InvalidArgumentException('Tag must be a string and non-empty');
        }
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
        return $this;
    }

    /**
     * Add many tags at once
     * 
     * @param  array $tags 
     * @return Definition
     */
    public function addTags(array $tags)
    {
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }
        return $this;
    }

    /**
     * Retrieve all tags associated with this class
     * 
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Is the given tag associated with this definition
     * 
     * @param  string $tag 
     * @return bool
     */
    public function hasTag($tag)
    {
        return (in_array($tag, $this->tags));
    }
    
    /**
     * Add a method to be called and injected
     * 
     * @param  string $name 
     * @param  array $args 
     * @return Definition
     */
    public function addMethodCall($name, array $args)
    {
        $method = new Method($name, $args);
        $this->injectibleMethods->insert($method);
        return $this;
    }

    /**
     * Get collection of injectible methods
     *
     * @return InjectibleMethods
     */
    public function getMethodCalls()
    {
        return $this->injectibleMethods;
    }

    /**
     * Build the constructor parameter map from Reflection
     * 
     * @return void
     */
    protected function buildConstructorParamMapFromReflection()
    {
        $class = new ReflectionClass($this->getClass());
        if (!$class->hasMethod('__construct')) {
            $this->setParamMap(array());
            return;
        }
        $constructor = $class->getMethod('__construct');
        $parameters  = $constructor->getParameters();
        $params      = array();
        foreach ($parameters as $param) {
            $params[$param->getName()] = $param->getPosition();
        }
        $this->setParamMap($params);
    }
}
