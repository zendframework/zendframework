<?php

namespace Zend\Di\ServiceLocator;

class GeneratorInstance
{
    /**
     * @var string
     */
    protected $name;

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
     * Constructor
     *
     * @param string $name
     * @param mixed $constructor
     * @param array $params
     */
    public function __construct($name, $constructor, array $params)
    {
        $this->name        = $name;
        $this->constructor = $constructor;
        $this->params      = $params;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set class name
     *
     * In the case of an instance created via a callback, we need to set the 
     * class name after creating the generator instance.
     * 
     * @param  string $name 
     * @return GeneratorInstance
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get constructor
     *
     * @return mixed
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
     * Get methods
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }
}
