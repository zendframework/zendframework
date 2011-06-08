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
    }
    
    public function getName($name)
    {
        return $this->name;
    }
    
    public function addParameter($name, $class = null, $position = self::PARAMETER_POSTION_NEXT)
    {
        if ($position == self::PARAMETER_POSTION_NEXT) {
            $this->parameters[$name] = $class;
        } else {
            throw new \Exception('Implementation for parameter placement is incomplete');
        }
    }
    
    public function getParameters()
    {
        return $this->parameters;
    }
    
}
