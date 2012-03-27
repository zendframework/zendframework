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
    
    public function addParameter($name, $class = null, $isRequired = null)
    {
        $this->parameters[] = array(
            $name,
            $class,
            ($isRequired == null) ? true : false
        );
        return $this;
    }
    
    public function getParameters()
    {
        return $this->parameters;
    }
    
}
