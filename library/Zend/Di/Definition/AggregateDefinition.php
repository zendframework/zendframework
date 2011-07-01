<?php

namespace Zend\Di\Definition;

use Zend\Di\Definition;

class AggregateDefinition implements Definition
{
    
    protected $definitions = array(); 
    
    
    public function addDefinition(Definition $definition)
    {
        $this->definitions[] = $definition;
    }
    
    public function getClasses()
    {
        $classes = array();
        foreach ($this->definitions as $definition) {
            $classes = array_merge($classes, $definition->getClasses());
        }
        return $classes;
    }
    
    public function hasClass($class)
    {
        foreach ($this->definitions as $definition) {
            if ($definition->hasClass($class)) {
                return true;
            }
        }
        return false;
    }
    
    public function getClassSupertypes($class)
    {
        $superTypes = array();
        foreach ($this->definitions as $definition) {
            $superTypes = array_merge($superTypes, $definition->getClassSupertypes());
        }
        return $superTypes;
    }
    
    public function getInstantiator($class)
    {
        foreach ($this->definitions as $definition) {
            if ($definition->hasClass($class)) {
                return $definition->getInstantiator($class);
            }
        }
        return false;
    }
    
    public function hasInjectionMethods($class)
    {
        foreach ($this->definitions as $definition) {
            if ($definition->hasClass($class)) {
                return $definition->hasInjectionMethods($class);
            }
        }
        return false;
    }
    
    public function hasInjectionMethod($class, $method)
    {
        foreach ($this->definitions as $definition) {
            if ($definition->hasClass($class)) {
                return $definition->hasInjectionMethod($class, $method);
            }
        }
        return false;
    }
    
    public function getInjectionMethods($class)
    {
        foreach ($this->definitions as $definition) {
            if ($definition->hasClass($class)) {
                return $definition->getInjectionMethods($class);
            }
        }
        return false;
    }
    
    public function getInjectionMethodParameters($class, $method)
    {
        foreach ($this->definitions as $definition) {
            if ($definition->hasClass($class) && $definition->hasInjectionMethod($class, $method)) {
                return $definition->getInjectionMethodParameters($class, $method);
            }
        }
        return false;
    }
    
}