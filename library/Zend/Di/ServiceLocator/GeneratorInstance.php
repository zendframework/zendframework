<?php

namespace Zend\Di\ServiceLocator;

class GeneratorInstance
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var string|null
     */
    protected $alias;

    /**
     * @var mixed
     */
    protected $constructor;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var array
     */
    protected $methods = array();

    /**
     * @var bool
     */
    protected $shared = true;

    /**
     * @param string|null $class
     * @param string|null $alias
     * @param mixed       $constructor
     * @param array       $params
     */
    public function __construct($class, $alias, $constructor, array $params)
    {
        $this->class       = $class;
        $this->alias       = $alias;
        $this->constructor = $constructor;
        $this->params      = $params;
    }

    /**
     * @return string instance or class name
     */
    public function getName()
    {
        return $this->alias ? $this->alias : $this->class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string|null
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set class name
     *
     * In the case of an instance created via a callback, we need to set the
     * class name after creating the generator instance.
     *
     * @param  string $class
     * @return GeneratorInstance
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Set instance alias
     *
     * @param  string $alias
     * @return GeneratorInstance
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * Get instantiator
     *
     * @return mixed constructor method or callable for the instance
     */
    public function getConstructor()
    {
        return $this->constructor;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set methods
     *
     * @param array $methods
     * @return GeneratorInstance
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * Add a method called on the instance
     *
     * @param $method
     * @return GeneratorInstance
     */
    public function addMethod($method)
    {
        $this->methods[] = $method;
        return $this;
    }

    /**
     * Retrieves an ordered list of methods called on the instance
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param bool $shared
     */
    public function setShared($shared)
    {
        $this->shared = (bool) $shared;
    }

    /**
     * @return bool
     */
    public function isShared()
    {
        return $this->shared;
    }
}
