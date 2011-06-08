<?php

namespace Zend\Di\Definition;

use Zend\Di\Definition;

class RuntimeDefinition implements Definition
{
    
    const LOOKUP_TYPE_IMPLICIT = 'implicit';
    const LOOKUP_TYPE_EXPLICIT = 'explicit';
    
    protected $lookupType = self::LOOKUP_TYPE_IMPLICIT;
    
    protected $classes = array();
    
    public function getClasses()
    {
        return array();
    }
    
    /**
     * Track classes when using EXPLICIT lookups
     * @param unknown_type $class
     */
    public function addClass($class)
    {
        $this->classes[] = $class;
    }
    
    public function setLookupType($lookupType)
    {
        $this->lookupType = $lookupType;
    }
    
    public function hasClass($class)
    {
        return class_exists($class, true);
    }
    
    public function getClassSupertypes($class)
    {
        return class_parents($class) + class_implements($class);
    }
    
    public function getInstantiator($class)
    {
        $class = new \ReflectionClass($class);
        if ($class->isInstantiable()) {
            return '__construct';
        }
        return false;
    }
    
    public function hasInjectionMethods($class)
    {
        
    }
    
    public function hasInjectionMethod($class, $method)
    {
        $c = new \ReflectionClass($class);
        return $c->hasMethod($method);
    }
    
    public function getInjectionMethods($class)
    {
        $methods = array();
        $c = new \ReflectionClass($class);
        if ($c->hasMethod('__construct')) {
            $methods[] = '__construct';
        }
        foreach ($c->getMethods() as $m) {
            if (preg_match('#^set[A-Z]#', $m->getName())) {
                $methods[] = $m->getName();
            }
        }
        return $methods;
    }
    
    public function getInjectionMethodParameters($class, $method)
    {
        $params = array();
        $rc = new \ReflectionClass($class);
        if (($rm = $rc->getMethod($method)) === false) {
            throw new \Exception('method not found');
        }
        
        $rps = $rm->getParameters();
        foreach ($rps as $rp) {
            $rpClass = $rp->getClass();
            $params[$rp->getName()] = ($rpClass !== null) ? $rpClass->getName() : null;
        }
        
        return $params;
    }
    
    
    
}