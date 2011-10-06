<?php

namespace Zend\Di\Definition;

class ArrayDefinition implements Definition
{
    
    protected $dataArray = array();
    
    public function __construct(Array $dataArray)
    {
        $this->dataArray = $dataArray;
    }
    
    public function getClasses()
    {
        return array_keys($this->dataArray);
    }
    
    public function hasClass($class)
    {
        return array_key_exists($class, $this->dataArray);
    }
    
    public function getClassSupertypes($class)
    {
        if (!isset($this->dataArray[$class])) {
            return array();
        }
        
        if (!isset($this->dataArray[$class]['superTypes'])) {
            return array();
        }
        
        return $this->dataArray[$class]['superTypes'];
    }
    
    public function getInstantiator($class)
    {
        if (!isset($this->dataArray[$class])) {
            return null;
        }
        
        if (!isset($this->dataArray[$class]['instantiator'])) {
            return '__construct';
        }
        
        return $this->dataArray[$class]['instantiator'];
    }
    
    public function hasMethods($class)
    {
        if (!isset($this->dataArray[$class])) {
            return array();
        }
        
        if (!isset($this->dataArray[$class]['injectionMethods'])) {
            return array();
        }
        
        return (count($this->dataArray[$class]['injectionMethods']) > 0);
    }
    
    public function hasMethod($class, $method)
    {
        if (!isset($this->dataArray[$class])) {
            return array();
        }
        
        if (!isset($this->dataArray[$class]['injectionMethods'])) {
            return array();
        }
        
        if (!isset($this->dataArray[$class]['injectionMethods'][$method])) {
            return array();
        }
        
        return array_key_exists($method, $this->dataArray[$class]['injectionMethods']);
    }
    
    public function getMethods($class)
    {
        if (!isset($this->dataArray[$class])) {
            return array();
        }
        
        if (!isset($this->dataArray[$class]['injectionMethods'])) {
            return array();
        }
        
        return array_keys($this->dataArray[$class]['injectionMethods']);
    }
    
    public function getMethodParameters($class, $method)
    {
        if (!isset($this->dataArray[$class])) {
            return array();
        }
        
        if (!isset($this->dataArray[$class]['injectionMethods'])) {
            return array();
        }
        
        if (!isset($this->dataArray[$class]['injectionMethods'][$method])) {
            return array();
        }
        
        return $this->dataArray[$class]['injectionMethods'][$method];
    }
    
    public function toArray()
    {
        return $this->dataArray;
    }
    
}
