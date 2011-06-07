<?php
namespace Zend\Di;

use ReflectionMethod,
    ReflectionException;

/**
 * A reference to an injectible method in a class
 *
 * Stores both the method name and arguments to pass.
 * 
 * @copyright Copyright (C) 2006-Present, Zend Technologies, Inc.
 * @license   New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class Method implements InjectibleMethod
{
    /**
     * Method name
     * @var string
     */
    protected $name;

    /**
     * Class to which method belongs
     * @var null|string
     */
    protected $class;

    /**
     * Arguments to pass to the method
     * @var array
     */
    protected $params;

    /**
     * Parameter map (order => argument name)
     * @var array
     */
    protected $paramMap;

    /**
     * The parameters in argument order
     * @var null|array
     */
    protected $orderedParams;

    /**
     * Construct the method signature
     * 
     * @param  strinb $name 
     * @param  array $params 
     * @param  array|null $paramMap
     * @return void
     */
    public function __construct($name, array $params = null, array $paramMap = null)
    {
        $this->name   = $name;
        if (is_array($params)) {
            $this->setParams($params);
        }
        if (is_array($paramMap)) {
            $this->setParamMap($paramMap);
        }
    }

    /**
     * Retrieve the method name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieve the arguments to pass to the method
     *
     * If no parameter map is present, the parameter values are returned in 
     * the order defined.
     *
     * If a parameter map is found, the parameter values are returned in the 
     * order specified by the map.
     * 
     * @return array
     */
    public function getParams()
    {
        if (null !== $this->orderedParams) {
            return $this->orderedParams;
        }

        $map = $this->getParamMap();
        if (!$map) {
            return array_values($this->params);
        }

        // Sort map, and flip such that positions become keys
        asort($map);
        $map = array_flip($map);

        $params = array();
        foreach ($map as $index => $key) {
            $value = null;
            if (isset($this->params[$key])) {
                $value = $this->params[$key];
            } elseif (isset($this->params[$index])) {
                $value = $this->params[$index];
            }
            $params[] = $value;
        }

        $this->orderedParams = $params;
        return $params;
    }

    /**
     * Set method parameters
     * 
     * @param  array $params 
     * @return Method
     */
    public function setParams(array $params)
    {
        $this->params        = $params;
        $this->orderedParams = null;
        return $this;
    }

    /**
     * Set class to which method belongs
     * 
     * @param  string $class 
     * @return Method
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Retrieve class to which method belongs, if set.
     * 
     * @return null|string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set parameter map (order of named arguments)
     *
     * Should be an ordered array, with string keys pointing to integer 
     * placement order.
     * 
     * @param  array $map 
     * @return Method
     */
    public function setParamMap(array $map)
    {
        $this->paramMap = $map;
        return $this;
    }

    /**
     * Get parameter map
     *
     * If a parameter map is present, returns it.
     *
     * If no map is set, and no class is set, returns false.
     *
     * If no map is set, but the class is set, creates a parameter map by 
     * reflecting on the method.
     * 
     * @return array|false
     */
    public function getParamMap()
    {
        if (is_array($this->paramMap)) {
            return $this->paramMap;
        }

        $class = $this->getClass();
        if ((null === $class) || !class_exists($class)) {
            return false;
        }

        $map = $this->buildParamMapFromReflection();
        $this->setParamMap($map);
        return $map;
    }

    /**
     * Create a parameter map from Reflection
     * 
     * @return array
     */
    protected function buildParamMapFromReflection()
    {
        try {
            $method = new ReflectionMethod($this->getClass(), $this->getName());
        } catch (ReflectionException $e) {
            throw new Exception\RuntimeException(sprintf(
                'Method definition for method "%s" cannot be reflected',
                $this->getName()
            ), $e->getCode(), $e);
        }
        $parameters = $method->getParameters();
        $map        = array();
        foreach ($parameters as $param) {
            $map[$param->getName()] = $param->getPosition();
        }
        return $map;
    }
}
