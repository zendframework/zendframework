<?php

namespace Zend\Di\Definition\Builder;

class InjectionMethod
{
    const PARAMETER_POSTION_NEXT = 'next';
    
    protected $name = null;
    protected $parameters = array();
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function addParameter($name, $class = null, $paramIsOptional = false, $classIsInstantiable = null)
    {
        $this->parameters[$name] = array(
            $class,
            $paramIsOptional,
            (($classIsInstantiable === null && $class !== null)
                ? (($classIsInstantiable === null) ? true : (bool) $classIsInstantiable)
                : null)
        );
        return $this;
    }
    
    public function getParameters()
    {
        return $this->parameters;
    }
    
}
