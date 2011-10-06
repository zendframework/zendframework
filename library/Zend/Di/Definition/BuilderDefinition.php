<?php

namespace Zend\Di\Definition;

use Zend\Di\Definition,
    Zend\Di\Exception;

class BuilderDefinition implements Definition
{
    protected $defaultClassBuilder = 'Zend\Di\Definition\Builder\PhpClass';
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
                    case 'methods':
                    case 'method':
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

    /**
     * Create a class builder object using default class builder class
     *
     * This method is a factory that can be used in place of addClass().
     * 
     * @param  null|string $name Optional name of class to assign
     * @return Builder\PhpClass
     */
    public function createClass($name = null)
    {
        $builderClass = $this->defaultClassBuilder;
        $class = new $builderClass();
        if (null !== $name) {
            $class->setName($name);
        }

        $this->addClass($class);
        return $class;
    }

    /**
     * Set the class to use with {@link createClass()}
     * 
     * @param  string $class 
     * @return BuilderDefinition
     */
    public function setClassBuilder($class)
    {
        $this->defaultClassBuilder = $class;
        return $this;
    }

    /**
     * Get the class used for {@link createClass()}
     *
     * This is primarily to allow developers to temporarily override 
     * the builder strategy.
     * 
     * @return string
     */
    public function getClassBuilder()
    {
        return $this->defaultClassBuilder;
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
    
    public function hasMethods($class)
    {
        /* @var $class Zend\Di\Definition\Builder\PhpClass */
        $class = $this->getClass($class);
        if ($class === false) {
            throw new Exception\RuntimeException('Cannot find class object in this builder definition.');
        }
        return (count($class->getInjectionMethods()) > 0);
    }
    
    public function getMethods($class)
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
    
    public function hasMethod($class, $method)
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
    
    public function getMethodParameters($class, $method)
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
