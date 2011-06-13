<?php

namespace Zend\Di\Definition\Builder;

class PhpClass
{
    protected $name = null;
    protected $instantiator = '__construct';
    protected $injectionMethods = array();
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setInstantiator($instantiator)
    {
        $this->instantiator = $instantiator;
        return $this;
    }
    
    public function getInstantiator()
    {
        return $this->instantiator;
    }
    
    public function addSuperType($superType)
    {
        $this->superTypes[] = $superType;
        return $this;
    }
    
    public function getSuperTypes()
    {
        return $this->superTypes;
    }
    
    public function addInjectionMethod(InjectionMethod $injectionMethod)
    {
        $this->injectionMethods[] = $injectionMethod;
        return $this;
    }
    
    public function getInjectionMethods()
    {
        return $this->injectionMethods;
    }

}
