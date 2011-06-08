<?php

namespace Zend\Di;

interface Definition
{
    public function getClasses();
    public function hasClass($class);
    public function getClassSupertypes($class);
    public function getInstantiator($class);
    public function hasInjectionMethods($class);
    public function getInjectionMethods($class);
    public function hasInjectionMethod($class, $method);
    public function getInjectionMethodParameters($class, $method);    
}

