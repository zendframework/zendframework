<?php

namespace Zend\Di\ServiceLocator;

class GeneratorInstance
{
    protected $name;
    protected $constructor;
    protected $params;
    protected $methods = array();

    public function __construct($name, $constructor, array $params)
    {
        $this->name        = $name;
        $this->constructor = $constructor;
        $this->params      = $params;
    }

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

    public function getConstructor()
    {
        return $this->constructor;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setMethods(array $methods)
    {
        $this->methods = $methods;
        return $this;
    }

    public function getMethods()
    {
        return $this->methods;
    }
}
