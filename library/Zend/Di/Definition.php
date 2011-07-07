<?php

namespace Zend\Di;

interface Definition
{
    const PARAMETER_REQUIRED = 0x00;
    const PARAMETER_OPTIONAL = 0x01;
    
    public function getClasses();
    public function hasClass($class);
    public function getClassSupertypes($class);
    public function getInstantiator($class);
    public function hasInjectionMethods($class);
    public function getInjectionMethods($class);
    public function hasInjectionMethod($class, $method);
    public function getInjectionMethodParameters($class, $method);    
}

