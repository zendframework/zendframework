<?php

namespace Zend\Di\Definition;

use Zend\Di\Definition,
    Zend\Di\Exception;

class BuilderDefinition implements Definition
{
    
    protected $classes = array();

    public function createClassesFromArray(array $builderData)
    {
        foreach ($builderData as $className => $classInfo) {
            $class = new Builder\PhpClass();
            $class->setName($className);
            foreach ($classInfo as $type => $typeData) {
                switch (strtolower($type)) {
                    case 'supertypes':
                        foreach ($typeData as $superType) {
                            $class->addSuperType($superType);
                        }
                        break;
                    case 'instantiator':
                        $class->setInstantiator($typeData);
                        break;
                    case 'injectionmethods':
                    case 'injectionmethod':
                        foreach ($typeData as $injectionMethodName => $injectionMethodData) {
                            $injectionMethod = new Builder\InjectionMethod();
                            $injectionMethod->setName($injectionMethodName);
                            foreach ($injectionMethodData as $parameterName => $parameterType) {
                                $parameterType = ($parameterType) ?: null; // force empty string to null
                                $injectionMethod->addParameter($parameterName, $parameterType);
                            }
                            $class->addInjectionMethod($injectionMethod);
                        }
                        break;
                    
                }
            }
            $this->addClass($class);
        }
    }
    
    public function addClass(Builder\PhpClass $phpClass)
    {
        $this->classes[] = $phpClass;
        return $this;
    }
    
    public function getClasses()
    {
        $classNames = array();
        foreach ($this->classes as $class) {
            $classNames[] = $class->getName();
        }
        return $classNames;
    }
    
    public function hasClass($class)
    {
        foreach ($this->classes as $classObj) {
            if ($classObj->getName() === $class) {
                return true;
            }
        }
        return false;
    }
    
    protected function getClass($name)
    {
        foreach ($this->classes as $classObj) {
            if ($classObj->getName() === $name) {
                return $classObj;
            }
        }
        return false;
    }
    
    public function getClassSupertypes($class)
    {
        $class = $this->getClass($class);
        if ($class === false) {
            throw new Exception\RuntimeException('Cannot find class object in this builder definition.');
        }
        return $class->getSuperTypes();
    }
    
    public function getInstantiator($class)
    {
        $class = $this->getClass($class);
        if ($class === false) {
            throw new Exception\RuntimeException('Cannot find class object in this builder definition.');
        }
        return $class->getInstantiator();
    }
    
    public function hasInjectionMethods($class)
    {
        /* @var $class Zend\Di\Definition\Builder\PhpClass */
        $class = $this->getClass($class);
        if ($class === false) {
            throw new Exception\RuntimeException('Cannot find class object in this builder definition.');
        }
        return (count($class->getInjectionMethods()) > 0);
    }
    
    public function getInjectionMethods($class)
    {
        $class = $this->getClass($class);
        if ($class === false) {
            throw new Exception\RuntimeException('Cannot find class object in this builder definition.');
        }
        $methods = $class->getInjectionMethods();
        $methodNames = array();
        foreach ($methods as $methodObj) {
            $methodNames[] = $methodObj->getName();
        }
        return $methodNames;
    }
    
    public function hasInjectionMethod($class, $method)
    {
        $class = $this->getClass($class);
        if ($class === false) {
            throw new Exception\RuntimeException('Cannot find class object in this builder definition.');
        }
        $methods = $class->getInjectionMethods();
        foreach ($methods as $methodObj) {
            if ($methodObj->getName() === $method) {
                return true;
            }
        }
        return false;
    }
    
    public function getInjectionMethodParameters($class, $method)
    {
        $class = $this->getClass($class);
        if ($class === false) {
            throw new Exception\RuntimeException('Cannot find class object in this builder definition.');
        }
        $methods = $class->getInjectionMethods();
        foreach ($methods as $methodObj) {
            if ($methodObj->getName() === $method) {
                $method = $methodObj;
            }
        }
        if (!$method instanceof Builder\InjectionMethod) {
            throw new Exception\RuntimeException('Cannot find method object for method ' . $method . ' in this builder definition.');
        }
        return $method->getParameters();
    }
}
